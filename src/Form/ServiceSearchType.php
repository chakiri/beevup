<?php

namespace App\Form;

use App\Entity\ServiceCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ServiceSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('query', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Trouvez le service qu\'il vous faut !',
                ],
            ])
            ->add('category', EntityType::class, [
                'class' => ServiceCategory::class,
                'placeholder' => 'Choisir categorie',
                'multiple' => false,
                'required' => false
            ])
            ->add('isDiscovery', CheckboxType::class, [
                'label_attr' => ['class' => 'checkbox-custom'],
                'required' => false,
                'label'    => 'Offres spéciales',
                'attr'     => [
                    'class' => 'custom-control custom-checkbox',
                ],
            ])
        ;
    }

}
