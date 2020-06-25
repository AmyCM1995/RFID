<?php

namespace App\Repository;

use App\Entity\HistorialLectores;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method HistorialLectores|null find($id, $lockMode = null, $lockVersion = null)
 * @method HistorialLectores|null findOneBy(array $criteria, array $orderBy = null)
 * @method HistorialLectores[]    findAll()
 * @method HistorialLectores[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistorialLectoresRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HistorialLectores::class);
    }
    public function findByLector($value)
    {
        return $this->createQueryBuilder('h')
            ->innerJoin('h.ipLector', 'i')
            ->innerJoin('i.lector', 'l', 'WITH', 'l.id = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10000)
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return HistorialLectores[] Returns an array of HistorialLectores objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?HistorialLectores
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
