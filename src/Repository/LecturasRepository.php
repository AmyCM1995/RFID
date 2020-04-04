<?php

namespace App\Repository;

use App\Entity\Lecturas;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Lecturas|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lecturas|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lecturas[]    findAll()
 * @method Lecturas[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LecturasRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lecturas::class);
    }

    // /**
    //  * @return Lecturas[] Returns an array of Lecturas objects
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
    public function findOneBySomeField($value): ?Lecturas
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
