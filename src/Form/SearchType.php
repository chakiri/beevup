<?php

namespace App\Form;

use App\Entity\Search;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => false,
                'required' => false,
                'attr'  => [
                    'placeholder' => 'Nom',
                    'class'=>'btn-radius'
                ]
            ])
            ->add('type', ChoiceType::class, [
                'label'=>false,
                'attr'  => [
                    'class'=>'btn-radius'
                ],
                'choices'  => [
                    'Personnes' => 'users',
                    'Entreprise' => 'company'
                ],
            ])
            ->add('category', TextType::class, [
                'label' => false,
                'required' => false,
                'attr'  => [
                    'placeholder' => 'Catégorie',
                    'class'=>'btn-radius'


                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
        ]);
    }
}
