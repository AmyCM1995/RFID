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
use Symfony\Component\String\UnicodeString;

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
        $this->denyAccessUnlessGranted('ROLE_ESPECIALISTA_DC');
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
     * @Route("/{id}/edit", name="fechas_no_correspondencia_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, FechasNoCorrespondencia $fechasNoCorrespondencium): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ESPECIALISTA_DC');
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
        $this->denyAccessUnlessGranted('ROLE_ESPECIALISTA_DC');
        if ($this->isCsrfTokenValid('delete'.$fechasNoCorrespondencium->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($fechasNoCorrespondencium);
            $entityManager->flush();
        }

        return $this->redirectToRoute('fechas_no_correspondencia_index');
    }

    /**
     * @Route("/fechas/pdf", name="fechas_no_correspondencia_pdf", methods={"GET"})
     */
    public function pdf_Fechas(FechasNoCorrespondenciaRepository $fechasNoCorrespondenciaRepository): Response
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);
        //****************************************
        $fechas = $fechasNoCorrespondenciaRepository->findAll();
        //****************************************
        $html = $this->renderView('fechas_no_correspondencia/pdf_Fechas.html.twig', [
            'fechas_no_correspondencias' => $fechas,
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $fecha = new \DateTime('now');
        $f = $fecha->format("d/m/y");
        $dmy = new UnicodeString($f);
        $d = $dmy->before("/");
        $my = $dmy->after("/");
        $m = $my->before("/");
        $y = $my->after("/");
        $nombre = "Fechas_".$d."_".$m."_".$y."_".".pdf";
        $dompdf->stream($nombre, [
            "Attachment" => true
        ]);
    }
}
