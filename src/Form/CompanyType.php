<?php

namespace App\Form;

use App\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class CompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('siret', TextType::class, [
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Siret',
                    'class'       =>'form-control'
                 ]
            ])
            ->add('email', EmailType::class, [
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Email',
                    'class'       =>'form-control'
                 ]
            ])
            ->add('phone', IntegerType::class, [
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Téléphone',
                    'class'       =>'form-control'
                ]
            ])
            ->add('addressNumber', IntegerType::class, [
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Numéro adresse',
                    'class'       =>'form-control'
                ]
            ])
            ->add('addressStreet', TextType::class, [
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Rue',
                    'class'       =>'form-control'
                ]
            ])
            ->add('addressPostCode', TextType::class, [
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Code Postale',
                    'class'       =>'form-control'
                ]
            ])
            ->add('city', TextType::class, [
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Ville',
                    'class'       =>'form-control'
                ]
            ])
            ->add('country', CountryType::class, [
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Pays',
                    'class'       =>'form-control'
                ]
            ])
            ->add('imageFile', FileType::class, [
                'mapped' => false,
                'label' => false,
                'required' => false,
                'attr'  => [
                    'placeholder' => 'Logo',
                    'class'       =>'form-control'
                ]
        
            ])
            ->add('video', TextType::class,[
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Video',
                    'class'       =>'form-control'
                ]
            ])
            ->add('name', TextType::class, [
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Entreprise',
                    'class'       =>'form-control'
                ]
            ])
            ->add('description', TextType::class, [
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Description',
                    'class'       =>'form-control'
                ]
            ])
            ->add('website', TextType::class, [
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Site web',
                    'class'       =>'form-control'
                ]
            ])
            ->add('latitude', TextType::class, [
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Latitude',
                    'class'       =>'form-control'
                ]
            ])
            ->add('longitude', TextType::class, [
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Longitude',
                    'class'       =>'form-control'
                ]
            ])
            ->add('Editer', SubmitType::class)
            
            
            
            
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Company::class,
        ]);
    }
}
