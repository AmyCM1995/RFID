<?php

namespace App\Controller;

use App\Entity\Corresponsal;
use App\Entity\Importaciones;
use App\Entity\PaisCorrespondencia;
use App\Entity\PlanDeImposicion;
use App\Entity\PlanImposicionCsv;
use App\Entity\ProvinciaCuba;
use App\Entity\Totales;
use App\Repository\PlanDeImposicionRepository;
use App\Repository\TotalesRepository;
use Doctrine\ORM\EntityRepository;
use DoctrineExtensions\Query\Mysql\Year;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\String\UnicodeString;

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
        $corresponsalesDestinoConPaises = $totalesRepositorio->agregarPaisesCorresponsalesDestino($corresponsalesDestino, $paisesDestino, sizeof($corresponsalesCubanos));
        $matriz = $totalesRepositorio->matrizTotales($corresponsalesCubanos, $corresponsalesDestinoConPaises);
        $enviosTotales = $totalesRepositorio->enviosTotales();
        $piRepository = $this->getDoctrine()->getRepository(PlanDeImposicion::class);
        $importacionRepositorio = $this->getDoctrine()->getRepository(Importaciones::class);
        $importacionUltima = $importacionRepositorio->findUltimaImportacionConPI($piRepository);
        $cicloEspanol = $importacionRepositorio->traducirCicloEspañol($importacionUltima);

        return $this->render('totales/index.html.twig', [
            'matriz' => $matriz,
            'corresponsalesCubanos' => $corresponsalesCubanos,
            'corresponsalesDestino' => $corresponsalesDestinoConPaises,
            'paisesDestino' => $paisesDestino,
            'totalesPaises' => $totalesPaises,
            'totalEnvios' => $enviosTotales,
            'importacion' => $importacionUltima,
            'cicloEspanol' => $cicloEspanol,
        ]);
    }

    /**
     * @Route("/pdf/materiales", name="pdf_materiales", methods={"GET"})
     */
    public function pdf_materialesIndex(TotalesRepository $totalesRepositorio): Response
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
        $cicloEspanol = $importacionRepositorio->traducirCicloEspañol($importacionUltima);

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
            'cicloEspanol' => $cicloEspanol,
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
        $piRepositorio = $this->getDoctrine()->getRepository(PlanDeImposicion::class);
        $importacionUltimaconPi = $importacionRepositorio->findUltimaImportacionConPI($piRepositorio);
        $importacionRepository = $this->getDoctrine()->getRepository(Importaciones::class);
        $cicloEspanol = $importacionRepository->traducirCicloEspañol($importacionUltimaconPi);
        $plan_de_imposicions = $planRepository->planesDeImposicionActuales($planDeImposicionRepositorio, $importacionUltimaconPi);
        $corresponsalRepository = $this->getDoctrine()->getRepository(Corresponsal::class);
        $corresponsales = $planRepository->corresponsalesdelPlan($corresponsalRepository, $plan_de_imposicions);
        //cojer los plan csv de la bd
        $csvReposirotio = $this->getDoctrine()->getRepository(PlanImposicionCsv::class);
        $planescsv = $csvReposirotio->findAll();
        //****************************Totales
        $repositorioPais = $this->getDoctrine()->getRepository(PaisCorrespondencia::class);
        $totalesRepositorio = $this->getDoctrine()->getRepository(Totales::class);
        $corresponsalesCubanos = $totalesRepositorio->buscarCorresponsalesCubanos();
        $corresponsalesDestino = $totalesRepositorio->buscarCorresponsalesDestino();
        $paisesDestino = $totalesRepositorio->buscarPaises($repositorioPais);
        $corresponsalesDestinoConPaises = $totalesRepositorio->agregarPaisesCorresponsalesDestino($corresponsalesDestino, $paisesDestino, sizeof($corresponsalesCubanos));
        $totalesPaises1 = $totalesRepositorio->totalesPaises($repositorioPais);
        $matriz = $totalesRepositorio->matrizTotales($corresponsalesCubanos, $corresponsalesDestinoConPaises);
        $enviosTotales = $totalesRepositorio->enviosTotales();
        $totalesRepositorio = $this->getDoctrine()->getRepository(Totales::class);
        $totalesPorCorresponsales = $totalesRepositorio->totalesPorCorresponsales($corresponsalesCubanos, $totalesRepositorio, $paisesDestino);
        //****************************Materiales

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

        //****************************************
        $html = $this->renderView('totales/pdf_planEstadisticas.html.twig', [

            'plan_de_imposicion_csvs' => $planescsv,
            'importacion' => $importacionUltimaconPi,
            'cicloEspanol' => $cicloEspanol,
            'corresponsales' =>$corresponsales,
            'matriz' => $matriz,
            'totales' => $totales,
            'totalesPorCorresponsales' => $totalesPorCorresponsales,
            'corresponsalesCubanos' => $corresponsalesCubanos,
            'corresponsalesDestino' => $corresponsalesDestino,
            'corresponsalesDestinoConPaises' => $corresponsalesDestinoConPaises,
            'paisesDestino' => $paisesDestino,
            'totalesPaises1' => $totalesPaises1,
            'totalesPaises' => $totalesPaises,
            'totalEnvios' => $enviosTotales,
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
        $fi = $importacionUltimaconPi->getFechaInicioPlan();
        $ff = $importacionUltimaconPi->getFechaFinPlan();
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
        $cicloEspanol = $importacionRepositorio->traducirCicloEspañol($importacionUltima);
        $plan_de_imposicions = $planRepository->planesDeImposicionActuales($planDeImposicionRepositorio, $importacionUltima);
        $corresponsalRepository = $this->getDoctrine()->getRepository(Corresponsal::class);
        $corresponsales = $planRepository->corresponsalesdelPlan($corresponsalRepository, $plan_de_imposicions);
        //cojer los plan csv de la bd
        $csvReposirotio = $this->getDoctrine()->getRepository(PlanImposicionCsv::class);
        $planescsv = $csvReposirotio->findAll();
        //****************************Totales
        $repositorioPais = $this->getDoctrine()->getRepository(PaisCorrespondencia::class);
        $totalesRepositorio = $this->getDoctrine()->getRepository(Totales::class);
        $corresponsalesCubanos = $totalesRepositorio->buscarCorresponsalesCubanos();
        $corresponsalesDestino = $totalesRepositorio->buscarCorresponsalesDestino();
        $paisesDestino = $totalesRepositorio->buscarPaises($repositorioPais);
        $corresponsalesDestinoConPaises = $totalesRepositorio->agregarPaisesCorresponsalesDestino($corresponsalesDestino, $paisesDestino, sizeof($corresponsalesCubanos));
        $totalesPaises1 = $totalesRepositorio->totalesPaises($repositorioPais);
        $matriz = $totalesRepositorio->matrizTotales($corresponsalesCubanos, $corresponsalesDestinoConPaises);
        $enviosTotales = $totalesRepositorio->enviosTotales();
        $totalesRepositorio = $this->getDoctrine()->getRepository(Totales::class);
        $totalesPorCorresponsales = $totalesRepositorio->totalesPorCorresponsales($corresponsalesCubanos, $totalesRepositorio, $paisesDestino);
        //****************************Materiales

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

        return $this->render('totales/pdf_planEstadisticas.html.twig', [
            'plan_de_imposicion_csvs' => $planescsv,
            'importacion' => $importacionUltima,
            'cicloEspanol' => $cicloEspanol,
            'corresponsales' =>$corresponsales,
            'matriz' => $matriz,
            'totales' => $totales,
            'totalesPorCorresponsales' => $totalesPorCorresponsales,
            'corresponsalesCubanos' => $corresponsalesCubanos,
            'corresponsalesDestino' => $corresponsalesDestino,
            'corresponsalesDestinoConPaises' => $corresponsalesDestinoConPaises,
            'paisesDestino' => $paisesDestino,
            'totalesPaises1' => $totalesPaises1,
            'totalesPaises' => $totalesPaises,
            'totalEnvios' => $enviosTotales,
            'totalEnvios065' => $totalEnvios065,
            'totalEnvios075' => $totalEnvios075,
            'totalEnvios085' => $totalEnvios085,
            'total065' => $total065,
            'total075' => $total075,
            'total085' => $total085,
        ]);
    }

    /**
     * @Route("/pdf/estadisticas/anuales/view", name="pdf_estadisticas_anuales_view")
     */
    public function pdf_estadisticasAnuales_view(Request $request): Response
    {
        $planRepository = $this->getDoctrine()->getRepository(PlanDeImposicion::class);
        /*$annos = $planRepository->diferentesAnnos();
        $ejemploDQL = $planRepository->findByEjemplo();
        foreach ($ejemploDQL as $ejemploDQ){
            echo $ejemploDQ->getId()."/";
        }

        /*$form = $this->createFormBuilder()
            ->add('anno', ChoiceType::class, [
                'label' => 'Año', 'attr' => array('required' => true),
                'choices' => $annos,
                //'choice_value' => $annos,
                'multiple' => false,
                'required' => true,
            ])
            ->getForm();*/
        $form = $this->createFormBuilder()
            ->add('anno', EntityType::class, [
                'attr' => array('class' => 'form-control', 'style' => 'margin:5px 0;'),
                'class'=> PlanDeImposicion::class,
                'query_builder' => function (EntityRepository $er){
                    return $er->createQueryBuilder('p')
                    ->where(distinct('YEAR(p.fecha)'));
                },
                'choice_label' => 'id',
                'multiple' => false,
                'required' => true,
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $anno = $form['anno']->getData();
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
        return $this->render('totales/reportesGenerales.html.twig', [
            'form' => $form->createView(),
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
        $cicloEspanol = $importacionRepositorio->traducirCicloEspañol($importacionUltima);
        $planRepository = $this->getDoctrine()->getRepository(PlanDeImposicion::class);
        $planAnno = $planRepository->findByCiclo($ciclo);
        $corresponsalRepository = $this->getDoctrine()->getRepository(Corresponsal::class);
        $corresponsales = $planRepository->corresponsalesdelPlan($corresponsalRepository, $planAnno);
        $codCorresponsales = $this->codigoCorresponsalesApartirCorresponsales($corresponsales);
        $paises = $planRepository->paisesDelPlan($planAnno);
        $corresponsalesDestino = $planRepository->corresponsalesDestinoDelPlan($planAnno);
        $totalesRepositorio = $this->getDoctrine()->getRepository(Totales::class);
        $corresponsalesDestinoConPaises = $totalesRepositorio->agregarPaisesCorresponsalesDestino($corresponsalesDestino, $paises, sizeof($codCorresponsales));
        $totalesPaises = $planRepository->totalArrPaisess($planAnno, $paises);
        $totalEnvios = $planRepository->totalEnvios($totalesPaises);
        $matrizEnvios = $this->matrizPlanAnual($planAnno, $codCorresponsales, $corresponsalesDestinoConPaises);
        $totalesPorCorresponsales = $totalesRepositorio->totalesPorCorresponsales($codCorresponsales, $totalesRepositorio, $paises);

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
            'corresponsalesDestino' => $corresponsalesDestinoConPaises,
            'paisesDestino' => $paises,
            'totalesPaises' => $totalesPaises,
            'totalEnvios' => $totalEnvios,
            'totalesPorCorresponsales' => $totalesPorCorresponsales,
            'cicloEspanol' => $cicloEspanol,
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
     * @Route("/pdf/estadisticas/anuales", name="pdf_estadisticas_anuales")
     */
    public function pdf_estadisticasAnuales($anno): Response
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);
        //*****************************************Datos
        //$anno = "2019";
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
        $cicloEspanol = $importacionRepositorio->traducirCicloEspañol($importacionUltima);
        $planRepository = $this->getDoctrine()->getRepository(PlanDeImposicion::class);
        $planAnno = $planRepository->findByCiclo($ciclo);
        $corresponsalRepository = $this->getDoctrine()->getRepository(Corresponsal::class);
        $corresponsales = $planRepository->corresponsalesdelPlan($corresponsalRepository, $planAnno);
        $codCorresponsales = $this->codigoCorresponsalesApartirCorresponsales($corresponsales);
        $paises = $planRepository->paisesDelPlan($planAnno);
        $corresponsalesDestino = $planRepository->corresponsalesDestinoDelPlan($planAnno);
        $totalesRepositorio = $this->getDoctrine()->getRepository(Totales::class);
        $corresponsalesDestinoConPaises = $totalesRepositorio->agregarPaisesCorresponsalesDestino($corresponsalesDestino, $paises, sizeof($codCorresponsales));
        $totalesPaises = $planRepository->totalArrPaisess($planAnno, $paises);
        $totalEnvios = $planRepository->totalEnvios($totalesPaises);
        $matrizEnvios = $this->matrizPlanAnual($planAnno, $codCorresponsales, $corresponsalesDestinoConPaises);
        $totalesPorCorresponsales = $totalesRepositorio->totalesPorCorresponsales($codCorresponsales, $totalesRepositorio, $paises);

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
            'corresponsalesDestino' => $corresponsalesDestinoConPaises,
            'paisesDestino' => $paises,
            'totalesPaises' => $totalesPaises,
            'totalEnvios' => $totalEnvios,
            'cicloEspanol' => $cicloEspanol,
            'totalesPorCorresponsales' => $totalesPorCorresponsales,
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
     * @Route("/pdf/materiales/corresponsal/incompleto", name="pdf_materiales_corresponsal_incompleto")
     */
    public function pdf_materiales_corresponsal_incompleto(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('corresponsal', EntityType::class, [
                'attr' => array('class' => 'form-control', 'style' => 'margin:5px 0;'),
                'label' => "Escoja el corresponsal al que se le va a realizar la entrega",
                'class'=> Corresponsal::class,
                'query_builder' => function (EntityRepository $er){
                    return $er->createQueryBuilder('c')->andWhere('c.es_activo = :val')
                        ->setParameter('val', true)->orderBy('c.codigo', 'ASC');
                },
                'choice_label' => 'nombre',
                'multiple' => false,
                'required' => true,
            ])
            ->getForm();
        $form->handleRequest($request);
        $array = $form->getData();
        if($form->isSubmitted() && $form->isValid()){
            $idCorresponsal = -1;
            $corresponsal = null;
            foreach ($array as $arra){
                if($arra != null){
                    $idCorresponsal = $arra->getId();
                }
            }
            if($idCorresponsal != -1){
                //buscar corresponsal
                $corresponsalRepository = $this->getDoctrine()->getRepository(Corresponsal::class);
                $corresponsal = $corresponsalRepository->findOneById($idCorresponsal);
            }
            $piRepository = $this->getDoctrine()->getRepository(PlanDeImposicion::class);
            $importacionRepositorio = $this->getDoctrine()->getRepository(Importaciones::class);
            $importacionUltima = $importacionRepositorio->findUltimaImportacionConPI($piRepository);
            $plan = $piRepository->findByImposicion($importacionUltima);
            $sobres = $this->cantEnviosCorresponsal($plan, $corresponsal->getCodigo());
            $totalesRepositorio = $this->getDoctrine()->getRepository(Totales::class);
            $repositorio = $this->getDoctrine()->getRepository(PaisCorrespondencia::class);
            $paisesDestino = $totalesRepositorio->buscarPaises($repositorio);
            $paisesDestino065 = $totalesRepositorio->paisesDestinoTarifas($paisesDestino, 0.65);
            $paisesDestino075 = $totalesRepositorio->paisesDestinoTarifas($paisesDestino, 0.75);
            $total065 = $totalesRepositorio->totalEnviosCorresponsalTarifa($corresponsal->getCodigo(), $paisesDestino065);
            $total075 = $totalesRepositorio->totalEnviosCorresponsalTarifa($corresponsal->getCodigo(), $paisesDestino075);
            $arrTotalesPaises = $totalesRepositorio->arrTotalCorresponsalesPaises($totalesRepositorio, $corresponsal->getCodigo(), $paisesDestino);
            $corresponsalesPaisesDestino = $totalesRepositorio->buscarCorresponsalesDestino();
            $codPaises65 = $this->arregloCodigosPaises($paisesDestino065);
            $codPaises75 = $this->arregloCodigosPaises($paisesDestino075);


            return $this->render('materiales/pdf_materialesCorreaponsalIncompleto.html.twig', [
                'form' => $form->createView(),
                'corresponsal' => $corresponsal,
                'sobres' => $sobres,
                'sellos65' => $total065,
                'sellos75' => $total075,
                'paises65' => $paisesDestino065,
                'paises75' => $paisesDestino075,
                'codPaises65' => $codPaises65,
                'codPaises75' => $codPaises75,
                'paisesDestino' => $paisesDestino,
                'corresponsalesPaisesDestino' => $corresponsalesPaisesDestino,
                'arrTotalesPaises' => $arrTotalesPaises,
                'importacion' => $importacionUltima,

            ]);
        }

        return $this->render('materiales/introducirCorresponsal.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/{id}/pdf/materiales/corresponsal", name="pdf_materiales_corresponsal", methods={"GET"})
     */
    public function pdf_materiales_corresponsal(Corresponsal $corresponsal): Response
    {
        $piRepository = $this->getDoctrine()->getRepository(PlanDeImposicion::class);
        $importacionRepositorio = $this->getDoctrine()->getRepository(Importaciones::class);
        $importacionUltima = $importacionRepositorio->findUltimaImportacionConPI($piRepository);
        $plan = $piRepository->findByImposicion($importacionUltima);
        $sobres = $this->cantEnviosCorresponsal($plan, $corresponsal->getCodigo());
        $totalesRepositorio = $this->getDoctrine()->getRepository(Totales::class);
        $repositorio = $this->getDoctrine()->getRepository(PaisCorrespondencia::class);
        $paisesDestino = $totalesRepositorio->buscarPaises($repositorio);
        $paisesDestino065 = $totalesRepositorio->paisesDestinoTarifas($paisesDestino, 0.65);
        $paisesDestino075 = $totalesRepositorio->paisesDestinoTarifas($paisesDestino, 0.75);
        $total065 = $totalesRepositorio->totalEnviosCorresponsalTarifa($corresponsal->getCodigo(), $paisesDestino065);
        $total075 = $totalesRepositorio->totalEnviosCorresponsalTarifa($corresponsal->getCodigo(), $paisesDestino075);
        $arrTotalesPaises = $totalesRepositorio->arrTotalCorresponsalesPaises($totalesRepositorio, $corresponsal->getCodigo(), $paisesDestino);
        $corresponsalesPaisesDestino = $totalesRepositorio->buscarCorresponsalesDestino();
        $codPaises65 = $this->arregloCodigosPaises($paisesDestino065);
        $codPaises75 = $this->arregloCodigosPaises($paisesDestino075);

        return $this->render('materiales/pdf_materialesCorresponsal.html.twig', [
            'corresponsal' => $corresponsal,
            'sobres' => $sobres,
            'sellos65' => $total065,
            'sellos75' => $total075,
            'paises65' => $paisesDestino065,
            'paises75' => $paisesDestino075,
            'codPaises65' => $codPaises65,
            'codPaises75' => $codPaises75,
            'paisesDestino' => $paisesDestino,
            'corresponsalesPaisesDestino' => $corresponsalesPaisesDestino,
            'arrTotalesPaises' => $arrTotalesPaises,
            'importacion' => $importacionUltima,

        ]);
    }
    public function matrizPlanAnual($plan, $corresponsalesCubanos, $corresponsalesDestino){
        $matriz[][] = 0;
        for($i=0; $i<sizeof($corresponsalesCubanos);$i++){
            for($j=0; $j<sizeof($corresponsalesDestino);$j++){
                $cod = new UnicodeString($corresponsalesDestino[$j]);
                if($cod->length() != 2){
                    $matriz[$i][$j] = $this->cantEnviosEntre($plan, $corresponsalesCubanos[$i], $corresponsalesDestino[$j]);
                }else{
                    //es un país
                    $total= $this->cantEnviosCorresponsalCodPais($plan, $corresponsalesCubanos[$i], $corresponsalesDestino[$j]);
                    $matriz[$i][$j] = $total;
                }
            }
        }
        return $matriz;
    }
    public function arregloCodigosPaises($paises){
        $codPaises[] = null;
        $size = 0;
        foreach ($paises as $pais){
            $codPaises[$size] = $pais->getCodigo();
            $size++;
        }
        return $codPaises;
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
    public function cantEnviosCorresponsalCodPais($plan, $corrCubano, $paisDestino){
        $cant = 0;
        foreach ($plan as $p){
            if($p->getCodPais() != null){
                if($p->getCodPais()->getCodigo() == $paisDestino && $p->getCodCorresponsal()->getCodigo() == $corrCubano){
                    $cant++;
                }
            }
        }
        return $cant;
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
