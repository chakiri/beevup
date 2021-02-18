<?php

namespace App\Service\Mail;

class ContactsHandler
{
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Create or update Sendinblue contact attributes
     * @param $user
     */
    public function handleContactSendinBlueRegistartion($user)
    {
        if ($this->mailer->isContact($user->getEmail()) === false){
            $this->mailer->createContact($user->getEmail(), $this->getContactsListId());
        }

        if ($advisor = $user->getStore()->getDefaultAdviser()) $advisorName = $advisor->getProfile()->getFirstname() . ' ' . $advisor->getProfile()->getLastname();

        $attributes = [
            'RAISON_SOCIALE' => $user->getCompany()->getName(),
            'MAG_LIB' => $user->getStore()->getName(),
            'CD_MAGASIN' => $user->getStore()->getReference(),
            'CONTACT_MAG' => $advisorName ?? null,
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

    /**
     * Get id of contacts list Sendinblue
     */
    private function getContactsListId(): int
    {
        if ($_ENV['APP_ENV'] === 'dev' || $_ENV['APP_ENV'] === 'test') {
            $contactsListId = 2;
        }elseif ($_ENV['APP_ENV'] === 'prod') {
            $contactsListId = 17;
        }

        return $contactsListId;
    }
}