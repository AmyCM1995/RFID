<?php

namespace App\Repository;

use App\Entity\Importaciones;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

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
    //  * @return Importaciones[] Returns an array of Importaciones objects
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
        $size = sizeof($importaciones)-1;
        return $importaciones[$size];
    }
}
