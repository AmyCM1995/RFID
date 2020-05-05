<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ImportacionCumplimientoPlanController extends AbstractController
{
    /**
     * @Route("/importacion/cumplimiento/plan", name="importacion_cumplimiento_plan")
     */
    public function index()
    {
        return $this->render('importacion_cumplimiento_plan/index.html.twig', [
            'controller_name' => 'ImportacionCumplimientoPlanController',
        ]);
    }
}
