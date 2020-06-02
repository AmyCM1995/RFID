<?php

namespace App\Repository;

use App\Entity\IPLectorCubano;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method IPLectorCubano|null find($id, $lockMode = null, $lockVersion = null)
 * @method IPLectorCubano|null findOneBy(array $criteria, array $orderBy = null)
 * @method IPLectorCubano[]    findAll()
 * @method IPLectorCubano[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IPLectorCubanoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IPLectorCubano::class);
    }

    // /**
    //  * @return IPLectorCubano[] Returns an array of IPLectorCubano objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?IPLectorCubano
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
