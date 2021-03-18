<?php

namespace App\Service\Utility;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AddressForm
{
    /**
     * Add address filed and hide other fields in form
     * @param $builder
     * @return mixed
     */
    public function addField($builder)
    {
        $builder
            ->add('address', TextType::class, [
                'mapped' => false,
                'required' => true,
                'attr'  => [
                    'placeholder'       => 'Adresse',
                    'class'             =>'form-control',
                ]
            ])
            ->add('addressNumber', HiddenType::class, [
                'attr'  => [
                    'placeholder' => 'Numéro adresse',
                    'class'       =>'form-control',
                ]
            ])
            ->add('addressStreet', HiddenType::class, [
                'attr'  => [
                    'class' =>'form-control',
                ]
            ])
            ->add('addressPostCode', HiddenType::class, [
                'attr'  => [
                    'class' =>'form-control'
                ]
            ])
            ->add('city', HiddenType::class, [
                'attr'  => [
                    'class' =>'form-control'
                ]
            ])
            ->add('country', HiddenType::class, [
                'attr'  => [
                    'class' =>'form-control'
                ]
            ])
        ;

        return $builder;
    }
}