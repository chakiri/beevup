<?php

namespace App\Form;

use App\Entity\Company;
use App\Entity\CompanyCategory;
use App\Entity\Store;
use App\Repository\StoreRepository;
use App\Service\Utility\AddressForm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Security\Core\Security;


class CompanyType extends AbstractType
{
    private $security;

    private $addressForm;

    public function __construct(Security $security, AddressForm $addressForm)
    {
        $this->security = $security;
        $this->addressForm = $addressForm;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('siret')
            ->add('introduction', TextType::class, [
                'attr'  => [
                    'maxlength'   => 500
                ]
            ])
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
            ->add('phone', TextType::class, [
                'required'=>false,
                'attr'  => [
                    'placeholder' => 'Téléphone',
                    'class'       =>'form-control'
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
                    'maxlength'   => 1500,
                   'rows'=>5
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

        //Add address Field to builder
        $this->addressForm->addField($builder);

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
