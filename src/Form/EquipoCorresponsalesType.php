<?php

namespace App\Form;

use App\Entity\Corresponsal;
use App\Entity\EquipoCorresponsales;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EquipoCorresponsalesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('codigo', TextType::class, [
                'attr' => array('class' => 'form-control', 'style' => 'margin:5px 0;')])
            ->add('cantidadMiembros', NumberType::class, [
                'attr' => array('class' => 'form-control', 'style' => 'margin:5px 0;')])
            ->add('CorresponsalCoordinador', EntityType::class, [
                'attr' => array('class' => 'form-control', 'style' => 'margin:5px 0;'),
                'class'=> Corresponsal::class,
                'query_builder' => function (EntityRepository $er){
                    return $er->createQueryBuilder('r')->orderBy('r.nombre', 'ASC');
                },
                'choice_label' => 'nombre',
                'multiple' => false,
                'required' => true,
                ])
            ->add('corresponsals', EntityType::class, [
                'attr' => array('class' => 'form-control', 'style' => 'margin:5px 0;'),
                'class'=> Corresponsal::class,
                'query_builder' => function (EntityRepository $er){
                    return $er->createQueryBuilder('r')->orderBy('r.nombre', 'ASC');
                },
                'choice_label' => 'nombre',
                'multiple' => true,
                'required' => true,
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EquipoCorresponsales::class,
        ]);
    }
}
