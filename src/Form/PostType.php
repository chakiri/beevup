<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\PostCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => ['placeholder' => 'Titre',
                           'maxlength' => 255
                ],
                'label' =>'Titre'

            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Saisissez le texte de votre publication, choisissez une catégorie de post ci dessous et cliquez sur Publier !',
                    'maxlength' => 1300,
                    'class'    =>'entity-description',
                ]
            ])
            ->add('category', EntityType::class, [
                    'label'=>'Catégorie',
                    'placeholder'=>'Choisissez votre catégorie',
                    'class' => PostCategory::class,
                ]
            )
            ->add('imageFile', FileType::class, [
                'required' => false,
                'label' => 'Image',
                'attr'  => [
                    'placeholder' => 'Une image vaut mille mots',
                    'class'       =>'form-control'
                ]
            ])
            ->add('urlYoutube', TextType::class, [
                'required' => false,
                'label' => "Vidéo",
                'attr' => [
                    'placeholder' => 'Collez le lien Youtube de votre vidéo !',
                    'class' => 'form-control'
                ]
            ])
            ->add('urlLink', TextType::class, [
                'required' => false,
                'label' => "Url web",
                'attr' => [
                    'placeholder' => 'Saisissez l\'url de votre article, on s\'occupe du reste',
                    'class' => 'form-control'
                ]
            ])
            ->add('imageLink', TextType::class, [
                'required' => false,
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
