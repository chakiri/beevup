<?php

namespace App\Form;

use App\Entity\Service;
use App\Entity\ServiceCategory;
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
                    'class'       =>'form-control input-textarea',
                     'maxlength' => 255
                ]

            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'placeholder' => 'Description',
                    'class'       =>'form-control input-textarea',
                    'maxlength' => 1500
                ],
            ])
            ->add('introduction', TextareaType::class, [
                'attr' => [
                    'placeholder' => 'Introduction',
                    'class'       =>'form-control input-textarea',
                    'maxlength' => 255
                ],
            ])
            ->add('category', EntityType::class, [
                'placeholder' => 'Choisir catégorie',
                'label' => 'Catégorie de service',
                'multiple'=>false,
                'class' => ServiceCategory::class,
                'choice_label' => 'name'
            ])
            ->add('price', TextType::class, [
                'label'    => 'Prix',
                'required' => false,
            ])
            ->add('isQuote', CheckboxType::class, [
                'label_attr' => ['class' => 'switch-custom'],
                'required' => false,
                'label'    => 'Sur devis',
                'attr'     => [
                    'class' => 'custom-control custom-switch',
                ],
            ])
            ->add('isDiscovery', CheckboxType::class, [
                'label_attr' => ['class' => 'switch-custom'],
                'required' => false,
                'label'    => 'Proposer une offre exclusive',
                'attr'     => [
                    'class' => 'custom-control custom-switch',
                    'checked'=> $options['isOffer']
                ],
            ])
            ->add('discoveryContent', TextareaType::class, [
                'label'    => 'Description de l\'offre exclusive',
                'required' => false,
                'attr' => [
                    'placeholder' => 'En quelques mots, décrivez votre offre exclusive du service',
                    'class'       =>'form-control'
                ],
            ])
            ->add('imageFile', FileType::class, [
                'required' => false,
                'label' => 'Photo principale',
                'attr'  => [
                    'placeholder' => 'Photo principale',
                    'class'       =>'form-control'
                ]
            ])
            ->add('imageFile1', FileType::class, [
                'required' => false,
                'label' => 'Photo secondaire 1',
                'attr'  => [
                    'placeholder' => 'Photo secondaire 1',
                    'class'       =>'form-control'
                ]
            ])
            ->add('imageFile2', FileType::class, [
                'required' => false,
                'label' => 'Photo secondaire 2',
                'attr'  => [
                    'placeholder' => 'Photo secondaire 2',
                    'class'       =>'form-control'
                ]
            ])
            ->add('imageFile3', FileType::class, [
                'required' => false,
                'label' => 'Photo secondaire 3',
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
            'isOffer' => null,
        ]);
    }
}
