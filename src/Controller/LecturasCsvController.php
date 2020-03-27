<?php

namespace App\Controller;

use App\Entity\LecturasCsv;
use App\Form\LecturasCsvType;
use App\Repository\LecturasCsvRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/lecturas/csv")
 */
class LecturasCsvController extends AbstractController
{
    /**
     * @Route("/", name="lecturas_csv_index", methods={"GET"})
     */
    public function index(LecturasCsvRepository $lecturasCsvRepository): Response
    {
        return $this->render('lecturas_csv/index.html.twig', [
            'lecturas_csvs' => $lecturasCsvRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="lecturas_csv_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $lecturasCsv = new LecturasCsv();
        $form = $this->createForm(LecturasCsvType::class, $lecturasCsv);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($lecturasCsv);
            $entityManager->flush();

            return $this->redirectToRoute('lecturas_csv_index');
        }

        return $this->render('lecturas_csv/new.html.twig', [
            'lecturas_csv' => $lecturasCsv,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="lecturas_csv_show", methods={"GET"})
     */
    public function show(LecturasCsv $lecturasCsv): Response
    {
        return $this->render('lecturas_csv/show.html.twig', [
            'lecturas_csv' => $lecturasCsv,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="lecturas_csv_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, LecturasCsv $lecturasCsv): Response
    {
        $form = $this->createForm(LecturasCsvType::class, $lecturasCsv);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('lecturas_csv_index');
        }

        return $this->render('lecturas_csv/edit.html.twig', [
            'lecturas_csv' => $lecturasCsv,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="lecturas_csv_delete", methods={"DELETE"})
     */
    public function delete(Request $request, LecturasCsv $lecturasCsv): Response
    {
        if ($this->isCsrfTokenValid('delete'.$lecturasCsv->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($lecturasCsv);
            $entityManager->flush();
        }

        return $this->redirectToRoute('lecturas_csv_index');
    }
}
