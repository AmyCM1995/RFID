<?php

namespace App\Controller;

use App\Entity\Corresponsal;
use App\Entity\Importaciones;
use App\Entity\PaisCorrespondencia;
use App\Entity\PlanDeImposicion;
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
        $importacionRepositorio = $this->getDoctrine()->getRepository(Importaciones::class);
        $importacionUltima = $importacionRepositorio->findOneByImportacion();
        $plan_de_imposicions = $planDeImposicionRepository->findByImposicion($importacionUltima->getId());
        $planDeDistintosCorresonsales = $this->buscarPlanesConDistintosCorresponsales($plan_de_imposicions);
        $corresponsales = $this->buscarCorresponsalesId($planDeDistintosCorresonsales);

        return $this->render('plan_de_imposicion/index.html.twig', [
            'plan_de_imposicions' => $plan_de_imposicions,
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
                'mapped' => false,
            ])
            ->add('save', SubmitType::class)
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
                        if($envio12->length() > 4){
                            $envio1 = $envio12->slice(0, 4);
                            $envio2 = $envio12->slice(4, 4);
                        }else{
                            $envio1 = $envio12;
                        }
                        $c3 = $c2->after(';');
                        $envio34 = $c3->before(';');
                        $envio3 = null;
                        $envio4 = null;
                        if($envio34->length() > 4){
                            $envio3 = $envio34->slice(0, 4);
                            $envio4 = $envio34->slice(4, 4);
                        }else{
                            $envio3 = $envio34;
                        }
                        $envio56 = $c3->after(';');
                        $envio5 = null;
                        $envio6 = null;
                        if($envio56->length() > 4){
                            $envio5 = $envio56->slice(0, 4);
                            $envio6 = $envio56->slice(4, 4);
                        }else{
                            $envio5 = $envio56;
                        }

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

        return $this->render('plan_imposicion_csv/index.html.twig', [
            'form' => $form->createView(),
        ]);
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
}
