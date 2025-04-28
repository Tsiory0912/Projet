<?php

namespace App\Repository;

use App\Entity\Entree;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Entree>
 */
class EntreeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entree::class);
    }

public function findAllWithArticle(): array
{
    return $this->createQueryBuilder('e')
        ->leftJoin('e.article', 'a')
        ->addSelect('a')
        ->orderBy('e.dateMaj', 'DESC')
        ->getQuery()
        ->getResult();
}

public function findByDate(\DateTime $date): array
{
    return $this->createQueryBuilder('e')
        ->leftJoin('e.article', 'a')
        ->addSelect('a')
        ->andWhere('DATE(e.dateMaj) = :date')
        ->setParameter('date', $date->format('Y-m-d'))
        ->orderBy('e.dateMaj', 'DESC')
        ->getQuery()
        ->getResult();
}


    //    /**
    //     * @return Entree[] Returns an array of Entree objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Entree
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
