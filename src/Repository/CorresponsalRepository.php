<?php

namespace App\Repository;

use App\Entity\Corresponsal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Corresponsal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Corresponsal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Corresponsal[]    findAll()
 * @method Corresponsal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CorresponsalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Corresponsal::class);
    }

    // /**
    //  * @return Corresponsal[] Returns an array of Corresponsal objects
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
    public function findOneByCodigo($value): ?Corresponsal
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.codigo = :val')
            ->andWhere('c.es_activo = 1')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneById($value): ?Corresponsal
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

}
