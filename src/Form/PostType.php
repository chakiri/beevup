<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
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
                           'maxlength' => 512

                          ]

            ])
            ->add('category', ChoiceType::class, [
                'label'=>'Catégorie',
                'choices'  => [
                    'Informations' => 'information',
                    'Opportunité commerciale' => 'Opportunities',
                    'Emploi' => 'Emploi',
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
