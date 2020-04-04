<?php

namespace App\Controller;

use App\Entity\BorradoPropio;
use App\Form\BorradoPropioType;
use App\Repository\BorradoPropioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/borrado/propio")
 */
class BorradoPropioController extends AbstractController
{
    /**
     * @Route("/", name="borrado_propio_index", methods={"GET"})
     */
    public function index(BorradoPropioRepository $borradoPropioRepository): Response
    {
        return $this->render('borrado_propio/index.html.twig', [
            'borrado_propios' => $borradoPropioRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="borrado_propio_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $borradoPropio = new BorradoPropio();
        $form = $this->createForm(BorradoPropioType::class, $borradoPropio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($borradoPropio);
            $entityManager->flush();

            return $this->redirectToRoute('borrado_propio_index');
        }

        return $this->render('borrado_propio/new.html.twig', [
            'borrado_propio' => $borradoPropio,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="borrado_propio_show", methods={"GET"})
     */
    public function show(BorradoPropio $borradoPropio): Response
    {
        return $this->render('borrado_propio/show.html.twig', [
            'borrado_propio' => $borradoPropio,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="borrado_propio_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, BorradoPropio $borradoPropio): Response
    {
        $form = $this->createForm(BorradoPropioType::class, $borradoPropio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('borrado_propio_index');
        }

        return $this->render('borrado_propio/edit.html.twig', [
            'borrado_propio' => $borradoPropio,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="borrado_propio_delete", methods={"DELETE"})
     */
    public function delete(Request $request, BorradoPropio $borradoPropio): Response
    {
        if ($this->isCsrfTokenValid('delete'.$borradoPropio->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($borradoPropio);
            $entityManager->flush();
        }

        return $this->redirectToRoute('borrado_propio_index');
    }
}
