<?php

namespace App\Repository;

use App\Entity\Ciudad;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Ciudad|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ciudad|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ciudad[]    findAll()
 * @method Ciudad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CiudadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ciudad::class);
    }

    // /**
    //  * @return Ciudad[] Returns an array of Ciudad objects
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


    public function findOneByCodigo($value): ?Ciudad
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.codigo = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

}
