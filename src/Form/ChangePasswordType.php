<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ChangePasswordType extends AbstractType
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
            ->add('old_password', PasswordType::class, [
                'label' => "Votre mot de passe",
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'placeholder' => "Merci de saisir votre mot de passe."
                ]
            ])
            ->add('new_password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Le mot de passe et la confirmation doivent être identiques.',
                'required' => false,
                'mapped' => false,
                'first_options' => [
                    'label' => "Votre nouveau mot de passe",
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
                'label' => "Mettre à jour",
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
