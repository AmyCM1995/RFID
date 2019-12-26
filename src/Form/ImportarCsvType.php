<?php

namespace App\Form;

use App\Entity\PlanImposicionCsv;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImportarCsvType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('save', SubmitType::class, [
                'label' => 'Importar',
                'attr' => [
                    'class' => 'btn btn-success'
                ],

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault([
            'data_class' => PlanImposicionCsv::class,
        ]);
    }
}