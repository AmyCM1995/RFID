<?php

namespace App\Form;

use App\Entity\PaisCorrespondencia;
use App\Entity\RegionMundial;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaisCorrespondenciaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('codigo', TextType::class, [
                'attr' => array('class' => 'form-control', 'style' => 'margin:5px 0;')])
            ->add('nombre', TextType::class, [
                'attr' => array('class' => 'form-control', 'style' => 'margin:5px 0;')])
            ->add('region', EntityType::class, [
                'attr' => array('class' => 'form-control', 'style' => 'margin:5px 0;'),
                'class' => RegionMundial::class,
                'query_builder' => function (EntityRepository $er){
                    return $er->createQueryBuilder('r')->orderBy('r.descripcion', 'ASC');
                },
                'choice_label' => 'descripcion',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PaisCorrespondencia::class,
        ]);
    }
}
