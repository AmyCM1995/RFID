<?php

namespace App\Controller;

use App\Entity\SitioLector;
use App\Form\SitioLectorType;
use App\Repository\SitioLectorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sitio/lector")
 */
class SitioLectorController extends AbstractController
{
    /**
     * @Route("/", name="sitio_lector_index", methods={"GET"})
     */
    public function index(SitioLectorRepository $sitioLectorRepository): Response
    {
        return $this->render('sitio_lector/index.html.twig', [
            'sitio_lectors' => $sitioLectorRepository->findByActivo(),
        ]);
    }

    /**
     * @Route("/new", name="sitio_lector_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $sitioLector = new SitioLector();
        $form = $this->createForm(SitioLectorType::class, $sitioLector);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $sitioLector->setEsActivo(true);
            $entityManager->persist($sitioLector);
            $entityManager->flush();

            return $this->redirectToRoute('sitio_lector_index');
        }

        return $this->render('sitio_lector/new.html.twig', [
            'sitio_lector' => $sitioLector,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/edit", name="sitio_lector_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, SitioLector $sitioLector): Response
    {
        $form = $this->createForm(SitioLectorType::class, $sitioLector);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('sitio_lector_index');
        }

        return $this->render('sitio_lector/edit.html.twig', [
            'sitio_lector' => $sitioLector,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="sitio_lector_delete", methods={"GET","POST"})
     */
    public function delete(Request $request, SitioLector $sitioLector): Response
    {
        //if ($this->isCsrfTokenValid('delete'.$sitioLector->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $sitioLector->setEsActivo(false);
            $entityManager->remove($sitioLector);
            $entityManager->flush();
       // }

        return $this->redirectToRoute('sitio_lector_index');
    }
}
