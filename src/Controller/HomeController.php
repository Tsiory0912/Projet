<?php

namespace App\Controller;

use App\Repository\AgentRepository;
use App\Repository\ArticleRepository;
use App\Repository\CategorieRepository;
use App\Repository\EntreeRepository;
use App\Repository\SortieRepository;
use App\Repository\StockRepository;
use App\Repository\UniteRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    // 🔹 Route PUBLIQUE : accessible à tous
    #[Route('/', name: 'app_root')]
    

    // 🔹 Route PRIVÉE : tableau de bord pour les utilisateurs connectés
    #[Route('/home', name: 'app_home')]
    public function index(
        AgentRepository $agentRepository,
        ArticleRepository $articleRepository,
        CategorieRepository $categorieRepository,
        EntreeRepository $entreeRepository,
        SortieRepository $sortieRepository,
        StockRepository $stockRepository,
        UniteRepository $uniteRepository,
        UserRepository $userRepository
    ): Response {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('home/index.html.twig', [
            'stats' => [
                'agents' => $agentRepository->count([]),
                'articles' => $articleRepository->count([]),
                'categories' => $categorieRepository->count([]),
                'entrees' => $entreeRepository->count([]),
                'sorties' => $sortieRepository->count([]),
                'stocks' => $stockRepository->count([]),
                'unites' => $uniteRepository->count([]),
                'users' => $userRepository->count([]),
            ],
            'user_roles' => $user->getRoles(),
        ]);
    }
}
