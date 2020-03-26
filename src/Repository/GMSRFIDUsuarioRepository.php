<?php

namespace App\Repository;

use
    App\Entity\GMSRFIDUsuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;


/**
 * @method GMSRFIDUsuario|null find($id, $lockMode = null, $lockVersion = null)
 * @method GMSRFIDUsuario|null findOneBy(array $criteria, array $orderBy = null)
 * @method GMSRFIDUsuario[]    findAll()
 * @method GMSRFIDUsuario[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GMSRFIDUsuarioRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GMSRFIDUsuario::class);
    }

    public function loadUserByUsername($nombreUsuario){
        $user = $this->createQueryBuilder('u')
            ->where('u.username = :username')
            ->setParameter('username', $nombreUsuario)
            ->getQuery()
            ->getOneOrNullResult();

        if (null === $user) {
            $message = sprintf(
                'Unable to find an active admin AppBundle:User object identified by "%s".',
                $nombreUsuario
            );
            throw new UsernameNotFoundException($message);
        }

        return $user;
    }

    public function flush(){
        $this->_em->flush();
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
