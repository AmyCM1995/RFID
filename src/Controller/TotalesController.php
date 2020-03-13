<?php

namespace App\Controller;

use App\Entity\Corresponsal;
use App\Entity\Importaciones;
use App\Entity\PaisCorrespondencia;
use App\Entity\PlanDeImposicion;
use App\Entity\PlanImposicionCsv;
use App\Entity\Totales;
use App\Repository\PlanDeImposicionRepository;
use App\Repository\TotalesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Dompdf\Dompdf;
use Dompdf\Options;

class TotalesController extends AbstractController
{
    /**
     * @Route("/totales", name="totales", methods={"GET"})
     */
    public function index(TotalesRepository $totalesRepositorio): Response
    {
        $repositorio = $this->getDoctrine()->getRepository(PaisCorrespondencia::class);
        $corresponsalesCubanos = $totalesRepositorio->buscarCorresponsalesCubanos();
        $corresponsalesDestino = $totalesRepositorio->buscarCorresponsalesDestino();
        $paisesDestino = $totalesRepositorio->buscarPaises($repositorio);
        $totalesPaises = $totalesRepositorio->totalesPaises($repositorio);
        $matriz = $totalesRepositorio->matrizTotales($corresponsalesCubanos, $corresponsalesDestino);
        $enviosTotales = $totalesRepositorio->enviosTotales();
        $importacionRepositorio = $this->getDoctrine()->getRepository(Importaciones::class);
        $importacionUltima = $importacionRepositorio->findUltimaImportacion();

        return $this->render('totales/index.html.twig', [
            'matriz' => $matriz,
            'corresponsalesCubanos' => $corresponsalesCubanos,
            'corresponsalesDestino' => $corresponsalesDestino,
            'paisesDestino' => $paisesDestino,
            'totalesPaises' => $totalesPaises,
            'totalEnvios' => $enviosTotales,
            'importacion' => $importacionUltima,
        ]);
    }

    /**
     * @Route("/materiales", name="materiales", methods={"GET"})
     */
    public function materialesIndex(TotalesRepository $totalesRepositorio): Response
    {
        $repositorio = $this->getDoctrine()->getRepository(PaisCorrespondencia::class);
        $corresponsalesCubanos = $totalesRepositorio->buscarCorresponsalesCubanos();
        $paisesDestino = $totalesRepositorio->buscarPaises($repositorio);
        $enviosTotales = $totalesRepositorio->enviosTotales();
        $corresponsalesDestino = $totalesRepositorio->buscarCorresponsalesDestino();
        $paisesDestino065 = $totalesRepositorio->paisesDestinoTarifas($paisesDestino, 0.65);
        $paisesDestino075 = $totalesRepositorio->paisesDestinoTarifas($paisesDestino, 0.75);
        $paisesDestino085 = $totalesRepositorio->paisesDestinoTarifas($paisesDestino, 0.85);
        $totalEnvios065 = $totalesRepositorio->totalEnviosCorresponsalesTarifa($paisesDestino065);
        $totalEnvios075 = $totalesRepositorio->totalEnviosCorresponsalesTarifa($paisesDestino075);
        $totalEnvios085 = $totalesRepositorio->totalEnviosCorresponsalesTarifa($paisesDestino085);
        $totales = $this->arregloTotalesCorresponsal($corresponsalesCubanos, $corresponsalesDestino);
        $total065 = $totalesRepositorio->totalArreglo($totalEnvios065);
        $total075 = $totalesRepositorio->totalArreglo($totalEnvios075);
        $total085 = $totalesRepositorio->totalArreglo($totalEnvios085);
        //*******************************Transpondedores
        $totalesPaises = $this->arregloTotalesPaises($corresponsalesCubanos, $paisesDestino);

        $importacionRepositorio = $this->getDoctrine()->getRepository(Importaciones::class);
        $importacionUltima = $importacionRepositorio->findUltimaImportacion();

        return $this->render('totales/materiales.html.twig',[
            'totales' => $totales,
            'totalesPaises' => $totalesPaises,
            'totalEnvios065' => $totalEnvios065,
            'totalEnvios075' => $totalEnvios075,
            'totalEnvios085' => $totalEnvios085,
            'total065' => $total065,
            'total075' => $total075,
            'total085' => $total085,
            'corresponsalesCubanos' => $corresponsalesCubanos,
            'paisesDestino' => $paisesDestino,
            'totalEnvios' => $enviosTotales,
            'importacion' => $importacionUltima,
        ]);
    }
    /**
     * @Route("/pdf/plan/estadisticas", name="plan_estadisticas_pdf", methods={"GET"})
     */
    public function pdf_PlanEstadisticas(PlanDeImposicionRepository $planDeImposicionRepository): Response
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);
        //****************************************Plan de imposicion
        $planRepository = $this->getDoctrine()->getRepository(PlanDeImposicion::class);
        $planDeImposicionRepositorio = $this->getDoctrine()->getRepository(PlanDeImposicion::class);
        $importacionRepositorio = $this->getDoctrine()->getRepository(Importaciones::class);
        $importacionUltima = $importacionRepositorio->findUltimaImportacion();
        $plan_de_imposicions = $planRepository->planesDeImposicionActuales($planDeImposicionRepositorio, $importacionUltima);
        $corresponsalRepository = $this->getDoctrine()->getRepository(Corresponsal::class);
        $corresponsales = $planRepository->corresponsalesdelPlan($corresponsalRepository, $plan_de_imposicions);
        //cojer los plan csv de la bd
        $csvReposirotio = $this->getDoctrine()->getRepository(PlanImposicionCsv::class);
        $planescsv = $csvReposirotio->findAll();
        //****************************Totales
        $totalesRepositorio = $this->getDoctrine()->getRepository(Totales::class);
        $repositorioP = $this->getDoctrine()->getRepository(PaisCorrespondencia::class);
        $corresponsalesCubanos = $totalesRepositorio->buscarCorresponsalesCubanos();
        $corresponsalesDestino = $totalesRepositorio->buscarCorresponsalesDestino();
        $paisesDestino = $totalesRepositorio->buscarPaises($repositorioP);
        $totalesPaises = $totalesRepositorio->totalesPaises($repositorioP);
        $totalesC1 = $totalesRepositorio->tablaTotalesCorresponsal($corresponsalesCubanos[0], $corresponsalesDestino);
        $totalesC2 = $totalesRepositorio->tablaTotalesCorresponsal($corresponsalesCubanos[1], $corresponsalesDestino);
        $totalesC3 = $totalesRepositorio->tablaTotalesCorresponsal($corresponsalesCubanos[2], $corresponsalesDestino);
        $enviosTotales = $totalesRepositorio->enviosTotales();
        //****************************Materiales
        $paisesDestino065 = $totalesRepositorio->paisesDestinoTarifas($paisesDestino, 0.65);
        $paisesDestino075 = $totalesRepositorio->paisesDestinoTarifas($paisesDestino, 0.75);
        $paisesDestino085 = $totalesRepositorio->paisesDestinoTarifas($paisesDestino, 0.85);
        $totalEnvios065 = $totalesRepositorio->totalEnviosCorresponsalesTarifa($paisesDestino065);
        $totalEnvios075 = $totalesRepositorio->totalEnviosCorresponsalesTarifa($paisesDestino075);
        $totalEnvios085 = $totalesRepositorio->totalEnviosCorresponsalesTarifa($paisesDestino085);
        $totalC1 = $totalesRepositorio->totalEnviosCorresponsal($totalesRepositorio->tablaTotalesCorresponsal($corresponsalesCubanos[0], $corresponsalesDestino));
        $totalC2 = $totalesRepositorio->totalEnviosCorresponsal($totalesRepositorio->tablaTotalesCorresponsal($corresponsalesCubanos[1], $corresponsalesDestino));
        $totalC3 = $totalesRepositorio->totalEnviosCorresponsal($totalesRepositorio->tablaTotalesCorresponsal($corresponsalesCubanos[2], $corresponsalesDestino));
        $total065 = $totalesRepositorio->totalArreglo($totalEnvios065);
        $total075 = $totalesRepositorio->totalArreglo($totalEnvios075);
        $total085 = $totalesRepositorio->totalArreglo($totalEnvios085);
        //*******************************Transpondedores
        $totalesPaisesC1 = $totalesRepositorio->arrTotalCorresponsalesPaises($totalesRepositorio, $corresponsalesCubanos[0], $paisesDestino);
        $totalesPaisesC2 = $totalesRepositorio->arrTotalCorresponsalesPaises($totalesRepositorio, $corresponsalesCubanos[1], $paisesDestino);
        $totalesPaisesC3 = $totalesRepositorio->arrTotalCorresponsalesPaises($totalesRepositorio, $corresponsalesCubanos[2], $paisesDestino);

        //****************************************
        $html = $this->renderView('totales/pdf_planEstadisticas.html.twig', [
            'plan_de_imposicion_csvs' => $planescsv,
            'importacion' => $importacionUltima,
            'corresponsales' =>$corresponsales,
            'totalesc1' => $totalesC1,
            'totalesc2' => $totalesC2,
            'totalesc3' => $totalesC3,
            'totalc1' => $totalC1,
            'totalc2' => $totalC2,
            'totalc3' => $totalC3,
            'corresponsalesCubanos' => $corresponsalesCubanos,
            'corresponsalesDestino' => $corresponsalesDestino,
            'paisesDestino' => $paisesDestino,
            'totalesPaises' => $totalesPaises,
            'totalEnvios' => $enviosTotales,
            'totalesPaisesC1' => $totalesPaisesC1,
            'totalesPaisesC2' => $totalesPaisesC2,
            'totalesPaisesC3' => $totalesPaisesC3,
            'totalEnvios065' => $totalEnvios065,
            'totalEnvios075' => $totalEnvios075,
            'totalEnvios085' => $totalEnvios085,
            'total065' => $total065,
            'total075' => $total075,
            'total085' => $total085,
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $fi = $importacionUltima->getFechaInicioPlan();
        $ff = $importacionUltima->getFechaFinPlan();
        $nombre = "PI_estadisticas-".$fi."-".$ff.".pdf";
        $dompdf->stream($nombre, [
            "Attachment" => true
        ]);
    }

    /**
     * @Route("/pdf/view/plan/estadisticas", name="plan_estadisticas_pdf_view", methods={"GET"})
     */
    public function pdf_view_PlanEstadisticas(PlanDeImposicionRepository $planDeImposicionRepository): Response
    {

        //****************************************Plan de imposicion
        $planRepository = $this->getDoctrine()->getRepository(PlanDeImposicion::class);
        $planDeImposicionRepositorio = $this->getDoctrine()->getRepository(PlanDeImposicion::class);
        $importacionRepositorio = $this->getDoctrine()->getRepository(Importaciones::class);
        $importacionUltima = $importacionRepositorio->findUltimaImportacion();
        $plan_de_imposicions = $planRepository->planesDeImposicionActuales($planDeImposicionRepositorio, $importacionUltima);
        $corresponsalRepository = $this->getDoctrine()->getRepository(Corresponsal::class);
        $corresponsales = $planRepository->corresponsalesdelPlan($corresponsalRepository, $plan_de_imposicions);
        //cojer los plan csv de la bd
        $csvReposirotio = $this->getDoctrine()->getRepository(PlanImposicionCsv::class);
        $planescsv = $csvReposirotio->findAll();
        //****************************Totales
        $totalesRepositorio = $this->getDoctrine()->getRepository(Totales::class);
        $repositorioP = $this->getDoctrine()->getRepository(PaisCorrespondencia::class);
        $corresponsalesCubanos = $totalesRepositorio->buscarCorresponsalesCubanos();
        $corresponsalesDestino = $totalesRepositorio->buscarCorresponsalesDestino();
        $paisesDestino = $totalesRepositorio->buscarPaises($repositorioP);
        $totalesPaises = $totalesRepositorio->totalesPaises($repositorioP);
        $totalesC1 = $totalesRepositorio->tablaTotalesCorresponsal($corresponsalesCubanos[0], $corresponsalesDestino);
        $totalesC2 = $totalesRepositorio->tablaTotalesCorresponsal($corresponsalesCubanos[1], $corresponsalesDestino);
        $totalesC3 = $totalesRepositorio->tablaTotalesCorresponsal($corresponsalesCubanos[2], $corresponsalesDestino);
        $enviosTotales = $totalesRepositorio->enviosTotales();
        //****************************Materiales
        $paisesDestino065 = $totalesRepositorio->paisesDestinoTarifas($paisesDestino, 0.65);
        $paisesDestino075 = $totalesRepositorio->paisesDestinoTarifas($paisesDestino, 0.75);
        $paisesDestino085 = $totalesRepositorio->paisesDestinoTarifas($paisesDestino, 0.85);
        $totalEnvios065 = $totalesRepositorio->totalEnviosCorresponsalesTarifa($paisesDestino065);
        $totalEnvios075 = $totalesRepositorio->totalEnviosCorresponsalesTarifa($paisesDestino075);
        $totalEnvios085 = $totalesRepositorio->totalEnviosCorresponsalesTarifa($paisesDestino085);
        $totalC1 = $totalesRepositorio->totalEnviosCorresponsal($totalesRepositorio->tablaTotalesCorresponsal($corresponsalesCubanos[0], $corresponsalesDestino));
        $totalC2 = $totalesRepositorio->totalEnviosCorresponsal($totalesRepositorio->tablaTotalesCorresponsal($corresponsalesCubanos[1], $corresponsalesDestino));
        $totalC3 = $totalesRepositorio->totalEnviosCorresponsal($totalesRepositorio->tablaTotalesCorresponsal($corresponsalesCubanos[2], $corresponsalesDestino));
        $total065 = $totalesRepositorio->totalArreglo($totalEnvios065);
        $total075 = $totalesRepositorio->totalArreglo($totalEnvios075);
        $total085 = $totalesRepositorio->totalArreglo($totalEnvios085);
        //*******************************Transpondedores
        $totalesPaisesC1 = $totalesRepositorio->arrTotalCorresponsalesPaises($totalesRepositorio, $corresponsalesCubanos[0], $paisesDestino);
        $totalesPaisesC2 = $totalesRepositorio->arrTotalCorresponsalesPaises($totalesRepositorio, $corresponsalesCubanos[1], $paisesDestino);
        $totalesPaisesC3 = $totalesRepositorio->arrTotalCorresponsalesPaises($totalesRepositorio, $corresponsalesCubanos[2], $paisesDestino);

        return $this->render('totales/pdf_view_planEstadisticas.html.twig', [
            'plan_de_imposicion_csvs' => $planescsv,
            'importacion' => $importacionUltima,
            'corresponsales' =>$corresponsales,
            'totalesc1' => $totalesC1,
            'totalesc2' => $totalesC2,
            'totalesc3' => $totalesC3,
            'totalc1' => $totalC1,
            'totalc2' => $totalC2,
            'totalc3' => $totalC3,
            'corresponsalesCubanos' => $corresponsalesCubanos,
            'corresponsalesDestino' => $corresponsalesDestino,
            'paisesDestino' => $paisesDestino,
            'totalesPaises' => $totalesPaises,
            'totalEnvios' => $enviosTotales,
            'totalesPaisesC1' => $totalesPaisesC1,
            'totalesPaisesC2' => $totalesPaisesC2,
            'totalesPaisesC3' => $totalesPaisesC3,
            'totalEnvios065' => $totalEnvios065,
            'totalEnvios075' => $totalEnvios075,
            'totalEnvios085' => $totalEnvios085,
            'total065' => $total065,
            'total075' => $total075,
            'total085' => $total085,
        ]);
    }

    /**
     * @Route("/pdf/estadisticas/anuales/view", name="pdf_estadisticas_anuales_view", methods={"GET"})
     */
    public function pdf_estadisticasAnuales_view(): Response
    {
        $anno = "2019";
        $planRepository = $this->getDoctrine()->getRepository(PlanDeImposicion::class);
        $planAnno = $planRepository->findByAnno($anno);
        $corresponsalRepository = $this->getDoctrine()->getRepository(Corresponsal::class);
        $corresponsales = $planRepository->corresponsalesdelPlan($corresponsalRepository, $planAnno);
        $codCorresponsales = $this->codigoCorresponsalesApartirCorresponsales($corresponsales);
        $paises = $planRepository->paisesDelPlan($planAnno);
        $corresponsalesDestino = $planRepository->corresponsalesDestinoDelPlan($planAnno);
        $totalesPaises = $planRepository->totalArrPaisess($planAnno, $paises);
        $totalEnvios = $planRepository->totalEnvios($totalesPaises);
        $matrizEnvios = $this->matrizPlanAnual($planAnno, $codCorresponsales, $corresponsalesDestino);

        $totalesRepositorio = $this->getDoctrine()->getRepository(Totales::class);
        $paisesDestino065 = $totalesRepositorio->paisesDestinoTarifas($paises, 0.65);
        $paisesDestino075 = $totalesRepositorio->paisesDestinoTarifas($paises, 0.75);
        $paisesDestino085 = $totalesRepositorio->paisesDestinoTarifas($paises, 0.85);
        $totalEnvios065 = $planRepository->totalEnviosCorresponsalesTarifa($corresponsales, $paisesDestino065, $planAnno);
        $totalEnvios075 = $planRepository->totalEnviosCorresponsalesTarifa($corresponsales, $paisesDestino075, $planAnno);
        $totalEnvios085 = $planRepository->totalEnviosCorresponsalesTarifa($corresponsales, $paisesDestino085, $planAnno);
        $total065 = $totalesRepositorio->totalArreglo($totalEnvios065);
        $total075 = $totalesRepositorio->totalArreglo($totalEnvios075);
        $total085 = $totalesRepositorio->totalArreglo($totalEnvios085);
        $totales = $this->arregloTotalesCorresponsales($planAnno, $codCorresponsales);
        $matrizPaisesDestinoCorresponsales = $this->matrizTranspondedoresCartasAnual($planAnno, $corresponsales, $paises);


        return $this->render('totales/pdf_estadisticasAnuales_view.html.twig', [
            'matrizE' => $matrizEnvios,
            'corresponsalesCubanos' => $codCorresponsales,
            'corresponsalesDestino' => $corresponsalesDestino,
            'paisesDestino' => $paises,
            'totalesPaises' => $totalesPaises,
            'totalEnvios' => $totalEnvios,
            'anno' => $anno,
            'totalEnvios065' => $totalEnvios065,
            'totalEnvios075' => $totalEnvios075,
            'totalEnvios085' => $totalEnvios085,
            'total065' => $total065,
            'total075' => $total075,
            'total085' => $total085,
            'matrizPC' => $matrizPaisesDestinoCorresponsales,
            'totales' => $totales,


        ]);
    }

    /**
     * @Route("/pdf/estadisticas/ciclo/view", name="pdf_estadisticas_ciclo_view")
     */
    public function pdf_estadisticasUltimoCiclo_view(): Response
    {
        $importacionRepositorio = $this->getDoctrine()->getRepository(Importaciones::class);
        $importacionUltima = $importacionRepositorio->findUltimaImportacion();
        $ciclo = $importacionUltima->getCiclo();
        $planRepository = $this->getDoctrine()->getRepository(PlanDeImposicion::class);
        $planAnno = $planRepository->findByCiclo($ciclo);
        $corresponsalRepository = $this->getDoctrine()->getRepository(Corresponsal::class);
        $corresponsales = $planRepository->corresponsalesdelPlan($corresponsalRepository, $planAnno);
        $codCorresponsales = $this->codigoCorresponsalesApartirCorresponsales($corresponsales);
        $paises = $planRepository->paisesDelPlan($planAnno);
        $corresponsalesDestino = $planRepository->corresponsalesDestinoDelPlan($planAnno);
        $totalesPaises = $planRepository->totalArrPaisess($planAnno, $paises);
        $totalEnvios = $planRepository->totalEnvios($totalesPaises);
        $matrizEnvios = $this->matrizPlanAnual($planAnno, $codCorresponsales, $corresponsalesDestino);

        $totalesRepositorio = $this->getDoctrine()->getRepository(Totales::class);
        $paisesDestino065 = $totalesRepositorio->paisesDestinoTarifas($paises, 0.65);
        $paisesDestino075 = $totalesRepositorio->paisesDestinoTarifas($paises, 0.75);
        $paisesDestino085 = $totalesRepositorio->paisesDestinoTarifas($paises, 0.85);
        $totalEnvios065 = $planRepository->totalEnviosCorresponsalesTarifa($corresponsales, $paisesDestino065, $planAnno);
        $totalEnvios075 = $planRepository->totalEnviosCorresponsalesTarifa($corresponsales, $paisesDestino075, $planAnno);
        $totalEnvios085 = $planRepository->totalEnviosCorresponsalesTarifa($corresponsales, $paisesDestino085, $planAnno);
        $total065 = $totalesRepositorio->totalArreglo($totalEnvios065);
        $total075 = $totalesRepositorio->totalArreglo($totalEnvios075);
        $total085 = $totalesRepositorio->totalArreglo($totalEnvios085);
        $totales = $this->arregloTotalesCorresponsales($planAnno, $codCorresponsales);
        $matrizPaisesDestinoCorresponsales = $this->matrizTranspondedoresCartasAnual($planAnno, $corresponsales, $paises);


        return $this->render('totales/pdf_estadisticasUltimoCiclo_view.html.twig', [
            'matrizE' => $matrizEnvios,
            'corresponsalesCubanos' => $codCorresponsales,
            'corresponsalesDestino' => $corresponsalesDestino,
            'paisesDestino' => $paises,
            'totalesPaises' => $totalesPaises,
            'totalEnvios' => $totalEnvios,
            'ciclo' => $ciclo,
            'totalEnvios065' => $totalEnvios065,
            'totalEnvios075' => $totalEnvios075,
            'totalEnvios085' => $totalEnvios085,
            'total065' => $total065,
            'total075' => $total075,
            'total085' => $total085,
            'matrizPC' => $matrizPaisesDestinoCorresponsales,
            'totales' => $totales,


        ]);
    }

    /**
     * @Route("/pdf/estadisticas/anuales", name="pdf_estadisticas_anuales", methods={"GET"})
     */
    public function pdf_estadisticasAnuales(): Response
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);
        //*****************************************Datos
        $anno = "2019";
        $planRepository = $this->getDoctrine()->getRepository(PlanDeImposicion::class);
        $planAnno = $planRepository->findByAnno($anno);
        $corresponsalRepository = $this->getDoctrine()->getRepository(Corresponsal::class);
        $corresponsales = $planRepository->corresponsalesdelPlan($corresponsalRepository, $planAnno);
        $codCorresponsales = $this->codigoCorresponsalesApartirCorresponsales($corresponsales);
        $paises = $planRepository->paisesDelPlan($planAnno);
        $corresponsalesDestino = $planRepository->corresponsalesDestinoDelPlan($planAnno);
        $totalesPaises = $planRepository->totalArrPaisess($planAnno, $paises);
        $totalEnvios = $planRepository->totalEnvios($totalesPaises);
        $matrizEnvios = $this->matrizPlanAnual($planAnno, $codCorresponsales, $corresponsalesDestino);

        $totalesRepositorio = $this->getDoctrine()->getRepository(Totales::class);
        $paisesDestino065 = $totalesRepositorio->paisesDestinoTarifas($paises, 0.65);
        $paisesDestino075 = $totalesRepositorio->paisesDestinoTarifas($paises, 0.75);
        $paisesDestino085 = $totalesRepositorio->paisesDestinoTarifas($paises, 0.85);
        $totalEnvios065 = $planRepository->totalEnviosCorresponsalesTarifa($corresponsales, $paisesDestino065, $planAnno);
        $totalEnvios075 = $planRepository->totalEnviosCorresponsalesTarifa($corresponsales, $paisesDestino075, $planAnno);
        $totalEnvios085 = $planRepository->totalEnviosCorresponsalesTarifa($corresponsales, $paisesDestino085, $planAnno);
        $total065 = $totalesRepositorio->totalArreglo($totalEnvios065);
        $total075 = $totalesRepositorio->totalArreglo($totalEnvios075);
        $total085 = $totalesRepositorio->totalArreglo($totalEnvios085);
        $totales = $this->arregloTotalesCorresponsales($planAnno, $codCorresponsales);
        $matrizPaisesDestinoCorresponsales = $this->matrizTranspondedoresCartasAnual($planAnno, $corresponsales, $paises);


         $html = $this->renderView('totales/pdf_estadisticasAnuales.html.twig', [
            'matrizE' => $matrizEnvios,
            'corresponsalesCubanos' => $codCorresponsales,
            'corresponsalesDestino' => $corresponsalesDestino,
            'paisesDestino' => $paises,
            'totalesPaises' => $totalesPaises,
            'totalEnvios' => $totalEnvios,
            'anno' => $anno,
            'totalEnvios065' => $totalEnvios065,
            'totalEnvios075' => $totalEnvios075,
            'totalEnvios085' => $totalEnvios085,
            'total065' => $total065,
            'total075' => $total075,
            'total085' => $total085,
            'matrizPC' => $matrizPaisesDestinoCorresponsales,
            'totales' => $totales,
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $nombre = "PI_anual-".$anno.".pdf";
        $dompdf->stream($nombre, [
            "Attachment" => true
        ]);
    }

    /**
     * @Route("/pdf/estadisticas/ciclo", name="pdf_estadisticas_ciclo")
     */
    public function pdf_estadisticasUltimoCiclo(): Response
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);
        //*****************************************Datos
        $importacionRepositorio = $this->getDoctrine()->getRepository(Importaciones::class);
        $importacionUltima = $importacionRepositorio->findUltimaImportacion();
        $ciclo = $importacionUltima->getCiclo();
        $planRepository = $this->getDoctrine()->getRepository(PlanDeImposicion::class);
        $planAnno = $planRepository->findByCiclo($ciclo);
        $corresponsalRepository = $this->getDoctrine()->getRepository(Corresponsal::class);
        $corresponsales = $planRepository->corresponsalesdelPlan($corresponsalRepository, $planAnno);
        $codCorresponsales = $this->codigoCorresponsalesApartirCorresponsales($corresponsales);
        $paises = $planRepository->paisesDelPlan($planAnno);
        $corresponsalesDestino = $planRepository->corresponsalesDestinoDelPlan($planAnno);
        $totalesPaises = $planRepository->totalArrPaisess($planAnno, $paises);
        $totalEnvios = $planRepository->totalEnvios($totalesPaises);
        $matrizEnvios = $this->matrizPlanAnual($planAnno, $codCorresponsales, $corresponsalesDestino);

        $totalesRepositorio = $this->getDoctrine()->getRepository(Totales::class);
        $paisesDestino065 = $totalesRepositorio->paisesDestinoTarifas($paises, 0.65);
        $paisesDestino075 = $totalesRepositorio->paisesDestinoTarifas($paises, 0.75);
        $paisesDestino085 = $totalesRepositorio->paisesDestinoTarifas($paises, 0.85);
        $totalEnvios065 = $planRepository->totalEnviosCorresponsalesTarifa($corresponsales, $paisesDestino065, $planAnno);
        $totalEnvios075 = $planRepository->totalEnviosCorresponsalesTarifa($corresponsales, $paisesDestino075, $planAnno);
        $totalEnvios085 = $planRepository->totalEnviosCorresponsalesTarifa($corresponsales, $paisesDestino085, $planAnno);
        $total065 = $totalesRepositorio->totalArreglo($totalEnvios065);
        $total075 = $totalesRepositorio->totalArreglo($totalEnvios075);
        $total085 = $totalesRepositorio->totalArreglo($totalEnvios085);
        $totales = $this->arregloTotalesCorresponsales($planAnno, $codCorresponsales);
        $matrizPaisesDestinoCorresponsales = $this->matrizTranspondedoresCartasAnual($planAnno, $corresponsales, $paises);


        $html =  $this->renderView('totales/pdf_estadisticasUltimoCiclo.html.twig', [
            'matrizE' => $matrizEnvios,
            'corresponsalesCubanos' => $codCorresponsales,
            'corresponsalesDestino' => $corresponsalesDestino,
            'paisesDestino' => $paises,
            'totalesPaises' => $totalesPaises,
            'totalEnvios' => $totalEnvios,
            'ciclo' => $ciclo,
            'totalEnvios065' => $totalEnvios065,
            'totalEnvios075' => $totalEnvios075,
            'totalEnvios085' => $totalEnvios085,
            'total065' => $total065,
            'total075' => $total075,
            'total085' => $total085,
            'matrizPC' => $matrizPaisesDestinoCorresponsales,
            'totales' => $totales,
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $nombre = "PI_ultimo_ciclo.pdf";
        $dompdf->stream($nombre, [
            "Attachment" => true
        ]);
    }

    /**
     * @Route("/reportes/generales", name="reportes_generales")
     */
    public function reportesGenerales(): Response
    {



        return $this->render('totales/reportesGenerales.html.twig', [

        ]);
    }



    public function matrizPlanAnual($plan, $corresponsalesCubanos, $corresponsalesDestino){
        $matriz[][] = 0;
        for($i=0; $i<sizeof($corresponsalesCubanos);$i++){
            for($j=0; $j<sizeof($corresponsalesDestino);$j++){
                $matriz[$i][$j] = $this->cantEnviosEntre($plan, $corresponsalesCubanos[$i], $corresponsalesDestino[$j]);
            }
        }
        return $matriz;
    }
    public function matrizTranspondedoresCartasAnual($plan, $corresponsalesCubanos, $paisesDestino){
        $matriz[][] = 0;
        for($i=0; $i<sizeof($corresponsalesCubanos); $i++){
            for($j=0; $j<sizeof($paisesDestino); $j++){
                $matriz[$i][$j] = $this->cantEnviosCorresponsalPais($plan, $corresponsalesCubanos[$i], $paisesDestino[$j]);
            }
        }
        return $matriz;
    }
    public function cantEnviosCorresponsalPais($plan, $corrCubano, $paisDestino){
        $cant = 0;
        foreach ($plan as $p){
            if($p->getCodPais() != null){
                if($p->getCodPais() == $paisDestino && $p->getCodCorresponsal() == $corrCubano){
                    $cant++;
                }
            }
        }
        return $cant;
    }

    public function cantEnviosEntre($plan, $corrCubano, $corrDestino){
        $cant = 0;
        foreach ($plan as $p){
            if($p->getCodEnvio() == $corrDestino && $p->getCodCorresponsal()->getCodigo() == $corrCubano){
                $cant++;
            }
        }
        return $cant;
    }

    public function codigoCorresponsalesApartirCorresponsales($corresponsales){
        $codigos = [];
        $size = 0;
        foreach ($corresponsales as $corresponsal){
            $codigos[$size] = $corresponsal->getCodigo();
            $size++;
        }
        return $codigos;
    }

    public function arregloTotalesCorresponsal($corresponsalesCubanos, $corresponsalesDestino){
        $arr[] = 0;
        $size = 0;
        $totalesRepositorio = $this->getDoctrine()->getRepository(Totales::class);
        foreach ($corresponsalesCubanos as $corrCub){
            $arr[$size] = $totalesRepositorio->totalEnviosCorresponsal($totalesRepositorio->tablaTotalesCorresponsal($corrCub, $corresponsalesDestino));
            $size++;
        }
        return $arr;
    }
    public function arregloTotalesCorresponsales($plan, $corresponsalesCubanos){
        $arr[] = 0;
        $size = 0;
        for($i=0; $i<sizeof($corresponsalesCubanos); $i++){
            $arr[$size] = $this->cantEnviosCorresponsal($plan, $corresponsalesCubanos[$i]);
                $size++;
        }
        return $arr;
    }
    public function cantEnviosCorresponsal($plan, $corresponsalC){
        $cant = 0;
        foreach ($plan as $p){
            if($p->getCodPais() != null){
                if($p->getCodCorresponsal()->getCodigo() == $corresponsalC){
                    $cant++;
                }
            }
        }
        return $cant;
    }
    public function arregloTotalesPaises($corresponsalesCubanos, $paisesDestino){
        $arr[] = 0;
        $size = 0;
        $totalesRepositorio = $this->getDoctrine()->getRepository(Totales::class);
        foreach ($corresponsalesCubanos as $corrCub){
            $arr[$size] = $totalesRepositorio->arrTotalCorresponsalesPaises($totalesRepositorio, $corrCub, $paisesDestino);
            $size++;
        }
        return $arr;
    }

}
