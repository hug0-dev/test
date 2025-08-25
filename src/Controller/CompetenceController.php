<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Competence;
use App\Entity\UserCompetence;
use App\Repository\UserRepository;
use App\Repository\CompetenceRepository;
use App\Repository\UserCompetenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/competences')]
#[IsGranted('ROLE_ADMIN')]
class CompetenceController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private CompetenceRepository $competenceRepository,
        private UserCompetenceRepository $userCompetenceRepository
    ) {}

    #[Route('/', name: 'app_competences_index')]
    public function index(): Response
    {
        $users = $this->userRepository->createQueryBuilder('u')
            ->where('u.nom_ouvrier IS NOT NULL')
            ->orderBy('u.nom_ouvrier', 'ASC')
            ->getQuery()
            ->getResult();

        $competences = $this->competenceRepository->findBy(['actif' => true], ['nom' => 'ASC']);

        return $this->render('competences/index.html.twig', [
            'users' => $users,
            'competences' => $competences
        ]);
    }

    #[Route('/user/{id}', name: 'app_competences_user')]
    public function userCompetences(User $user): Response
    {
        if ($user->isAdmin()) {
            $this->addFlash('error', 'Les administrateurs ne peuvent pas avoir de compétences.');
            return $this->redirectToRoute('app_competences_index');
        }

        $userCompetences = $this->userCompetenceRepository->findBy(['user' => $user]);
        $allCompetences = $this->competenceRepository->findBy(['actif' => true], ['nom' => 'ASC']);

        return $this->render('competences/user_competences.html.twig', [
            'user' => $user,
            'userCompetences' => $userCompetences,
            'allCompetences' => $allCompetences,
            'niveaux' => ['DEBUTANT' => 'Débutant', 'CONFIRME' => 'Confirmé', 'EXPERT' => 'Expert']
        ]);
    }

    #[Route('/add/{userId}/{competenceId}', name: 'app_competences_add', methods: ['POST'])]
    public function addCompetence(int $userId, int $competenceId, Request $request): Response
    {
        $user = $this->userRepository->find($userId);
        $competence = $this->competenceRepository->find($competenceId);
        $niveau = $request->request->get('niveau', 'DEBUTANT');

        if (!$user || !$competence) {
            $this->addFlash('error', 'Utilisateur ou compétence introuvable.');
            return $this->redirectToRoute('app_competences_index');
        }

        $existing = $this->userCompetenceRepository->findOneBy([
            'user' => $user,
            'competence' => $competence
        ]);

        if ($existing) {
            $existing->setNiveau($niveau);
            $existing->setAttribueePar($this->getUser());
            $existing->setDateAttribution(new \DateTimeImmutable());
        } else {
            $userCompetence = new UserCompetence();
            $userCompetence->setUser($user);
            $userCompetence->setCompetence($competence);
            $userCompetence->setNiveau($niveau);
            $userCompetence->setAttribueePar($this->getUser());

            $this->entityManager->persist($userCompetence);
        }

        $this->entityManager->flush();
        $this->addFlash('success', 'Compétence ajoutée avec succès !');

        return $this->redirectToRoute('app_competences_user', ['id' => $userId]);
    }

    #[Route('/remove/{id}', name: 'app_competences_remove', methods: ['POST'])]
    public function removeCompetence(UserCompetence $userCompetence): Response
    {
        $userId = $userCompetence->getUser()->getId();
        
        $this->entityManager->remove($userCompetence);
        $this->entityManager->flush();
        
        $this->addFlash('success', 'Compétence retirée avec succès !');
        
        return $this->redirectToRoute('app_competences_user', ['id' => $userId]);
    }

    #[Route('/nouvelle', name: 'app_competences_new', methods: ['GET', 'POST'])]
    public function newCompetence(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $nom = trim($request->request->get('nom'));
            $description = trim($request->request->get('description'));

            if (empty($nom)) {
                $this->addFlash('error', 'Le nom de la compétence est obligatoire.');
            } else {
                $existing = $this->competenceRepository->findOneBy(['nom' => $nom]);
                
                if ($existing) {
                    $this->addFlash('error', 'Cette compétence existe déjà.');
                } else {
                    $competence = new Competence();
                    $competence->setNom($nom);
                    $competence->setDescription($description);

                    $this->entityManager->persist($competence);
                    $this->entityManager->flush();

                    $this->addFlash('success', 'Compétence créée avec succès !');
                    return $this->redirectToRoute('app_competences_index');
                }
            }
        }

        return $this->render('competences/new.html.twig');
    }

    #[Route('/search', name: 'app_competences_search')]
    public function search(Request $request): Response
    {
        $competenceId = $request->query->get('competence');
        $niveau = $request->query->get('niveau');
        
        $resultats = [];
        $competence = null;

        if ($competenceId) {
            $competence = $this->competenceRepository->find($competenceId);
            if ($competence) {
                $criteria = ['competence' => $competence];
                if ($niveau) {
                    $criteria['niveau'] = $niveau;
                }
                $resultats = $this->userCompetenceRepository->findBy($criteria);
            }
        }

        $competences = $this->competenceRepository->findBy(['actif' => true], ['nom' => 'ASC']);

        return $this->render('competences/search.html.twig', [
            'competences' => $competences,
            'competence_selectionnee' => $competence,
            'niveau_selectionne' => $niveau,
            'resultats' => $resultats,
            'niveaux' => ['DEBUTANT' => 'Débutant', 'CONFIRME' => 'Confirmé', 'EXPERT' => 'Expert']
        ]);
    }
}