<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Post;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => "Nom",
                'required' => true,
                'attr' => [
                    'placeholder' => "Merci de saisir le nom de l'article."
                ]
            ])
            ->add('file', FileType::class, [
                'label' => "Image",
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => ['image/gif', 'image/png', 'image/jpeg', 'image/bmp', 'image/webp', 'image/svg+xml'],
                        'mimeTypesMessage' => "Le fichier téléchargé n'est pas dans un format image.",
                        'maxSize' => '8192k',
                        'maxSizeMessage' => "L'image est trop lourde pour être téléchargé."
                    ])
                ],
                'attr' => [
                    'placeholder' => "Merci de choisir l'image de l'article."
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => "Contenu",
                'required' => true,
                'attr' => [
                    'placeholder' => "Merci de saisir le contenu de l'article."
                ]
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie associé',
                'required' => true,
                'class' => Category::class,
                'attr' => [
                    'placeholder' => "Merci de sélectionner la catégorie de l'article."
                ]
            ])
            ->add('isBest', CheckboxType::class, [
                'label' => 'A la une',
                'required' => false,
                'value' => 0
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
