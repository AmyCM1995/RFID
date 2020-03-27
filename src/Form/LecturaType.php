<?php

namespace App\Form;

use App\Entity\Lectura;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LecturaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fecha_hora')
            ->add('dia')
            ->add('validada')
            ->add('valida')
            ->add('es_marcado_como_terminal_dues')
            ->add('es_primero_calcular_HTD')
            ->add('codigo_lectura_borrada')
            ->add('detalle_lectura_borrada')
            ->add('ctd_lecturas_luego_entregado')
            ->add('tiene_lecturas_marcadas_como_TD')
            ->add('cant_lecturas_entre_enviado_y_recibido')
            ->add('cant_lecturas_despues_recibido')
            ->add('lector')
            ->add('envio')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lectura::class,
        ]);
    }
}
