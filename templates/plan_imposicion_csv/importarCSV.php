<?php
$fecha = 2019-04-01;
$importacion = (new \App\Entity\Importaciones())
    ->setFechaImportado(new \DateTime('now'))
    ->setFechaInicioPlan('ayer')
    ->setFechaFinPlan('mañana')
    ->setCiclo('prueba')
    ;


$plan_csv = (new \App\Entity\PlanImposicionCsv())
    ->setFecha($fecha)
    ->setFechaDia(\Symfony\Component\Intl\DateFormatter\DateFormat\DayOfWeekTransformer::$fecha)
    ->setEnvio11('prueba1')
    ->setEnvio12('prueba1')
    ->setEnvio21('prueba1')
    ->setEnvio22('prueba1')
    ->setEnvio31('prueba1')
    ->setEnvio32('prueba1')
    ;
