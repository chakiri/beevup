<?php

namespace App\Form;

use App\Entity\Profile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProfileImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('imageFile', FileType::class, [
                'required' => false,
                'label' => 'Photo',

                'attr'  => [
                    'placeholder' => 'Sélectionnez une image',
                    'class'       =>'form-control form-imageFile',
                    'onChange'=>'previousImage()',
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '3072k',
                        'mimeTypes' => [
                            'image/*'
                        ],
                        'mimeTypesMessage' => 'Ce type de fichier n\'est pas autorisé.Merci d\'en essayer un autre(jpeg, png, jpg)',
                    ])
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
