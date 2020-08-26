<?php

namespace App\Form;

use App\Entity\IPLectorCubano;
use App\Entity\Lector;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IPLectorCubanoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lector', EntityType::class, [
                'attr' => array('class' => 'form-control', 'style' => 'margin:5px 0;'),
                'class' => Lector::class,'query_builder' => function (EntityRepository $er){
                        return $er->createQueryBuilder('p')
                            ->innerJoin('p.sitio', 's', 'WITH', 's.pais = 250')
                            ->where('l.id')
                            ->orderBy('p.nombre', 'ASC');
                    },
                    'choice_label' => 'nombre',
                    'multiple' => false,
                    'required' => true,
                ])
            ->add('ip', TextType::class, [
                'attr' => array('class' => 'form-control', 'style' => 'margin:5px 0;'), 'label' => 'IP',])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => IPLectorCubano::class,
        ]);
    }
}
