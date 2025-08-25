<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class LoginAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';
    private const MAX_ATTEMPTS = 3;
    private const BLOCK_MINUTES = 10;

    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function authenticate(Request $request): Passport
    {
        if (!$request->isMethod('POST')) {
            throw new AuthenticationException('Invalid authentication request.');
        }

        $email = (string) $request->request->get('email', '');
        $password = (string) $request->request->get('password', '');
        $csrfToken = (string) $request->request->get('_csrf_token');

        if ('' === $email || '' === $password) {
            throw new AuthenticationException('Email and password must not be empty.');
        }

        $session = $request->getSession();
        if (!$session->has('login_attempts')) {
            $session->set('login_attempts', 0);
        }
        if (!$session->has('blocked_until')) {
            $session->set('blocked_until', null);
        }

        $blockedUntil = $session->get('blocked_until');
        if ($blockedUntil && time() < $blockedUntil) {
            $remaining = (int) ceil(($blockedUntil - time()) / 60);
            throw new CustomUserMessageAuthenticationException(
                sprintf('Trop de tentatives. Réessayez dans ~%d min.', max(1, $remaining))
            );
        }

        $session->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password),
            [ new CsrfTokenBadge('authenticate', $csrfToken) ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Reset counters on success
        $session = $request->getSession();
        $session->set('login_attempts', 0);
        $session->set('blocked_until', null);

        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('app_chantier_index')); // change si besoin
    }

    // <<< FIX: Response non-nullable >>>
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $session = $request->getSession();

        // Si déjà bloqué, on ne change rien
        $blockedUntil = $session->get('blocked_until');
        if (!$blockedUntil || time() >= $blockedUntil) {
            $attempts = (int) $session->get('login_attempts', 0) + 1;
            $session->set('login_attempts', $attempts);

            if ($attempts >= self::MAX_ATTEMPTS) {
                $session->set('blocked_until', time() + (self::BLOCK_MINUTES * 60)); // blocage 10 min
            }
        }

        // Laisse la mécanique standard afficher l’erreur
        return parent::onAuthenticationFailure($request, $exception);
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
