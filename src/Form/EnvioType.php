<?php

namespace App\Form;

use App\Entity\Envio;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnvioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('codigo')
            ->add('codigo_transpondedor')
            ->add('tipo_medida')
            ->add('fecha_plan_enviado')
            ->add('fecha_real_enviado')
            ->add('area_origen')
            ->add('area_destino')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Envio::class,
        ]);
    }
}
