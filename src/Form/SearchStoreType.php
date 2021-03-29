<?php

namespace App\Form;

use App\Repository\StoreRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SearchStoreType extends AbstractType
{
    private $storeRepository;

    public function __construct(StoreRepository $storeRepository)
    {
        $this->storeRepository = $storeRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('querySearch', TextType::class, [
                'label' => false,
                'required' => true,
                'mapped' => false,
                'attr'  => [
                    'placeholder' => 'Entrez vos mots clés',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'store' => null,
        ]);
    }
}
