<?php

namespace App\Controller;

use App\Entity\HistorialLectores;
use App\Entity\Lector;
use App\Form\HistorialLectoresType;
use App\Repository\HistorialLectoresRepository;
use App\Repository\LectorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/historial/lectores")
 */
class HistorialLectoresController extends AbstractController
{
    /**
     * @Route("/{id}", name="historial_lectores_index", methods={"GET"})
     */
    public function index(HistorialLectoresRepository $historialLectoresRepository, Lector $lector): Response
    {
        //mostrar el historial del lector seleccionado
        $historialLector = $historialLectoresRepository->findByLector($lector->getId());
        return $this->render('historial_lectores/index.html.twig', [
            'historial_lectores' => $historialLector,
        ]);
    }
    /**
     * @Route("/seleccionar/lector", name="seleccionar_lector")
     */
    public function seleccionarLector(LectorRepository $lectorRepository): Response
    {
        return $this->render('historial_lectores/seleccionar_lector.html.twig', [
            'lectors' => $lectorRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="historial_lectores_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $historialLectore = new HistorialLectores();
        $form = $this->createForm(HistorialLectoresType::class, $historialLectore);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($historialLectore);
            $entityManager->flush();

            return $this->redirectToRoute('historial_lectores_index');
        }

        return $this->render('historial_lectores/new.html.twig', [
            'historial_lectore' => $historialLectore,
            'form' => $form->createView(),
        ]);
    }
        /**
     * @Route("/{id}/edit", name="historial_lectores_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, HistorialLectores $historialLectore): Response
    {
        $form = $this->createForm(HistorialLectoresType::class, $historialLectore);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('historial_lectores_index');
        }

        return $this->render('historial_lectores/edit.html.twig', [
            'historial_lectore' => $historialLectore,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="historial_lectores_delete", methods={"DELETE"})
     */
    public function delete(Request $request, HistorialLectores $historialLectore): Response
    {
        if ($this->isCsrfTokenValid('delete'.$historialLectore->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($historialLectore);
            $entityManager->flush();
        }

        return $this->redirectToRoute('historial_lectores_index');
    }
}
