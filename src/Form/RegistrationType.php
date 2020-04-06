<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Store;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('company', InscriptionCompanyType::class, [
            'label' => false,
         ])
            ->add('name', TextType::class, [
                'label' => false,
                'mapped'=>false,
                   'attr'  => [
                   'placeholder' => 'Raison Sociale',
                   'class'       =>'form-control'
                ]
            ])
             ->add('store', EntityType::class, [
                'label' => false,
                'multiple'=>false,
                'class' => Store::class,
                'choice_label' =>'name'

            ])
            ->add('email', EmailType::class, [
                'label' => false,
                   'attr'  => [
                   'placeholder' => 'Email',
                   'class'       =>'form-control'
                   ]
               ])
            ->add('password', PasswordType::class, [
                   'label' => false,
                   'attr'  => [
                     'placeholder' => 'Mot de passe',
                     'class'       =>'form-control'
                   ]
               ])
               ->add('Inscription', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}