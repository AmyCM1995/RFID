<?php

namespace App\Repository;

use App\Entity\BorradoPropio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BorradoPropio|null find($id, $lockMode = null, $lockVersion = null)
 * @method BorradoPropio|null findOneBy(array $criteria, array $orderBy = null)
 * @method BorradoPropio[]    findAll()
 * @method BorradoPropio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BorradoPropioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BorradoPropio::class);
    }

    // /**
    //  * @return BorradoPropio[] Returns an array of BorradoPropio objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */


    public function findOneByCodigo($value): ?BorradoPropio
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.codigo = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

}
