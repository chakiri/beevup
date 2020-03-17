<?php

namespace App\Form;

use App\Entity\Service;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label'    => 'Titre',
                'attr'  => [
                    'placeholder' => 'Titre',
                    'class'       =>'form-control'
                ]

            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'tinymce'],
            ])
            ->add('type', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'Génerique' => 0,
                    'Locale bureau vallée' => 1,
                    'Locale entreprise' => 2,
                    'national' =>3
                ],
            ])
            ->add('isFree', CheckboxType::class, [
                'label'    => 'Service Payant',
                'required' => false,
            ])
			
            ->add('price')
            ->add('introduction')
            ->add('imageFile', FileType::class, [
                'required' => false,
                'attr'  => [
                    'placeholder' => 'Photo',
                    'class'       =>'form-control'
                ]
            ])
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
        ]);
    }
}
