<?php

namespace App\Form;

use App\Entity\Company;
use App\Entity\CompanyCategory;
use App\Entity\Store;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class CompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('siret')
            ->add('introduction', TextType::class)
            ->add('description', TextareaType::class)
            ->add('store', EntityType::class, [
                'multiple'=>false,
                'class' => Store::class,
                'choice_label' =>'name'
            ])
            ->add('category', EntityType::class, [
                'multiple'=>false,
                'class' => CompanyCategory::class,
                'choice_label' =>'name',
                'attr'  => [
                   'class'       =>'form-control company-category'
                ]
            ])
            ->add('otherCategory', TextType::class, [
                'attr'  => [
                    'placeholder' => 'Autre',
                    'class'       =>'form-control'
                 ]
           ])
            ->add('email', EmailType::class, [
                'attr'  => [
                    'placeholder' => 'Email',
                    'class'       =>'form-control'
                 ]
            ])
            ->add('phone', IntegerType::class, [
                'attr'  => [
                    'placeholder' => 'Téléphone',
                    'class'       =>'form-control'
                ]
            ])
            ->add('addressNumber', IntegerType::class, [
                'attr'  => [
                    'placeholder' => 'Numéro adresse',
                    'class'       =>'form-control'
                ]
            ])
            ->add('addressStreet', TextType::class, [
                'attr'  => [
                    'placeholder' => 'Rue',
                    'class'       =>'form-control'
                ]
            ])
            ->add('addressPostCode', TextType::class, [
                'attr'  => [
                    'placeholder' => 'Code Postale',
                    'class'       =>'form-control'
                ]
            ])
            ->add('city', TextType::class, [
                'attr'  => [
                    'placeholder' => 'Ville',
                    'class'       =>'form-control'
                ]
            ])
            ->add('country', CountryType::class, [
                'placeholder' => 'Sélectionnez votre pays',
                'attr'  => [
                    'class'       =>'form-control'
                ]
            ])
            ->add('imageFile', FileType::class, [
                'mapped' => false,
                'required' => false,
                'attr'  => [
                    'placeholder' => 'Logo',
                    'class'       =>'form-control'
                ]
        
            ])
            ->add('video')
            ->add('name', TextType::class, [
                'attr'  => [
                    'placeholder' => 'Entreprise',
                    'class'       =>'form-control'
                ]
            ])
            ->add('description', TextType::class, [
                'attr'  => [
                    'placeholder' => 'Description',
                    'class'       =>'form-control'
                ]
            ])
            ->add('website', TextType::class, [
                'attr'  => [
                    'placeholder' => 'Site web',
                    'class'       =>'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Company::class,
        ]);
    }
}
