<?php

namespace App\Controller;

use App\Entity\Importaciones;
use App\Form\ImportacionesType;
use App\Repository\ImportacionesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/importaciones")
 */
class ImportacionesController extends AbstractController
{
    /**
     * @Route("/", name="importaciones_index", methods={"GET"})
     */
    public function index(ImportacionesRepository $importacionesRepository): Response
    {
        return $this->render('importaciones/index.html.twig', [
            'importaciones' => $importacionesRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="importaciones_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $importacione = new Importaciones();
        $form = $this->createForm(ImportacionesType::class, $importacione);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($importacione);
            $entityManager->flush();

            return $this->redirectToRoute('importaciones_index');
        }

        return $this->render('importaciones/new.html.twig', [
            'importacione' => $importacione,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="importaciones_show", methods={"GET"})
     */
    public function show(Importaciones $importacione): Response
    {
        return $this->render('importaciones/show.html.twig', [
            'importacione' => $importacione,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="importaciones_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Importaciones $importacione): Response
    {
        $form = $this->createForm(ImportacionesType::class, $importacione);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('importaciones_index');
        }

        return $this->render('importaciones/edit.html.twig', [
            'importacione' => $importacione,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="importaciones_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Importaciones $importacione): Response
    {
        if ($this->isCsrfTokenValid('delete'.$importacione->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($importacione);
            $entityManager->flush();
        }

        return $this->redirectToRoute('importaciones_index');
    }
}
