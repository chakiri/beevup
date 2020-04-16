<?php

namespace App\Form;

use App\Entity\Service;
use App\Entity\TypeService;
use App\Repository\TypeServiceRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Security\Core\Security;

class ServiceType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label'    => 'Titre',
                'attr'  => [
                    'placeholder' => 'Titre',
                    'class'       =>'form-control'
                ]

            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'placeholder' => 'Description',
                    'class'       =>'form-control'
                ],
            ])
            ->add('introduction', TextareaType::class, [
                'attr' => [
                    'placeholder' => 'Introduction',
                    'class'       =>'form-control'
                ],
            ])
            ->add('isPayant', CheckboxType::class, [
                'label_attr' => ['class' => 'switch-custom'],
                'required' => false,
                'label'    => 'Service Payant',
                'attr'     => [
                    'class' => 'custom-control custom-switch',
                ],
            ])
            ->add('price', TextType::class, [
                'label'    => 'Prix',
                'required' => false,
            ])
            ->add('imageFile', FileType::class, [
                'required' => false,
                'label' => 'Images',
                'attr'  => [
                    'placeholder' => 'Photo principale',
                    'class'       =>'form-control'
                ]
            ])
            ->add('imageFile1', FileType::class, [
                'required' => false,
                'label' => 'Images',
                'attr'  => [
                    'placeholder' => 'Photo secondaire 1',
                    'class'       =>'form-control'
                ]
            ])
            ->add('imageFile2', FileType::class, [
                'required' => false,
                'label' => 'Images',
                'attr'  => [
                    'placeholder' => 'Photo secondaire 2',
                    'class'       =>'form-control'
                ]
            ])
            ->add('imageFile3', FileType::class, [
                'required' => false,
                'label' => 'Images',
                'attr'  => [
                    'placeholder' => 'Photo secondaire 3',
                    'class'       =>'form-control'
                ]
            ])
        ;

        if ($this->security->isGranted('ROLE_SUPER_ADMIN')){
            $builder
                ->add('type', EntityType::class, [
                    'label' => 'Type de service',
                    'multiple'=>false,
                    'class' => TypeService::class,
                    'query_builder' => function (TypeServiceRepository $er) {
                        return $er->createQueryBuilder('t')
                            ->where('t.name = :val1')
                            ->orWhere('t.name = :val2')
                            ->setParameter('val1', 'plateform')
                            ->setParameter('val2', 'foreign');
                    },
                    'choice_label' => function ($type) {
                        if ($type->getName() == 'plateform') return 'plateforme';
                        elseif ($type->getName() == 'foreign') return 'externe';
                    }
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
        ]);
    }
}
