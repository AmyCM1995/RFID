<?php

namespace App\Repository;

use App\Entity\Totales;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Totales|null find($id, $lockMode = null, $lockVersion = null)
 * @method Totales|null findOneBy(array $criteria, array $orderBy = null)
 * @method Totales[]    findAll()
 * @method Totales[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TotalesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Totales::class);
    }

     /**
      * @return Totales[] Returns an array of Totales objects
      */

    public function findByCorresponsalDestino($value1, $value2)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.corresponsalCuba = :val')
            ->andWhere('t.corresponsalDestino = :v')
            ->setParameter('val', $value1)
            ->setParameter('v', $value2)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(100)
            ->getQuery()
            ->getResult()
        ;
    }



    public function findOneByCorresponsalDestino($value1, $value2): ?Totales
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.corresponsalCuba = :val')
            ->andWhere('t.corresponsalDestino = :v')
            ->setParameter('val', $value1)
            ->setParameter('v', $value2)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

}
