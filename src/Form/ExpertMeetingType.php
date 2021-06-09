<?php

namespace App\Form;

use App\Entity\ExpertMeeting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExpertMeetingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('expertise')
            ->add('way', ChoiceType::class, [
                'label' => 'Vous acceptez :',
                'choices' => [
                    'En visio-conférence' => 'visio',
                    'En entreprise' => 'company'
                ],
                'expanded'  => true,
                'multiple'  => true,
            ])
            ->add('address')
            ->add('breakTime', ChoiceType::class, [
                'choices' => [
                    '10 min' => 10,
                    '20 min' => 20,
                    '30 min' => 30,
                ]
            ])
            ->add('timeSlots', CollectionType::class, [
                'entry_type' => TimeSlotType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
                'delete_empty' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ExpertMeeting::class,
        ]);
    }
}
