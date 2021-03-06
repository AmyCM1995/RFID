<?php

namespace App\Repository;

use App\Entity\ProvinciaCuba;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ProvinciaCuba|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProvinciaCuba|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProvinciaCuba[]    findAll()
 * @method ProvinciaCuba[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProvinciaCubaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProvinciaCuba::class);
    }

    public function findAllByNombre(){
        return $this->createQueryBuilder('p')
            ->andWhere('p.es_activo = :val')
            ->setParameter('val', true)
            ->addOrderBy('p.nombre', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return ProvinciaCuba[] Returns an array of ProvinciaCuba objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProvinciaCuba
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
