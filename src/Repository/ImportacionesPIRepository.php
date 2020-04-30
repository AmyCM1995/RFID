<?php

namespace App\Repository;

use App\Entity\ImportacionesPI;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ImportacionesPI|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImportacionesPI|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImportacionesPI[]    findAll()
 * @method ImportacionesPI[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImportacionesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImportacionesPI::class);
    }

    // /**
    //  * @return ImportacionesPI[] Returns an array of ImportacionesPI objects
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
    public function findOneBySomeField($value): ?ImportacionesPI
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
        $size = sizeof($importaciones)-1;
        return $importaciones[$size];
    }
    public function existeImportacionMismoRangoFechas($fechaI, $fechaF){
        $existe = false;
        $result = $this->createQueryBuilder('i')
            ->andWhere('i.fecha_inicio_plan = :val')
            ->andWhere('i.fecha_fin_plan = :v')
            ->setParameter('val', $fechaI)
            ->setParameter('v', $fechaF)
            ->getQuery()
            ->getOneOrNullResult()
        ;
        if($result != null){
            $existe = true;
        }
        return $existe;
    }
}
