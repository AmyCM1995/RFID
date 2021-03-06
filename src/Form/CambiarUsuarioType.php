<?php

namespace App\Form;

use App\Entity\GMSRFIDUsuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CambiarUsuarioType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
                'label' => '',
                'attr' => array('class' => 'form-control', 'placeholder'=>"Contraseña actual", 'style' => 'margin:5px 0;'),
                //'translation_domain' => 'FOSUserBundle',
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, introduzca la contraseña',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'attr' => array('class' => 'form-control', 'placeholder'=>"Contraseña", 'style' => 'margin:5px 0;'),
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'type' => PasswordType::class,
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, introduzca la contraseña',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Su contraseña debe tener al menos {{ limit }} caracteres',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
                'first_options' => ['label' => ' ',
                    'attr' => array('class' => 'form-control','placeholder' => 'Nueva contraseña')],
                'second_options' => ['label' => ' ',
                    'attr' => array('class' => 'form-control','placeholder' => 'Confirmar contraseña')],
                'invalid_message' => 'Su contraseña no coincide'
            ]);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            //'data_class' => GMSRFIDUsuario::class,
        ]);
    }
}