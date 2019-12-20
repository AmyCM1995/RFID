<?php

namespace App\Repository;

use App\Entity\RegionMundial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method RegionMundial|null find($id, $lockMode = null, $lockVersion = null)
 * @method RegionMundial|null findOneBy(array $criteria, array $orderBy = null)
 * @method RegionMundial[]    findAll()
 * @method RegionMundial[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RegionMundialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RegionMundial::class);
    }

    // /**
    //  * @return RegionMundial[] Returns an array of RegionMundial objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RegionMundial
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
