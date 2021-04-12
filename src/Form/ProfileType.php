<?php

namespace App\Form;

use App\Entity\Profile;
use App\Entity\UserFunction;
use App\Repository\UserFunctionRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Security\Core\Security;


class ProfileType extends AbstractType
{

    private $userFunctionRepository;

    public function __construct(Security $security, UserFunctionRepository $userFunctionRepository)
    {
        $this->userFunctionRepository = $userFunctionRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'attr'  => [
                    'placeholder' => 'Nom',
                    'class'       =>'form-control'
                 ]
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'attr'  => [
                    'placeholder' => 'Prénom',
                    'class'       =>'form-control'
                 ]
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Civilité',
                'choices' => [
                    'Madame' => 0,
                    'Monsieur' => 1,
                ],
            ])
            ->add('introduction', TextareaType::class, [
                'label' => 'Introduction',
                'required' => false,
                'attr'  => [
                    'placeholder' => 'Intro ...',
                    'maxlength' => 1500
                 ]
            ])
            ->add('mobileNumber', TextType::class, [
                'label' => 'Téléphone',
                'attr'  => [
                    'placeholder' => 'Téléphone mobile'
                 ]
            ])
            ->add('phoneNumber', TextType::class, [
                'required' => false,
                'label' => 'Téléphone fixe',
                'attr'  => [
                    'placeholder' => 'Téléphone fixe',
                ]
            ])
            ->add('function', EntityType::class, [
                'class' => UserFunction::class,
                'multiple'=>false,
                'required' => true,
                'label' => 'Fonction dans l\'entreprise',
                'placeholder' => 'Saisissez votre fonction',
                'query_builder' => $this->userFunctionRepository->getListFunctionsUser(),
                'choice_label' =>'name'
            ])

            ->add('jobTitle', TextType::class, [
                'required' => false,
                'label' => 'Titre',
                'attr'  => [
                    'placeholder' => 'Titre',
                    'id'         => 'Titre'
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
