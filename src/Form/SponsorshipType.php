<?php

namespace App\Form;
use App\Entity\Sponsorship;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Security\Core\Security;

class SponsorshipType extends AbstractType
{
    private $security;
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
         $companyName = '';
         $userName ='';
         $emailSignature='';
         $userStore ='';
         if($this->security->getUser()->getStore() != null) {
             $userStore   = $this->security->getUser()->getStore()->getName();
         }

           if($this->security->getUser()->getProfile() != null){
               $userFirstName =  $this->security->getUser()->getProfile()->getFirstName();
               $userName =  $this->security->getUser()->getProfile()->getFirstName(). ' '.$this->security->getUser()->getProfile()->getLastName();
           }


            if($this->security->getUser()->getCompany() != null ) {
               $companyName = ' -'. $this->security->getUser()->getCompany()->getName();
           }
        $emailSignature = $userName.$companyName;
        $builder
            ->add(
         'emailsList', TextareaType::class, [
                'required' => true,
                'mapped' => false,
                'label' => 'Veuillez saisir l\'adress email des personnes que vous souhaitez inviter',
                'attr' => [
                    'cols' => 100,
                    'rows' => 5
                ]])

           ->add(
        'message', TextareaType::class, [
               'required' => false,
               'label' => 'Modifier votre message',
               'attr' => [
                   'rows'=>15,
                   'cols' => 100,
                   'data-email-footer'=>$emailSignature,
                   'data-store'=>$userStore


               ]


              ]) ;


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sponsorship::class,
        ]);
    }
}
