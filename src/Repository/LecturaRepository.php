<?php

namespace App\Repository;

use App\Entity\Lectura;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Lectura|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lectura|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lectura[]    findAll()
 * @method Lectura[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LecturaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lectura::class);
    }

    // /**
    //  * @return Lectura[] Returns an array of Lectura objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Lectura
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
