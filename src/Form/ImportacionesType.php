<?php

namespace App\Form;

use App\Entity\Importaciones;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImportacionesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fecha_importado')
            ->add('fecha_inicio_plan')
            ->add('fecha_fin_plan')
            ->add('ciclo')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Importaciones::class,
        ]);
    }
}
