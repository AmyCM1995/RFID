<?php

namespace App\Form;

use App\Entity\LecturasCsv;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LecturasCsvType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('codigo_pais_origen')
            ->add('codigo_ciudad_origen')
            ->add('nombre_ciudad_origen')
            ->add('codigo_area_origen')
            ->add('nombre_area_origen')
            ->add('tipo_dimension')
            ->add('id_envio')
            ->add('id_transpondedor')
            ->add('fecha_plan_enviada')
            ->add('fecha_real_enviada')
            ->add('codigo_pais_destino')
            ->add('codigo_ciudad_destino')
            ->add('nombre_ciudad_destino')
            ->add('codigo_area_destino')
            ->add('nombre_area_destino')
            ->add('fecha_recibida')
            ->add('validado')
            ->add('valido')
            ->add('hora_fecha_lectura')
            ->add('dia_lectura')
            ->add('codigo_sitio_pais')
            ->add('codigo_sitio')
            ->add('nombre_sitio')
            ->add('nombre_sitio_area')
            ->add('nombre_lector')
            ->add('id_lector')
            ->add('proposito_lector')
            ->add('es_marcado_como_terminal_dues')
            ->add('es_primero_calcular_HTD')
            ->add('codigo_lectura_borrada')
            ->add('detalle_lectura_borrada')
            ->add('ctd_lecturas_luego_entregado')
            ->add('tiene_lecturas_marcadas_como_TD')
            ->add('cant_lecturas_entre_enviado_y_recibido')
            ->add('cant_lecturas_despues_recibido')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LecturasCsv::class,
        ]);
    }
}
