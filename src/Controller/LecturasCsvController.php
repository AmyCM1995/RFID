<?php

namespace App\Controller;

use App\Entity\BorradoPropio;
use App\Entity\ImportacionesLecturas;
use App\Entity\Lector;
use App\Entity\LecturasCsv;
use App\Entity\PaisCorrespondencia;
use App\Entity\SitioLector;
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
            ->add('save', SubmitType::class, [ 'label' => 'Guardar',  'attr' =>
                array('id' => 'importacion', 'onclick' => 'cargando()')])
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
                //validacion y limpieza de datos
                $this->eliminarLecturasDuplicadas();
                $this->validarDatosdeCadaLecturaCSV();

            }else{
                echo "no se aceptan ficheros xls";
            }
            $alertas = null;
            return $this->render('plan_imposicion_csv/importacion_correcta.html.twig', [
                'encabezado' => "Importar fichero de lecturas",
                'alertas' => $alertas,
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


    public function eliminarLecturasDuplicadas(){
        $lecturasCsvRepository = $this->getDoctrine()->getRepository(LecturasCsv::class);
        $borradoRepository = $this->getDoctrine()->getRepository(BorradoPropio::class);
        $lecturas = $lecturasCsvRepository->findAll();
        $horaFechaAnterior = null;
        $idTranspondedorAnterior = null;
        $idLectorAnterior = null;
        for ($i=0; $i<sizeof($lecturas); $i++){
            $criterio = null;
            if($i==0){
                $horaFechaAnterior = $lecturas[$i]->getHoraFechaLectura();
                $idTranspondedorAnterior = $lecturas[$i]->getIdTranspondedor();
                $idLectorAnterior = $lecturas[$i]->getIdLector();
            }else{
                if($lecturas[$i]->getIdTranspondedor() == $idTranspondedorAnterior &&
                    $lecturas[$i]->getIdLector() == $idLectorAnterior){
                    if($lecturas[$i]->getHoraFechaLectura() == $horaFechaAnterior ||
                    $this->verificarMismaFechaHoraSinImportarSegundosYMilisegundos($horaFechaAnterior, $lecturas[$i]->getHoraFechaLectura())){
                        $criterio = $borradoRepository->findOneByCodigo(5);
                    }
                }else{
                    $horaFechaAnterior = $lecturas[$i]->getHoraFechaLectura();
                    $idTranspondedorAnterior = $lecturas[$i]->getIdTranspondedor();
                    $idLectorAnterior = $lecturas[$i]->getIdLector();
                }
            }
            if($criterio != null){
                //borrar la lectura
                $lecturas[$i]->setCodigoBorradoPropio($criterio);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($lecturas[$i]);
                $entityManager->flush();
            }
        }
    }
    public function verificarMismaFechaHoraSinImportarSegundosYMilisegundos($fechaAnterior, $fechaActual){
        $iguales = false;
        $fAnterior = new UnicodeString($fechaAnterior);
        $fActual = new UnicodeString($fechaActual);
        $p1 = $fAnterior->slice(0, $fAnterior->length()-10);
        $p2 = $fActual->slice(0, $fActual->length()-10);
        if($p1 == $p2){
            $iguales = true;
        }
        return $iguales;
    }
    public function validarDatosdeCadaLecturaCSV(){
        $lecturasCsvRepository = $this->getDoctrine()->getRepository(LecturasCsv::class);
        $lecturas = $lecturasCsvRepository->findAll();
        $paisesRepository = $this->getDoctrine()->getRepository(PaisCorrespondencia::class);
        $paisesActivos = $paisesRepository->findByActivo();
        $borradoRepository = $this->getDoctrine()->getRepository(BorradoPropio::class);
        $criterio = null;
        foreach ($lecturas as $lectura){
            if($this->esValidaoPaisOrigen($lectura, $paisesActivos) == false){
                $criterio = $borradoRepository->findOneByCodigo(8);
            }elseif($this->esValidoIdEnvio($lectura, $paisesActivos) == false){
                $criterio = $borradoRepository->findOneByCodigo(9);
            }elseif ($this->idTranspondedorValido($lectura) == false){
                $criterio = $borradoRepository->findOneByCodigo(10);
            }elseif($this->tieneDatosDeLectura($lectura) == true){
                if($this->lectorValido($lectura) == false){
                    $criterio = $borradoRepository->findOneByCodigo(11);
                }
            }else{
                //no tiene los datos de las lecturas: verificar id_envio y id_transpondedor para emitir o no alarma
                $criterio = $borradoRepository->findOneByCodigo(13);
            }



            if($criterio != null){
                //borrar la lectura
                $lectura->setCodigoBorradoPropio($criterio);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($lectura);
                $entityManager->flush();
            }
        }
    }
    public function esValidaoPaisOrigen($lectura, $paises){
        $valido = false;
        foreach ($paises as $pais){
            if($lectura->getCodigoPaisOrigen() == "CU" || $lectura->getCodigoPaisOrigen() == $pais->getCodigo()){
                $valido = true;
            }
        }
        return $valido;
    }
    public function esValidoIdEnvio($lectura, $paises){
        $valido = false;
        $envio = new UnicodeString($lectura->getIdEnvio());
        $origen = $envio->slice(0, 2);
        $destino = $envio->slice($envio->length()-2, $envio->length());
        if($envio->length() == 10 && $lectura->getCodigoPaisOrigen() == $origen){
            foreach ($paises as $pais){
                if($destino == "CU" || $destino == $pais->getCodigo()){
                    $valido = true;
                }
            }
        }
        return $valido;
    }
    public function idTranspondedorValido($lectura){
        $valido = false;
        $transpondedor = new UnicodeString($lectura->getIdTranspondedor());
        if($transpondedor->length() == 17){
            $valido = true;
        }
        return $valido;
    }
    public function tieneDatosDeLectura($lectura){
        $tiene = true;
        if($lectura->getHoraFechaLectura() == null || $lectura->getDiaLectura() == null || $lectura->getCodigoSitioPais() == null
            || $lectura->getCodigoSitio() == null || $lectura->getNombreSitio() == null || $lectura->getNombreSitioArea() == null
            || $lectura->getNombreLector() == null || $lectura->getIdLector() == null || $lectura->getPropositoLector() == null){
            $tiene = false;
        }
        return $tiene;
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
    public function lectorValido($lectura){
        $valido = false;
        $id_lector = $lectura->getIdLector();
        $proposito = $lectura->getPropositoLector();
        $cod_sitio = $lectura->getCodigoSitio();
        $cod_pais_sitio = $lectura->getCodigoSitioPais();
        $lectorRepository = $this->getDoctrine()->getRepository(Lector::class);
        $lectores = $lectorRepository->findAll();
        foreach ($lectores as $lectore){
            if($lectore->getCodigo() == $id_lector && $lectore->getProposito() == $proposito){
                if($lectore->getSitio()->getCodigo() == $cod_sitio && $lectore->getSitio()->getPais()->getCodigo() == $cod_pais_sitio){
                    $valido = true;
                    break;
                }
            }
        }
        return $valido;
    }
}


