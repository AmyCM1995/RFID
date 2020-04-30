<?php

namespace App\Controller;

use App\Entity\ImportacionesPI;
use App\Entity\ImportacionesLecturas;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PrincipalController extends AbstractController
{
    /**
     * @Route("/principal", name="principal")
     */
    public function index()
    {
        $usuario = $this->getUser();
        $rol = $usuario->getRoles();
        $hoy = new \DateTime('now');
        if($rol[0] == 'ROLE_ESPECIALISTA_DC'){
            $importacionPIRepositorio = $this->getDoctrine()->getRepository(ImportacionesPI::class);
            $importacionUltima = $importacionPIRepositorio->findUltimaImportacion();
            $fechaUltima = $importacionUltima->getFechaImportado();
            $ultimoPI = $fechaUltima->diff($hoy)->format('%d');
            $importacionLecturasCSVRepository = $this->getDoctrine()->getRepository(ImportacionesLecturas::class);
            $importacionUltima = $importacionLecturasCSVRepository->findUltimaImportacion();
            $fechaUltima = $importacionUltima->getFecha();
            $ultimaLecturaCSV = $fechaUltima->diff($hoy)->format('%d');
        }

        return $this->render('principal/index.html.twig', [
            'ultimoPI' => $ultimoPI,
            'ultimaLecturaCSV' => $ultimaLecturaCSV,

        ]);
    }

}
