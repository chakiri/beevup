<?php

namespace App\Form;

use App\Entity\Service;
use App\Entity\ServiceCategory;
use App\Entity\TypeService;
use App\Repository\TypeServiceRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;



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
                'label'    => 'Titre du service',
                'attr'  => [
                    'placeholder' => 'Ajouter un titre de service, par exemple Shooting, Photo, Manutention, Conseil marketing 1h',
                    'class'       =>'form-control input-textarea',
                     'maxlength' => 255
                ]

            ])
            ->add('description', TextareaType::class, [
                'label'    => 'Description du service',
                'attr' => [
                    'placeholder' => 'Ajouter une description de 5 à 10 lignes pour présenter votre service',
                    'class'       =>'form-control input-textarea entity-description',
                    'maxlength' => 1500,
                    'rows'=>5
                ],
            ])
            ->add('category', TextType::class, [
                'label'    => 'Catégorie du service'
            ])
            ->add('price', TextType::class, [
                'label'    => 'Prix HT',
                'required' => false,
            ])
            ->add('priceTTC', TextType::class, [
                'label'    => 'Prix TTC',
                'mapped' => false,
                'attr'=>[
                    'disabled'=>'disabled'
                ]
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
                'label'    => 'Offre exclusive pour les membres Beev\'Up',
                'attr'     => [
                    'class' => 'custom-control custom-switch',
                    'checked'=> $options['isOffer']
                ],
            ])
            ->add('discoveryContent', TextareaType::class, [
                'label'    => 'Description de l\'offre exclusive',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Exemple: 10% du prix, 20min de conseil supplémentaire, goodies offerts, ...',
                    'class'       =>'form-control'

                ],
            ])
            ->add('imageFile', FileType::class, [
                'required' => true,
                'attr'  => [
                    'class'       =>'form-control form-imageFile custom-input-file',
                    'onChange'=>'previousImage(event)',
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '2048k',
                        'mimeTypes' => [
                            'image/*'
                        ],
                        'mimeTypesMessage' => 'Ce type de fichier n\'est pas autorisé.Merci d\'en essayer un autre(jpeg, png, jpg)',
                    ])
                ]
            ])
            ->add('imageFile1', FileType::class, [
                'required' => false,
                'label' => 'Photo supplémentaire 1',
                'attr'  => [
                    'placeholder' => 'Photo secondaire 1',
                    'class'       =>'form-control custom-input-file',
                    'onChange'=>'previousImage(event)'
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '2048k',
                        'mimeTypes' => [
                            'image/*'
                        ],
                        'mimeTypesMessage' => 'Ce type de fichier n\'est pas autorisé.Merci d\'en essayer un autre(jpeg, png, jpg)',
                    ])
                ]
            ])
            ->add('imageFile2', FileType::class, [
                'required' => false,
                'label' => 'Photo supplémentaire 2',
                'attr'  => [
                    'placeholder' => 'Photo secondaire 2',
                    'class'       =>'form-control custom-input-file',
                    'onChange'=>'previousImage(event)'
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '2048k',
                        'mimeTypes' => [
                            'image/*'
                        ],
                        'mimeTypesMessage' => 'Ce type de fichier n\'est pas autorisé.Merci d\'en essayer un autre(jpeg, png, jpg)',
                    ])
                ]
            ])
            ->add('imageFile3', FileType::class, [
                'required' => false,
                'label' => 'Photo supplémentaire 3',
                'attr'  => [
                    'placeholder' => 'Photo secondaire 3',
                    'class'       =>'form-control custom-input-file',
                    'onChange'=>'previousImage(event)'
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '2048k',
                        'mimeTypes' => [
                            'image/*'
                        ],
                        'mimeTypesMessage' => 'Ce type de fichier n\'est pas autorisé.Merci d\'en essayer un autre(jpeg, png, jpg)',
                    ])
                ]
            ])
            ->add('toIndividuals', CheckboxType::class, [
                'label'    => 'Pour les particuliers',
                'required' => false
            ])
            ->add('toProfessionals', CheckboxType::class, [
            'label'    => 'Pour les pros',
            'required' => false

        ])
            ->add('vatRate', ChoiceType::class, [
                'label'    => 'TVA',
                'required' => false,
                 'choices'  => [
                    '2,1' => '2,1',
                    '5,5' => '5,5',
                    '10' => '10',
                    '20' => '20',
                ],

            ])
            ->add('unity', ChoiceType::class, [
                'label'    => 'Unité',
                'required' => false,
                'choices'  => [
                    'Heure' => 'Heure',
                    'Jour' => 'Jour',
                    'Semaine' => 'Semaine',
                    'Prestation' => 'Prestation',
                ],

            ])
            ->add('previousUrl', HiddenType::class, [
                'mapped'    => false,
                'attr'  => [
                    'value' => $options['previousPage']
                ],
            ])
           /* ->add('imgGallerie', HiddenType::class, [
                'mapped'    => false,
                'attr'  => [
                    'class' =>'img-gallery',
                      'value' =>'edit'
                ],
            ])
            ->add('imgGallerie1', HiddenType::class, [
                'mapped'    => false,
                'attr'  => [
                    'class' =>'img-gallery-1',
                    'value' => 'edit'
                ],
            ])
            ->add('imgGallerie2', HiddenType::class, [
                'mapped'    => false,
                'attr'  => [
                    'class' =>'img-gallery-2',
                    'value' => 'edit'

                ],
            ])
            ->add('imgGallerie3', HiddenType::class, [
                'mapped'    => false,
                'attr'  => [
                    'class' =>'img-gallery-3',
                    'value' => 'edit'
                ],
            ])*/
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
                            ->orWhere('t.name = :val3')
                            ->setParameter('val1', 'plateform')
                            ->setParameter('val2', 'foreign')
                            ->setParameter('val3', 'model')
                            ;
                    },
                    'choice_label' => function ($type) {
                        if ($type->getName() == 'plateform') return 'plateforme';
                        elseif ($type->getName() == 'foreign') return 'externe';
                        elseif ($type->getName() == 'model') return 'modèle';
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
            'previousPage' => null,
            'validation_groups' => function (FormInterface $form) {
                $data = $form->getData();

                //If checkbox isDiscovery is checked apply group validation "isDiscovery"
                if ($data->getIsDiscovery() === true) {
                    return array('Default', 'isDiscovery');
                }

                return array('Default');
            },
        ]);
    }


}
