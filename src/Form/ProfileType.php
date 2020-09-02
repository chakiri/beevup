<?php

namespace App\Form;

use App\Entity\Profile;
use App\Entity\Company;
use App\Entity\UserFunction;
use App\Repository\TypeServiceRepository;
use App\Repository\UserFunctionRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Security\Core\Security;


class ProfileType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
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
                    'Femme' => 0,
                    'Homme' => 1,
                ],
            ])
            ->add('introduction', TextareaType::class, [
                'label' => 'Introduction',
                'required' => false,
                'attr'  => [
                    'placeholder' => 'Intro ...',
                    'maxlength' => 255
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
                'label' => 'Fonction',
                'multiple'=>false,
                'required' => true,
                'placeholder' => 'Saisissez votre fonction',
                'class' => UserFunction::class,
                'query_builder' => function (UserFunctionRepository $er) {
                    $user = $this->security->getUser();
                if($user->getCompany() != null) {
                    return $er->createQueryBuilder('t')
                        ->where('t.relatedTo =  :val1')
                        ->setParameter('val1', 'Company')
                        ->orderBy('t.name', 'ASC');

                }
                else {
                    return $er->createQueryBuilder('t')
                        ->where('t.relatedTo  =  :val1')
                        ->setParameter('val1', 'Store')
                        ->orderBy('t.name', 'ASC');
                }
                },
                'choice_label' =>'name'
            ])
            ->add('imageFile', FileType::class, [
                'required' => false,
                'label' => 'Photo',
                'attr'  => [
                    'placeholder' => 'Photo',
                    'class'       =>'form-control'
                ]
            ])
            ->add('addressPostCode', TextType::class, [
                'required' => true,
                'label' => 'Code postal',
                'attr'  => [
                    'placeholder' => 'Code postal',
                     'id'         => 'addressPostCode'
                ]
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
