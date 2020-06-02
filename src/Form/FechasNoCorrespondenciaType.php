<?php

namespace App\Form;


use App\Entity\FechasNoCorrespondencia;
use App\Entity\ProvinciaCuba;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;



class FechasNoCorrespondenciaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $date = new \DateTime('now');
        $builder
            ->add('fecha', DateType::class, ['label' => 'Fecha', 'widget' => 'single_text', 'data' => $date,
                 'attr' => array('class' => 'form-control')])
            ->add('es_anual', CheckboxType::class, [
                'label' => 'Anual','required' => false
            ])
            ->add('descripcion', TextType::class, [
                'attr' => array('class' => 'form-control', 'style' => 'margin:5px 0;'), 'label' => 'Descripción',])
            ->add('provincia', EntityType::class, [
                'attr' => array('class' => 'form-control', 'style' => 'margin:5px 0;'),
                'class'=> ProvinciaCuba::class,
                'query_builder' => function (EntityRepository $er){
                    return $er->createQueryBuilder('p')->orderBy('p.nombre', 'ASC');
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
            'data_class' => FechasNoCorrespondencia::class,
        ]);
    }
}
