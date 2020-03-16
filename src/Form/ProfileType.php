<?php

namespace App\Form;

use App\Entity\Profile;
use App\Entity\Company;
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
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Nom',
                    'class'       =>'form-control'
                 ]
            ])
            ->add('firstname', TextType::class, [
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Prénom',
                    'class'       =>'form-control'
                 ]
            ])
            ->add('gender', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'Femme' => 0,
                    'Homme' => 1,
                ],
            ])
            ->add('mobileNumber', TextType::class, [
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Téléphone mobile'
                 ]
            ])
            ->add('phoneNumber', TextType::class, [
                'required' => false,
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Téléphone fixe',
                ]
            ])
            ->add('function', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'Responsable marketing' => 0,
                    'Chef de projet' => 1,
                ],
            ])
            ->add('imageFile', FileType::class, [
                'mapped' => false,
                'required' => false,
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Logo',
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
