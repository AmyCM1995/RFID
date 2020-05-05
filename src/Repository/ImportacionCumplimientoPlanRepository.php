<?php

namespace App\Repository;

use App\Entity\ImportacionCumplimientoPlan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ImportacionCumplimientoPlan|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImportacionCumplimientoPlan|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImportacionCumplimientoPlan[]    findAll()
 * @method ImportacionCumplimientoPlan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImportacionCumplimientoPlanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImportacionCumplimientoPlan::class);
    }

    // /**
    //  * @return ImportacionCumplimientoPlan[] Returns an array of ImportacionCumplimientoPlan objects
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
    public function findOneBySomeField($value): ?ImportacionCumplimientoPlan
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findUltimaImportacion(){
        $importaciones = $this->findAll();
        $resultado = null;
        if($importaciones != null){
            $size = sizeof($importaciones)-1;
            $resultado = $importaciones[$size];
        }
        return $resultado;
    }
}
