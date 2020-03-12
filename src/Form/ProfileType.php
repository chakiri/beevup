<?php

namespace App\Form;

use App\Entity\Profile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastname')
            ->add('firstname')
            ->add('gender', ChoiceType::class, [
                'choices' => [
                    'Femme' => 0,
                    'Homme' => 1,
                ],
            ])
            ->add('mobileNumber', TextType::class, [
                'required' => false,
                'attr'  => [
                    'placeholder' => 'Téléphone mobile'
                 ]
            ])
            ->add('phoneNumber', TextType::class, [
                'required' => false,
                'attr'  => [
                    'placeholder' => 'Téléphone fixe',
                ]
            ])
            ->add('function', ChoiceType::class, [
                'choices' => [
                    'Responsable marketing' => 0,
                    'Chef de projet' => 1,
                ],
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
