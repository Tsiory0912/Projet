<?php

namespace App\Repository;

use App\Entity\Stock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Stock>
 */
class StockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stock::class);
    }

    /**
     * Recherche les stocks dont la date d’entrée correspond à la date donnée.
     *
     * @param \DateTimeInterface $date
     * @return Stock[]
     */
    public function findByDateEntree(\DateTimeInterface $date): array
    {
        $start = \DateTime::createFromInterface($date)->setTime(0, 0, 0);
        $end = \DateTime::createFromInterface($date)->setTime(23, 59, 59);

        return $this->createQueryBuilder('s')
            ->andWhere('s.dateEntree BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('s.dateEntree', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche tous les stocks, avec option de filtre par date (si donnée).
     *
     * @param \DateTimeInterface|null $date
     * @return Stock[]
     */
    public function findAllWithOptionalDateFilter(?\DateTimeInterface $date = null): array
    {
        $qb = $this->createQueryBuilder('s');

        if ($date) {
            $start = \DateTime::createFromInterface($date)->setTime(0, 0, 0);
            $end = \DateTime::createFromInterface($date)->setTime(23, 59, 59);

            $qb->andWhere('s.dateEntree BETWEEN :start AND :end')
               ->setParameter('start', $start)
               ->setParameter('end', $end);
        }

        return $qb->orderBy('s.dateEntree', 'DESC')
                  ->getQuery()
                  ->getResult();
    }
     /**
* Recherche tous les stocks, avec option de filtre par date, nom d'article et catégorie.
*
* @param \DateTimeInterface|null $date
* @param string|null $nomArticle
* @param string|null $categorie
* @return Stock[]
*/
public function findAllWithFilters(?\DateTimeInterface $date = null, ?string $nomArticle = null, ?string $categorie = null): array
{
   $qb = $this->createQueryBuilder('s')
       ->innerJoin('s.article', 'a') // Jointure avec l'entité Article
       ->innerJoin('a.categorie', 'c'); // Jointure avec l'entité Catégorie

   // Filtre par date
   if ($date) {
       $start = \DateTime::createFromInterface($date)->setTime(0, 0, 0);
       $end = \DateTime::createFromInterface($date)->setTime(23, 59, 59);
       $qb->andWhere('s.dateEntree BETWEEN :start AND :end')
          ->setParameter('start', $start)
          ->setParameter('end', $end);
   }

   // Filtre par nom d'article
   if ($nomArticle) {
       $qb->andWhere('a.nom LIKE :nomArticle')
          ->setParameter('nomArticle', '%' . $nomArticle . '%');
   }

   // Filtre par catégorie
   if ($categorie) {
       $qb->andWhere('c.nom LIKE :categorie')
          ->setParameter('categorie', '%' . $categorie . '%');
   }

   return $qb->orderBy('s.dateEntree', 'DESC')
             ->getQuery()
             ->getResult();
}
}   
