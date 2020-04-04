<?php

namespace App\Repository;

use App\Entity\LecturasCsv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method LecturasCsv|null find($id, $lockMode = null, $lockVersion = null)
 * @method LecturasCsv|null findOneBy(array $criteria, array $orderBy = null)
 * @method LecturasCsv[]    findAll()
 * @method LecturasCsv[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LecturasCsvRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LecturasCsv::class);
    }

    // /**
    //  * @return LecturasCsv[] Returns an array of LecturasCsv objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LecturasCsv
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findEveryEnvio(){
        $lecturas = $this->findAll();
        $envios = null;
        $size = 1;
        $envios[0] = $lecturas[0]->getIdEnvio();
        foreach ($lecturas as $l){
            if($this->existe($envios, $l->getIdEnvio()) == false){
                $envios[$size] = $l->getIdEnvio();
                $size++;
            }
        }
        return $envios;
    }
    public function existe($envios, $id_envio){
        $existe = false;
        foreach ($envios as $e){
            if($e == $id_envio){
                $existe = true;
            }
        }
        return $existe;
    }
}
