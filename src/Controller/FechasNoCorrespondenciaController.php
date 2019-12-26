<?php

namespace App\Controller;

use App\Entity\FechasNoCorrespondencia;
use App\Form\FechasNoCorrespondenciaType;
use App\Repository\FechasNoCorrespondenciaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * @Route("/fechas/no/correspondencia")
 */
class FechasNoCorrespondenciaController extends AbstractController
{
    /**
     * @Route("/", name="fechas_no_correspondencia_index", methods={"GET"})
     */
    public function index(FechasNoCorrespondenciaRepository $fechasNoCorrespondenciaRepository): Response
    {
        return $this->render('fechas_no_correspondencia/index.html.twig', [
            'fechas_no_correspondencias' => $fechasNoCorrespondenciaRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="fechas_no_correspondencia_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $fechasNoCorrespondencium = new FechasNoCorrespondencia();
        $form = $this->createForm(FechasNoCorrespondenciaType::class, $fechasNoCorrespondencium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($fechasNoCorrespondencium);
            $entityManager->flush();

            return $this->redirectToRoute('fechas_no_correspondencia_index');
        }

        return $this->render('fechas_no_correspondencia/new.html.twig', [
            'fechas_no_correspondencium' => $fechasNoCorrespondencium,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="fechas_no_correspondencia_show", methods={"GET"})
     */
    public function show(FechasNoCorrespondencia $fechasNoCorrespondencium): Response
    {
        return $this->render('fechas_no_correspondencia/show.html.twig', [
            'fechas_no_correspondencium' => $fechasNoCorrespondencium,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="fechas_no_correspondencia_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, FechasNoCorrespondencia $fechasNoCorrespondencium): Response
    {
        $form = $this->createForm(FechasNoCorrespondenciaType::class, $fechasNoCorrespondencium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('fechas_no_correspondencia_index');
        }

        return $this->render('fechas_no_correspondencia/edit.html.twig', [
            'fechas_no_correspondencium' => $fechasNoCorrespondencium,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="fechas_no_correspondencia_delete", methods={"DELETE"})
     */
    public function delete(Request $request, FechasNoCorrespondencia $fechasNoCorrespondencium): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fechasNoCorrespondencium->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($fechasNoCorrespondencium);
            $entityManager->flush();
        }

        return $this->redirectToRoute('fechas_no_correspondencia_index');
    }

    /**
     * @Route("/", name="fechas_no_correspondencia_pdf", methods={"GET"})
     */
    public function pdf_Fechas(FechasNoCorrespondenciaRepository $fechasNoCorrespondenciaRepository): Response
    {
        ob_start();
        $fechas = $fechasNoCorrespondenciaRepository->findAll();
        $dompdf = new Dompdf();
        $dompdf->loadHtml(ob_get_clean());
        $dompdf->render();
        $dompdf->stream();

        /*require_once 'dompdf/Autoloader.php';

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($pdfOptions);

        $html = $this->render('fechas_no_correspondencia/pdf_Fechas.html.twig', [
            'fechas_no_correspondencias' => $fechasNoCorrespondenciaRepository->findAll(),
        ]);

        $dompdf->setPaper('A4' , 'portrait');
        $dompdf->render();
        $output = $dompdf->output();
        $publicDirectory = $this->get('kernel')->getProjectDir().'/public';
        $pdfFilepath = $publicDirectory.'/fechasNoCorrespondencia.pdf';
        file_put_contents($pdfFilepath, $output);

        return new Response("El pdf se generó correctamente!");*/
    }
}
