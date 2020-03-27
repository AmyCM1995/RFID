<?php

namespace App\Controller;

use App\Entity\Envio;
use App\Form\EnvioType;
use App\Repository\EnvioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/envio")
 */
class EnvioController extends AbstractController
{
    /**
     * @Route("/", name="envio_index", methods={"GET"})
     */
    public function index(EnvioRepository $envioRepository): Response
    {
        return $this->render('envio/index.html.twig', [
            'envios' => $envioRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="envio_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $envio = new Envio();
        $form = $this->createForm(EnvioType::class, $envio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($envio);
            $entityManager->flush();

            return $this->redirectToRoute('envio_index');
        }

        return $this->render('envio/new.html.twig', [
            'envio' => $envio,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="envio_show", methods={"GET"})
     */
    public function show(Envio $envio): Response
    {
        return $this->render('envio/show.html.twig', [
            'envio' => $envio,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="envio_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Envio $envio): Response
    {
        $form = $this->createForm(EnvioType::class, $envio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('envio_index');
        }

        return $this->render('envio/edit.html.twig', [
            'envio' => $envio,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="envio_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Envio $envio): Response
    {
        if ($this->isCsrfTokenValid('delete'.$envio->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($envio);
            $entityManager->flush();
        }

        return $this->redirectToRoute('envio_index');
    }
}
