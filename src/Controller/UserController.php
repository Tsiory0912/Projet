<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/user/add', name: 'user_add')]
    public function addUser(
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = new User();
        $user->setEmail('demo@example.com');
        $user->setUsername('demoUser');
        $user->setRoles(['ROLE_ADMIN']);
        $hashedPassword = $passwordHasher->hashPassword($user, 'motdepasse123');
        $user->setPassword($hashedPassword);

        $em->persist($user);
        $em->flush();

        return new Response('Nouvel utilisateur ajouté !');
    }
}
