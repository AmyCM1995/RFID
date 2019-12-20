<?php

namespace App\Form;

use App\Entity\GMSRFIDUsuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
//use Symfony\Component\Mime\Email;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Email;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre' , TextType::class, [
                'attr' => array('class' => 'form-control', 'style' => 'margin:5px 0;')])
            ->add('correo', EmailType::class, [
                'attr' => array('class' => 'form-control', 'style' => 'margin:5px 0;'),
                'constraints' => [
                    new Email(['message' => 'Por favor introduzca una dirección de correo válida'])
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'attr' => array('class' => 'form-control', 'style' => 'margin:5px 0;'),
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
                'first_options' => ['label' => 'Contraseña'],
                'second_options' => ['label' => 'Confirmar contraseña'],
                'invalid_message' => 'Su contraseña no coincide'
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
           # ->add('agreeTerms', CheckboxType::class, [
           #     'mapped' => false,
           #     'constraints' => [
           #        new IsTrue([
           #           'message' => 'You should agree to our terms.',
           #      ]),
           #     ],
           #])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => GMSRFIDUsuario::class,
        ]);
    }
}
