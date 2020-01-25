<?php

namespace App\Repository;

use App\Entity\PaisCorrespondencia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PaisCorrespondencia|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaisCorrespondencia|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaisCorrespondencia[]    findAll()
 * @method PaisCorrespondencia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaisCorrespondenciaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaisCorrespondencia::class);
    }

     /**
      * @return PaisCorrespondencia[] Returns an array of PaisCorrespondencia objects
      */

    public function findByActivo()
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.es_activo = :val')
            ->setParameter('val', true)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
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
    public function findAllByNombreAndDelete(){
        return $this->createQueryBuilder('p')
            ->andWhere('p.es_activo = :val')
            ->setParameter('val', false)
            ->addOrderBy('p.nombre', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }



    public function findOneByCodigo($value): ?PaisCorrespondencia
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.codigo = :val')
            ->andWhere('p.es_activo = 1')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

}
