<?php

namespace App\Repository;

use App\Entity\PlanImposicionCsv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PlanImposicionCsv|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanImposicionCsv|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanImposicionCsv[]    findAll()
 * @method PlanImposicionCsv[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanImposicionCsvRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanImposicionCsv::class);
    }

    // /**
    //  * @return PlanImposicionCsv[] Returns an array of PlanImposicionCsv objects
    //  */
    /*
        public function findByFecha($value)
        {
            return $this->createQueryBuilder('p')
                ->andWhere('p.fecha = :val')
                ->setParameter('val', $value)
                ->orderBy('p.id', 'ASC')
                ->setMaxResults(10)
                ->getQuery()
                ->getResult()
            ;
        }
    */


        public function findOneByFecha($value): ?PlanImposicionCsv
        {
            return $this->createQueryBuilder('p')
                ->andWhere('p.fecha = :val')
                ->setParameter('val', $value)
                ->getQuery()
                ->getOneOrNullResult()
            ;
        }

}
