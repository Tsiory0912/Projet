<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    /**
     * Retourne les sorties filtrées par date, article, catégorie et agent
     *
     * @param \DateTimeInterface|null $date
     * @param string|null $article
     * @param string|null $categorie
     * @param string|null $agent
     * @return Sortie[]
     */
    public function findFiltered(?\DateTimeInterface $date, ?string $article, ?string $categorie, ?string $agent): array
    {
        $qb = $this->createQueryBuilder('s')
            ->leftJoin('s.article', 'a')
            ->leftJoin('a.categorie', 'c')
            ->leftJoin('s.agent', 'ag')
            ->addSelect('a', 'c', 'ag')
            ->orderBy('s.date_sortie', 'DESC');

            
        // Filtrage par date
        if ($date) {
            $qb->andWhere('DATE(s.date_sortie) = :date')
               ->setParameter('date', $date->format('Y-m-d'));
        }

        // Filtrage par article
        if ($article) {
            $qb->andWhere('LOWER(a.nom) LIKE :article')
               ->setParameter('article', '%' . strtolower($article) . '%');
        }

        
        // Filtrage par catégorie
        if ($categorie) {
            $qb->andWhere('LOWER(c.nom) LIKE :categorie')
               ->setParameter('categorie', '%' . strtolower($categorie) . '%');
        }

         // Filtrage par agent
         if ($agent) {
            $qb->andWhere('ag.username LIKE :agent')
               ->setParameter('agent', '%' . strtolower($agent) . '%');
        }

        // Exécution de la requête et récupération des résultats
        return $qb->getQuery()->getResult();
    }
}


