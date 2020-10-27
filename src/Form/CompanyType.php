<?php

namespace App\Form;

use App\Entity\Company;
use App\Entity\CompanyCategory;
use App\Entity\Store;
use App\Repository\StoreRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Security\Core\Security;


class CompanyType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('siret')
            ->add('introduction', TextType::class)
            ->add('category', EntityType::class, [
                'multiple'=>false,
                'class' => CompanyCategory::class,
                'choice_label' =>'name',
                'attr'  => [
                   'class'       =>'form-control company-category'
                ]
            ])
            ->add('otherCategory', TextType::class, [
               'attr'  => [
                    'placeholder' => 'Fleuriste',
                    'class'       =>'form-control',


                 ]
           ])
            ->add('email', EmailType::class, [
                'attr'  => [
                    'placeholder' => 'Email',
                    'class'       =>'form-control'
                 ]
            ])
            ->add('phone', IntegerType::class, [
                'required'=>false,
                'attr'  => [
                    'placeholder' => 'Téléphone',
                    'class'       =>'form-control'
                ]
            ])
            ->add('addressNumber', TextType::class, [
                'attr'  => [
                    'placeholder' => 'Numéro adresse',
                    'class'       =>'form-control'
                ]
            ])
            ->add('addressStreet', TextType::class, [
                'attr'  => [
                    'placeholder' => 'Rue',
                    'class'       =>'form-control'
                ]
            ])
            ->add('addressPostCode', TextType::class, [
                'attr'  => [
                    'placeholder' => 'Code Postal',
                    'class'       =>'form-control'
                ]
            ])
            ->add('city', TextType::class, [
                'attr'  => [
                    'placeholder' => 'Ville',
                    'class'       =>'form-control'
                ]
            ])
            ->add('country', CountryType::class, [
                'preferred_choices' => ['FR'],
                'placeholder' => 'Sélectionnez votre pays',
                'attr'  => [
                    'class'       =>'form-control'
                ]
            ])
            ->add('imageFile', FileType::class, [
                'required' => false,
                'attr'  => [
                    'class'       =>'form-control',
                    'placeholder' => 'Sélectionnez une image',
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/*'
                        ],
                        'mimeTypesMessage' => 'Ce type de fichier n\'est pas autorisé.Merci d\'en essayer un autre(jpeg, png, jpg)',
                    ])
                ]
        
            ])
            ->add('video', TextType::class, [
                'required' => false
            ])
            ->add('name', TextType::class, [
                'attr'  => [
                    'placeholder' => 'Entreprise',
                    'class'       =>'form-control'
                ]
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
               'attr'  => [
                    'placeholder' => 'Description',
                    'class'       =>'form-control entity-description',
                    'maxlength'   => 1500
                ]
            ])
            ->add('website', TextType::class, [
                'required' => false,
                'attr'  => [
                    'placeholder' => 'Site web',
                    'class'       =>'form-control'
                ]
            ])
        ;

        if ($this->security->isGranted('ROLE_SUPER_ADMIN')){
            $builder->add('store', EntityType::class, [
                'multiple'=>false,
                'class' => Store::class,
                'choice_label' =>'name',
            ]);
        }else{
            $builder->add('store', EntityType::class, [
                'multiple'=>false,
                'class' => Store::class,
                'choice_label' =>function (Store $store){
                    return $store->getAddressPostCode().' - '.$store->getName();
                },
                'query_builder' => function (StoreRepository $storeRepository){
                    return $storeRepository->getAllStoresOrderByPostalCode();
                }
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Company::class,
        ]);
    }
}
