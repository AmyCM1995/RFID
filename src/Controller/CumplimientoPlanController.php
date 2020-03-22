<?php

namespace App\Controller;

use App\Entity\CumplimientoPlan;
use App\Entity\FechasNoCorrespondencia;
use App\Entity\PlanDeImposicion;
use App\Entity\PlanImposicionCsv;
use App\Form\CumplimientoPlanType;
use App\Repository\CumplimientoPlanRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\UnicodeString;

/**
 * @Route("/cumplimiento/plan")
 */
class CumplimientoPlanController extends AbstractController
{
    /**
     * @Route("/", name="cumplimiento_plan_index", methods={"GET"})
     */
    public function index(CumplimientoPlanRepository $cumplimientoPlanRepository): Response
    {
        return $this->render('cumplimiento_plan/index.html.twig', [
            'cumplimiento_plans' => $cumplimientoPlanRepository->findAll(),
        ]);
    }

    /**
     * @Route("/importar", name="cumplimiento_plan_importar")
     */
    public function importar(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('file', FileType::class, [
                'mapped' => false, 'label' => ' '
            ])
            ->add('save', SubmitType::class, [ 'label' => 'Guardar'])
            ->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $file = $form['file']->getData();
            $nombre = new UnicodeString($file->getClientOriginalName());
            $extension = $nombre->after('.');
            if($extension->equalsTo('xlsx')){
                $file->move('csv', $file->getClientOriginalName());
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                $spreadsheet = $reader->load("csv/".$file->getClientOriginalName());
                $sheet = $spreadsheet->getActiveSheet();
                foreach ($sheet->getRowIterator() as $row){
                    $this->procesarFila($row);
                }
            }else{
                echo "Solo son permitidos los ficheros de extensión xslx";
            }

            return $this->render('plan_imposicion_csv/importacion_correcta.html.twig', [
                'encabezado' => "Importar cumplimiento del plan de imposición"
            ]);
        }
        return $this->render('cumplimiento_plan/importar.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="cumplimiento_plan_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $cumplimientoPlan = new CumplimientoPlan();
        $form = $this->createForm(CumplimientoPlanType::class, $cumplimientoPlan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cumplimientoPlan);
            $entityManager->flush();

            return $this->redirectToRoute('cumplimiento_plan_index');
        }

        return $this->render('cumplimiento_plan/new.html.twig', [
            'cumplimiento_plan' => $cumplimientoPlan,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="cumplimiento_plan_show", methods={"GET"})
     */
    public function show(CumplimientoPlan $cumplimientoPlan): Response
    {
        return $this->render('cumplimiento_plan/show.html.twig', [
            'cumplimiento_plan' => $cumplimientoPlan,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="cumplimiento_plan_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, CumplimientoPlan $cumplimientoPlan): Response
    {
        $form = $this->createForm(CumplimientoPlanType::class, $cumplimientoPlan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cumplimiento_plan_index');
        }

        return $this->render('cumplimiento_plan/edit.html.twig', [
            'cumplimiento_plan' => $cumplimientoPlan,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="cumplimiento_plan_delete", methods={"DELETE"})
     */
    public function delete(Request $request, CumplimientoPlan $cumplimientoPlan): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cumplimientoPlan->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($cumplimientoPlan);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cumplimiento_plan_index');
    }

    //**********************************************************************************************************
    public function procesarFila($row){
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);
        $columna = 0;
        $sent = true;
        $cod_envio = null;
        $id_envio_fisico = null;
        $fecha_plan = null;
        $corrEnvia = null;
        $corrRecibe = null;
        $fecha_real = null;
        foreach ($cellIterator as $cell){
            if($cell->getCalculatedValue() != null){
                $columna++;
                if ($columna == 1){
                    $cod_envio = $cell->getCalculatedValue();
                }elseif ($columna == 2){
                    $id_envio_fisico = $cell->getCalculatedValue();
                }elseif ($columna == 3){
                    $fecha_plan = new UnicodeString($cell->getCalculatedValue());
                    $d = $fecha_plan->before('.');
                    $ma = $fecha_plan->after('.');
                    $m = $ma->before('.');
                    $a = $ma->after('.');
                    $fecha_plan = $a."-".$m."-".$d;
                }elseif ($columna == 4){
                    $c= new UnicodeString($cell->getCalculatedValue());
                    $corrEnvia = $c->before(" - ");
                }elseif ($columna == 5){
                    $c= new UnicodeString($cell->getCalculatedValue());
                    $corrRecibe = $c->before(" - ");
                }elseif ($columna == 6){
                    $es_sent = new UnicodeString($cell->getCalculatedValue());
                    if ($es_sent->equalsTo("Sent") == false){
                        $sent = false;
                    }
                }elseif ($columna == 7){
                    $fecha_real = new UnicodeString($cell->getCalculatedValue());
                    $dma = $fecha_real->before(" ");
                    $d = $dma->before('.');
                    $ma = $dma->after('.');
                    $m = $ma->before('.');
                    $a = $ma->after('.');
                    $fecha_real = $a."-".$m."-".$d;
                }
            }
            if($columna == 7 && $sent == true){
                //guardar datos
                $planImposicionRepository = $this->getDoctrine()->getRepository(PlanDeImposicion::class);
                $planesFechaPlan = $planImposicionRepository->findByFecha($fecha_plan);
                if($planesFechaPlan != null){
                    $this->verificarGuardarCumplimientoPlan($planesFechaPlan, $cod_envio, $id_envio_fisico, $fecha_plan, $corrEnvia, $corrRecibe, $fecha_real);
                }else{
                    echo "No hay planes para la fecha".$fecha_plan;
                }

            }
        }

    }
    public function verificarGuardarCumplimientoPlan($planesFechaPlan, $cod_envio, $id_envio_fisico, $fecha_plan, $corrEnvia, $corrRecibe, $fecha_real){
        $ocurrencia = false;
        $en_tiempo = false;
        $id_planImposicion = null;
        foreach ($planesFechaPlan as $planFechaPlan){
            if($planFechaPlan->getCodCorresponsal()->getCodigo() == $corrEnvia){
                if($planFechaPlan->getCodEnvio() == $corrRecibe){
                    $ocurrencia = true;
                    //Comprobar cumplimiento
                    if($fecha_plan == $fecha_real){
                        $en_tiempo = true;
                        $id_planImposicion = $planFechaPlan;
                    }
                }
            }
        }
        if($ocurrencia == false){
            echo "No hay plan para la fecha, el corresponsal de envio y el corresponsal que recibe dados";
        }else{
            $cumplimiento = new CumplimientoPlan();
            $cumplimiento->setCodigoEnvio($cod_envio);
            $cumplimiento->setIdTranspondedor($id_envio_fisico);
            $cumplimiento->setFechaReal(new \DateTime($fecha_real));
            $cumplimiento->setIdPlanImposicion($id_planImposicion);
            $cumplimiento->setEsEnTiempo($en_tiempo);
            if($en_tiempo == false){
                //buscar Fecha No Correspondencia
                $cumplimiento->setFechaNoCorrespondencia($this->buscarFechaNoCorrespondencia($fecha_plan));
            }
            //persistir
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cumplimiento);
            $entityManager->flush();
            //llenar PlanImposicionCsv
            $this->actualizarPlanImposicionCsv($cumplimiento);
        }
    }
    public function actualizarPlanImposicionCsv($cumplimiento){
        $planesCsvRepository = $this->getDoctrine()->getRepository(PlanImposicionCsv::class);
        $planCsvFecha = $planesCsvRepository->findOneByFecha($cumplimiento->getIdPlanImposicion()->getFecha());
        $corrRec = $cumplimiento->getIdPlanImposicion()->getCodEnvio();
        if($planCsvFecha != null){
            if($planCsvFecha->getEnvio11() != null){
                if($planCsvFecha->getEnvio11() == $corrRec){
                    if($cumplimiento->getEsEnTiempo() == true){
                        $planCsvFecha->setCump11(true);
                    }else{
                        $planCsvFecha->setCump11(false);
                    }
                }
            }
            if($planCsvFecha->getEnvio12() != null){
                if($planCsvFecha->getEnvio12() == $corrRec){
                    if($cumplimiento->getEsEnTiempo() == true){
                        $planCsvFecha->setCump12(true);
                    }else{
                        $planCsvFecha->setCump12(false);
                    }
                }
            }
            if($planCsvFecha->getEnvio21() != null){
                if($planCsvFecha->getEnvio21() == $corrRec){
                    if($cumplimiento->getEsEnTiempo() == true){
                        $planCsvFecha->setCump21(true);
                    }else{
                        $planCsvFecha->setCump21(false);
                    }
                }
            }
            if($planCsvFecha->getEnvio22() != null){
                if($planCsvFecha->getEnvio22() == $corrRec){
                    if($cumplimiento->getEsEnTiempo() == true){
                        $planCsvFecha->setCump22(true);
                    }else{
                        $planCsvFecha->setCump22(false);
                    }
                }
            }
            if($planCsvFecha->getEnvio31() != null){
                if($planCsvFecha->getEnvio31() == $corrRec){
                    if($cumplimiento->getEsEnTiempo() == true){
                        $planCsvFecha->setCump31(true);
                    }else{
                        $planCsvFecha->setCump31(false);
                    }
                }
            }
            if($planCsvFecha->getEnvio32() != null){
                if($planCsvFecha->getEnvio32() == $corrRec){
                    if($cumplimiento->getEsEnTiempo() == true){
                        $planCsvFecha->setCump32(true);
                    }else{
                        $planCsvFecha->setCump32(false);
                    }
                }
            }
            $this->getDoctrine()->getManager()->flush();
        }
    }

    public function buscarFechaNoCorrespondencia ($fecha){
        $resultado = null;
        $fechasNoCorrespondenciaRepository = $this->getDoctrine()->getRepository(FechasNoCorrespondencia::class);
        $resultado = $fechasNoCorrespondenciaRepository->findOneByFecha($fecha);
        return $resultado;
    }

}
