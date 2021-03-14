<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => "Prénom",
                'required' => true,
                'attr' => [
                    'placeholder' => "Merci de saisir un prénom."
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => "Nom",
                'required' => true,
                'attr' => [
                    'placeholder' => "Merci de saisir un nom."
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => "Adresse e-mail",
                'required' => true,
                'attr' => [
                    'placeholder' => "Merci de saisir une adresse e-mail."
                ]
            ])
            ->add('new_password', PasswordType::class, [
                'label' => "Mot de passe",
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'placeholder' => "Merci de saisir un mot de passe."
                ]
            ])
            ->add('is_admin', CheckboxType::class, [
                'label' => 'Administration',
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Valider"
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
