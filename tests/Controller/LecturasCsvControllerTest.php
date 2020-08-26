<?php


namespace App\Tests\Controller;


use App\Controller\LecturasCsvController;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class LecturasCsvControllerTest extends TestCase
{
    public function testVerificarMismaFechaHoraSinImportarSegundosYMilisegundos(){
        //fechas iguales, resultado esperado: true
        $lectura = new LecturasCsvController();
        $fecha1 = "2018-08-16 08:37:35.7460000";
        $fecha2 = "2018-08-16 08:37:46.7460000";
        $result = $lectura->verificarMismaFechaHoraSinImportarSegundosYMilisegundos($fecha1, $fecha2);
        $this->assertTrue($result);
    }

}