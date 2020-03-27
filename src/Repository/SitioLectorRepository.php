<?php

namespace App\Repository;

use App\Entity\SitioLector;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SitioLector|null find($id, $lockMode = null, $lockVersion = null)
 * @method SitioLector|null findOneBy(array $criteria, array $orderBy = null)
 * @method SitioLector[]    findAll()
 * @method SitioLector[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SitioLectorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SitioLector::class);
    }

    // /**
    //  * @return SitioLector[] Returns an array of SitioLector objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SitioLector
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
