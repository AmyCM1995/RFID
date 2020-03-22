<?php

namespace App\Form;

use App\Entity\CumplimientoPlan;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CumplimientoPlanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*$builder
            ->add('save', SubmitType::class, [
                'label' => 'Importar',
                'attr' => [
                    'class' => 'btn btn-success button',
                    'id' => 'buttonImportar'
                ],

            ])
        ;*/
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CumplimientoPlan::class,
        ]);
    }
}
