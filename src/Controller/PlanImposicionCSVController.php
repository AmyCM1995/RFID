<?php

namespace App\Controller;

use App\Repository\PlanImposicionCsvRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PlanImposicionCSVController extends AbstractController
{
    /**
     * @Route("/plan/imposicion/c/s/v", name="plan_imposicion_c_s_v")
     */
    public function index(PlanImposicionCsvRepository $planImposicionCsv): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render('plan_imposicion_csv/index.html.twig', [
            'plan_imposicion_csvs' => $planImposicionCsv -> findAll(),
        ]);
    }
}
