<?php

namespace App\Repository;

use App\Entity\Corresponsal;
use App\Entity\PlanDeImposicion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PlanDeImposicion|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanDeImposicion|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanDeImposicion[]    findAll()
 * @method PlanDeImposicion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanDeImposicionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanDeImposicion::class);
    }

     /**
      * @return PlanDeImposicion[] Returns an array of PlanDeImposicion objects
      */

    public function findByImposicion($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.importacion = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(1000)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return PlanDeImposicion[] Returns an array of PlanDeImposicion objects
     */
    public function findByAnno($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.fecha LIKE :val')
            ->setParameter('val', '%'.$value.'%')
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(100000)
            ->getQuery()
            ->getResult()
            ;
    }
    public function findByCiclo($value)
    {
        $arr[] = 0;
        $size = 0;
        $todos = $this->findAll();
        foreach ($todos as $plan){
            if($plan->getImportacion()->getCiclo() == $value){
                $arr[$size] = $plan;
                $size++;
            }
        }
        return $arr;
    }

    /**
     * @return PlanDeImposicion[] Returns an array of PlanDeImposicion objects
     */
    public function findByFecha($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.fecha = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(1000)
            ->getQuery()
            ->getResult()
            ;
    }

    /*
    public function findOneBySomeField($value): ?PlanDeImposicion
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function planesDeImposicionActuales($planDeImposicionRepositorio, $importacionUltima){
        $plan_de_imposicions = $planDeImposicionRepositorio->findByImposicion($importacionUltima->getId());
        return $plan_de_imposicions;
    }
    public function corresponsalesdelPlan($corresponsalRepository, $plan_de_imposicions){
        $planDeDistintosCorresonsales = $this->buscarPlanesConDistintosCorresponsales($plan_de_imposicions);
        $corresponsales = $this->buscarCorresponsalesId($corresponsalRepository, $planDeDistintosCorresonsales);
        return $corresponsales;
    }
    public function buscarPlanesConDistintosCorresponsales($plan){
        $esta = false;
        $size = 1;
        $planesDistintosCorresponsales = [$plan[0]];
        for($i=0; $i<sizeof($plan); $i++){
            for($j=0; $j<$size; $j++){
                if($plan[$i]->getCodCorresponsal()->getId() == $planesDistintosCorresponsales[$j]->getCodCorresponsal()->getId()){
                    $esta = true;
                    break;
                }else{
                    $esta = false;
                }
            }
            if($esta == false){
                $planesDistintosCorresponsales[$size] = $plan[$i];
                $size++;
            }
        }
        return$planesDistintosCorresponsales;
    }
    public function buscarCorresponsalesId($corresponsalRepository, $planDeDistintosCorresonsales){
        $corresponsales = null;
        for($i=0; $i<sizeof($planDeDistintosCorresonsales); $i++){
            $corresponsales[$i] = $corresponsalRepository->findOneById($planDeDistintosCorresonsales[$i]->getCodCorresponsal()->getId());
        }
        return $corresponsales;
    }

    public function paisesDelPlan($plan_de_imposicions){
        $paises = [];
        $size = 0;
        foreach ($plan_de_imposicions as $plan_de_imposicion){
            if($plan_de_imposicion->getCodPais() != null){
                if($size == 0){
                    $paises[0] = $plan_de_imposicion->getCodPais();
                    $size++;
                }else{
                    if($this->existePais($paises, $plan_de_imposicion->getCodPais()) == false){
                        $paises[$size] = $plan_de_imposicion->getCodPais();
                        $size++;
                    }
                }
            }

        }
        return $this->organizarPaises($paises);
    }

    public function organizarPaises($paises){
        $result = [];
        $codPaises = [];
        $size = 0;
        foreach ($paises as $paise){
            $codPaises[$size] = $paise->getCodigo();
            $size++;
        }
        usort($codPaises, "strnatcmp");
        $size = 0;
        foreach ($codPaises as $cod){
            foreach ($paises as $pais){
                if($cod == $pais->getCodigo()){
                    $result[$size] = $pais;
                    $size++;
                }
            }
        }
        return $result;
    }

    public function existePais($paises, $pais){
        $existe = false;
        foreach ($paises as $paise){
            if($paise->getId() == $pais->getId()){
                $existe = true;
            }
        }
        return $existe;
    }

    public function corresponsalesDestinoDelPlan($plan_de_imposicions){
        $corresponsalesDestino = [];
        $size = 0;
        foreach ($plan_de_imposicions as $plan_de_imposicion){
            if($plan_de_imposicion->getCodEnvio() != null){
                if($size == 0){
                    $corresponsalesDestino[0] = $plan_de_imposicion->getCodEnvio();
                    $size++;
                }else{
                    if($this->existeCorresponsalDestino($corresponsalesDestino, $plan_de_imposicion->getCodEnvio()) == false){
                        $corresponsalesDestino[$size] = $plan_de_imposicion->getCodEnvio();
                        $size++;
                    }
                }
            }
        }
        usort($corresponsalesDestino, "strnatcmp");
        return $corresponsalesDestino;
    }
    public function existeCorresponsalDestino($corresponsalesDestino, $envio){
        $existe = false;
        foreach ($corresponsalesDestino as $corr){
            if($corr == $envio){
                $existe = true;
            }
        }
        return $existe;
    }

    public function totalArrPaisess($plan, $paises){
        $ctdArrPaises = [];
        $size = 0;
        foreach ($paises as $pais){
            $ctdArrPaises[$size] = $this->totalPais($plan, $pais);
            $size++;
        }
        return $ctdArrPaises;
    }

    public function totalPais($plan, $pais){
        $ctd = 0;
        foreach ($plan as $p){
            if($p->getCodPais() != null){
                if($p->getCodPais()->getId() == $pais->getId()){
                    $ctd++;
                }
            }
        }
        return $ctd;
    }

    public function totalEnvios($totalesPaises){
        $total = 0;
        for($i=0; $i<sizeof($totalesPaises); $i++){
            $total += $totalesPaises[$i];
        }
        return $total;
    }

    public function totalEnviosCorresponsalesTarifa($CorrCuba, $paisesaTarifa, $plan){
        $total = [];
        $size = 0;
        for($i=0; $i<sizeof($CorrCuba); $i++){
            $total[$size] = $this->totalEnviosCorresponsalTarifa($CorrCuba[$i], $paisesaTarifa, $plan);
            $size++;
        }
        return $total;
    }
    public function totalEnviosCorresponsalTarifa($corresponsal, $paisesTarifa, $plan){
        $totalEnvios = 0;
        for($i=0; $i<sizeof($plan); $i++){
            for($j=0; $j<sizeof($paisesTarifa); $j++){
                if($plan[$i]->getCodPais() != null){
                    if($plan[$i]->getCodPais()->getCodigo() == $paisesTarifa[$j]->getCodigo() && $plan[$i]->getCodCorresponsal() == $corresponsal){
                        $totalEnvios++;
                    }
                }
            }
        }
        return $totalEnvios;
    }

    public function compararPIdeImportacionesDiferentes($idImportacionNueva, $idImportacionVieja){
        $piNuevos = $this->findByImposicion($idImportacionNueva);
        $piViejos = $this->findByImposicion($idImportacionVieja);
        $iguales = true;
        if(sizeof($piNuevos) == sizeof($piViejos)){
            for($i=0; $i<sizeof($piNuevos); $i++){
                if($piNuevos[$i]->getFecha()->diff($piViejos[$i]->getFecha())->format('%d') != 0 ||
                    $piNuevos[$i]->getCodEnvio() != $piViejos[$i]->getCodEnvio() ||
                    $piNuevos[$i]->getCodCorresponsal()->getId() != $piViejos[$i]->getCodCorresponsal()->getId()){
                    $iguales = false;
                }
                if($iguales == false){
                    break;
                }
            }
        }else{
            $iguales = false;
        }
        return $iguales;
    }
}

