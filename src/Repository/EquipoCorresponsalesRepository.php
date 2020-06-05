<?php

namespace App\Repository;

use App\Entity\EquipoCorresponsales;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method EquipoCorresponsales|null find($id, $lockMode = null, $lockVersion = null)
 * @method EquipoCorresponsales|null findOneBy(array $criteria, array $orderBy = null)
 * @method EquipoCorresponsales[]    findAll()
 * @method EquipoCorresponsales[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EquipoCorresponsalesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EquipoCorresponsales::class);
    }
    public function findByActivo()
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.es_activo = false')
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(1000)
            ->getQuery()
            ->getResult()
            ;
    }
    // /**
    //  * @return EquipoCorresponsales[] Returns an array of EquipoCorresponsales objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EquipoCorresponsales
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
