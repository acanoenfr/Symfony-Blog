<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => "Votre prénom",
                'required' => true,
                'attr' => [
                    'placeholder' => "Merci de saisir votre prénom."
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => "Votre nom",
                'required' => true,
                'attr' => [
                    'placeholder' => "Merci de saisir votre nom."
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => "Votre adresse e-mail",
                'required' => true,
                'attr' => [
                    'placeholder' => "Merci de saisir votre adresse e-mail."
                ]
            ])
            ->add('new_password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Le mot de passe et la confirmation doivent être identiques.',
                'required' => true,
                'mapped' => false,
                'first_options' => [
                    'label' => "Votre mot de passe",
                    'attr' => [
                        'placeholder' => "Merci de saisir votre mot de passe."
                    ]
                ],
                'second_options' => [
                    'label' => "Confirmez votre mot de passe",
                    'attr' => [
                        'placeholder' => "Merci de confirmer votre mot de passe."
                    ]
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => "S'inscrire",
                'attr' => [
                    'class' => 'btn btn-block btn-primary'
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
