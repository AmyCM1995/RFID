<?php

namespace App\Controller;


use App\Entity\ProvinciaCuba;
use App\Form\ProvinciaCubaType;
use App\Repository\ProvinciaCubaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/provincia/cuba")
 */
class ProvinciaCubaController extends AbstractController
{
    /**
     * @Route("/", name="provincia_cuba_index", methods={"GET"})
     */
    public function index(ProvinciaCubaRepository $provinciaCubaRepository): Response
    {
        return $this->render('provincia_cuba/index.html.twig', [
            'provincia_cubas' => $provinciaCubaRepository->findAllByNombre(),
        ]);
    }



    /**
     * @Route("/new", name="provincia_cuba_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ESPECIALISTA_DC');
        $provinciaCuba = new ProvinciaCuba();
        $provinciaCuba->setEsActivo(true);
        $form = $this->createForm(ProvinciaCubaType::class, $provinciaCuba);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($provinciaCuba);
            $entityManager->flush();

            return $this->redirectToRoute('provincia_cuba_index');
        }

        return $this->render('provincia_cuba/new.html.twig', [
            'provincia_cuba' => $provinciaCuba,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="provincia_cuba_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ProvinciaCuba $provinciaCuba): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ESPECIALISTA_DC');
        $form = $this->createForm(ProvinciaCubaType::class, $provinciaCuba);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('provincia_cuba_index');
        }

        return $this->render('provincia_cuba/edit.html.twig', [
            'provincia_cuba' => $provinciaCuba,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}", name="provincia_cuba_delete", methods={"GET","POST"})
     */
    public function delete(Request $request, ProvinciaCuba $provinciaCuba): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ESPECIALISTA_DC');
        //if ($this->isCsrfTokenValid('delete'.$provinciaCuba->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->find(ProvinciaCuba::class, $provinciaCuba->getId());
            $provinciaCuba->setEsActivo(false);
           // $entityManager->remove($provinciaCuba);
            $entityManager->flush();
        //}

        return $this->redirectToRoute('provincia_cuba_index');
    }

}
