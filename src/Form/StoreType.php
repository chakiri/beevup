<?php

namespace App\Form;

use App\Entity\Store;
use App\Entity\UserFunction;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\UserTypeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StoreType extends AbstractType
{
    private $userRepository;
    private $userTypeRepository;


    public function __construct(UserRepository $userRepository, UserTypeRepository $userTypeRepository)
    {
        $this->userRepository = $userRepository;
        $this->userTypeRepository =$userTypeRepository;


    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $users = $this->userRepository->findAdvisersOfStore($builder->getData(), $this->userTypeRepository->findBy(['name'=>'conseiller_magasin']));
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'attr'  => [
                    'placeholder' => 'Nom',
                    'class'       =>'form-control'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr'  => [
                    'placeholder' => 'Email',
                    'class'       =>'form-control'
                ]
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
                'attr'  => [
                    'placeholder' => 'téléphone',
                    'class'       =>'form-control'
                ]
            ])
            ->add('addressNumber', IntegerType::class, [
                'label' => 'Numéro de rue',
                'attr'  => [
                    'placeholder' => 'Numéro de rue',
                    'class'       =>'form-control'
                ]
            ])
            ->add('addressStreet', TextType::class, [
                'label' => 'Rue',
                'attr'  => [
                    'placeholder' => 'Rue',
                    'class'       =>'form-control'
                ]
            ])
            ->add('addressPostCode', TextType::class, [
                'label' => 'Code postal',
                'attr'  => [
                    'placeholder' => 'Code postal',
                    'class'       =>'form-control'
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'attr'  => [
                    'placeholder' => 'Ville',
                    'class'       =>'form-control'
                ]
            ])
            ->add('country', CountryType::class, [
                'label' => 'Pays',
                'attr'  => [
                    'placeholder' => 'Pays',
                    'class'       =>'form-control'
                ]
            ])
            ->add('imageFile', FileType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Photo',
                'attr'  => [
                    'placeholder' => 'Photo',
                    'class'       =>'form-control'
                ]
            ])
            ->add('introduction', TextareaType::class, [
                'label' => 'Introduction',
                'attr'  => [
                    'placeholder' => 'Introduction',
                    'class'       =>'form-control'
                    ]
                ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr'  => [
                    'placeholder' => 'Description',
                    'class'       =>'form-control'
                ]
            ])

            ->add('defaultAdviser', EntityType::class, [
            'label' => 'conseiller',
            'multiple'=>false,
            'required' => true,
            'placeholder' => '-Séléctionner-',
            'class' => User::class,
            'choices' =>$users,
           // 'choice_label' =>'profile.firstname',
                'choice_label'  => function ($users) {
                    return $users->getProfile()->getFirstName().' '.$users->getProfile()->getLastname();
                }

        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Store::class,
        ]);
    }
}
