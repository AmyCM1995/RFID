<?php

namespace App\Controller;

use App\Entity\ImportacionCumplimientoPlan;
use App\Entity\Importaciones;
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
        $ultimoPI = null;
        $ultimaLecturaCSV = null;
        $ultimoCumplimientoPI = null;
        $usuario = $this->getUser();
        $rol = $usuario->getRoles();
        $hoy = new \DateTime('now');
        if($rol[0] == 'ROLE_ESPECIALISTA_DC'){
            $importacionRepositorio = $this->getDoctrine()->getRepository(Importaciones::class);
            $importacionUltima = $importacionRepositorio->findUltimaImportacion();
            if($importacionUltima != null){
                $fechaUltima = $importacionUltima->getFechaImportado();
                $ultimoPI = $fechaUltima->diff($hoy)->format('%d');
            }else{
                $ultimoPI = null;
            }
            $importacionLecturasCSVRepository = $this->getDoctrine()->getRepository(ImportacionesLecturas::class);
            $importacionUltima = $importacionLecturasCSVRepository->findUltimaImportacion();
            if($importacionUltima != null){
                $fechaUltima = $importacionUltima->getFecha();
                $ultimaLecturaCSV = $fechaUltima->diff($hoy)->format('%d');
            }else{
                $ultimaLecturaCSV = null;
            }
            $importacionCumplimientoPIRepositorio = $this->getDoctrine()->getRepository(ImportacionCumplimientoPlan::class);
            $importacionUltima = $importacionCumplimientoPIRepositorio->findUltimaImportacion();
            if($importacionUltima != null){
                $fechaUltima = $importacionUltima->getFecha();
                $ultimoCumplimientoPI = $fechaUltima->diff($hoy)->format('%d');
            }else{
                $ultimoCumplimientoPI = null;
            }
        }

        return $this->render('principal/index.html.twig', [
            'ultimoPI' => $ultimoPI,
            'ultimaLecturaCSV' => $ultimaLecturaCSV,
            'ultimoCumplimientoPI' => $ultimoCumplimientoPI,

        ]);
    }

}
