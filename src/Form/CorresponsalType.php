<?php

namespace App\Form;

use App\Entity\Corresponsal;
use App\Entity\EquipoCorresponsales;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CorresponsalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('codigo', TextType::class, [
                'attr' => array('class' => 'form-control', 'style' => 'margin:5px 0;')])
            ->add('nombre', TextType::class, [
                'attr' => array('class' => 'form-control', 'style' => 'margin:5px 0;')])
            ->add('apellidos', TextType::class, [
                'attr' => array('class' => 'form-control', 'style' => 'margin:5px 0;')])
            ->add('direccion',  TextType::class, [
                'attr' => array('class' => 'form-control', 'style' => 'margin:5px 0;')])
            ->add('correo', EmailType::class, [
                'attr' => array('class' => 'form-control', 'style' => 'margin:5px 0;')])
            /*->add('equipo', EntityType::class, [
                'attr' => array('class' => 'form-control', 'style' => 'margin:5px 0;'),
                'class' => EquipoCorresponsales::class,
                'query_builder' => function (EntityRepository $er){
                    return $er->createQueryBuilder('e')->orderBy('e.codigo', 'ASC');
                },
                'choice_label' => 'codigo',
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Corresponsal::class,
        ]);
    }
}