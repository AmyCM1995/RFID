<?php

namespace App\Form;

use App\Entity\GMSRFIDUsuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;

class GMSRFIDUsuarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', TextType::class, [
                'attr' => array('class' => 'form-control', 'style' => 'margin:5px 0;'), 'label' => 'Nombre',])
            ->add('correo', EmailType::class, [
                'attr' => array('class' => 'form-control', 'style' => 'margin:5px 0;'),
                'constraints' => [
                    new Email(['message' => 'Por favor introduzca una dirección de correo válida'])
                ]
            ])
            ->add('roles', ChoiceType::class, array(
                'attr' => array('class' => 'form-control', 'style' => 'margin:5px 0;'),
                'choices' =>
                    array(
                        'Observador' => 'ROLE_OBSERVADOR',
                        'Corresponsal' =>'ROLE_CORRESPONSAL',
                        'Especialista de Desarrollo y Calidad' =>'ROLE_ESPECIALISTA_DC',
                        'Administrador' => 'ROLE_ADMIN',
                    ),
                'multiple' => true,
                'required' => true,
            ))
           // ->add('roles')
            ->add('password')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => GMSRFIDUsuario::class,
        ]);
    }
}
