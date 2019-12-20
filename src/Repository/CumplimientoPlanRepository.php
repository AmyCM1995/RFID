<?php

namespace App\Repository;

use App\Entity\CumplimientoPlan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CumplimientoPlan|null find($id, $lockMode = null, $lockVersion = null)
 * @method CumplimientoPlan|null findOneBy(array $criteria, array $orderBy = null)
 * @method CumplimientoPlan[]    findAll()
 * @method CumplimientoPlan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CumplimientoPlanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CumplimientoPlan::class);
    }

    // /**
    //  * @return CumplimientoPlan[] Returns an array of CumplimientoPlan objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CumplimientoPlan
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
