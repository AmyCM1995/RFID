<?php

namespace App\Controller;

use App\Entity\PaisCorrespondencia;
use App\Form\PaisCorrespondenciaType;
use App\Repository\PaisCorrespondenciaRepository;
use http\Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/pais/correspondencia")
 */
class PaisCorrespondenciaController extends AbstractController
{
    /**
     * @Route("/", name="pais_correspondencia_index", methods={"GET"})
     */
    public function index(PaisCorrespondenciaRepository $paisCorrespondenciaRepository): Response
    {
        return $this->render('pais_correspondencia/index.html.twig', [
            'pais_correspondencias' => $paisCorrespondenciaRepository->findAllByNombreMenosCuba(),
        ]);
    }

    /**
     * @Route("/añadir", name="pais_correspondencia_añadir", methods={"GET"})
     */
    public function añadir(PaisCorrespondenciaRepository $paisCorrespondenciaRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ESPECIALISTA_DC');
        return $this->render('pais_correspondencia/añadir.html.twig', [
            'pais_correspondencias' => $paisCorrespondenciaRepository->findAllByNombreAndDelete(),
        ]);
    }

    /**
     * @Route("/new", name="pais_correspondencia_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ESPECIALISTA_DC');
        $paisCorrespondencium = new PaisCorrespondencia();
        $paisCorrespondencium ->setEsActivo(false);
        $form = $this->createForm(PaisCorrespondenciaType::class, $paisCorrespondencium);
        $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($paisCorrespondencium);
                $entityManager->flush();

                return $this->redirectToRoute('pais_correspondencia_añadir');

    }

        return $this->render('pais_correspondencia/new.html.twig', [
            'pais_correspondencium' => $paisCorrespondencium,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="pais_correspondencia_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, PaisCorrespondencia $paisCorrespondencium): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ESPECIALISTA_DC');
        $form = $this->createForm(PaisCorrespondenciaType::class, $paisCorrespondencium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('pais_correspondencia_index');
        }

        return $this->render('pais_correspondencia/edit.html.twig', [
            'pais_correspondencium' => $paisCorrespondencium,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/activar", name="pais_correspondencia_activar", methods={"GET","POST"})
     */
    public function activar(PaisCorrespondencia $paisCorrespondencium): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ESPECIALISTA_DC');
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->find(PaisCorrespondencia::class, $paisCorrespondencium->getId());
        $paisCorrespondencium->setEsActivo(true);
        $entityManager->flush();


        return $this->redirectToRoute('pais_correspondencia_index');
    }

    /**
     * @Route("/{id}", name="pais_correspondencia_delete", methods={"GET","POST"})
     */
    public function delete(Request $request, PaisCorrespondencia $paisCorrespondencium): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ESPECIALISTA_DC');
        //if ($this->isCsrfTokenValid('delete'.$paisCorrespondencium->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->find(PaisCorrespondencia::class, $paisCorrespondencium->getId());
            $paisCorrespondencium->setEsActivo(false);
           // $entityManager->remove($paisCorrespondencium);
            $entityManager->flush();
        //}

        return $this->redirectToRoute('pais_correspondencia_index');
    }

    public function verificarNoExistenciaCodigo(PaisCorrespondencia $pais){
        $em = $this->getDoctrine()->getManager()->getRepository(PaisCorrespondencia::class);
        $p = $em->findAll();
        $existe = false;
        for($i=0; $i<sizeof($p); $i++){
            if($p[$i]->getCodigo() == $pais->getCodigo()){
                $existe = true;
                break;
            }
        }
        return $existe;
    }
}
