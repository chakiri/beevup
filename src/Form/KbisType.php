<?php

namespace App\Form;

use App\Entity\Label;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class KbisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('kbisFile', FileType::class, [
                'required' => true,
                'label' => 'Votre fichier Kbis',
                'attr'  => [
                    'placeholder' => 'Sélectionnez un fichier',
                    'class'       =>'form-control form-imageFile',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Label::class,
        ]);
    }
}
