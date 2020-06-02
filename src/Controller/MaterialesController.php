<?php

namespace App\Controller;

use App\Entity\Corresponsal;
use App\Entity\Materiales;
use App\Entity\ProvinciaCuba;
use App\Form\MaterialesType;
use App\Repository\MaterialesRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/materiales")
 */
class MaterialesController extends AbstractController
{
    /**
     * @Route("/", name="materiales_index", methods={"GET"})
     */
    public function index(MaterialesRepository $materialesRepository): Response
    {
        return $this->render('materiales/index.html.twig', [
            'materiales' => $materialesRepository->findAll(),
        ]);
    }
    /**
     * @Route("/introducir", name="materiales_escoger_corresponsal")
     */
    public function escogerCorresopnsal(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('Escoja_al_corresponsal_que_se_le_va_a_realizar_la_entrega', EntityType::class, [
            'attr' => array('class' => 'form-control', 'style' => 'margin:5px 0;'),
                'class'=> Corresponsal::class,
                'query_builder' => function (EntityRepository $er){
                    return $er->createQueryBuilder('c')->andWhere('c.es_activo = :val')
                        ->setParameter('val', true)->orderBy('c.codigo', 'ASC');
                    },
                'choice_label' => 'nombre',
                'multiple' => false,
                'required' => true,
            ])
            ->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->introducirMateriales();
        }

        return $this->render('materiales/introducirCorresponsal.html.twig', [
            'form' => $form->createView(),

        ]);
    }

    /**
     * @Route("/introducir", name="materiales_escoger_corresponsal")
     */
    public function introducirMateriales(Request $request, $corresponsal): Response
    {

    }

    /**
     * @Route("/new", name="materiales_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $materiale = new Materiales();
        $form = $this->createForm(MaterialesType::class, $materiale);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($materiale);
            $entityManager->flush();

            return $this->redirectToRoute('materiales_index');
        }

        return $this->render('materiales/new.html.twig', [
            'materiale' => $materiale,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="materiales_show", methods={"GET"})
     */
    public function show(Materiales $materiale): Response
    {
        return $this->render('materiales/show.html.twig', [
            'materiale' => $materiale,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="materiales_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Materiales $materiale): Response
    {
        $form = $this->createForm(MaterialesType::class, $materiale);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('materiales_index');
        }

        return $this->render('materiales/edit.html.twig', [
            'materiale' => $materiale,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="materiales_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Materiales $materiale): Response
    {
        if ($this->isCsrfTokenValid('delete'.$materiale->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($materiale);
            $entityManager->flush();
        }

        return $this->redirectToRoute('materiales_index');
    }


}
