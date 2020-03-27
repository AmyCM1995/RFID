<?php

namespace App\Repository;

use App\Entity\LecturasCsv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method LecturasCsv|null find($id, $lockMode = null, $lockVersion = null)
 * @method LecturasCsv|null findOneBy(array $criteria, array $orderBy = null)
 * @method LecturasCsv[]    findAll()
 * @method LecturasCsv[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LecturasCsvRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LecturasCsv::class);
    }

    // /**
    //  * @return LecturasCsv[] Returns an array of LecturasCsv objects
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
    public function findOneBySomeField($value): ?LecturasCsv
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
