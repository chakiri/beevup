<?php


namespace App\Service;

use App\Repository\UserRepository;
use App\Repository\UserTypeRepository;

class ContactsHandler
{
    private $mailer;
    private $userRepository;
    private $userTypeRepository;

    public function __construct(Mailer $mailer, UserRepository $userRepository, UserTypeRepository $userTypeRepository)
    {
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
        $this->userTypeRepository = $userTypeRepository;
    }

    /**
     * Create or update Sendinblue contact attributes
     * @param $user
     */
    public function handleContactSendinBlueRegistartion($user)
    {
        if ($this->mailer->isContact($user->getEmail()) === false){
            $this->mailer->createContact($user->getEmail(), 2);
        }

        $userTypePatron = $this->userTypeRepository->findOneBy(['id'=> 2]);
        $advisor = $this->userRepository->findOneBy(['type' => $userTypePatron, 'store' => $user->getStore(), 'isValid' => 1]);
        if ($advisor) $advisorName = $advisor->getProfile()->getFirstname() . ' ' . $advisor->getProfile()->getLastname();
        else $advisorName = null;

        $attributes = [
            'RAISON_SOCIALE' => $user->getCompany()->getName(),
            'MAG_LIB' => $user->getStore()->getName(),
            'CONTACT_MAG' => $advisorName,
            'STATUT_CLIENT' => 3,
            'TITRE_CONTACT' => 'Conseiller Beev\'Up.fr',
            'PROFIL_COMPLET' => 0,
            'ENTREPRISE_COMPLET' => 0,
        ];
        $this->mailer->updateContact($user->getEmail(), $attributes);
    }

    /**
     * Update Sendinblue contact attributes
     * @param $user
     */
    public function handleContactSendinBlueCompleteProfile($user)
    {
        if ($this->mailer->isContact($user->getEmail()) !== false){
            $attributes = [
                'NOM' => $user->getProfile()->getLastname(),
                'PRENOM' => $user->getProfile()->getFirstname(),
                //'SMS' => $user->getProfile()->getMobileNumber(),
                'STATUT_CLIENT' => 2,
                'PROFIL_COMPLET' => 1,
            ];
            $this->mailer->updateContact($user->getEmail(), $attributes);
        }
    }

    /**
     * Update Sendinblue contact attributes
     * @param $user
     */
    public function handleContactSendinBlueCompleteCompany($user)
    {
        if ($this->mailer->isContact($user->getEmail()) !== false){
            $attributes = [
                'ADRESSE' => $user->getCompany()->getAddressNumber() . ' ' . $user->getCompany()->getAddressStreet() . ' ' . $user->getCompany()->getAddressPostCode(),
                'VILLE' => $user->getCompany()->getCity(),
                'STATUT_CLIENT' => 2,
                'ENTREPRISE_COMPLET' => 1,
            ];
            $this->mailer->updateContact($user->getEmail(), $attributes);
        }
    }
}