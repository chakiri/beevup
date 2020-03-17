<?php

namespace App\Form;

use App\Entity\Profile;
use App\Entity\Company;
use App\Entity\UserFunction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'attr'  => [
                    'placeholder' => 'Nom',
                    'class'       =>'form-control'
                 ]
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'attr'  => [
                    'placeholder' => 'Prénom',
                    'class'       =>'form-control'
                 ]
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Genre',
                'choices' => [
                    'Femme' => 0,
                    'Homme' => 1,
                ],
            ])
            ->add('mobileNumber', TextType::class, [
                'label' => 'Téléphone',
                'attr'  => [
                    'placeholder' => 'Téléphone mobile'
                 ]
            ])
            ->add('phoneNumber', TextType::class, [
                'required' => false,
                'label' => 'Téléphone fixe',
                'attr'  => [
                    'placeholder' => 'Téléphone fixe',
                ]
            ])
            ->add('function', EntityType::class, [
                'label' => 'Fonction',
                'multiple'=>false,
                'class' => UserFunction::class,
                'choice_label' =>'name'
            ])
            ->add('imageFile', FileType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Photo',
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
            'data_class' => Profile::class,
        ]);
    }
}
