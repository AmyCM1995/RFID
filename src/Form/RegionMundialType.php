<?php

namespace App\Form;

use App\Entity\RegionMundial;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegionMundialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numero', TextType::class, [
                'attr' => array('class' => 'form-control', 'style' => 'margin:5px 0;'), 'label' => 'Número'])
            ->add('descripcion', TextType::class, [
                'attr' => array('class' => 'form-control', 'style' => 'margin:5px 0;'), 'label' => 'Descripción'])
            ->add('tarifa', MoneyType::class, [
                'attr' => array('class' => 'form-control', 'style' => 'margin:5px 0;'), 'label' => 'Tarifa'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RegionMundial::class,
        ]);
    }
}
