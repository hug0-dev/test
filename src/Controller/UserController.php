<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')] // Seuls les admins peuvent voir la liste des utilisateurs
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->createQueryBuilder('u')
            ->where('u.nom_ouvrier IS NOT NULL')
            ->orderBy('u.nom_ouvrier', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')] // Seuls les admins peuvent créer des utilisateurs
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hasher le mot de passe
            if ($form->has('password') && $form->get('password')->getData()) {
                $plainPassword = $form->get('password')->getData();
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }

            // Assigner automatiquement les rôles corrects
            $this->assignCorrectRoles($user);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur créé avec succès !');
            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);
        $currentUser = $this->getUser();

        if (!$user) {
            throw new NotFoundHttpException('Utilisateur non trouvé');
        }

        // Contrôle d'accès : Admin voit tout, Ouvrier voit seulement son profil
        if (!$this->isGranted('ROLE_ADMIN') && $currentUser !== $user) {
            throw $this->createAccessDeniedException('Vous ne pouvez voir que votre propre profil.');
        }

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id, UserRepository $userRepository, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $userRepository->find($id);
        $currentUser = $this->getUser();

        if (!$user) {
            throw new NotFoundHttpException('Utilisateur non trouvé');
        }

        // Contrôle d'accès : Admin modifie tout, Ouvrier modifie seulement son profil
        if (!$this->isGranted('ROLE_ADMIN') && $currentUser !== $user) {
            throw $this->createAccessDeniedException('Vous ne pouvez modifier que votre propre profil.');
        }

        $form = $this->createForm(UserType::class, $user, [
            'is_admin' => $this->isGranted('ROLE_ADMIN') // Passer le contexte au formulaire
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hasher le nouveau mot de passe si fourni
            if ($form->has('password') && $form->get('password')->getData()) {
                $plainPassword = $form->get('password')->getData();
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }

            // Réassigner les rôles corrects
            $this->assignCorrectRoles($user);

            $entityManager->flush();
            $this->addFlash('success', 'Profil modifié avec succès !');
            
            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('app_user_index');
            } else {
                return $this->redirectToRoute('app_user_show', ['id' => $user->getId()]);
            }
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_user_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')] // Seuls les admins peuvent supprimer
    public function delete(int $id, UserRepository $userRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $user = $userRepository->find($id);

        if (!$user) {
            throw new NotFoundHttpException('Utilisateur non trouvé');
        }

        // Vérification CSRF
        if ($this->isCsrfTokenValid('delete-user-'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'Utilisateur supprimé avec succès !');
        }

        return $this->redirectToRoute('app_user_index');
    }

    #[Route('/profile', name: 'app_user_profile', methods: ['GET'])]
    public function profile(): Response
    {
        $user = $this->getUser();
        
        return $this->render('user/show.html.twig', [
            'user' => $user,
            'is_own_profile' => true
        ]);
    }

    /**
     * Assigne automatiquement les bons rôles en fonction du type d'utilisateur
     */
    private function assignCorrectRoles(User $user): void
    {
        $roles = ['ROLE_USER']; // Rôle de base

        // Si l'utilisateur a des informations d'ouvrier, lui donner ROLE_OUVRIER
        if ($user->getNomOuvrier()) {
            $roles[] = 'ROLE_OUVRIER';
        }

        // Garder ROLE_ADMIN s'il l'avait déjà (seuls les admins peuvent créer d'autres admins)
        if (in_array('ROLE_ADMIN', $user->getRoles()) && $this->isGranted('ROLE_ADMIN')) {
            $roles[] = 'ROLE_ADMIN';
        }

        $user->setRoles(array_unique($roles));
    }
}