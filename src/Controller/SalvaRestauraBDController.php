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
     * @Route("/salva/restaura/b/d/salva/bd", name="salva_restaura_b_d_salva_bd")
     */
    public function salvaBD()
    {
        $host = "localhost";
        $usuario = "root";
        $contrasena = "";
        $nbBD = "GMS_RFID_2";
        $nbBackup = "backup_db/myBackup.sql";
        $tabas = "data";
        $commando = 'mysqldump --opt -h' . $host .' -u' . $usuario . ' -p' . $contrasena .' ' .$nbBD . ' > ~/' .$nbBackup;
        exec($commando);
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

        /*$dbName = 'GMS_RFID_2';
        $process = new Process(array(
           'mysqldump',
           '--user='.getenv('DB_USER'),
           '--password'.getenv('DB_PASS'),
           $dbName,
        ));
        //$process->setTimeout(3600);
        $process->run();*/

        return $this->render('salva_restaura_bd/index.html.twig', [
            'controller_name' => 'SalvaRestauraBDController',
        ]);
        //return $this->render('salva_restaura_bd/salvaPI.php');
    }
}
