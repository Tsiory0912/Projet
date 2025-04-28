<?php

namespace App\Controller;

use App\Entity\Stock;
use App\Form\StockType;
use App\Repository\StockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/stock')]
class StockController extends AbstractController
{
    #[Route('/', name: 'app_stock_index', methods: ['GET'])]
    public function index(Request $request, StockRepository $stockRepository, PaginatorInterface $paginator): Response
    {
        $dateFilter = $request->query->get('date');

        $queryBuilder = $stockRepository->createQueryBuilder('s');

        if ($dateFilter) {
            $date = new \DateTime($dateFilter);
            $queryBuilder
                ->andWhere('DATE(s.dateEntree) = :date')
                ->setParameter('date', $date->format('Y-m-d'));
        }

        $query = $queryBuilder->orderBy('s.dateEntree', 'DESC')->getQuery();

        $stocks = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('stock/index.html.twig', [
            'stocks' => $stocks,
            'dateFilter' => $dateFilter,
        ]);
    }

    #[Route('/new', name: 'app_stock_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $stock = new Stock();
        $form = $this->createForm(StockType::class, $stock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifie si la dateEntree est définie, sinon l'initialise avec la date actuelle
            if ($stock->getDateEntree() === null) {
                $stock->setDateEntree(new \DateTime()); // Définit la date actuelle comme date d'entrée
            }

            // Persiste le stock après avoir défini la date d'entrée
            $em->persist($stock);
            $em->flush();

            $this->addFlash('success', 'Entrée de stock ajoutée avec succès.');
            return $this->redirectToRoute('app_stock_index');
        }

        return $this->render('stock/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_stock_show', methods: ['GET'])]
    public function show(Stock $stock): Response
    {
        return $this->render('stock/show.html.twig', [
            'stock' => $stock,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_stock_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Stock $stock, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(StockType::class, $stock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $stock->setUpdatedAt(new \DateTime());
            $em->flush();

            $this->addFlash('success', 'Entrée de stock modifiée avec succès.');
            return $this->redirectToRoute('app_stock_index');
        }

        return $this->render('stock/edit.html.twig', [
            'form' => $form->createView(),
            'stock' => $stock,
        ]);
    }

    #[Route('/{id}', name: 'app_stock_delete', methods: ['POST'])]
    public function delete(Request $request, Stock $stock, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $stock->getId(), $request->request->get('_token'))) {
            $em->remove($stock);
            $em->flush();

            $this->addFlash('success', 'Entrée de stock supprimée avec succès.');
        }

        return $this->redirectToRoute('app_stock_index');
    }
}
