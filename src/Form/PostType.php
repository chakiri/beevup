<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
                'attr' => ['placeholder' => 'Description',
                           'maxlength' => 1300,
                            'class'    =>'entity-description',
                          ]
            ])
            ->add('urlYoutube', TextType::class, [
                'required' => false,
                'label' => "Vidéo",
                'attr' => [
                    'placeholder' => 'Votre url Youtube',
                ]
            ])
            ->add('imageFile', FileType::class, [
                'required' => false,
                'label' => 'Image',
                'attr'  => [
                    'placeholder' => 'Choisir une image',
                    'class'       =>'form-control'
                ]
            ])
            ->add('category', ChoiceType::class, [
                'label'=>'Catégorie',
                'choices'  => [
                    'Informations' => 'information',
                    'Opportunité commerciale' => 'Opportunité commerciale',
                    'Emploi' => 'Emploi',
                    'Événement' => 'Événement',
                    'Question à la communauté' => 'Question à la communauté',
                    'Autre' => 'Autre'
                ],
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
