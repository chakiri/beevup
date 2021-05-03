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
                    'placeholder' => 'Exemple : Photographe',
                    'class'=>'btn-radius'
                ]
            ])
            ->add('isCompany', CheckboxType::class, [
                'label' => 'Entreprises',
                'required' =>false,
                'attr' => [
                    'checked' => 'checked'
                ]
            ])
            ->add('isService', CheckboxType::class, [
                'label' => 'Services',
                'required' =>false,
                'attr' => [
                    'checked' => 'checked'
                ]
            ])
            ->add('category', EntityType::class, [
                'class' => ServiceCategory::class,
                'choice_label' =>'name',
                'label' => false,
                'placeholder' => 'Choisissez une categorie',
                'required' =>false
            ])
            ->add('isDiscovery', CheckboxType::class, [
                'label' => 'Offres Exclusives',
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
