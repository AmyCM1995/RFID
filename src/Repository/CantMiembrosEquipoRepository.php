<?php

namespace App\Repository;

use App\Entity\CantMiembrosEquipo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CantMiembrosEquipo|null find($id, $lockMode = null, $lockVersion = null)
 * @method CantMiembrosEquipo|null findOneBy(array $criteria, array $orderBy = null)
 * @method CantMiembrosEquipo[]    findAll()
 * @method CantMiembrosEquipo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CantMiembrosEquipoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CantMiembrosEquipo::class);
    }

    public function findUltimo(): ?CantMiembrosEquipo
    {
        $cant = $this->findAll();
        return $cant[sizeof($cant)-1];
    }
    // /**
    //  * @return CantMiembrosEquipo[] Returns an array of CantMiembrosEquipo objects
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

    /*
    public function findOneBySomeField($value): ?CantMiembrosEquipo
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
