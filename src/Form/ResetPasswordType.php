<?php

namespace App\Form;

use App\Entity\Service;
use App\Entity\Store;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('password', PasswordType::class, [
                'attr'  => [
                    'placeholder' => 'Mot de passe'
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
            'isNewAccount' => User::class,
            'csrf_protection' => false,
        ]);
    }
}
