<?php

namespace App\Form;

use App\Entity\Member;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class MemberModifyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('phone', TextType::class, [
                'label' => 'Téléphone :',
                'attr' => [
                    'placeholder' => "Téléphone"
                ]
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse :',
                'attr' => [
                    'placeholder' => "Adresse"
                ]
            ])
            ->add('postal', TextType::class, [
                'label' => 'Code postal :',
                'attr' => [
                    'placeholder' => "Code postal"
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville :',
                'attr' => [
                    'placeholder' => "Ville"
                ]
            ])
            // ->add('IPAdress')
            // ->add('level')
            // ->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Member::class
        ]);
    }
}
