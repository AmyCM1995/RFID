<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Process\Process;

class SalvaRestauraBDController extends AbstractController
{
    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/salva/restaura/b/d", name="salva_restaura_b_d")
     */
    public function index()
    {
        return $this->render('salva_restaura_bd/index.html.twig', [
            'controller_name' => 'SalvaRestauraBDController',
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/salva/restaura/b/d/salva/pi", name="salva_restaura_b_d_salva_pi")
     */
    public function salvaPI()
    {
        /*$process = new Process('mysqldump --user={{ db_user }} --password={{ db_pass }} {{db_name}} > {{ db_backup_path }}');
        $process->run(null, [
           'db_user' => getenv('DB_USER'),
           'db_password' => getenv('DB_PASS'),
           'db_name' => 'GMS_RFID_2',
           'db_backup_path' => '/var/backup/db-'.time().'.sql',
        ]);*/
        $dbName = 'GMS_RFID_2';
        $process = new Process(array(
           'mysqldump',
           '--user='.getenv('DB_USER'),
           '--password'.getenv('DB_PASS'),
           $dbName,
        ));
        //$process->setTimeout(3600);
        $process->run();

        return $this->render('salva_restaura_bd/index.html.twig', [
            'controller_name' => 'SalvaRestauraBDController',
        ]);
        //return $this->render('salva_restaura_bd/salvaPI.php');
    }
}
