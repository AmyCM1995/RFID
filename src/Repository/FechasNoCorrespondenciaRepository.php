<?php

namespace App\Repository;

use App\Entity\FechasNoCorrespondencia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FechasNoCorrespondencia|null find($id, $lockMode = null, $lockVersion = null)
 * @method FechasNoCorrespondencia|null findOneBy(array $criteria, array $orderBy = null)
 * @method FechasNoCorrespondencia[]    findAll()
 * @method FechasNoCorrespondencia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FechasNoCorrespondenciaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FechasNoCorrespondencia::class);
    }

    // /**
    //  * @return FechasNoCorrespondencia[] Returns an array of FechasNoCorrespondencia objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */


    public function findOneByFecha($value): ?FechasNoCorrespondencia
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.fecha = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

}
