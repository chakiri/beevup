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
        $ways = $this->getChoicesValues($options['data']);

        $builder
            ->add('way', ChoiceType::class, [
                'label' => false,
                'choices' => $ways,
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
            ->add('description', TextareaType::class, [
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ExpertBooking::class,
        ]);
    }

    private function getChoicesValues($optionsData)
    {
        $expertBooking = $optionsData;
        $wayExpertMeeting = $expertBooking->getExpertMeeting()->getWay();
        $ways = [];
        if (in_array('visio', $wayExpertMeeting)){
            $ways['En visio-conférence'] = 'visio';
        }
        if (in_array('company', $wayExpertMeeting)){
            $ways['En entreprise'] = 'company';
        }

        return $ways;
    }
}
