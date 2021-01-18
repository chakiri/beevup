<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Store;
use App\Repository\StoreRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('company', InscriptionCompanyType::class, [
            'label' => false,
         ])

            ->add('get_siret_from_api', CheckboxType::class, [
                'label'    => 'Recherchez votre SIRET',
                'required' => false,
                'mapped'   => false,
            ])
            ->add('name', TextType::class, [

                'mapped'=>false,
                   'attr'  => [
                   'placeholder' => 'Raison sociale ou Nom',
                   'class'       =>'form-control'
                ]
            ])
             ->add('store', EntityType::class, [
                 'multiple'=>false,
                 'placeholder' => 'Choisissez votre communauté locale',
                 'class' => Store::class,
                 'choice_label' =>function (Store $store){
                return $store->getAddressPostCode().' - '.$store->getName();
                 },
                 'query_builder' => function (StoreRepository $storeRepository){
                    return $storeRepository->getAllStoresOrderByPostalCode();
                 }
            ])
            ->add('email', EmailType::class, [

                   'attr'  => [
                   'placeholder' => 'Email',
                   'class'       =>'form-control'
                   ]
               ])
            ->add('password', PasswordType::class, [

                   'attr'  => [
                     'placeholder' => 'Mot de passe',
                     'class'       =>'form-control'
                   ]
               ])
            ->add('acceptConditions', CheckboxType::class, [
                'mapped'=>false,
                'label'    => 'J\'accepte',
                'required' => false,
                'attr'  => [
                   'class'       =>'accpet-condition'
                ]
            ])
            ->add('addressNumber', HiddenType::class, [
                'mapped' => false,
                'attr'  => [
                    'placeholder' => 'Numéro adresse',
                    'class'       =>'form-control',

                ]
            ])

            ->add('addressStreet', HiddenType::class, [
                'mapped' => false,
                'attr'  => [
                    'class'             =>'form-control',
                ]
            ])
            ->add('addressPostCode', HiddenType::class, [
                'mapped' => false,
                'attr'  => [

                    'class'       =>'form-control'
                ]
            ])
            ->add('city', HiddenType::class, [
                'mapped' => false,
                'attr'  => [

                    'class'       =>'form-control'
                ]
            ])
            ->add('country', HiddenType::class, [
                'mapped' => false,
                'attr'  => [
                    'class'       =>'form-control'
                ]
            ])
        ;
        ;

    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
