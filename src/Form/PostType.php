<?php

namespace App\Form;

use App\Entity\Post;
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
                'attr' => ['placeholder' => 'Dites-nous en plus !',
                           'maxlength' => 1300,
                            'class'    =>'entity-description',
                          ]
            ])
            ->add('urlYoutube', TextType::class, [
                'required' => false,
                'label' => "Vidéo",
                'attr' => [
                    'placeholder' => 'Animez votre post avec une vidéo Youtube !',
                ]
            ])
            ->add('imageFile', FileType::class, [
                'required' => false,
                'label' => 'Image',
                'attr'  => [
                    'placeholder' => 'Une image vaut mille mots',
                    'class'       =>'form-control'
                ]
            ])
            ->add('category', HiddenType::class, [
                'label'=>'Catégorie',
                'required' => true,
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
