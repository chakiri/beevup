<?php

namespace App\Form;

use App\Entity\Profile;
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
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
                    'placeholder' => 'Téléphone mobile',
                    'class'       =>'form-control'
                 ]
            ])
            ->add('phoneNumber', TextType::class, [
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Téléphone fixe',
                    'class'       =>'form-control'
                ]
            ])
            ->add('function', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'Responsable marketing' => 0,
                    'Chef de projet' => 1,
                ],
            ])
          
            ->add('Editer', SubmitType::class)
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Profile::class,
        ]);
    }
}
