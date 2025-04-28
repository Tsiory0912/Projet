<?php

namespace App\Controller;

use App\Entity\Entree;
use App\Entity\Stock;
use App\Form\EntreeType;
use App\Repository\EntreeRepository;
use App\Repository\StockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/entree')]
final class EntreeController extends AbstractController
{
    #[Route(name: 'app_entree_index', methods: ['GET'])]
    public function index(Request $request, EntreeRepository $entreeRepository): Response
    {
        $dateRecherche = $request->query->get('date');
        $article = $request->query->get('article');
        $categorie = $request->query->get('categorie');
        $date = null;

        // Validation et parsing de la date
        if ($dateRecherche) {
            try {
                $date = new \DateTime($dateRecherche);
            } catch (\Exception $e) {
                $this->addFlash('error', 'La date fournie est invalide.');
                $date = null;
            }
        }

        // Récupérer les entrées filtrées
        if ($article || $categorie) {
            $entrees = $entreeRepository->findWithFilters($article, $categorie);
        } elseif ($date) {
            $entrees = $entreeRepository->findByDate($date);
        } else {
            $entrees = $entreeRepository->findAllWithArticle();
        }

        return $this->render('entree/index.html.twig', [
            'entrees' => $entrees,
            'dateRecherche' => $dateRecherche,
            'articleRecherche' => $article,
            'categorieRecherche' => $categorie,
        ]);
    }

    #[Route('/new', name: 'app_entree_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, StockRepository $stockRepository): Response
    {
        $entree = new Entree();
        $entree->setDateEntree(new \DateTime()); // Initialiser la date actuelle

        $form = $this->createForm(EntreeType::class, $entree);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entree->setDateMaj(new \DateTime()); // Date de mise à jour

            $article = $entree->getArticle();
            $quantiteEntree = $entree->getQuantite();

            // Gestion de la mise à jour du stock
            $stock = $stockRepository->findOneBy(['article' => $article]);

            if ($stock) {
                $stock->setQuantite($stock->getQuantite() + $quantiteEntree);
            } else {
                $stock = new Stock();
                $stock->setArticle($article);
                $stock->setQuantite($quantiteEntree);
                $entityManager->persist($stock);
            }

            $entityManager->persist($entree);
            $entityManager->flush();

            $this->addFlash('success', 'Nouvelle entrée enregistrée avec succès.');

            return $this->redirectToRoute('app_entree_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('entree/new.html.twig', [
            'entree' => $entree,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_entree_show', methods: ['GET'])]
    public function show(Entree $entree): Response
    {
        return $this->render('entree/show.html.twig', [
            'entree' => $entree,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_entree_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Entree $entree, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EntreeType::class, $entree);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entree->setDateMaj(new \DateTime());
            $entityManager->flush();

            $this->addFlash('success', 'L\'entrée a été mise à jour avec succès.');

            return $this->redirectToRoute('app_entree_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('entree/edit.html.twig', [
            'entree' => $entree,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_entree_delete', methods: ['POST'])]
    public function delete(Request $request, Entree $entree, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $entree->getId(), $request->request->get('_token'))) {
            $entityManager->remove($entree);
            $entityManager->flush();

            $this->addFlash('success', 'L\'entrée a été supprimée avec succès.');
        }

        return $this->redirectToRoute('app_entree_index', [], Response::HTTP_SEE_OTHER);
    }
}
