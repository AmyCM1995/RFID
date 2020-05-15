<?php

namespace App\Repository;

use App\Entity\Importaciones;
use App\Entity\PlanDeImposicion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Symfony\Component\String\UnicodeString;

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

    public function traducirCicloEspañol($importación){
        $ciclo = new UnicodeString($importación->getCiclo());
        $inicio = $ciclo->before(" to ");
        $anoInicio = $inicio->slice($inicio->length()-4, $inicio->length());
        $mesInicio = $inicio->slice(1, $inicio->length()-6);
        $fin = $ciclo->after(" to ");
        $anoFin = $fin->slice($fin->length()-4, $fin->length());
        $mesFin = $fin->slice(0, $fin->length()-5);
        $mesInicioEspanol = $this->equivalenciasMeses($mesInicio);
        $mesFinEspanol = $this->equivalenciasMeses($mesFin);
        $cicloEspanol = $mesInicioEspanol." ".$anoInicio." a ".$mesFinEspanol." ".$anoFin;
        return $cicloEspanol;
    }

    public function equivalenciasMeses($mesIngles){
        $mesEspanol = null;
        if($mesIngles == "January"){
            $mesEspanol = "Enero";
        }elseif ($mesIngles == "February"){
            $mesEspanol = "Febrero";
        }elseif ($mesIngles == "March"){
            $mesEspanol = "Marzo";
        }elseif ($mesIngles == "April"){
            $mesEspanol = "Abril";
        }elseif ($mesIngles == "May"){
            $mesEspanol = "Mayo";
        }elseif ($mesIngles == "June"){
            $mesEspanol = "Junio";
        }elseif ($mesIngles == "July"){
            $mesEspanol = "Julio";
        }elseif ($mesIngles == "August"){
            $mesEspanol = "Agosto";
        }elseif ($mesIngles == "September"){
            $mesEspanol = "Septiembre";
        }elseif ($mesIngles == "October"){
            $mesEspanol = "Octubre";
        }elseif ($mesIngles == "November"){
            $mesEspanol = "Noviembre";
        }elseif ($mesIngles == "December"){
            $mesEspanol = "Diciembre";
        }
        return $mesEspanol;
    }
}
