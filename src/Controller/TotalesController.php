<?php

namespace App\Controller;

use App\Entity\Corresponsal;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TotalesController extends AbstractController
{
    /**
     * @Route("/totales", name="totales")
     */
    public function index()
    {
        return $this->render('totales/index.html.twig', [
            'controller_name' => 'TotalesController',
        ]);
    }







}
