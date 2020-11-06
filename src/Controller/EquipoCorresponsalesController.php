<?php

namespace App\Controller;

use App\Entity\CantMiembrosEquipo;
use App\Entity\Corresponsal;
use App\Entity\EquipoCorresponsales;
use App\Form\EquipoCorresponsalesType;
use App\Repository\CantMiembrosEquipoRepository;
use App\Repository\EquipoCorresponsalesRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/equipo/corresponsales")
 */
class EquipoCorresponsalesController extends AbstractController
{
    /**
     * @Route("/", name="equipo_corresponsales_index", methods={"GET"})
     */
    public function index(EquipoCorresponsalesRepository $equipoCorresponsalesRepository): Response
    {
        $existenCorresponsalesSinEquipo = $this->existeCorresponsalNoAsignado();
        return $this->render('equipo_corresponsales/index.html.twig', [
            'equipo_corresponsales' => $equipoCorresponsalesRepository->findByActivo(),
            'existenCorresponsalesSinAsignarEquipo' => $existenCorresponsalesSinEquipo,
        ]);
    }

    /**
     * @Route("/new", name="equipo_corresponsales_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $alerta = null;
        $cantRepository =  $this->getDoctrine()->getRepository(CantMiembrosEquipo::class);
        $this->denyAccessUnlessGranted('ROLE_ESPECIALISTA_DC');
        $equipoCorresponsale = new EquipoCorresponsales();
        $equipoCorresponsale->setEsActivo(true);
        $equipoCorresponsale->setCantMiembros($cantRepository->findUltimo());

        $form = $this->createForm(EquipoCorresponsalesType::class, $equipoCorresponsale);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            if ($equipoCorresponsale->getCantMiembros()->getCantidad() == sizeof($equipoCorresponsale->getCorresponsals())) {
                if($form->isValid()){
                    $entityManager = $this->getDoctrine()->getManager();

                    for ( $i = 0; $i < sizeof($equipoCorresponsale->getCorresponsals()); $i++){
                        $corr = new Corresponsal();
                        $corr = $equipoCorresponsale->getCorresponsals()[$i];
                        $corr->setEquipo($equipoCorresponsale);
                        $entityManager->persist($corr);
                        $entityManager->flush();
                    }
                    $equipoCorresponsale->setEsActivo(true);
                    $entityManager->persist($equipoCorresponsale);
                    $entityManager->flush();

                    return $this->redirectToRoute('equipo_corresponsales_index');
                }
            }else{
                $alerta = "El equipo debe tener ".$cantRepository->findUltimo()->getCantidad()." miembros.";
            }
        }

        return $this->render('equipo_corresponsales/new.html.twig', [
            'equipo_corresponsale' => $equipoCorresponsale,
            'form' => $form->createView(),
            'alerta' => $alerta,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="equipo_corresponsales_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, EquipoCorresponsales $equipoCorresponsale): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ESPECIALISTA_DC');
        $form = $this->createForm(EquipoCorresponsalesType::class, $equipoCorresponsale);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('equipo_corresponsales_index');
        }

        return $this->render('equipo_corresponsales/edit.html.twig', [
            'equipo_corresponsale' => $equipoCorresponsale,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="equipo_corresponsales_delete", methods={"GET","POST"})
     */
    public function delete(Request $request, EquipoCorresponsales $equipoCorresponsale): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ESPECIALISTA_DC');
        //if ($this->isCsrfTokenValid('delete'.$equipoCorresponsale->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->find(EquipoCorresponsales::class, $equipoCorresponsale->getId());
            $equipoCorresponsale->setEsActivo(false);
            //$entityManager->remove($equipoCorresponsale);
            $entityManager->flush();
        //}

        return $this->redirectToRoute('equipo_corresponsales_index');
    }
    public function existeCorresponsalNoAsignado(){
        $result = false;
        $corresponsalRepository = $this->getDoctrine()->getRepository(Corresponsal::class);
        $corresponsales = $corresponsalRepository->findByActivo();
        foreach ($corresponsales as $corresponsal){
            if($corresponsal->getEquipo() == null){
                $result = true;
                break;
            }
        }
        return $result;
    }
}
