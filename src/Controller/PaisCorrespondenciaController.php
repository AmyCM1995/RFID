<?php

namespace App\Controller;

use App\Entity\PaisCorrespondencia;
use App\Form\PaisCorrespondenciaType;
use App\Repository\PaisCorrespondenciaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/pais/correspondencia")
 */
class PaisCorrespondenciaController extends AbstractController
{
    /**
     * @Route("/", name="pais_correspondencia_index", methods={"GET"})
     */
    public function index(PaisCorrespondenciaRepository $paisCorrespondenciaRepository): Response
    {
        return $this->render('pais_correspondencia/index.html.twig', [
            'pais_correspondencias' => $paisCorrespondenciaRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="pais_correspondencia_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $paisCorrespondencium = new PaisCorrespondencia();
        $paisCorrespondencium ->setEsActivo(true);
        $form = $this->createForm(PaisCorrespondenciaType::class, $paisCorrespondencium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($paisCorrespondencium);
            $entityManager->flush();

            return $this->redirectToRoute('pais_correspondencia_index');
        }

        return $this->render('pais_correspondencia/new.html.twig', [
            'pais_correspondencium' => $paisCorrespondencium,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="pais_correspondencia_show", methods={"GET"})
     */
    public function show(PaisCorrespondencia $paisCorrespondencium): Response
    {
        return $this->render('pais_correspondencia/show.html.twig', [
            'pais_correspondencium' => $paisCorrespondencium,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="pais_correspondencia_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, PaisCorrespondencia $paisCorrespondencium): Response
    {
        $form = $this->createForm(PaisCorrespondenciaType::class, $paisCorrespondencium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('pais_correspondencia_index');
        }

        return $this->render('pais_correspondencia/edit.html.twig', [
            'pais_correspondencium' => $paisCorrespondencium,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="pais_correspondencia_delete", methods={"DELETE"})
     */
    public function delete(Request $request, PaisCorrespondencia $paisCorrespondencium): Response
    {
        if ($this->isCsrfTokenValid('delete'.$paisCorrespondencium->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->find(PaisCorrespondencia::class, $paisCorrespondencium->getId());
            $paisCorrespondencium->setEsActivo(false);
           // $entityManager->remove($paisCorrespondencium);
            $entityManager->flush();
        }

        return $this->redirectToRoute('pais_correspondencia_index');
    }
}
