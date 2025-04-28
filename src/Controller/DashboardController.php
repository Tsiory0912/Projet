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
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
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
        // Statistiques générales
        $totalStocks = $stockRepository->count([]);
        $totalAgents = $agentRepository->count([]);
        $totalArticles = $articleRepository->count([]);
        $totalCategories = $categorieRepository->count([]);
        $totalSorties = $sortieRepository->count([]);
        $totalEntrees = $entreeRepository->count([]);

        // Calcul de l'état actuel des stocks (en cours ou disponibles)
        $availableStocks = $stockRepository->findAll(); // exemple pour récupérer tous les stocks
        $agents = $agentRepository->findAll(); // exemple pour récupérer les agents

        return $this->render('dashboard/index.html.twig', [
            'user' => $this->getUser(),
            'stats' => [
                'agents' => $totalAgents,
                'articles' => $totalArticles,
                'categories' => $totalCategories,
                'entrees' => $totalEntrees,
                'sorties' => $totalSorties,
                'stocks' => $totalStocks,
            ],
            'availableStocks' => $availableStocks,
            'agents' => $agents,
        ]);
    }
}
