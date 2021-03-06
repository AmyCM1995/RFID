<?php

namespace App\Form;

use App\Entity\PaisCorrespondencia;
use App\Entity\ProvinciaCuba;
use App\Entity\SitioLector;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SitioLectorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('codigo', TextType::class, [
                'attr' => array('class' => 'form-control', 'style' => 'margin:5px 0;'), 'label' => 'Código',])
            ->add('nombre', TextType::class, [
                'attr' => array('class' => 'form-control', 'style' => 'margin:5px 0;'), 'label' => 'Nombre',])
            ->add('pais', EntityType::class, [
                'attr' => array('class' => 'form-control', 'style' => 'margin:5px 0;'),
                'class'=> PaisCorrespondencia::class,
                'query_builder' => function (EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->andWhere('p.es_activo = :val')
                        ->setParameter('val', true)
                        ->orderBy('p.nombre', 'ASC');
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
            'data_class' => SitioLector::class,
        ]);
    }
}
