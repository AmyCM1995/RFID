<?php

namespace App\Controller;

use App\Entity\Lector;
use App\Form\LectorType;
use App\Repository\LectorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/lector")
 */
class LectorController extends AbstractController
{
    /**
     * @Route("/", name="lector_index", methods={"GET"})
     */
    public function index(LectorRepository $lectorRepository): Response
    {
        return $this->render('lector/index.html.twig', [
            'lectors' => $lectorRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="lector_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $lector = new Lector();
        $form = $this->createForm(LectorType::class, $lector);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($lector);
            $entityManager->flush();

            return $this->redirectToRoute('lector_index');
        }

        return $this->render('lector/new.html.twig', [
            'lector' => $lector,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/edit", name="lector_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Lector $lector): Response
    {
        $form = $this->createForm(LectorType::class, $lector);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('lector_index');
        }

        return $this->render('lector/edit.html.twig', [
            'lector' => $lector,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="lector_delete", methods={"GET","POST"})
     */
    public function delete(Request $request, Lector $lector): Response
    {
        //if ($this->isCsrfTokenValid('delete'.$lector->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($lector);
            $entityManager->flush();
        //}

        return $this->redirectToRoute('lector_index');
    }
}
