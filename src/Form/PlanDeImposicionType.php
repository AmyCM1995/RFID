<?php

namespace App\Form;

use App\Entity\PlanDeImposicion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanDeImposicionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fecha')
            ->add('cod_corresponsal_destino')
            ->add('cod_pais')
            ->add('importacion')
            ->add('cod_corresponsal')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PlanDeImposicion::class,
        ]);
    }
}
