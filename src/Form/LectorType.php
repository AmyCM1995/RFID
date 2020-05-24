<?php

namespace App\Form;

use App\Entity\Lector;
use App\Entity\ProvinciaCuba;
use App\Entity\SitioLector;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LectorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('codigo', TextType::class, [
                'attr' => array('class' => 'form-control', 'style' => 'margin:5px 0;'), 'label' => 'Código',])
            ->add('nombre', TextType::class, [
                'attr' => array('class' => 'form-control', 'style' => 'margin:5px 0;'), 'label' => 'Nombre',])
            ->add('proposito', TextType::class, [
                'attr' => array('class' => 'form-control', 'style' => 'margin:5px 0;'), 'label' => 'Propósito',])
            ->add('sitio', EntityType::class, [
                'attr' => array('class' => 'form-control', 'style' => 'margin:5px 0;'),
                'class'=> SitioLector::class,
                'query_builder' => function (EntityRepository $er){
                    return $er->createQueryBuilder('p')->orderBy('p.nombre', 'ASC');
                },
                'choice_label' => 'nombre',
                'multiple' => false,
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lector::class,
        ]);
    }
}
