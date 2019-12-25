<?php

namespace App\Controller;

use App\Entity\PaisCorrespondencia;
use App\Repository\TotalesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\UnicodeString;

class TotalesController extends AbstractController
{
    /**
     * @Route("/totales", name="totales", methods={"GET"})
     */
    public function index(TotalesRepository $totalesRepositorio): Response
    {
        $totaless = $totalesRepositorio->findAll();
        $corresponsalesCubanos = $this->buscarCorresponsalesCubanos($totaless);
        $corresponsalesDestino = $this->buscarCorresponsalesDestino($totaless);
        $paisesDestino = $this->buscarPaises($this->buscarCodigoPaíses($totaless));
        $totalesPaises = $this->totalesPaises($totaless, $paisesDestino);
        $totalesC1 = $this->tablaTotalesCorresponsal($totaless, $corresponsalesCubanos[0], $corresponsalesDestino);
        $totalesC2 = $this->tablaTotalesCorresponsal($totaless, $corresponsalesCubanos[1], $corresponsalesDestino);
        $totalesC3 = $this->tablaTotalesCorresponsal($totaless, $corresponsalesCubanos[2], $corresponsalesDestino);
        $enviosTotales = $this->enviosTotales($totaless);
        return $this->render('totales/index.html.twig', [
            'totalesc1' => $totalesC1,
            'totalesc2' => $totalesC2,
            'totalesc3' => $totalesC3,
            'corresponsalesCubanos' => $corresponsalesCubanos,
            'corresponsalesDestino' => $corresponsalesDestino,
            'paisesDestino' => $paisesDestino,
            'totalesPaises' => $totalesPaises,
            'totalEnvios' => $enviosTotales,
        ]);
    }

    /**
     * @Route("/materiales", name="materiales", methods={"GET"})
     */
    public function materialesIndex(TotalesRepository $totalesRepositorio): Response
    {
        $totaless = $totalesRepositorio->findAll();
        $corresponsalesCubanos = $this->buscarCorresponsalesCubanos($totaless);
        $paisesDestino = $this->buscarPaises($this->buscarCodigoPaíses($totaless));
        $enviosTotales = $this->enviosTotales($totaless);
        $corresponsalesDestino = $this->buscarCorresponsalesDestino($totaless);
        $paisesDestino065 = $this->paisesDestinoTarifas($paisesDestino, 0.65);
        $paisesDestino075 = $this->paisesDestinoTarifas($paisesDestino, 0.75);
        $totalEnvios065 = $this->totalEnviosCorresponsalesTarifa($totaless, $corresponsalesCubanos, $paisesDestino065);
        $totalEnvios075 = $this->totalEnviosCorresponsalesTarifa($totaless, $corresponsalesCubanos, $paisesDestino075);
        $totalesC1 = $this->totalEnviosCorresponsal($this->tablaTotalesCorresponsal($totaless, $corresponsalesCubanos[0], $corresponsalesDestino));
        $totalesC2 = $this->totalEnviosCorresponsal($this->tablaTotalesCorresponsal($totaless, $corresponsalesCubanos[1], $corresponsalesDestino));
        $totalesC3 = $this->totalEnviosCorresponsal($this->tablaTotalesCorresponsal($totaless, $corresponsalesCubanos[2], $corresponsalesDestino));
        $total065 = $this->totalArreglo($totalEnvios065);
        $total075 = $this->totalArreglo($totalEnvios075);
        //*******************************Transpondedores
        $totalesPaisesC1 = $this->arrTotalCorresponsalesPaises($totalesRepositorio, $corresponsalesCubanos[0], $paisesDestino);
        $totalesPaisesC2 = $this->arrTotalCorresponsalesPaises($totalesRepositorio, $corresponsalesCubanos[1], $paisesDestino);
        $totalesPaisesC3 = $this->arrTotalCorresponsalesPaises($totalesRepositorio, $corresponsalesCubanos[2], $paisesDestino);


        return $this->render('totales/materiales.html.twig',[
            'totalesc1' => $totalesC1,
            'totalesc2' => $totalesC2,
            'totalesc3' => $totalesC3,
            'totalesPaisesC1' => $totalesPaisesC1,
            'totalesPaisesC2' => $totalesPaisesC2,
            'totalesPaisesC3' => $totalesPaisesC3,
            'totalEnvios065' => $totalEnvios065,
            'totalEnvios075' => $totalEnvios075,
            'total065' => $total065,
            'total075' => $total075,
            'corresponsalesCubanos' => $corresponsalesCubanos,
            'paisesDestino' => $paisesDestino,
            'totalEnvios' => $enviosTotales,
        ]);
    }


    public function arrTotalCorresponsalesPaises($repositorio, $corresponsal, $paisesDestino){
        $arr = [];
        $size = 0;
        for($i=0; $i<sizeof($paisesDestino); $i++){
            $arr[$size] = $repositorio->findOneByCorresponsalDestino($corresponsal, $paisesDestino[$i]->getCodigo())->getTotalEnvios();
            $size++;
        }
        return $arr;
    }

    public function buscarCorresponsalesCubanos($totaless){
        $corresponsalesCuba = [$totaless[0]->getCorresponsalCuba()];
        $size=1;
        for($i=1; $i<sizeof($totaless); $i++){
            if($totaless[$i]->getCorresponsalCuba() != null){
                if($this->existeCorresponsalCuba($corresponsalesCuba, $totaless[$i]->getCorresponsalCuba()) == false){
                    $corresponsalesCuba[$size] = $totaless[$i]->getCorresponsalCuba();
                    $size++;
                }
            }
        }
        return $corresponsalesCuba;
    }

    public function paisesDestinoTarifas($paises, $tarifa){
        $paisesTarifa = [];
        $size = 0;
        for($i=0; $i<sizeof($paises); $i++){
            if($paises[$i]->getRegion()->getTarifa() == $tarifa){
                $paisesTarifa[$size] = $paises[$i];
                $size++;
            }
        }
        return $paisesTarifa;
    }

    public function existeCorresponsalCuba($corresponsalesCuba, $c){
        $existe = false;
        for($i=0; $i<sizeof($corresponsalesCuba); $i++){
            if($corresponsalesCuba[$i] == $c){
                $existe = true;
                break;
            }
        }
        return $existe;
    }

    public function buscarCorresponsalesDestino($totaless){
        $corresponsalesDestino = [$totaless[0]->getCorresponsalDestino()];
        $size=1;
        for($i=1; $i<sizeof($totaless); $i++){
            if($totaless[$i]->getCorresponsalDestino() != null){
                $p = new UnicodeString($totaless[$i]->getCorresponsalDestino());
                if($p->length() == 4){
                    if($this->existeCorresponsalDestino($corresponsalesDestino, $totaless[$i]->getCorresponsalDestino()) == false){
                        $corresponsalesDestino[$size] = $totaless[$i]->getCorresponsalDestino();
                        $size++;
                    }
                }
            }
        }
        return $corresponsalesDestino;
    }
    public function existeCorresponsalDestino($corresponsalesDestino, $c){
        $existe = false;
        for($i=0; $i<sizeof($corresponsalesDestino); $i++){
            if($corresponsalesDestino[$i] == $c){
                $existe = true;
                break;
            }
        }
        return $existe;
    }

    public function buscarCodigoPaíses($totaless){
        $paisesDestino = [$totaless[0]->getCorresponsalDestino()];
        $size=1;
        for($i=1; $i<sizeof($totaless); $i++){
            if($totaless[$i]->getCorresponsalDestino() != null){
                $p = new UnicodeString($totaless[$i]->getCorresponsalDestino());
                if($p->length() == 2){
                    if($this->existePaisDestino($paisesDestino, $totaless[$i]->getCorresponsalDestino()) == false){
                        $paisesDestino[$size] = $totaless[$i]->getCorresponsalDestino();
                        $size++;
                    }
                }
            }
        }
        return $paisesDestino;
    }
    public function existePaisDestino($paisesDestino, $p){
        $existe = false;
        for($i=0; $i<sizeof($paisesDestino); $i++){
            if($paisesDestino[$i] == $p){
                $existe = true;
                break;
            }
        }
        return $existe;
    }

    public function buscarPaises($paisesCodDestino){
        $repositorio = $this->getDoctrine()->getRepository(PaisCorrespondencia::class);
        $paisesGnral = $repositorio->findByActivo();
        $paisesDestino = [new PaisCorrespondencia()];
        $size = 0;
        for($i=0; $i<sizeof($paisesCodDestino); $i++){
            for($j=0; $j<sizeof($paisesGnral); $j++){
                if($paisesCodDestino[$i] == $paisesGnral[$j]->getCodigo()){
                    $paisesDestino[$size] = $paisesGnral[$j];
                    $size++;
                }
            }
        }
        return $paisesDestino;

    }

    public function tablaTotalesCorresponsal($totaless, $corresponsalCubano, $corresponsalesDestino){
        $totales = [];
        $size = 0;
        for($i=0; $i<sizeof($corresponsalesDestino); $i++){
            for($j=0; $j<sizeof($totaless); $j++){
                if($totaless[$j]->getCorresponsalCuba() == $corresponsalCubano){
                    if($corresponsalesDestino[$i] == $totaless[$j]->getCorresponsalDestino()){
                        $totales[$size] = $totaless[$j]->getTotalEnvios();
                        $size++;
                    }
                }
            }
        }
        return $totales;
    }
    public function totalEnviosCorresponsal($tablaCorresponsalesCorresponsal){
        $total = 0;
        for($i=0; $i<sizeof($tablaCorresponsalesCorresponsal); $i++){
            $total += $tablaCorresponsalesCorresponsal[$i];
        }
        return $total;
    }

    public function enviosTotales($totaless){
        $total = 0;
        for($i=0; $i<sizeof($totaless); $i++){
            if($totaless[$i]->getCorresponsalDestino() != null && $totaless[$i]->getCorresponsalCuba() == null) {
                $p = new UnicodeString($totaless[$i]->getCorresponsalDestino());
                if ($p->length() == 2) {
                    $total += $totaless[$i]->getTotalEnvios();
                }
            }
        }
        return $total;
    }

    public function totalesPaises($totaless, $paises){
        $totalesPaises=[];
        $size = 0;
        for($i=0; $i<sizeof($paises); $i++){
            for($j=0; $j<sizeof($totaless); $j++){
                if($paises[$i]->getCodigo() == $totaless[$j]->getCorresponsalDestino() && $totaless[$j]->getCorresponsalCuba() == null){
                    $totalesPaises[$size] = $totaless[$j]->getTotalEnvios();
                    $size++;
                }
            }
        }
        return $totalesPaises;
    }
    public function totalEnviosCorresponsalTarifa($totaless, $corresponsal, $paisesTarifa){
        $totalEnvios = 0;
        for($i=0; $i<sizeof($paisesTarifa); $i++){
            for($j=0; $j<sizeof($totaless); $j++){
                if($totaless[$j]->getCorresponsalDestino() == $paisesTarifa[$i]->getCodigo() && $totaless[$j]->getCorresponsalCuba() == $corresponsal){
                    $totalEnvios += $totaless[$j]->getTotalEnvios();
                }
            }
        }
        return $totalEnvios;
    }
    public function totalEnviosCorresponsalesTarifa($totaless, $corresponsalesCuba, $paisesaTarifa){
        $total = [];
        $size = 0;
        for($i=0; $i<sizeof($corresponsalesCuba); $i++){
            $total[$size] = $this->totalEnviosCorresponsalTarifa($totaless, $corresponsalesCuba[$i], $paisesaTarifa);
            $size++;
        }
        return $total;
    }
    public function totalArreglo ($t){
        $total = 0;
        for($i=0; $i<sizeof($t); $i++){
            $total += $t[$i];
        }
        return $total;
    }
}
