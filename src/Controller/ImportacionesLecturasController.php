<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ImportacionesLecturasController extends AbstractController
{
    /**
     * @Route("/importaciones/lecturas", name="importaciones_lecturas")
     */
    public function index()
    {
        return $this->render('importaciones_lecturas/index.html.twig', [
            'controller_name' => 'ImportacionesLecturasController',
        ]);
    }
}
