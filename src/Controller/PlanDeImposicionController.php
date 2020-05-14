<?php

namespace App\Controller;

use App\Entity\Corresponsal;
use App\Entity\Importaciones;
use App\Entity\PaisCorrespondencia;
use App\Entity\PlanDeImposicion;
use App\Entity\PlanImposicionCsv;
use App\Entity\Totales;
use App\Repository\PlanDeImposicionRepository;
use League\Csv\Reader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\UnicodeString;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * @Route("/plan/de/imposicion")
 */
class PlanDeImposicionController extends AbstractController
{
    /**
     * @Route("/", name="plan_de_imposicion_index", methods={"GET"})
     */
    public function index(PlanDeImposicionRepository $planDeImposicionRepository): Response
    {
        $planRepository = $this->getDoctrine()->getRepository(PlanDeImposicion::class);
        $importacionUltimaconPi = $this->utimaImportacionConPI();
        $planDeImposicionRepositorio = $this->getDoctrine()->getRepository(PlanDeImposicion::class);
        $plan_de_imposicions = $planRepository->planesDeImposicionActuales($planDeImposicionRepositorio, $importacionUltimaconPi);
        $corresponsalRepository = $this->getDoctrine()->getRepository(Corresponsal::class);
        $corresponsales = $planRepository->corresponsalesdelPlan($corresponsalRepository, $plan_de_imposicions);
        //cojer los plan csv de la bd
        $csvReposirotio = $this->getDoctrine()->getRepository(PlanImposicionCsv::class);
        $planescsv = $csvReposirotio->findAll();
        return $this->render('plan_de_imposicion/index.html.twig', [
            'plan_de_imposicion_csvs' => $planescsv,
            'importacion' => $importacionUltimaconPi,
            'corresponsales' =>$corresponsales,
        ]);
    }

    //*****************************************************************************************************************
    /**
     * @Route("/plan/imposicion/persistir", name="plan_imposicion_persistir")
     */
    public function persistir(Request $request){

        $this->denyAccessUnlessGranted('ROLE_ESPECIALISTA_DC');
        $form = $this->createFormBuilder()
            ->add('file', FileType::class, [
                'mapped' => false, 'label' => ' '
            ])
            ->add('save', SubmitType::class, [ 'label' => 'Guardar',
                'attr' => array('class' => 'btn btn-primary')
                ])
            ->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $file = $form['file']->getData();
            $nombre = new UnicodeString($file->getClientOriginalName());
            $extension = $nombre->after('.');
            $notificar = null;
            if($extension->equalsTo('csv')){
                $file->move('csv', $file->getClientOriginalName());
                $csv = Reader::createFromPath('csv/'.$file->getClientOriginalName());
                $records = $csv->getRecords();
                $notificar = $this->principalPersistirCSV($records);
            }else{
                if($extension->equalsTo('xlsx')){
                    $file->move('csv', $file->getClientOriginalName());
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                    $spreadsheet = $reader->load("csv/".$file->getClientOriginalName());
                    $sheet = $spreadsheet->getActiveSheet();
                    $notificar = $this->principalPersistirXls($sheet);
                }elseif ($extension->equalsTo('xls')){
                    echo "no se aceptan ficheros xls";
                }
            }
            //si $notificar == null -> es un nuevo PI
            //si $notificar == false -> hubo cambios en el PI
            //si $notificar == true -> no hubo cambios en el PI
            return $this->render('plan_imposicion_csv/importacion_correcta.html.twig', [
                'encabezado' => "Importar plan de imposición",
                'alertas' => $notificar
            ]);
        }

        return $this->render('plan_imposicion_csv/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/plan/imposicion/visualizar/cumplimiento", name="plan_imposicion_visualizar_cumplimiento")
     */
    public function visualizarCumplimiento()
    {
        $planRepository = $this->getDoctrine()->getRepository(PlanDeImposicion::class);
        $importacionUltimaconPi = $this->utimaImportacionConPI();
        $planDeImposicionRepositorio = $this->getDoctrine()->getRepository(PlanDeImposicion::class);
        $plan_de_imposicions = $planRepository->planesDeImposicionActuales($planDeImposicionRepositorio, $importacionUltimaconPi);
        $corresponsalRepository = $this->getDoctrine()->getRepository(Corresponsal::class);
        $corresponsales = $planRepository->corresponsalesdelPlan($corresponsalRepository, $plan_de_imposicions);
        //cojer los plan csv de la bd
        $csvReposirotio = $this->getDoctrine()->getRepository(PlanImposicionCsv::class);
        $planescsv = $csvReposirotio->findAll();
        $success = $this->buscarSuccessPlanes($planescsv);
        $danger = $this->buscarDangerPlanes($planescsv);
        return $this->render('plan_de_imposicion/visualizarCumplimiento.html.twig', [
            'plan_de_imposicion_csvs' => $planescsv,
            'importacion' => $importacionUltimaconPi,
            'corresponsales' => $corresponsales,
            'success' => $success,
            'danger' => $danger,
        ]);
    }

    public function buscarSuccessPlanes($planescsv){
        $success[] = null;
        $size = 0;
        foreach ($planescsv as $plancsv){
            if($plancsv->getCump11() != null){
                if($plancsv->getCump11() == true){
                    $success[$size] = $plancsv->getEnvio11();
                    $size++;
                }
            }
            if($plancsv->getCump12() != null){
                if($plancsv->getCump12() == true){
                    $success[$size] = $plancsv->getEnvio12();
                    $size++;
                }
            }
            if($plancsv->getCump21() != null){
                if($plancsv->getCump21() == true){
                    $success[$size] = $plancsv->getEnvio21();
                    $size++;
                }
            }
            if($plancsv->getCump22() != null){
                if($plancsv->getCump22() == true){
                    $success[$size] = $plancsv->getEnvio22();
                    $size++;
                }
            }
            if($plancsv->getCump31() != null){
                if($plancsv->getCump31() == true){
                    $success[$size] = $plancsv->getEnvio31();
                    $size++;
                }
            }
            if($plancsv->getCump32() != null){
                if($plancsv->getCump32() == true){
                    $success[$size] = $plancsv->getEnvio32();
                    $size++;
                }
            }
        }
        return $success;
    }
    public function buscarDangerPlanes($planescsv){
        $danger[] = null;
        $size = 0;
        foreach ($planescsv as $plancsv){
            if($plancsv->getCump11() != null){
                if($plancsv->getCump11() == false){
                    $danger[$size] = $plancsv->getEnvio11();
                    $size++;
                }
            }
            if($plancsv->getCump12() != null){
                if($plancsv->getCump12() == false){
                    $danger[$size] = $plancsv->getEnvio12();
                    $size++;
                }
            }
            if($plancsv->getCump21() != null){
                if($plancsv->getCump21() == false){
                    $danger[$size] = $plancsv->getEnvio21();
                    $size++;
                }
            }
            if($plancsv->getCump22() != null){
                if($plancsv->getCump22() == false){
                    $danger[$size] = $plancsv->getEnvio22();
                    $size++;
                }
            }
            if($plancsv->getCump31() != null){
                if($plancsv->getCump31() == false){
                    $danger[$size] = $plancsv->getEnvio31();
                    $size++;
                }
            }
            if($plancsv->getCump32() != null){
                if($plancsv->getCump32() == false){
                    $danger[$size] = $plancsv->getEnvio32();
                    $size++;
                }
            }
        }
        return $danger;
    }
    public function borrarCSVAnteriores(){
        $repositorio = $this->getDoctrine()->getRepository(PlanImposicionCsv::class);
        $entityManager = $this->getDoctrine()->getManager();
        $anterioresCSV = $repositorio->findAll();
        for($i=0; $i<sizeof($anterioresCSV); $i++){
            $entityManager->remove($anterioresCSV[$i]);
            $entityManager->flush();

        }
    }
    public function persistirCSV($planescsv){
        $entityManager = $this->getDoctrine()->getManager();
        for($i=0; $i<sizeof($planescsv);$i++){
            $entityManager->persist($planescsv[$i]);
            $entityManager->flush();
        }
    }
    public function principalPersistirXls($sheet){
        $result = null;
        $existe = null;
        $importacion = new Importaciones();
        $importacion->setFechaImportado(new \DateTime('now'));
        $columnas = 0;
        $filaActual = -1;
        $entityManager = $this->getDoctrine()->getManager();
        $corresponsales[] = null;
        $corres[] = null;
        $sizeCorr = 0;
        foreach ($sheet->getRowIterator() as $row){
            $filaActual++;
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            if($filaActual <= 4){
                if($filaActual == 0){
                    foreach ($cellIterator as $cell){
                        if($cell->getCalculatedValue() != null){
                            $dimensionCiclo = new UnicodeString($cell->getCalculatedValue());
                            $c = $dimensionCiclo->after(',');
                            $d = $dimensionCiclo->before(',');
                            $dimension = $d->after(':');
                            $ciclo = $c->after(':');
                            $importacion->setCiclo($ciclo);
                            $importacion->setDimension($dimension);
                        }
                    }
                }elseif($filaActual == 1){
                    foreach ($cellIterator as $cell){
                        if($cell->getCalculatedValue() != null){
                            $fecha = new UnicodeString($cell->getCalculatedValue());
                            $fechaI = $fecha->after('from');
                            $fechaF = $fechaI->after('to');
                            $importacion->setFechaInicioPlan($fechaI->before('to'));
                            $importacion->setFechaFinPlan($fechaF);
                            //verificar si existen planes con esos datos
                            $importacionRepositorio = $this->getDoctrine()->getRepository(Importaciones::class);
                            $existe = $importacionRepositorio->importacionesMismoRangoFechas($fechaI->before('to'), $fechaF->before(';'));
                            $entityManager->persist($importacion);
                            $entityManager->flush();
                        }
                    }
                }elseif ($filaActual == 3){
                    foreach ($cellIterator as $cell){
                        if($cell->getCalculatedValue() != null){
                            $corres[$sizeCorr] = $cell->getCalculatedValue();
                            $sizeCorr++;
                        }
                    }
                }elseif($filaActual == 4){
                    $corresponsales = $this->buscarCorresponsalesAPartirDeCodigos($corres);
                    $columnas = sizeof($corresponsales)+2;
                }
            }else{
                //envios
                $fecha = null;
                $envios = null;

                $size = 0;
                foreach ($cellIterator as $cell){
                    if($cell->getCalculatedValue() != null){
                        if($size == 0){
                            $size++;
                        }elseif ($size == 1){
                            $fecha = $cell->getCalculatedValue();

                            $size++;
                        }else{
                            $envios = new UnicodeString($cell->getCalculatedValue());
                            if($envios->length() > 1){  //tiene envios
                                if($envios->length() == 4){  //tiene un solo envio
                                    $plan = new PlanDeImposicion();
                                    $plan->setImportacion($importacion);
                                    $plan->setFecha(new \DateTime($fecha));
                                    $this->persistirPlanDeImposicion($plan, $corresponsales[$size-2], $envios);
                                }else{  //tiene dos envios
                                    $e1 = $envios->after(',');
                                    $e2 = $envios->before(',');
                                    $plan = new PlanDeImposicion();
                                    $plan->setImportacion($importacion);
                                    $plan->setFecha(new \DateTime($fecha));
                                    $this->persistirPlanDeImposicion($plan, $corresponsales[$size-2], $e1);
                                    $plan = new PlanDeImposicion();
                                    $plan->setImportacion($importacion);
                                    $plan->setFecha(new \DateTime($fecha));
                                    $this->persistirPlanDeImposicion($plan, $corresponsales[$size-2], $e2);
                                }
                            }
                            $size++;
                        }
                    }
                }
            }
        }
        $iguales = false;
        if(sizeof($existe) != 0){//existe un PI con las mismas fechas de inicio y fin
            $importacionUltima = $this->utimaImportacion();
            $piRepositorio = $this->getDoctrine()->getRepository(PlanDeImposicion::class);
            for($i=sizeof($existe)-1; $i>=0; $i--){ //recorro el array de fin a inicio
                //verificar si la importación tiene PI
                $pis = $piRepositorio->findByImposicion($existe[$i]->getId());
                if(sizeof($pis) != 0){
                    //verificar si hay cambios en los 2 PI
                    $iguales = $piRepositorio->compararPIdeImportacionesDiferentes($importacionUltima->getId(), $existe[$i]->getId());
                    break;
                }
            }
            //notificar que existieron cambios o no en el PI en dependencia del resultado de $iguales
            $result = $iguales;
        }
        if($iguales == true){
            //borrar los PI nuevos y dejar la importación
            $piBorrar = $piRepositorio->findByImposicion($importacionUltima->getId());
            foreach ($piBorrar as $pi){
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($pi);
                $entityManager->flush();
            }
        }
        if(sizeof($existe) == 0 || $iguales == false) { //si es un PI nuevo o existen cambios con el anterior
            //*****************************************Plan de imposicion CSV
            $plan_de_imposicions = $this->llenarPlanDeImposicionCSV();
            //********************************************Estadísticas
            $paises = $this->paisesDelPlan($plan_de_imposicions);
            $envios = $this->enviosCorresponsalesEnviosDelPlan($plan_de_imposicions);
            $totales=$this->generarTotales($corresponsales, $envios, $paises, $plan_de_imposicions);
            $this->borrarTotalesAnteriores();
            $this->persistirTotales($totales);
            //*********************************************Limpiar Plan de Imposicion
            $importacionUltima = $this->utimaImportacion();
            $this->limpiarPlanImposicion($importacionUltima);
        }
        return $result;
    }
    public function principalPersistirCSV($records){
        $result = null;
        $importacion = new Importaciones();
        $importacion->setFechaImportado(new \DateTime('now'));
        $entityManager = $this->getDoctrine()->getManager();
        $corresponsales = null;
        $corres = null;
        $existe = null;
        foreach ($records as $offset=>$record){
            if($offset <= 4){
                if ($offset == 0){
                    $dimension = new UnicodeString($record[0]);
                    $ciclo = new UnicodeString($record[1]);
                    $ciclo1 = $ciclo->after(':');
                    $importacion->setDimension($dimension->after(':'));
                    $importacion->setCiclo($ciclo1->before(';'));
                }elseif ($offset == 1){
                    $fecha = new UnicodeString($record[0]);
                    $fechaI = $fecha->after('from');
                    $fechaF = $fechaI->after('to');
                    $importacion->setFechaInicioPlan($fechaI->before('to'));
                    $importacion->setFechaFinPlan($fechaF->before(';'));
                    //verificar si existen planes con esos datos
                    $importacionRepositorio = $this->getDoctrine()->getRepository(Importaciones::class);
                    $existe = $importacionRepositorio->importacionesMismoRangoFechas($fechaI->before('to'), $fechaF->before(';'));
                    $entityManager->persist($importacion);
                    $entityManager->flush();
                }elseif ($offset == 3){
                    //corresponsales
                    $rec = new UnicodeString($record[0]);
                    $size = 0;
                    $rec = $rec->after(';;');
                    while($rec->length() >4 ){
                        $corres[$size] = $rec->before(';');
                        $rec = $rec->after(';');
                        $size++;
                    }
                    if($rec->length() == 4){
                        $corres[$size] = $rec;
                    }
                    $corresponsales = $this->buscarCorresponsalesAPartirDeCodigos($corres);
                }
            }else{
                $linea = "";
                for($i=0; $i<sizeof($record); $i++){
                    $linea = $linea.$record[$i];
                }
                $l = new UnicodeString($linea);
                if(!$l->equalsTo(';;;;')){
                    $c1 = $l->after(';');
                    $f = $c1->before(';');
                    $c2 = $c1->after(';');
                    $envio12 = $c2->before(';');
                    $envio1= null;
                    $envio2 = null;
                    if($envio12->length() > 1){
                        if($envio12->length() > 4){
                            $envio1 = $envio12->slice(0, 4);
                            $envio2 = $envio12->slice(4, 4);
                        }else{
                            $envio1 = $envio12;
                        }
                    }
                    $c3 = $c2->after(';');
                    $envio34 = $c3->before(';');
                    $envio3 = null;
                    $envio4 = null;
                    if($envio34->length() > 1){
                        if($envio34->length() > 4){
                            $envio3 = $envio34->slice(0, 4);
                            $envio4 = $envio34->slice(4, 4);
                        }else{
                            $envio3 = $envio34;
                        }
                    }
                    $envio56 = $c3->after(';');
                    $envio5 = null;
                    $envio6 = null;
                    if($envio56->length() > 1){
                        if($envio56->length() > 4){
                            $envio5 = $envio56->slice(0, 4);
                            $envio6 = $envio56->slice(4, 4);
                        }else{
                            $envio5 = $envio56;
                        }
                    }
                    if($envio1 != null){
                        $plan = new PlanDeImposicion();
                        $plan->setImportacion($importacion);
                        $plan->setFecha(new \DateTime($f));
                        $this->persistirPlanDeImposicion($plan, $corresponsales[0], $envio1);
                        if($envio2 != ""){
                            $plan1 = new PlanDeImposicion();
                            $plan1->setImportacion($importacion);
                            $plan1->setFecha(new \DateTime($f));
                            $this->persistirPlanDeImposicion($plan1, $corresponsales[0], $envio2);
                        }
                    }
                    if($envio3 != null){
                        $plan2 = new PlanDeImposicion();
                        $plan2->setImportacion($importacion);
                        $plan2->setFecha(new \DateTime($f));
                        $this->persistirPlanDeImposicion($plan2, $corresponsales[1], $envio3);
                        if($envio4 != ""){
                            $plan3 = new PlanDeImposicion();
                            $plan3->setImportacion($importacion);
                            $plan3->setFecha(new \DateTime($f));
                            $this->persistirPlanDeImposicion($plan3, $corresponsales[1], $envio4);
                        }
                    }
                    if($envio5 != null){
                        $plan4 = new PlanDeImposicion();
                        $plan4->setImportacion($importacion);
                        $plan4->setFecha(new \DateTime($f));
                        $this->persistirPlanDeImposicion($plan4, $corresponsales[2], $envio5);
                        if($envio6 != ""){
                            $plan5 = new PlanDeImposicion();
                            $plan5->setImportacion($importacion);
                            $plan5->setFecha(new \DateTime($f));
                            $this->persistirPlanDeImposicion($plan5, $corresponsales[2], $envio6);
                        }
                    }
                }
            }
        }
        $iguales = false;
        if(sizeof($existe) != 0){//existe un PI con las mismas fechas de inicio y fin
            $importacionUltima = $this->utimaImportacion();
            $piRepositorio = $this->getDoctrine()->getRepository(PlanDeImposicion::class);
            for($i=sizeof($existe)-1; $i>=0; $i--){ //recorro el array de fin a inicio
                //verificar si la importación tiene PI
                $pis = $piRepositorio->findByImposicion($existe[$i]->getId());
                if(sizeof($pis) != 0){
                    //verificar si hay cambios en los 2 PI
                    $iguales = $piRepositorio->compararPIdeImportacionesDiferentes($importacionUltima->getId(), $existe[$i]->getId());
                    break;
                }
            }
            //notificar que existieron cambios o no en el PI en dependencia del resultado de $iguales
            $result = $iguales;
        }
        if($iguales == true){
            //borrar los PI nuevos y dejar la importación
            $piBorrar = $piRepositorio->findByImposicion($importacionUltima->getId());
            foreach ($piBorrar as $pi){
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($pi);
                $entityManager->flush();
            }
        }
        if(sizeof($existe) == 0 || $iguales == false){ //si es un PI nuevo o existen cambios con el anterior
            //*****************************************Plan de imposicion CSV
            $plan_de_imposicions = $this->llenarPlanDeImposicionCSV();
            //********************************************Estadísticas
            $paises = $this->paisesDelPlan($plan_de_imposicions);
            $envios = $this->enviosCorresponsalesEnviosDelPlan($plan_de_imposicions);
            $totales=$this->generarTotales($corresponsales, $envios, $paises, $plan_de_imposicions);
            $this->borrarTotalesAnteriores();
            $this->persistirTotales($totales);
            //*********************************************Limpiar Plan de Imposicion
            $importacionUltima = $this->utimaImportacion();
            $this->limpiarPlanImposicion($importacionUltima);
        }
        return $result;
    }
    public function llenarPlanDeImposicionCSV(){
        $importacionUltima = $this->utimaImportacion();
        $planImposicionRepository = $this->getDoctrine()->getRepository(PlanDeImposicion::class);
        $corresponsalRepository = $this->getDoctrine()->getRepository(Corresponsal::class);
        $plan_de_imposicions = $planImposicionRepository->planesDeImposicionActuales($planImposicionRepository, $importacionUltima);
        $corresponsales = $planImposicionRepository->corresponsalesdelPlan($corresponsalRepository, $plan_de_imposicions);
        $planesCorresponsales = null;
        $size = 0;
        foreach ($corresponsales as $corr){
            $planesCorresponsales[$size] = $this->buscarPlanesPorCorresponsales($plan_de_imposicions, $corr);
            $size++;
        }
        $planescsv = [null];
        $pos = 0;
        while($this->existePlan($planesCorresponsales[0])){
            $csv = $this->generarPlanDeImposicionCSV($plan_de_imposicions, $planesCorresponsales[0], $planesCorresponsales[1], $planesCorresponsales[2]);
            $planescsv[$pos] = $csv;
            $pos++;
        }
        $this->borrarCSVAnteriores();
        $this->persistirCSV($planescsv);
        return $plan_de_imposicions;
    }
    //quita los PI de anteriores importaciones
    public function limpiarPlanImposicion($importacionUltima){
        $planImposicionRepository = $this->getDoctrine()->getRepository(PlanDeImposicion::class);
        $planImposicionsNuevos = $planImposicionRepository->findByImposicion($importacionUltima);
        foreach ($planImposicionsNuevos as $planImposicionNueva){
            $planImposicionsMismaFecha = $planImposicionRepository->findByFecha($planImposicionNueva->getFecha());
            foreach ($planImposicionsMismaFecha as $planImposicionFecha){
                if($planImposicionFecha->getImportacion()->getId() != $importacionUltima->getId()){
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->remove($planImposicionFecha);
                    $entityManager->flush();
                }
            }
        }

    }
    public function persistirPlanDeImposicion(PlanDeImposicion $plan, Corresponsal $corresponsal, $envio){
        $entityManager = $this->getDoctrine()->getManager();
        $codpais = $envio->slice(0,2);
        //buscar y asignar país al plan de imposición
        $repositorioPais = $this->getDoctrine()->getRepository(PaisCorrespondencia::class);
        $pais = $repositorioPais->findOneByCodigo($codpais);
        //persistir
        $plan->setCodCorresponsal($corresponsal);
        $plan->setCodEnvio($envio);
        $plan->setCodPais($pais);
        $entityManager->persist($plan);
        $entityManager->flush();
    }
    public function buscarCorrespnsales($c1, $c2, $c3){
        $repositorioCorresponsal = $this->getDoctrine()->getRepository(Corresponsal::class);
        $corr1 = $repositorioCorresponsal->findOneByCodigo($c1);
        $corr2 = $repositorioCorresponsal->findOneByCodigo($c2);
        $corr3 = $repositorioCorresponsal->findOneByCodigo($c3);
        $resultado = [$corr1, $corr2, $corr3];
        return $resultado;
    }
    public function buscarCorresponsalesAPartirDeCodigos ($corresponsales){
        $repositorioCorresponsal = $this->getDoctrine()->getRepository(Corresponsal::class);
        $resultado[] = null;
        $size = 0;
        foreach ($corresponsales as $c){
            $resultado[$size] = $repositorioCorresponsal->findOneByCodigo($c);
            $size++;
        }
        return $resultado;
    }
    public function buscarPlanesPorCorresponsales($plan, $corresponsal){
        $Plancorr = null;
        $size = 0;
        for($i=0; $i<sizeof($plan); $i++){
            if($plan[$i]->getCodCorresponsal()->getCodigo() == $corresponsal->getCodigo()){
                $Plancorr[$size] = $plan[$i];
                $size++;
            }
        }
        return $Plancorr;
    }
    public function generarPlanDeImposicionCSV($plan, &$planesCorr1, &$planesCorr2, &$planesCorr3){
        $planCSV = new PlanImposicionCsv();
        $i = 0;
        while($planesCorr1[$i] == null){
            $i++;
        }
        $planCSV->setFecha($planesCorr1[$i]->getFecha());
        $planCSV->setEnvio11($planesCorr1[$i]->getCodEnvio());
        $planesCorr1[$i] = null;
        for($i=0; $i<sizeof($planesCorr1); $i++){
            if($planesCorr1[$i] != null){
                if($planesCorr1[$i]->getFecha() == $planCSV->getFecha()){
                    $planCSV->setEnvio12($planesCorr1[$i]->getCodEnvio());
                    $planesCorr1[$i] = null;
                }
            }
        }
        for ($i=0; $i<sizeof($planesCorr2); $i++){
            if($planesCorr2[$i] != null){
                if($planesCorr2[$i]->getFecha() == $planCSV->getFecha()){
                    if($planCSV->getEnvio21() == null){
                        $planCSV->setEnvio21($planesCorr2[$i]->getCodEnvio());
                        $planesCorr2[$i] = null;
                    }else{
                        $planCSV->setEnvio22($planesCorr2[$i]->getCodEnvio());
                        $planesCorr2[$i] = null;
                        break;
                    }
                }
            }
        }
        for ($i=0; $i<sizeof($planesCorr3); $i++){
            if($planesCorr3[$i] != null){
                if($planesCorr3[$i]->getFecha() == $planCSV->getFecha()){
                    if($planCSV->getEnvio31() == null){
                        $planCSV->setEnvio31($planesCorr3[$i]->getCodEnvio());
                        $planesCorr3[$i] = null;
                    }else{
                        $planCSV->setEnvio32($planesCorr3[$i]->getCodEnvio());
                        $planesCorr3[$i] = null;
                        break;
                    }
                }
            }
        }
        return $planCSV;
    }
    public function existePlan($planesCorr1){
        $existe = false;
        for($i=0; $i<sizeof($planesCorr1); $i++){
            if($planesCorr1[$i] != null){
                $existe = true;
            }
        }
        return $existe;
    }
    public function utimaImportacion(){
        $importacionRepositorio = $this->getDoctrine()->getRepository(Importaciones::class);
        $importacionUltima = $importacionRepositorio->findUltimaImportacion();
        return $importacionUltima;
    }
    public function utimaImportacionConPI(){
        $importacionRepositorio = $this->getDoctrine()->getRepository(Importaciones::class);
        $piRepositorio = $this->getDoctrine()->getRepository(PlanDeImposicion::class);
        $importacionUltima = $importacionRepositorio->findUltimaImportacionConPI($piRepositorio);
        return $importacionUltima;
    }
    public function paisesDelPlan($plan_de_imposicions){
        $paises = [$plan_de_imposicions[0]->getCodPais()];
        $size = 1;
        for($i=1; $i<sizeof($plan_de_imposicions); $i++){
            if($plan_de_imposicions[$i]->getCodPais()!= null){
                if($this->existePais($paises, $plan_de_imposicions[$i]->getCodPais()) == false){
                    $paises[$size] = $plan_de_imposicions[$i]->getCodPais();
                    $size++;
                }
            }
        }
        return $paises;
    }
    public function existePais($paises, $pais){
        $existe = false;
        for($i=0; $i<sizeof($paises); $i++){
                if($paises[$i]->getId() == $pais->getId()){
                    $existe = true;
                    break;
                }
        }
        return $existe;
    }
    public function enviosCorresponsalesEnviosDelPlan($plan_de_imposicions){
        $envios = [$plan_de_imposicions[0]->getCodEnvio()];
        $size = 1;
        for($i=1; $i<sizeof($plan_de_imposicions); $i++){
            if($this->existeEnvio($envios, $plan_de_imposicions[$i]->getCodEnvio()) == false){
                $envios[$size] = $plan_de_imposicions[$i]->getCodEnvio();
                $size++;
            }
        }
        return $envios;
    }
    public function existeEnvio($envios, $envio){
        $existe = false;
        for($i=0; $i<sizeof($envios); $i++){
            if($envios[$i] == $envio){
                $existe = true;
                break;
            }
        }
        return $existe;
    }
    public function CantidadEnviosEntreCorresponsales(Corresponsal $cCuba, $cDest, $planesActuales){
        $total = 0;
        for($i=0; $i<sizeof($planesActuales); $i++){
            if($planesActuales[$i]->getCodCorresponsal()->getId() == $cCuba->getId()){
                if($planesActuales[$i]->getCodEnvio() == $cDest){
                    $total++;
                }
            }
        }
        return $total;
    }
    public function CantidadEnviosCorresponsalPais($corr, $pais, $planesActuales){
        $total = 0;
        for($i=0; $i<sizeof($planesActuales); $i++){
            if($planesActuales[$i]->getCodCorresponsal()->getId() == $corr->getId()){
                if($planesActuales[$i]->getCodPais()->getCodigo() == $pais->getCodigo()){
                    $total++;
                }
            }
        }
        return $total;
    }
    public  function CantidadEnviosPorCorresponsal (Corresponsal $c, $planesActuales){
        $total = 0;
        for($i=0; $i<sizeof($planesActuales); $i++){
            if($planesActuales[$i]->getCodCorresponsal()->getId() == $c->getId()){
                $total++;
            }
        }
        return $total;
    }
    public function CantidadEnviosPorPais ($pais, $planesActuales){
        $total = 0;
        for($i=0; $i<sizeof($planesActuales); $i++){
            if($planesActuales[$i]->getCodPais()->getCodigo() == $pais->getCodigo()){
                $total++;
            }
        }
        return $total;
    }
    public function generarTotales($corresponsales, $envios, $paises, $plan){
        $totales=[new Totales()];
        $size = 0;
        for($i=0; $i<sizeof($corresponsales); $i++){
            for($j=0; $j<sizeof($envios); $j++){
                $t = new Totales();
                $t->setCorresponsalCuba($corresponsales[$i]->getCodigo());
                $t->setCorresponsalDestino($envios[$j]);
                $t->setTotalEnvios($this->CantidadEnviosEntreCorresponsales($corresponsales[$i], $envios[$j], $plan));
                $totales[$size]=$t;
                $size++;
            }
            for($k=0; $k<sizeof($paises); $k++){
                $t = new Totales();
                $t->setCorresponsalCuba($corresponsales[$i]->getCodigo());
                $t->setCorresponsalDestino($paises[$k]->getCodigo());
                $t->setTotalEnvios($this->CantidadEnviosCorresponsalPais($corresponsales[$i], $paises[$k], $plan));
                $totales[$size]=$t;
                $size++;
            }
            $t = new Totales();
            $t->setCorresponsalCuba($corresponsales[$i]->getCodigo());
            $t->setCorresponsalDestino(null);
            $t->setTotalEnvios($this->CantidadEnviosPorCorresponsal($corresponsales[$i], $plan));
            $totales[$size]=$t;
            $size++;
        }
        for($l=0; $l<sizeof($paises); $l++){
            $t = new Totales();
            $t->setCorresponsalDestino($paises[$l]->getCodigo());
            $t->setTotalEnvios($this->CantidadEnviosPorPais($paises[$l], $plan));
            $totales[$size]=$t;
            $size++;
        }
        return $totales;
    }
    public function persistirTotales($totales){
        $entityManager = $this->getDoctrine()->getManager();
        for($i=0; $i<sizeof($totales);$i++){
            $entityManager->persist($totales[$i]);
            $entityManager->flush();
        }

    }
    public function borrarTotalesAnteriores(){
        $repositorio = $this->getDoctrine()->getRepository(Totales::class);
        $entityManager = $this->getDoctrine()->getManager();
        $anterioresTotales = $repositorio->findAll();
        for($i=0; $i<sizeof($anterioresTotales); $i++){
            $entityManager->remove($anterioresTotales[$i]);
            $entityManager->flush();

        }
    }

    /**
     * @Route("/pdf/plan/imposicion", name="plan_de_imposicion_pdf", methods={"GET"})
     */
    public function pdf_PlanDeImposicion(PlanDeImposicionRepository $planDeImposicionRepository): Response
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);
        //****************************************
        $planRepository = $this->getDoctrine()->getRepository(PlanDeImposicion::class);
        $planDeImposicionRepositorio = $this->getDoctrine()->getRepository(PlanDeImposicion::class);
        $corresponsalRepository = $this->getDoctrine()->getRepository(Corresponsal::class);
        $importacionUltimaconPi = $this->utimaImportacionConPI();
        $plan_de_imposicions = $planRepository->planesDeImposicionActuales($planDeImposicionRepositorio, $importacionUltimaconPi);
        $corresponsales = $planRepository->corresponsalesdelPlan($corresponsalRepository, $plan_de_imposicions);
        //cojer los plan csv de la bd
        $csvReposirotio = $this->getDoctrine()->getRepository(PlanImposicionCsv::class);
        $planescsv = $csvReposirotio->findAll();
        //****************************************
        //***********************PDF
        $html = $this->renderView('plan_de_imposicion/pdf_planImposicion.html.twig', [
            'plan_de_imposicion_csvs' => $planescsv,
            'importacion' => $importacionUltimaconPi,
            'corresponsales' =>$corresponsales,
            ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $fi = $importacionUltimaconPi->getFechaInicioPlan();
        $ff = $importacionUltimaconPi->getFechaFinPlan();
        $nombre = "PI-".$fi."-".$ff.".pdf";
        $dompdf->stream($nombre, [
            "Attachment" => true
        ]);
       //*************************Vista Previa
       /* $fi = $importacionUltima->getFechaInicioPlan();
        $ff = $importacionUltima->getFechaFinPlan();
        $nombre = "PI-".$fi."-".$ff.".pdf";
        echo $nombre;
       return $this->render('plan_de_imposicion/pdf_planImposicion.html.twig', [
           'plan_de_imposicion_csvs' => $planescsv,
           'importacion' => $importacionUltima,
           'corresponsales' =>$corresponsales,
       ]);*/
    }

}
