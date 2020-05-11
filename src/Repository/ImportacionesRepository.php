<?php

namespace App\Repository;

use App\Entity\Importaciones;
use App\Entity\PlanDeImposicion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;

/**
 * @method Importaciones|null find($id, $lockMode = null, $lockVersion = null)
 * @method Importaciones|null findOneBy(array $criteria, array $orderBy = null)
 * @method Importaciones[]    findAll()
 * @method Importaciones[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImportacionesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Importaciones::class);
    }

    // /**
    //  * @return ImportacionesPI[] Returns an array of Importaciones objects
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
    public function findOneBySomeField($value): ?Importaciones
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
    public function findUltimaImportacionConPI($piRepositorio){
        $importaciones = $this->findAll();
        $resultado = null;
        if($importaciones != null){
            for($i=sizeof($importaciones)-1; $i>=0; $i--) { //recorro el array de fin a inicio
                //verificar si la importación tiene PI
                $pis = $piRepositorio->findByImposicion($importaciones[$i]->getId());
                if (sizeof($pis) != 0) {
                    $resultado = $importaciones[$i];
                    break;
                }
            }
        }
        return $resultado;
    }
    public function importacionesMismoRangoFechas($fechaI, $fechaF){
        return $this->createQueryBuilder('i')
            ->andWhere('i.fecha_inicio_plan = :val')
            ->andWhere('i.fecha_fin_plan = :v')
            ->setParameter('val', $fechaI)
            ->setParameter('v', $fechaF)
            ->setMaxResults(100)
            ->getQuery()
            ->getResult()
            ;
    }
}
