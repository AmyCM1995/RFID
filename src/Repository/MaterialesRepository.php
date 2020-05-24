<?php

namespace App\Repository;

use App\Entity\Materiales;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Materiales|null find($id, $lockMode = null, $lockVersion = null)
 * @method Materiales|null findOneBy(array $criteria, array $orderBy = null)
 * @method Materiales[]    findAll()
 * @method Materiales[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MaterialesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Materiales::class);
    }

    // /**
    //  * @return Materiales[] Returns an array of Materiales objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Materiales
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
