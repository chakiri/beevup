<?php

namespace App\Form;

use App\Entity\ExpertMeeting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExpertMeetingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('expertise')
            ->add('isVisio')
            ->add('isInCompany')
            ->add('address')
            ->add('timeSlots', CollectionType::class, [
                'entry_type' => TimeSlotType::class,
                'allow_add' => true,
                'by_reference' => false,
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
