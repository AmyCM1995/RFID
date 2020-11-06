<?php

namespace App\Controller;

use App\Entity\Envio;
use App\Entity\PlanImposicionCsv;
use App\Form\EnvioType;
use App\Repository\EnvioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\UnicodeString;

/**
 * @Route("/envio")
 */
class EnvioController extends AbstractController
{
    /**
     * @Route("/", name="envio_index", methods={"GET"})
     */
    public function index(EnvioRepository $envioRepository): Response
    {
        $planCSVRepository = $this->getDoctrine()->getRepository(PlanImposicionCsv::class);
        $fecha = $planCSVRepository->findPrimerPlan()->getFecha();
        $envios = $envioRepository->findAfterDate($fecha);
        $detalleEnvio = $this->detalleEnvios($envios);
        return $this->render('envio/index.html.twig', [
            'envios' => $envios,
            'fecha' => $fecha,
            'detalles' => $detalleEnvio,
        ]);
    }

    /**
     * @Route("/new", name="envio_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $envio = new Envio();
        $form = $this->createForm(EnvioType::class, $envio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($envio);
            $entityManager->flush();

            return $this->redirectToRoute('envio_index');
        }

        return $this->render('envio/new.html.twig', [
            'envio' => $envio,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="envio_show", methods={"GET"})
     */
    public function show(Envio $envio): Response
    {
        return $this->render('envio/show.html.twig', [
            'envio' => $envio,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="envio_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Envio $envio): Response
    {
        $form = $this->createForm(EnvioType::class, $envio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('envio_index');
        }

        return $this->render('envio/edit.html.twig', [
            'envio' => $envio,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="envio_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Envio $envio): Response
    {
        if ($this->isCsrfTokenValid('delete'.$envio->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($envio);
            $entityManager->flush();
        }

        return $this->redirectToRoute('envio_index');
    }
    public function detalleEnvios($envios){
        $detalle[] = null;
        $size = 0;
        foreach ($envios as $e){
            if($e->getFechaRecibido() != null){ //verificar si ya llegó a su destino
                $detalle[$size] = $this->verificarValidezEnvio($e);
                $size++;
            }else{ //si no ha llegado a su destino, mostrar datos de la última lectura
                $lecturas = $e->getLecturas();
                $u = sizeof($lecturas)-1;
                $lectura = $lecturas[$u];
                $tipoPuerta = null;
                $proposito = new UnicodeString($lectura->getLector()->getProposito());
                if(strpos($proposito, 'entrance') != false){
                    if(strpos($proposito, 'exit') != false){
                        $tipoPuerta = "puerta de entrada y salida";
                    }
                    $tipoPuerta = "puerta de entrada";
                }elseif (strpos($proposito, 'exit') != false){
                    $tipoPuerta = "puerta de salida";
                }
                $mensaje = "Última lectura en ".$lectura->getLector()->getSitio()->getNombre()." el día ".date_format($lectura->getFechaHora(), 'd-m-Y')." a las ".date_format($lectura->getFechaHora(), 'H:i')." en una ".$tipoPuerta;
                $detalle[$size] = $mensaje;
                $size++;
            }
        }
        return $detalle;
    }
    public function verificarValidezEnvio($envio){
        $resultado = "Válido";
        return $resultado;
    }
}
