<?php

namespace App\Controller;

use App\Entity\Corresponsal;
use App\Entity\EquipoCorresponsales;
use App\Form\EquipoCorresponsalesType;
use App\Repository\EquipoCorresponsalesRepository;
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
        return $this->render('equipo_corresponsales/index.html.twig', [
            'equipo_corresponsales' => $equipoCorresponsalesRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="equipo_corresponsales_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $equipoCorresponsale = new EquipoCorresponsales();
        $equipoCorresponsale->setEsActivo(true);

       // if($equipoCorresponsale->getCantidadMiembros() == sizeof($equipoCorresponsale->getCorresponsals())){
            $form = $this->createForm(EquipoCorresponsalesType::class, $equipoCorresponsale);
            $form->handleRequest($request);
           // $equipoCorresponsale->setCantidadMiembros(sizeof($equipoCorresponsale->getCorresponsals()));


            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();

                for ( $i = 0; $i < sizeof($equipoCorresponsale->getCorresponsals()); $i++){
                    $corr = new Corresponsal();
                    $corr = $equipoCorresponsale->getCorresponsals()[$i];
                    $corr->addEquipo($equipoCorresponsale);
                    $entityManager->persist($corr);
                    $entityManager->flush();
                }

                $entityManager->persist($equipoCorresponsale);
                $entityManager->flush();

                return $this->redirectToRoute('equipo_corresponsales_index');
            }
       // }

        return $this->render('equipo_corresponsales/new.html.twig', [
            'equipo_corresponsale' => $equipoCorresponsale,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="equipo_corresponsales_show", methods={"GET"})
     */
    public function show(EquipoCorresponsales $equipoCorresponsale): Response
    {
        return $this->render('equipo_corresponsales/show.html.twig', [
            'equipo_corresponsale' => $equipoCorresponsale,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="equipo_corresponsales_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, EquipoCorresponsales $equipoCorresponsale): Response
    {
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
     * @Route("/{id}", name="equipo_corresponsales_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EquipoCorresponsales $equipoCorresponsale): Response
    {
        if ($this->isCsrfTokenValid('delete'.$equipoCorresponsale->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->find(EquipoCorresponsales::class, $equipoCorresponsale->getId());
            $equipoCorresponsale->setEsActivo(false);
            //$entityManager->remove($equipoCorresponsale);
            $entityManager->flush();
        }

        return $this->redirectToRoute('equipo_corresponsales_index');
    }
}
