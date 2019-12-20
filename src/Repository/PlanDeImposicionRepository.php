<?php

namespace App\Repository;

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
            ->setMaxResults(100)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return PlanDeImposicion[] Returns an array of PlanDeImposicion objects
     */
    public function planesConLosDistintosCorresponsales(){
        $em = $this->getEntityManager();
        $query = $em->createQuery(
          'SELECT DISTINCT cod_corresponsal_id
          FROM planDeImposicion'
        );
        return $query->getResult();
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
}
