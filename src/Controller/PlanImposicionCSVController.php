<?php

namespace App\Controller;


use App\Entity\Corresponsal;
use App\Entity\Importaciones;
use App\Entity\PaisCorrespondencia;
use App\Entity\PlanDeImposicion;
use App\Entity\PlanImposicionCsv;
use App\Repository\CorresponsalRepository;
use App\Repository\PlanImposicionCsvRepository;
use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use League\Csv\Reader;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\UnicodeString;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PlanImposicionCSVController extends AbstractController
{
    /**
     * @Route("/plan/imposicion/c/s/v", name="plan_imposicion_c_s_v")
     */
    public function index(PlanImposicionCsvRepository $planImposicionCsv): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render('plan_imposicion_csv/index.html.twig', [
            'plan_imposicion_csvs' => $planImposicionCsv -> findAll(),
        ]);
    }
    /**
     * @Route("/{id}", name="plan_imposicion_c_s_v_delete", methods={"DELETE"})
     * @param Request $request
     * @param PlanImposicionCsv $planImposicionCsv
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Request $request, PlanImposicionCsv $planImposicionCsv): \Symfony\Component\HttpFoundation\Response
    {
        if ($this->isCsrfTokenValid('delete'.$planImposicionCsv->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($planImposicionCsv);
            $entityManager->flush();
        }

        return $this->redirectToRoute('plan_imposicion_c_s_v');
    }









}
