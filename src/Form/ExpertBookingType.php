<?php

namespace App\Form;

use App\Entity\ExpertBooking;
use App\Entity\Slot;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExpertBookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isVisio', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'En visio-conférence' => true,
                    'En entreprise' => false,
                ],
                'multiple' => false
            ])
            /*->add('timeSlot', HiddenType::class)*/
            ->add('slot', EntityType::class, [
                'class' => Slot::class,
                'label' => false,
                'placeholder' => 'null',
                'choice_label' =>function (Slot $slot){
                    return $slot->getId();
                },
                'required' => false
            ])
            ->add('description', TextareaType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ExpertBooking::class,
        ]);
    }
}
