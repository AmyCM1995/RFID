<?php

namespace App\Controller;

use App\Entity\IPLectorCubano;
use App\Form\IPLectorCubanoType;
use App\Repository\IPLectorCubanoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/i/p/lector/cubano")
 */
class IPLectorCubanoController extends AbstractController
{
    /**
     * @Route("/", name="i_p_lector_cubano_index", methods={"GET"})
     */
    public function index(IPLectorCubanoRepository $iPLectorCubanoRepository): Response
    {
        return $this->render('ip_lector_cubano/index.html.twig', [
            'i_p_lector_cubanos' => $iPLectorCubanoRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="i_p_lector_cubano_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $iPLectorCubano = new IPLectorCubano();
        $form = $this->createForm(IPLectorCubanoType::class, $iPLectorCubano);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($iPLectorCubano);
            $entityManager->flush();

            return $this->redirectToRoute('i_p_lector_cubano_index');
        }

        return $this->render('ip_lector_cubano/new.html.twig', [
            'i_p_lector_cubano' => $iPLectorCubano,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/{id}/edit", name="i_p_lector_cubano_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, IPLectorCubano $iPLectorCubano): Response
    {
        $form = $this->createForm(IPLectorCubanoType::class, $iPLectorCubano);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('i_p_lector_cubano_index');
        }

        return $this->render('ip_lector_cubano/edit.html.twig', [
            'i_p_lector_cubano' => $iPLectorCubano,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="i_p_lector_cubano_delete", methods={"GET","POST"})
     */
    public function delete(Request $request, IPLectorCubano $iPLectorCubano): Response
    {
        //if ($this->isCsrfTokenValid('delete'.$iPLectorCubano->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($iPLectorCubano);
            $entityManager->flush();
        //}

        return $this->redirectToRoute('i_p_lector_cubano_index');
    }
}
