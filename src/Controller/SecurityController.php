<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Vérifie si l'utilisateur est déjà connecté   
        if ($this->getUser()) {
            // Redirige vers la page d'accueil si l'utilisateur est connecté
            return $this->redirectToRoute('app_home');
        }

        // Récupère les erreurs de connexion s’il y en a
        $error = $authenticationUtils->getLastAuthenticationError();

        // Dernier nom d’utilisateur saisi
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Symfony intercepte cette route automatiquement
        throw new \LogicException('This method is blank because Symfony handles the logout route.');
    }

    #[Route(path: '/force-logout', name: 'force_logout')]
    public function forceLogout(Request $request): RedirectResponse
    {
        // Invalide la session actuelle
        $request->getSession()->invalidate();

        // Redirige vers la page de déconnexion gérée par Symfony
        return $this->redirectToRoute('app_logout');
    }
}
