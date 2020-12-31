<?php

namespace App\Form;

use App\Entity\BeContacted;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BeContactedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class, [
                'label' => 'Votre adresse mail',
                'attr' => [
                    'placeholder' => 'xyz@bureau-vallee.com',
                    'class' => 'form-control'
                ]
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
                'attr' => [
                    'placeholder' => 'Votre numéro de téléphone',
                    'class' => 'form-control'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Motif',
                'attr'  => [
                    'placeholder' => 'Décrivez en quelques mots l\'objet de votre demande',
                    'class'       =>'form-control form-message textArea-min-height'
                ]
            ])
            ->add('acceptConditions', CheckboxType::class, [
                'mapped'=>false,
                'label'    => 'J\'accepte',
                'required' => false,
                'attr'  => [
                    'class'       =>'accpet-condition'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BeContacted::class,
        ]);
    }
}
