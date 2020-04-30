<?php

namespace App\Controller;

use App\Entity\Corresponsal;
use App\Form\CorresponsalType;
use App\Repository\CorresponsalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;

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
        $this->denyAccessUnlessGranted('ROLE_ESPECIALISTA_DC');
        $corresponsal = new Corresponsal();
        $corresponsal->setEsActivo(true);
        $form = $this->createForm(CorresponsalType::class, $corresponsal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($corresponsal);
            $entityManager->flush();
            //pdf corresponsal
            $this->pdfNuevoCorresponsal($corresponsal);

            return $this->redirectToRoute('corresponsal_index');
        }
        return $this->render('corresponsal/new.html.twig', [
            'corresponsal' => $corresponsal,
            'form' => $form->createView(),
        ]);
    }

    public function pdfNuevoCorresponsal($corresponsal){
        $this->redirectToRoute('corresponsal_index');
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);
        $html = $this->renderView('corresponsal/pdf_nuevoCorresponsal.html.twig', [
            'corresponsal' => $corresponsal,
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4' , 'portrait');
        $dompdf->render();
        $dompdf->stream("NuevoCorresponsal.pdf", [
            "Attachment" => true
        ]);
    }

    /**
     * @Route("/{id}/edit", name="corresponsal_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Corresponsal $corresponsal): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ESPECIALISTA_DC');
        $form = $this->createForm(CorresponsalType::class, $corresponsal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            //**************************************PDF
            $pdfOptions = new Options();
            $pdfOptions->set('defaultFont', 'Arial');
            $dompdf = new Dompdf($pdfOptions);

            $html = $this->renderView('corresponsal/pdf_editarCorresponsal.html.twig', [
                'corresponsal' => $corresponsal,
            ]);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4' , 'portrait');
            $dompdf->render();
            $dompdf->stream("CorresponsalModificado.pdf", [
                "Attachment" => true
            ]);

            return $this->redirectToRoute('corresponsal_index');
        }

        return $this->render('corresponsal/edit.html.twig', [
            'corresponsal' => $corresponsal,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="corresponsal_delete", methods={"GET","POST"})
     */
    public function delete(Request $request, Corresponsal $corresponsal): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ESPECIALISTA_DC');
        //if ($this->isCsrfTokenValid('delete'.$corresponsal->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->find(Corresponsal::class, $corresponsal->getId());
            $corresponsal->setEsActivo(false);
            //$entityManager->remove($corresponsal);
            $entityManager->flush();
        //}

        return $this->redirectToRoute('corresponsal_index');
    }


}
