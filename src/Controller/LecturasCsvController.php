<?php

namespace App\Controller;

use App\Entity\BorradoPropio;
use App\Entity\ImportacionesLecturas;
use App\Entity\LecturasCsv;
use App\Form\LecturasCsvType;
use App\Repository\LecturasCsvRepository;
use League\Csv\Reader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\UnicodeString;

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
     * @Route("/importar", name="lecturas_csv_importar")
     */
    public function importar(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ESPECIALISTA_DC');
        $form = $this->createFormBuilder()
            ->add('file', FileType::class, [
                'mapped' => false, 'label' => ' '
            ])
            ->add('save', SubmitType::class, [ 'label' => 'Guardar'])
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $file = $form['file']->getData();
            $nombre = new UnicodeString($file->getClientOriginalName());
            $extension = $nombre->after('.');
            if($extension->equalsTo('csv')){
                $file->move('csv', $file->getClientOriginalName());
                $csv = Reader::createFromPath('csv/'.$file->getClientOriginalName());
                $records = $csv->getRecords();
                $this->borrarLecturasAnteriores();
                $this->guardarLecturas($records);
                $this->eliminarLecturasIncompletas();
                $this->eliminarLecturasNoValidas();

            }else{
                echo "no se aceptan ficheros xls";
            }

            return $this->render('plan_imposicion_csv/importacion_correcta.html.twig', [
                'encabezado' => "Importar fichero de lecturas"
            ]);
        }

        return $this->render('lecturas_csv/importar.html.twig', [
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

    public function guardarLecturas($records){
        $entityManager = $this->getDoctrine()->getManager();
        $importacion = new ImportacionesLecturas();
        $importacion->setFecha(new \DateTime('now'));
        $entityManager->persist($importacion);
        $entityManager->flush();
        foreach ($records as $offset=>$record){
            if($offset != 0){
                $lectura = new LecturasCsv();
                $lectura->setCodigoPaisOrigen($record[0]);
                $lectura->setCodigoCiudadOrigen($record[1]);
                $lectura->setNombreCiudadOrigen($record[2]);
                $lectura->setCodigoAreaOrigen($record[3]);
                $lectura->setNombreAreaOrigen($record[4]);
                $lectura->setTipoDimension($record[5]);
                $lectura->setIdEnvio($record[6]);
                $lectura->setIdTranspondedor($record[7]);
                $lectura->setFechaPlanEnviada($record[8]);
                $lectura->setFechaRealEnviada($record[9]);
                $lectura->setCodigoPaisDestino($record[10]);
                $lectura->setCodigoCiudadDestino($record[11]);
                $lectura->setNombreCiudadDestino($record[12]);
                $lectura->setCodigoAreaDestino($record[13]);
                $lectura->setNombreAreaDestino($record[14]);
                $lectura->setFechaRecibida($record[15]);
                $lectura->setValidado($record[16]);
                $lectura->setValido($record[17]);
                $lectura->setHoraFechaLectura($record[18]);
                $lectura->setDiaLectura($record[19]);
                $lectura->setCodigoSitioPais($record[20]);
                $lectura->setCodigoSitio($record[21]);
                $lectura->setNombreSitio($record[22]);
                $lectura->setNombreSitioArea($record[23]);
                $lectura->setNombreLector($record[24]);
                $lectura->setIdLector($record[25]);
                $lectura->setPropositoLector($record[26]);
                $lectura->setEsMarcadoComoTerminalDues($record[27]);
                $lectura->setEsPrimeroCalcularHTD($record[28]);
                $lectura->setCodigoLecturaBorrada($record[29]);
                $lectura->setDetalleLecturaBorrada($record[30]);
                $lectura->setCtdLecturasLuegoEntregado($record[31]);
                $lectura->setTieneLecturasMarcadasComoTD($record[32]);
                $lectura->setCantLecturasEntreEnviadoYRecibido($record[33]);
                $lectura->setCantLecturasDespuesRecibido($record[34]);
                $lectura->setImportacion($importacion);

                $entityManager->persist($lectura);
                $entityManager->flush();
            }
        }
    }


    public function eliminarLecturasIncompletas(){
        $lecturasCsvRepository = $this->getDoctrine()->getRepository(LecturasCsv::class);
        $borradoRepository = $this->getDoctrine()->getRepository(BorradoPropio::class);
        $lecturas = $lecturasCsvRepository->findAll();
        foreach ($lecturas as $l){
            $criterio = null;
            if($l->getCodigoPaisOrigen() == null || $l->getCodigoCiudadOrigen() == null || $l->getCodigoAreaOrigen() == null ||
                $l->getTipoDimension() == null || $l->getIdEnvio() != null && $l->getIdTranspondedor() == null ||
                $l->getFechaPlanEnviada() == null || $l->getFechaRealEnviada() == null || $l->getCodigoPaisDestino() == null ||
                $l->getCodigoCiudadDestino() == null || $l->getCodigoAreaDestino() == null){         //envio
                $criterio = $borradoRepository->findOneByCodigo(1);
            }elseif($l->getHoraFechaLectura() == null || $l->getDiaLectura() == null || $l->getCodigoSitioPais() == null ||
                    $l->getCodigoSitio() == null || $l->getNombreSitio() == null || $l->getNombreSitioArea() == null  ||
                    $l->getIdLector() == null || $l->getPropositoLector() == null) {//lectura
                $criterio = $borradoRepository->findOneByCodigo(2);
                if($l->getFechaRecibida() != null){
                    $criterio = null;
                }
            }elseif($l->getCodigoLecturaBorrada() != null && $l->getDetalleLecturaBorrada() != null) { //lectura borrada por la UPU
                $criterio = $borradoRepository->findOneByCodigo(3);
            }
            if($criterio != null){
                //borrar la lectura
                $l->setCodigoBorradoPropio($criterio);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($l);
                $entityManager->flush();
            }
        }
    }

    public function eliminarLecturasNoValidas(){
        $lecturasCsvRepository = $this->getDoctrine()->getRepository(LecturasCsv::class);
        $lecturas = $lecturasCsvRepository->findAll();
        $envios = $lecturasCsvRepository->findEveryEnvio();
        $temporal = null;
        $size = 0;
        foreach ($envios as $envio){
            foreach ($lecturas as $lectura){
                if($lectura->getIdEnvio() == $envio){
                    $temporal[$size] = $lectura;
                    $size++;
                }
            }
            $this->eliminarLecturasMismoEnvio($temporal);
            $temporal = null;
            $size = 0;
        }
    }

    public function eliminarLecturasMismoEnvio($lecturas){
        $temporal = null;
        $size = 0;
        for ($i=0; $i<sizeof($lecturas); $i++){
            $temporal[$size] = $lecturas[$i];
            $size++;
            if($i+1 < sizeof($lecturas)){
                $l = $lecturas[$i+1];
                if($lecturas[$i]->getIdLector() != $l->getIdLector()){
                    $this->eliminarLecturasSegunPropositoLectorPocoTiempoDiferencia($temporal);
                    $temporal = null;
                    $size = 0;
                }
            }else{
                $this->eliminarLecturasSegunPropositoLectorPocoTiempoDiferencia($temporal);
            }
        }
    }

    public function eliminarLecturasSegunPropositoLectorPocoTiempoDiferencia($lecturas){
        if(sizeof($lecturas) > 0){
            $borradoRepository = $this->getDoctrine()->getRepository(BorradoPropio::class);
            if($lecturas[0]->getPropositoLector() == "Reader at the office entrance"){
                $criterio = $borradoRepository->findOneByCodigo(5);
                for($i=1; $i<sizeof($lecturas); $i++){
                    $lecturas[$i]->setCodigoBorradoPropio($criterio);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($lecturas[$i]);
                    $entityManager->flush();
                }
            }elseif($lecturas[0]->getPropositoLector() == "Reader at the office exit"){
                $criterio = $borradoRepository->findOneByCodigo(6);
                for($i=0; $i<sizeof($lecturas)-1; $i++){
                    $lecturas[$i]->setCodigoBorradoPropio($criterio);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($lecturas[$i]);
                    $entityManager->flush();
                }
            }elseif($lecturas[0]->getPropositoLector() == "Reader at customes entrance and exit" ||
                $lecturas[0]->getPropositoLector() == "Reader at office entrance and exit"){
                $criterio = $borradoRepository->findOneByCodigo(7);
                for($i=1; $i<sizeof($lecturas)-1; $i++){
                    $lecturas[$i]->setCodigoBorradoPropio($criterio);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($lecturas[$i]);
                    $entityManager->flush();
                }
            }
        }
    }
    public function borrarLecturasAnteriores(){
        $lecturasRepository = $this->getDoctrine()->getRepository(LecturasCsv::class);
        $entityManager = $this->getDoctrine()->getManager();
        $anteriores = $lecturasRepository->findAll();
        for($i=0; $i<sizeof($anteriores); $i++){
            $entityManager->remove($anteriores[$i]);
            $entityManager->flush();
        }
    }
}


