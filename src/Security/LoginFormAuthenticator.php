<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Core\Exception\CsrfTokenMismatchException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class LoginFormAuthenticator extends AbstractAuthenticator
{

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }
    public function supports(Request $request): bool
    {

        // Vérifie si la requête est une requête POST et contient les données nécessaires
        return $request->isMethod('POST') && $request->request->has('_username') && $request->request->has('password');
    }

    public function authenticate(Request $request): Passport
    {

        // Récupère les informations du formulaire
        $email = $request->request->get('_username');
        $password = $request->request->get('password');
        $csrfToken = $request->request->get('_csrf_token'); // Le token CSRF pour validation

        // Vérifie si le token CSRF est valide
        /*  if (!$this->isCsrfTokenValid('authenticate', $csrfToken)) {
            throw new CsrfTokenMismatchException('Invalid CSRF token');
        } */

        if (!$email || !$password) {
            throw new AuthenticationException('Invalid credentials.');
        }


        // Crée un "passport" qui contient l'email et le mot de passe
        return new Passport(
            new UserBadge($email), // Identifiant de l'utilisateur (email dans ce cas)
            new PasswordCredentials($password), // Les informations de mot de passe
            [
                //new CsrfTokenBadge('authenticate', $csrfToken) // Ajouter un badge pour la validation CSRF
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {


        // Redirige l'utilisateur après une connexion réussie
        $url = $this->urlGenerator->generate('app_home');
        return new RedirectResponse($url);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        if ($request->hasSession()) {
            // Ajoute un message flash
            $request->getSession()->getFlashBag()->add('error', 'Identifiants invalides.');
        }
        $url = $this->urlGenerator->generate('app_login');
        return new RedirectResponse($url);
        /* $url = $this->urlGenerator->generate('app_login');
        return new RedirectResponse($url); */
        // En cas d'échec d'authentification, tu peux retourner une réponse d'erreur
        return new Response('Authentication failed: ' . $exception->getMessage(), Response::HTTP_FORBIDDEN);
    }
}
