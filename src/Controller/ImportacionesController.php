<?php

namespace App\Controller;

use App\Repository\ImportacionesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/importaciones")
 */
class ImportacionesController extends AbstractController
{
    /**
     * @Route("/", name="importaciones_index", methods={"GET"})
     */
    public function index(ImportacionesRepository $importacionesRepository): Response
    {
        return $this->render('importaciones/index.html.twig', [
            'importaciones' => $importacionesRepository->findAll(),
        ]);
    }
}
