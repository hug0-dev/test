<?php

namespace App\Controller;

use App\Entity\Equipe;
use App\Form\EquipeType;
use App\Repository\EquipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/equipe')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class EquipeController extends AbstractController
{
    #[Route('/', name: 'app_equipe_index', methods: ['GET'])]
    #[IsGranted('ROLE_OUVRIER')] // Ouvriers peuvent voir les équipes
    public function index(EquipeRepository $equipeRepository): Response
    {
        $user = $this->getUser();
        
        if ($this->isGranted('ROLE_ADMIN')) {
            $equipes = $equipeRepository->findAll();
            $title = 'Toutes les équipes';
        } else {
            // Ouvrier voit seulement son équipe
            $equipes = $user->getEquipe() ? [$user->getEquipe()] : [];
            $title = 'Mon équipe';
        }

        return $this->render('equipe/index.html.twig', [
            'equipes' => $equipes,
            'title' => $title,
            'can_manage' => $this->isGranted('ROLE_ADMIN'),
        ]);
    }

    #[Route('/new', name: 'app_equipe_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')] // Seuls les admins créent des équipes
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $equipe = new Equipe();
        $form = $this->createForm(EquipeType::class, $equipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $competances = $form->get('competance_equipe')->getData();
            $equipe->setCompetanceEquipe($competances);

            $ouvriers = $form->get('ouvriers')->getData();
            $equipe->setNombre(count($ouvriers));

            $entityManager->persist($equipe);

            foreach ($ouvriers as $ouvrier) {
                $ouvrier->setEquipe($equipe);
                $entityManager->persist($ouvrier);
            }

            if ($equipe->getChefEquipe()) {
                $chef = $equipe->getChefEquipe();
                $chef->setEquipe($equipe);
                $entityManager->persist($chef);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Équipe créée avec succès !');
            return $this->redirectToRoute('app_equipe_index');
        }

        return $this->render('equipe/new.html.twig', [
            'equipe' => $equipe,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_equipe_show', methods: ['GET'])]
    #[IsGranted('ROLE_OUVRIER')]
    public function show(Equipe $equipe): Response
    {
        $user = $this->getUser();

        // Ouvrier ne peut voir que son équipe, Admin voit tout
        if (!$this->isGranted('ROLE_ADMIN') && $user->getEquipe() !== $equipe) {
            throw $this->createAccessDeniedException('Vous ne pouvez voir que votre propre équipe.');
        }

        return $this->render('equipe/show.html.twig', [
            'equipe' => $equipe,
            'can_manage' => $this->isGranted('ROLE_ADMIN'),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_equipe_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')] // Seuls les admins modifient
    public function edit(Request $request, Equipe $equipe, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EquipeType::class, $equipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $competances = $form->get('competance_equipe')->getData();
            $equipe->setCompetanceEquipe($competances);

            $ouvriers = $form->get('ouvriers')->getData();
            $equipe->setNombre(count($ouvriers));

            $entityManager->flush();
            $this->addFlash('success', 'Équipe modifiée avec succès !');
            return $this->redirectToRoute('app_equipe_index');
        }

        return $this->render('equipe/edit.html.twig', [
            'equipe' => $equipe,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_equipe_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')] // Seuls les admins suppriment
    public function delete(Request $request, Equipe $equipe, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete-equipe-'.$equipe->getId(), $request->request->get('_token'))) {
            $entityManager->remove($equipe);
            $entityManager->flush();
            $this->addFlash('success', 'Équipe supprimée avec succès !');
        }

        return $this->redirectToRoute('app_equipe_index');
    }
}