<?php

namespace App\Repository;

use App\Entity\GMSRFIDUsuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method GMSRFIDUsuario|null find($id, $lockMode = null, $lockVersion = null)
 * @method GMSRFIDUsuario|null findOneBy(array $criteria, array $orderBy = null)
 * @method GMSRFIDUsuario[]    findAll()
 * @method GMSRFIDUsuario[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GMSRFIDUsuarioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GMSRFIDUsuario::class);
    }

    public function obtenerUsuarioPorNombre($nombreUsuario){
        return $this->createQuery(
            'SELECT u FROM App\src\Entity\GMSRFIDUsuario u
            WHERE u.nombre =:query'
        )
            ->setParameter('query', $nombreUsuario)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // /**
    //  * @return GMSRFIDUsuario[] Returns an array of GMSRFIDUsuario objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GMSRFIDUsuario
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
