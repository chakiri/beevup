<?php

namespace App\Form;

use App\Entity\Search;
use App\Entity\CompanyCategory;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => false,
                'required' => false,
                'attr'  => [
                    'placeholder' => 'Entrez vos mots clés',
                    'class'=>'btn-radius'
                ]
            ])
            ->add('type', ChoiceType::class, [
                'label'=>false,
                'attr'  => [
                    'class'=>'btn-radius'
                ],
                'choices'  => [
                    'Entreprise' => 'company',
                    'Personnes' => 'users'

                ],
            ])
           /* ->add('category', EntityType::class, [
                'label' => false,
                'required' => false,
                'placeholder' => 'Catégorie d\'entreprise',
                'attr'  => [
                    'placeholder' => 'Catégories d\'entreprise',
                    'class'=>'btn-radius',
                    'disabled'=> true
                ],
                'query_builder' => function (CategoryRepository $categoryRepository) {
                        return $categoryRepository->createQueryBuilder('a');
                },
                'class' => CompanyCategory::class,
                'choice_label' =>'name'

            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
        ]);
    }
}
