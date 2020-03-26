<?php

namespace App\Form;

use App\Entity\Recommandation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class RecommandationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('message', TextareaType::class, [
            'label' => false,
               'attr'  => [
               'placeholder' => 'Message',
               'class'       =>'form-control form-message'
               ]
           ])
           ->add('companyId', HiddenType::class, [
            'mapped' => false,
            'attr'  => [
                'class'       =>'form-company'
                ]
        ])
            ->add('serviceId', HiddenType::class, [
                'mapped' => false,
                'attr'  => [
                    'class'       =>'form-service'
                    ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Recommandation::class,
        ]);
    }
}
