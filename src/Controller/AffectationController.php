<?php

namespace App\Controller;

use App\Entity\Affectation;
use App\Form\AffectationType;
use App\Repository\AffectationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Chantier;
use App\Repository\ChantierRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/affectation')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class AffectationController extends AbstractController
{
    #[Route('/', name: 'app_affectation_index', methods: ['GET'])]
    #[IsGranted('ROLE_OUVRIER')] // Seuls les ouvriers peuvent voir les affectations
    public function index(AffectationRepository $affectationRepository, ChantierRepository $chantierRepository): Response
    {
        $user = $this->getUser();

        if ($this->isGranted('ROLE_ADMIN')) {
            // Admin voit toutes les affectations
            $affectations = $affectationRepository->findAll();
            $title = 'Toutes les affectations';
        } else {
            // Ouvrier voit seulement ses propres affectations
            $affectations = $affectationRepository->findBy(['ouvrier' => $user]);
            $title = 'Mes affectations';
        }

        return $this->render('affectation/index.html.twig', [
            'affectations' => $affectations,
            'title' => $title,
            'chantiers' => $this->isGranted('ROLE_ADMIN') ? $chantierRepository->findAll() : [],
        ]);
    }
    
    #[Route('/new/{id}', name: 'app_affectation_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')] // Seuls les admins peuvent créer des affectations
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager, 
        ChantierRepository $chantierRepository, 
        AffectationRepository $affectationRepository,
        ?Chantier $chantier
    ): Response {
        $chantierId = $request->attributes->get('id');
        $chantier = $chantierId ? $chantierRepository->find($chantierId) : null;

        $affectation = new Affectation();

        if ($chantier) {
            $affectation->setChantier($chantier);
        }

        $form = $this->createForm(AffectationType::class, $affectation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $equipe = $affectation->getEquipe();
            $dateDebut = $affectation->getDateDebut();
            $dateFin = $affectation->getDateFin();

            $errors = [];
            
            // Vérifier pour chaque ouvrier de l'équipe s'il a déjà une affectation qui chevauche
            foreach ($equipe->getOuvriers() as $ouvrier) {
                $existingAffectations = $affectationRepository->findOverlappingAffectations(
                    $ouvrier, $dateDebut, $dateFin
                );
                
                foreach ($existingAffectations as $existing) {
                    $errors[] = sprintf(
                        "L'ouvrier %s est déjà affecté au chantier '%s' du %s au %s.",
                        $ouvrier->getNomOuvrier(),
                        $existing->getChantier()->getNom(),
                        $existing->getDateDebut()->format('d/m/Y'),
                        $existing->getDateFin()->format('d/m/Y')
                    );
                }
            }

            if (!empty($errors)) {
                foreach ($errors as $error) {
                    $this->addFlash('error', $error);
                }
                return $this->redirectToRoute('app_affectation_new', ['id' => $chantier?->getId()]);
            }

            $entityManager->persist($affectation);
            $entityManager->flush();

            $this->addFlash('success', 'Affectation créée avec succès.');
            return $this->redirectToRoute('app_affectation_index');
        }

        return $this->render('affectation/new.html.twig', [
            'affectation' => $affectation,
            'form' => $form->createView(),
            'chantier' => $chantier,
        ]);
    }

    #[Route('/{id}', name: 'app_affectation_show', methods: ['GET'])]
    #[IsGranted('ROLE_OUVRIER')]
    public function show(Affectation $affectation): Response
    {
        $user = $this->getUser();

        // Ouvrier ne peut voir que ses propres affectations, Admin voit tout
        if (!$this->isGranted('ROLE_ADMIN')) {
            if ($affectation->getOuvrier() !== $user && 
                !$affectation->getEquipe()->getOuvriers()->contains($user)) {
                throw $this->createAccessDeniedException('Vous ne pouvez voir que vos propres affectations.');
            }
        }
    
        return $this->render('affectation/show.html.twig', [
            'affectation' => $affectation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_affectation_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')] // Seuls les admins peuvent modifier
    public function edit(Request $request, Affectation $affectation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AffectationType::class, $affectation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Affectation modifiée avec succès.');
            return $this->redirectToRoute('app_affectation_index');
        }

        return $this->render('affectation/edit.html.twig', [
            'affectation' => $affectation,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_affectation_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')] // Seuls les admins peuvent supprimer
    public function delete(Request $request, Affectation $affectation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete-affectation-'.$affectation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($affectation);
            $entityManager->flush();
            $this->addFlash('success', 'Affectation supprimée avec succès.');
        }

        return $this->redirectToRoute('app_affectation_index');
    }
}