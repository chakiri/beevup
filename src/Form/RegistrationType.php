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
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('company', InscriptionCompanyType::class, [
            'label' => false,
         ])
            ->add('name', TextType::class, [

                'mapped'=>false,
                   'attr'  => [
                   'placeholder' => 'Raison sociale (Société) ou Prénom Nom (Indépendant)',
                   'class'       =>'form-control'
                ]
            ])
             ->add('store', EntityType::class, [

                 'multiple'=>false,
                 'placeholder' => 'Votre magasin Bureau Vallée le plus proche',
                 'class' => Store::class,
                'choice_label' =>'name'

            ])
            ->add('email', EmailType::class, [

                   'attr'  => [
                   'placeholder' => 'Email',
                   'class'       =>'form-control'
                   ]
               ])
            ->add('password', PasswordType::class, [

                   'attr'  => [
                     'placeholder' => 'Mot de passe',
                     'class'       =>'form-control'
                   ]
               ])
            ->add('acceptConditions', CheckboxType::class, [
                'mapped'=>false,
                'label'    => 'J\'accepte',
                'required' => false,
                'attr'  => [
                   'class'       =>'accpet-condition'
                ]
            ]);
        ;

    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
