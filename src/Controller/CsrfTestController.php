<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

final class CsrfTestController extends AbstractController
{
    #[Route('/csrf/test', name: 'app_csrf_test', methods: ['GET'])]
    public function index(SessionInterface $session): Response
    {
        $csrfToken = bin2hex(random_bytes(32));
        $session->set('csrf_token', $csrfToken);

        return $this->render('csrf_test/index.html.twig', [
            'csrf_token' => $csrfToken
        ]);
    }

    #[Route('/csrf/test', name: 'app_csrf_test_submit', methods: ['POST'])]
    public function submit(Request $request, SessionInterface $session): Response
    {
        $submittedToken = $request->request->get('csrf_token');
        $sessionToken = $session->get('csrf_token');

        if (!$submittedToken || !hash_equals($sessionToken, $submittedToken)) {
            return new Response('Token CSRF invalide', 403);
        }

        $data = $request->request->get('data');

        return new Response('Formulaire valide, données reçues : ' . htmlspecialchars($data));
    }
}