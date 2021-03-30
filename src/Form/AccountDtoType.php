<?php

namespace App\Form;

use App\Entity\CompanyCategory;
use App\Entity\UserFunction;
use App\Model\AccountDto;
use App\Repository\UserFunctionRepository;
use App\Service\Utility\AddressForm;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountDtoType extends AbstractType
{
    private $userFunctionRepository;

    private $addressForm;

    public function __construct(UserFunctionRepository $userFunctionRepository, AddressForm $addressForm)
    {
        $this->userFunctionRepository = $userFunctionRepository;
        $this->addressForm = $addressForm;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('gender', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    'Madame' => 0,
                    'Monsieur' => 1,
                ],
            ])
            ->add('lastname')
            ->add('firstname')
            ->add('siret')
            ->add('function', EntityType::class, [
                'class' => UserFunction::class,
                'multiple'=>false,
                'required' => true,
                'placeholder' => 'Saisissez votre fonction',
                'query_builder' => $this->userFunctionRepository->getListFunctionsUser(),
                'choice_label' =>'name'
            ])
            ->add('name')
            ->add('companyPhone')
            ->add('personalPhone')
            ->add('website')
            ->add('category', EntityType::class, [
                'multiple' => false,
                'required' => true,
                'class' => CompanyCategory::class,
                'choice_label' =>'name'
            ])
            ->add('saveAndAdd', SubmitType::class, ['label' => 'Créer votre premier service'])
            ->add('save', SubmitType::class, ['label' => 'Terminer'])
        ;

        //Add address Field to builder
        $this->addressForm->addField($builder);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AccountDto::class
        ]);
    }

}
