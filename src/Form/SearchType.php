<?php

namespace App\Form;

use App\Entity\Search;
use App\Entity\ServiceCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => false,
                'required' => false,
                'attr'  => [
                    'placeholder' => 'Entrez vos mots clés',
                    'class'=>'btn-radius'
                ]
            ])
            ->add('company', CheckboxType::class, [
                'label' => 'Entreprises',
                'required' =>false
            ])
            ->add('service', CheckboxType::class, [
                'label' => 'Services',
                'required' =>false
            ])
            ->add('category', EntityType::class, [
                'class' => ServiceCategory::class,
                'choice_label' =>'name',
                'label' => false,
                'placeholder' => 'Choisissez une categorie',
                'required' =>false
            ])
            ->add('isExclusif', CheckboxType::class, [
                'label' => 'Services Exclusifs',
                'required' =>false
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
