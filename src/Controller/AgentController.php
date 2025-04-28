<?php

namespace App\Controller;

use App\Entity\Agent;
use App\Form\AgentType;
use App\Repository\AgentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/agent')]
final class AgentController extends AbstractController
{
    #[Route(name: 'app_agent_index', methods: ['GET'])]
    public function index(Request $request, AgentRepository $agentRepository, PaginatorInterface $paginator): Response
    {
        // Vérification que l'utilisateur est authentifié
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Récupération du terme de recherche
        $search = $request->query->get('search', '');
        
        // Création de la requête de base
        $queryBuilder = $agentRepository->createQueryBuilder('a');

        // Ajout du filtre de recherche si un terme est fourni
        if ($search) {
            $queryBuilder
                ->where('a.nom LIKE :search OR a.matricule LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        // Pagination
        $agents = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            10 // Nombre d'éléments par page
        );

        return $this->render('agent/index.html.twig', [
            'agents' => $agents,
            'search' => $search, // On passe la recherche à la vue
        ]);
    }

    #[Route('/new', name: 'app_agent_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, AgentRepository $agentRepository): Response
    {
        // Vérification que l'utilisateur est authentifié
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $agent = new Agent();
        $form = $this->createForm(AgentType::class, $agent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Génération automatique d’un numeroAgent unique
            do {
                $numero = random_int(10000, 99999);
                $existing = $agentRepository->findOneBy(['numeroAgent' => $numero]);
            } while ($existing !== null);

            $agent->setNumeroAgent($numero);

            $entityManager->persist($agent);
            $entityManager->flush();

            return $this->redirectToRoute('app_agent_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('agent/new.html.twig', [
            'agent' => $agent,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_agent_show', methods: ['GET'])]
    public function show(Agent $agent): Response
    {
        // Vérification que l'utilisateur est authentifié
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('agent/show.html.twig', [
            'agent' => $agent,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_agent_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Agent $agent, EntityManagerInterface $entityManager): Response
    {
        // Vérification que l'utilisateur est authentifié
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(AgentType::class, $agent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_agent_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('agent/edit.html.twig', [
            'agent' => $agent,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_agent_delete', methods: ['POST'])]
    public function delete(Request $request, Agent $agent, EntityManagerInterface $entityManager): Response
    {
        // Vérification que l'utilisateur est authentifié
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Vérification du token CSRF pour éviter les suppressions accidentelles
        if ($this->isCsrfTokenValid('delete' . $agent->getId(), $request->request->get('_token'))) {
            $entityManager->remove($agent);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_agent_index', [], Response::HTTP_SEE_OTHER);
    }
}
