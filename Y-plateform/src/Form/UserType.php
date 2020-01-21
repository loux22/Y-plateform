<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'attr' => [
                    'placeholder' => "Firstname",
                    'class' => "input_register"
                    // 'label' => 'firstname' on peut changer le label
                ]
            ])
            ->add('lastname', TextType::class, [
                'attr' => [
                    'placeholder' => "Lastname",
                    'class' => "input_register"
                ]
            ])
            ->add('pseudo', TextType::class, [
                'attr' => [
                    'placeholder' => "Pseudo",
                    'class' => "input_register"
                ]
            ])
            ->add('mail', EmailType::class, [
                'attr' => [
                    'placeholder' => "Email",
                    'class' => "input_register"
                ]
            ])
            ->add('age', BirthdayType::class, [
                'widget' => 'choice',
                'label' => 'Date de naissance' ,
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'le mot de passe n\'est pas confirmer.',
                'first_options' => [
                    'label' => 'Password',
                    'attr' => [
                        'placeholder' => "Password",
                        'class' => "input_register"
                    ]
                ],
                'second_options' => [
                    'label' => 'Répeter password',
                    'attr' => [
                        'placeholder' => "Répeter Password",
                        'class' => "input_register"
                    ]
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}