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
}
