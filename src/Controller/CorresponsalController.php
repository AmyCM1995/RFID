<?php

namespace App\Controller;

use App\Entity\Corresponsal;
use App\Form\CorresponsalType;
use App\Repository\CorresponsalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/corresponsal")
 */
class CorresponsalController extends AbstractController
{
    /**
     * @Route("/", name="corresponsal_index", methods={"GET"})
     */
    public function index(CorresponsalRepository $corresponsalRepository): Response
    {
        return $this->render('corresponsal/index.html.twig', [
            'corresponsals' => $corresponsalRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="corresponsal_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $corresponsal = new Corresponsal();
        $corresponsal->setEsActivo(true);
        $form = $this->createForm(CorresponsalType::class, $corresponsal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($corresponsal);
            $entityManager->flush();

            return $this->redirectToRoute('corresponsal_index');
        }

        return $this->render('corresponsal/new.html.twig', [
            'corresponsal' => $corresponsal,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="corresponsal_show", methods={"GET"})
     */
    public function show(Corresponsal $corresponsal): Response
    {
        return $this->render('corresponsal/show.html.twig', [
            'corresponsal' => $corresponsal,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="corresponsal_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Corresponsal $corresponsal): Response
    {
        $form = $this->createForm(CorresponsalType::class, $corresponsal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('corresponsal_index');
        }

        return $this->render('corresponsal/edit.html.twig', [
            'corresponsal' => $corresponsal,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="corresponsal_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Corresponsal $corresponsal): Response
    {
        if ($this->isCsrfTokenValid('delete'.$corresponsal->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->find(Corresponsal::class, $corresponsal->getId());
            $corresponsal->setEsActivo(false);
            //$entityManager->remove($corresponsal);
            $entityManager->flush();
        }

        return $this->redirectToRoute('corresponsal_index');
    }


}
