<?php

namespace App\Controller;

use App\Entity\Lectura;
use App\Form\LecturaType;
use App\Repository\LecturaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/lectura")
 */
class LecturaController extends AbstractController
{
    /**
     * @Route("/", name="lectura_index", methods={"GET"})
     */
    public function index(LecturaRepository $lecturaRepository): Response
    {
        return $this->render('lectura/index.html.twig', [
            'lecturas' => $lecturaRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="lectura_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $lectura = new Lectura();
        $form = $this->createForm(LecturaType::class, $lectura);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($lectura);
            $entityManager->flush();

            return $this->redirectToRoute('lectura_index');
        }

        return $this->render('lectura/new.html.twig', [
            'lectura' => $lectura,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="lectura_show", methods={"GET"})
     */
    public function show(Lectura $lectura): Response
    {
        return $this->render('lectura/show.html.twig', [
            'lectura' => $lectura,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="lectura_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Lectura $lectura): Response
    {
        $form = $this->createForm(LecturaType::class, $lectura);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('lectura_index');
        }

        return $this->render('lectura/edit.html.twig', [
            'lectura' => $lectura,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="lectura_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Lectura $lectura): Response
    {
        if ($this->isCsrfTokenValid('delete'.$lectura->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($lectura);
            $entityManager->flush();
        }

        return $this->redirectToRoute('lectura_index');
    }
}
