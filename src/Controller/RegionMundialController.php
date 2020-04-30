<?php

namespace App\Controller;

use App\Entity\RegionMundial;
use App\Form\RegionMundialType;
use App\Repository\RegionMundialRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/region/mundial")
 */
class RegionMundialController extends AbstractController
{
    /**
     * @Route("/", name="region_mundial_index", methods={"GET"})
     */
    public function index(RegionMundialRepository $regionMundialRepository): Response
    {
        return $this->render('region_mundial/index.html.twig', [
            'region_mundials' => $regionMundialRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="region_mundial_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ESPECIALISTA_DC');
        $regionMundial = new RegionMundial();
        $form = $this->createForm(RegionMundialType::class, $regionMundial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($regionMundial);
            $entityManager->flush();

            return $this->redirectToRoute('region_mundial_index');
        }

        return $this->render('region_mundial/new.html.twig', [
            'region_mundial' => $regionMundial,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="region_mundial_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, RegionMundial $regionMundial): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ESPECIALISTA_DC');
        $form = $this->createForm(RegionMundialType::class, $regionMundial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('region_mundial_index');
        }

        return $this->render('region_mundial/edit.html.twig', [
            'region_mundial' => $regionMundial,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="region_mundial_delete", methods={"GET","POST"})
     */
    public function delete(Request $request, RegionMundial $regionMundial): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ESPECIALISTA_DC');
        //if ($this->isCsrfTokenValid('delete'.$regionMundial->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($regionMundial);
            $entityManager->flush();
        //}

        return $this->redirectToRoute('region_mundial_index');
    }
}
