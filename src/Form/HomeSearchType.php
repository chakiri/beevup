<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HomeSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('query', TextType::class, [
                'label' => 'Mots-clés',
                'attr' => [
                    'placeholder' => 'Exemple: Comptable'
                ]
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Code Postal',
                'attr' => [
                    'placeholder' => '75001'
                ]
            ])
            ->add('lat', HiddenType::class, [
                'mapped' => false,
            ])
            ->add('lon', HiddenType::class, [
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
