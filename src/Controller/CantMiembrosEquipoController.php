<?php

namespace App\Controller;

use App\Entity\CantMiembrosEquipo;
use App\Form\CantMiembrosEquipoType;
use App\Repository\CantMiembrosEquipoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/cant/miembros/equipo")
 */
class CantMiembrosEquipoController extends AbstractController
{
    /**
     * @Route("/", name="cant_miembros_equipo_index", methods={"GET"})
     */
    public function index(CantMiembrosEquipoRepository $cantMiembrosEquipoRepository): Response
    {
        return $this->render('cant_miembros_equipo/index.html.twig', [
            'cant_miembros_equipo' => $cantMiembrosEquipoRepository->findUltimo(),
        ]);
    }

    /**
     * @Route("/new", name="cant_miembros_equipo_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $cantMiembrosEquipo = new CantMiembrosEquipo();
        $form = $this->createForm(CantMiembrosEquipoType::class, $cantMiembrosEquipo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cantMiembrosEquipo);
            $entityManager->flush();

            return $this->redirectToRoute('cant_miembros_equipo_index');
        }

        return $this->render('cant_miembros_equipo/new.html.twig', [
            'cant_miembros_equipo' => $cantMiembrosEquipo,
            'form' => $form->createView(),
        ]);
    }
}
