<?php

namespace App\Repository;

use App\Entity\PaisCorrespondencia;
use App\Entity\Totales;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Symfony\Component\String\UnicodeString;

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

    //********************************************************************Totales
    public function buscarCorresponsalesCubanos(){
        $totaless = $this->findAll();
        $corresponsalesCuba = [$totaless[0]->getCorresponsalCuba()];
        $size=1;
        for($i=1; $i<sizeof($totaless); $i++){
            if($totaless[$i]->getCorresponsalCuba() != null){
                if($this->existeCorresponsalCuba($corresponsalesCuba, $totaless[$i]->getCorresponsalCuba()) == false){
                    $corresponsalesCuba[$size] = $totaless[$i]->getCorresponsalCuba();
                    $size++;
                }
            }
        }
        return $corresponsalesCuba;
    }
    public function buscarCorresponsalesDestino(){
        $totaless = $this->findAll();
        $corresponsalesDestino = [$totaless[0]->getCorresponsalDestino()];
        $size=1;
        for($i=1; $i<sizeof($totaless); $i++){
            if($totaless[$i]->getCorresponsalDestino() != null){
                $p = new UnicodeString($totaless[$i]->getCorresponsalDestino());
                if($p->length() == 4){
                    if($this->existeCorresponsalDestino($corresponsalesDestino, $totaless[$i]->getCorresponsalDestino()) == false){
                        $corresponsalesDestino[$size] = $totaless[$i]->getCorresponsalDestino();
                        $size++;
                    }
                }
            }
        }
        //ordenar arreglo
        usort($corresponsalesDestino, "strnatcmp");

        return $corresponsalesDestino;
    }
    public function buscarCodigoPaíses(){
        $totaless = $this->findAll();
        $paisesDestino = [$totaless[0]->getCorresponsalDestino()];
        $size=1;
        for($i=1; $i<sizeof($totaless); $i++){
            if($totaless[$i]->getCorresponsalDestino() != null){
                $p = new UnicodeString($totaless[$i]->getCorresponsalDestino());
                if($p->length() == 2){
                    if($this->existePaisDestino($paisesDestino, $totaless[$i]->getCorresponsalDestino()) == false){
                        $paisesDestino[$size] = $totaless[$i]->getCorresponsalDestino();
                        $size++;
                    }
                }
            }
        }
        return $paisesDestino;
    }
    public function buscarPaises($repositorio){
        $paisesCodDestino = $this->buscarCodigoPaíses();
        $paisesGnral = $repositorio->findByActivo();
        $paisesDestino = [new PaisCorrespondencia()];
        $size = 0;
        for($i=0; $i<sizeof($paisesCodDestino); $i++){
            for($j=0; $j<sizeof($paisesGnral); $j++){
                if($paisesCodDestino[$i] == $paisesGnral[$j]->getCodigo()){
                    $paisesDestino[$size] = $paisesGnral[$j];
                    $size++;
                }
            }
        }
        return $paisesDestino;

    }
    public function totalesPaises($repositorio){
        $totaless = $this->findAll();
        $paises = $this->buscarPaises($repositorio);
        $totalesPaises=[];
        $size = 0;
        for($i=0; $i<sizeof($paises); $i++){
            for($j=0; $j<sizeof($totaless); $j++){
                if($paises[$i]->getCodigo() == $totaless[$j]->getCorresponsalDestino() && $totaless[$j]->getCorresponsalCuba() == null){
                    $totalesPaises[$size] = $totaless[$j]->getTotalEnvios();
                    $size++;
                }
            }
        }
        return $totalesPaises;
    }
    public function enviosTotales(){
        $totaless = $this->findAll();
        $total = 0;
        for($i=0; $i<sizeof($totaless); $i++){
            if($totaless[$i]->getCorresponsalDestino() != null && $totaless[$i]->getCorresponsalCuba() == null) {
                $p = new UnicodeString($totaless[$i]->getCorresponsalDestino());
                if ($p->length() == 2) {
                    $total += $totaless[$i]->getTotalEnvios();
                }
            }
        }
        return $total;
    }
    public function tablaTotalesCorresponsal($corresponsalCubano, $corresponsalesDestino){
        $totaless = $this->findAll();
        $totales = [];
        $size = 0;
        for($i=0; $i<sizeof($corresponsalesDestino); $i++){
            for($j=0; $j<sizeof($totaless); $j++){
                if($totaless[$j]->getCorresponsalCuba() == $corresponsalCubano){
                    if($corresponsalesDestino[$i] == $totaless[$j]->getCorresponsalDestino()){
                        $totales[$size] = $totaless[$j]->getTotalEnvios();
                        $size++;
                    }
                }
            }
        }
        return $totales;
    }
    public function existeCorresponsalCuba($corresponsalesCuba, $c){
        $existe = false;
        for($i=0; $i<sizeof($corresponsalesCuba); $i++){
            if($corresponsalesCuba[$i] == $c){
                $existe = true;
                break;
            }
        }
        return $existe;
    }
    public function totalEnviosCorresponsal($tablaCorresponsalesCorresponsal){
        $total = 0;
        for($i=0; $i<sizeof($tablaCorresponsalesCorresponsal); $i++){
            $total += $tablaCorresponsalesCorresponsal[$i];
        }
        return $total;
    }
    public function existePaisDestino($paisesDestino, $p){
        $existe = false;
        for($i=0; $i<sizeof($paisesDestino); $i++){
            if($paisesDestino[$i] == $p){
                $existe = true;
                break;
            }
        }
        return $existe;
    }
    public function existeCorresponsalDestino($corresponsalesDestino, $c){
        $existe = false;
        for($i=0; $i<sizeof($corresponsalesDestino); $i++){
            if($corresponsalesDestino[$i] == $c){
                $existe = true;
                break;
            }
        }
        return $existe;
    }

    public function matrizTotales($corresponsalesCubanos, $corresponsalesDestino){
        $matriz[][] = 0;
        for($i=0; $i<sizeof($corresponsalesCubanos); $i++){
            for($j=0; $j<sizeof($corresponsalesDestino); $j++){
                $total = $this->findOneByCorresCubanoYDestino($corresponsalesCubanos[$i], $corresponsalesDestino[$j]);
                if($total != null){
                    $matriz[$i][$j] = $total->getTotalEnvios();
                }else{
                    $matriz[$i][$j] = -1;
                }
            }
        }

        return $matriz;
    }

    public function findOneByCorresCubanoYDestino($corrCuba, $corrDest){
        return $this->createQueryBuilder('t')
            ->andWhere('t.corresponsalCuba = :val')
            ->andWhere('t.corresponsalDestino = :v')
            ->setParameter('val', $corrCuba)
            ->setParameter('v', $corrDest)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }


    //**********************************************************Materiales
    public function paisesDestinoTarifas($paises, $tarifa){
        $paisesTarifa = [];
        $size = 0;
        for($i=0; $i<sizeof($paises); $i++){
            if($paises[$i]->getRegion()->getTarifa() == $tarifa){
                $paisesTarifa[$size] = $paises[$i];
                $size++;
            }
        }
        return $paisesTarifa;
    }
    public function totalArreglo ($t){
        $total = 0;
        for($i=0; $i<sizeof($t); $i++){
            $total += $t[$i];
        }
        return $total;
    }
    public function arrTotalCorresponsalesPaises($repositorio, $corresponsal, $paisesDestino){
        $arr = [];
        $size = 0;
        for($i=0; $i<sizeof($paisesDestino); $i++){
            $arr[$size] = $repositorio->findOneByCorresponsalDestino($corresponsal, $paisesDestino[$i]->getCodigo())->getTotalEnvios();
            $size++;
        }
        return $arr;
    }
    public function totalEnviosCorresponsalesTarifa($paisesaTarifa){
        $corresponsalesCuba = $this->buscarCorresponsalesCubanos();
        $total = [];
        $size = 0;
        for($i=0; $i<sizeof($corresponsalesCuba); $i++){
            $total[$size] = $this->totalEnviosCorresponsalTarifa($corresponsalesCuba[$i], $paisesaTarifa);
            $size++;
        }
        return $total;
    }
    public function totalEnviosCorresponsalTarifa($corresponsal, $paisesTarifa){
        $totaless = $this->findAll();
        $totalEnvios = 0;
        for($i=0; $i<sizeof($paisesTarifa); $i++){
            for($j=0; $j<sizeof($totaless); $j++){
                if($totaless[$j]->getCorresponsalDestino() == $paisesTarifa[$i]->getCodigo() && $totaless[$j]->getCorresponsalCuba() == $corresponsal){
                    $totalEnvios += $totaless[$j]->getTotalEnvios();
                }
            }
        }
        return $totalEnvios;
    }

}
