<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\AffectationRepository;
use App\Repository\ChantierRepository;
use App\Repository\EquipeRepository;
use App\Repository\UserRepository;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(
        AffectationRepository $affectationRepository,
        ChantierRepository $chantierRepository,
        EquipeRepository $equipeRepository,
        UserRepository $userRepository
    ): Response {
        $user = $this->getUser();

        if ($this->isGranted('ROLE_ADMIN')) {
            // Dashboard admin
            $stats = [
                'total_users' => $userRepository->count(['nom_ouvrier' => ['!=' => null]]),
                'total_chantiers' => $chantierRepository->count([]),
                'total_equipes' => $equipeRepository->count([]),
                'affectations_stats' => $affectationRepository->getStats(),
                'recent_affectations' => array_slice($affectationRepository->findAll(), 0, 5),
            ];

            return $this->render('home/admin_dashboard.html.twig', [
                'stats' => $stats,
            ]);
        } else {
            // Dashboard ouvrier
            $mesAffectations = $affectationRepository->findByOuvrier($user);
            $monEquipe = $user->getEquipe();

            return $this->render('home/ouvrier_dashboard.html.twig', [
                'user' => $user,
                'affectations' => array_slice($mesAffectations, 0, 5),
                'equipe' => $monEquipe,
            ]);
        }
    }
}