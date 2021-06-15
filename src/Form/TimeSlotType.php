<?php

namespace App\Form;

use App\Entity\TimeSlot;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimeSlotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateType::class, [
                    'widget' => 'single_text',
                    'attr' => ['class' => 'datepicker js-datepicker'],
                    'html5' => false,
                    'format' => 'dd/MM/yyyy'
                ]
            )
            ->add('startTime', TimeType::class, [
                'minutes' => [0, 15, 30, 45],
                'hours' => [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20],
                'attr' => ['class' => 'js-startTime'],
            ])
            ->add('endTime', TimeType::class, [
                'minutes' => [0, 15, 30, 45],
                'hours' => [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20],
                'attr' => ['class' => 'js-endTime'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TimeSlot::class,
        ]);
    }
}
