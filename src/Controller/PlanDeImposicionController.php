<?php

namespace App\Controller;

use App\Entity\Corresponsal;
use App\Entity\Importaciones;
use App\Entity\PaisCorrespondencia;
use App\Entity\PlanDeImposicion;
use App\Entity\PlanImposicionCsv;
use App\Entity\Totales;
use App\Form\PlanDeImposicionType;
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
        $importacionUltima = $this->utimaImportacion();
        $plan_de_imposicions = $this->planesDeImposicionActuales($importacionUltima);
        $corresponsales = $this->corresponsalesdelPlan($plan_de_imposicions);
        //cojer los plan csv de la bd
        $csvReposirotio = $this->getDoctrine()->getRepository(PlanImposicionCsv::class);
        $planescsv = $csvReposirotio->findAll();
        return $this->render('plan_de_imposicion/index.html.twig', [
            'plan_de_imposicion_csvs' => $planescsv,
            'importacion' => $importacionUltima,
            'corresponsales' =>$corresponsales,
        ]);
    }

    /**
     * @Route("/new", name="plan_de_imposicion_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $planDeImposicion = new PlanDeImposicion();
        $form = $this->createForm(PlanDeImposicionType::class, $planDeImposicion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($planDeImposicion);
            $entityManager->flush();

            return $this->redirectToRoute('plan_de_imposicion_index');
        }

        return $this->render('plan_de_imposicion/new.html.twig', [
            'plan_de_imposicion' => $planDeImposicion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="plan_de_imposicion_show", methods={"GET"})
     */
    public function show(PlanDeImposicion $planDeImposicion): Response
    {
        return $this->render('plan_de_imposicion/show.html.twig', [
            'plan_de_imposicion' => $planDeImposicion,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="plan_de_imposicion_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, PlanDeImposicion $planDeImposicion): Response
    {
        $form = $this->createForm(PlanDeImposicionType::class, $planDeImposicion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('plan_de_imposicion_index');
        }

        return $this->render('plan_de_imposicion/edit.html.twig', [
            'plan_de_imposicion' => $planDeImposicion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="plan_de_imposicion_delete", methods={"DELETE"})
     */
    public function delete(Request $request, PlanDeImposicion $planDeImposicion): Response
    {
        if ($this->isCsrfTokenValid('delete'.$planDeImposicion->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($planDeImposicion);
            $entityManager->flush();
        }

        return $this->redirectToRoute('plan_de_imposicion_index');
    }
    /**
     * @Route("/plan/imposicion/persistir", name="plan_imposicion_persistir")
     */
    public function persistir(Request $request){
        $form = $this->createFormBuilder()
            ->add('file', FileType::class, [
                'mapped' => false, 'label' => ' '
            ])
            ->add('save', SubmitType::class, [ 'label' => 'Guardar'])
            ->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $file = $form['file']->getData();
            $file->move('csv', $file->getClientOriginalName());
            $csv = Reader::createFromPath('csv/'.$file->getClientOriginalName());
            $records = $csv->getRecords();

            $importacion = new Importaciones();
            $importacion->setFechaImportado(new \DateTime('now'));

            $entityManager = $this->getDoctrine()->getManager();
            $corresponsales = null;

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

                        $entityManager->persist($importacion);
                        $entityManager->flush();

                    }elseif ($offset == 3){
                        //corresponsales
                        $rec = new UnicodeString($record[0]);
                        $codCorr1 = $rec->after(';;');
                        $rec = $codCorr1;
                        $codCorr1 = $rec->before(';');
                        $codCorr2 = $rec->after(';');
                        $rec = $codCorr2;
                        $codCorr2 = $rec->before(';');
                        $codCorr3 = $rec->after(';');

                        $corresponsales = $this->buscarCorrespnsales($codCorr1, $codCorr2, $codCorr3);

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
            //*****************************************Plan de imposicion CSV
            $importacionUltima = $this->utimaImportacion();
            $plan_de_imposicions = $this->planesDeImposicionActuales($importacionUltima);
            $corresponsales = $this->corresponsalesdelPlan($plan_de_imposicions);
            $planesCorr1 = $this->buscarPlanesPorCorresponsales($plan_de_imposicions, $corresponsales[0]);
            $planesCorr2 = $this->buscarPlanesPorCorresponsales($plan_de_imposicions, $corresponsales[1]);
            $planesCorr3 = $this->buscarPlanesPorCorresponsales($plan_de_imposicions, $corresponsales[2]);
            $planescsv = [null];
            $pos = 0;
            while($this->existePlan($planesCorr1)){
                $csv = $this->generarPlanDeImposicionCSV($plan_de_imposicions, $planesCorr1, $planesCorr2, $planesCorr3);
                $planescsv[$pos] = $csv;
                $pos++;
            }
            $this->borrarCSVAnteriores();
            $this->persistirCSV($planescsv);
            //********************************************Estadísticas
            $paises = $this->paisesDelPlan($plan_de_imposicions);
            $envios = $this->enviosCorresponsalesEnviosDelPlan($plan_de_imposicions);
            $totales=$this->generarTotales($corresponsales, $envios, $paises, $plan_de_imposicions);
            $this->borrarTotalesAnteriores();
            $this->persistirTotales($totales);

            return $this->render('plan_imposicion_csv/importacion_correcta.html.twig');
        }

        return $this->render('plan_imposicion_csv/index.html.twig', [
            'form' => $form->createView(),
        ]);
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

    public function buscarPlanesConDistintosCorresponsales($plan){
        $esta = false;
        $size = 1;
        $planesDistintosCorresponsales = [$plan[0]];
        for($i=0; $i<sizeof($plan); $i++){
            for($j=0; $j<$size; $j++){
                if($plan[$i]->getCodCorresponsal()->getId() == $planesDistintosCorresponsales[$j]->getCodCorresponsal()->getId()){
                    $esta = true;
                    break;
                }else{
                    $esta = false;
                }
            }
            if($esta == false){
                $planesDistintosCorresponsales[$size] = $plan[$i];
                $size++;
            }
        }
        return$planesDistintosCorresponsales;
    }

    public function buscarCorresponsalesId($planDeDistintosCorresonsales){
        $em = $this->getDoctrine()->getRepository(Corresponsal::class);
        $corresponsales = null;
        for($i=0; $i<sizeof($planDeDistintosCorresonsales); $i++){
            $corresponsales[$i] = $em->findOneById($planDeDistintosCorresonsales[$i]->getCodCorresponsal()->getId());
        }
        return $corresponsales;
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
    public function planesDeImposicionActuales($importacionUltima){
        $planDeImposicionRepositorio = $this->getDoctrine()->getRepository(PlanDeImposicion::class);
        $plan_de_imposicions = $planDeImposicionRepositorio->findByImposicion($importacionUltima->getId());
        return $plan_de_imposicions;
    }
    public function corresponsalesdelPlan($plan_de_imposicions){
        $planDeDistintosCorresonsales = $this->buscarPlanesConDistintosCorresponsales($plan_de_imposicions);
        $corresponsales = $this->buscarCorresponsalesId($planDeDistintosCorresonsales);
        return $corresponsales;
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
     * @Route("/", name="plan_de_imposicion_pdf", methods={"GET"})
     */
    public function pdf_PlanDeImposicion(PlanDeImposicionRepository $planDeImposicionRepository): Response
    {
        require 'vendor/autoload.php';

        /*$importacionUltima = $this->utimaImportacion();
        $plan_de_imposicions = $this->planesDeImposicionActuales($importacionUltima);
        $corresponsales = $this->corresponsalesdelPlan($plan_de_imposicions);
        //coge los planes csv de la bd
        $csvReposirotio = $this->getDoctrine()->getRepository(PlanImposicionCsv::class);
        $planescsv = $csvReposirotio->findAll();

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($pdfOptions);

        $html = $this->render('plan_de_imposicion/index.html.twig', [
            'plan_de_imposicion_csvs' => $planescsv,
            'importacion' => $importacionUltima,
            'corresponsales' =>$corresponsales,
        ]);

        $dompdf->setPaper('A4' , 'portrait');
        $dompdf->render();
        $output = $dompdf->output();
        $publicDirectory = $this->get('kernel')->getProjectDir().'/public';
        $pdfFilepath = $publicDirectory.'/planDeImposicion.pdf';
        file_put_contents($pdfFilepath, $output);*/






        return new Response("El pdf se generó correctamente!");
    }

}
