<?php

namespace App\Controller\Admin\Company\Traits;

/**
 * Trait CompanyTrait is used in Company entity by administarion
 */
trait CompanyTrait
{
    /**
     * get company administrator
     */
    public function getCompanyAdministrator() {
        foreach ($this->users as $user){
            if ( $user->getType()->getId() === 3) return $user;
        }
    }

    public function getCompanyAdministratorFullName() {
        foreach ($this->users as $user){
            if ( $user->getType()->getId() == 3)
                if($user->getProfile()->getFirstName() != '')
                    return $user->getProfile()->getFirstName().' '.$user->getProfile()->getLastname();
                else return 'N/C';
        }
    }

    public function getEmailAdministrator() {
        foreach ($this->users as $user){
            if ( $user->getType()->getId() == 3)
                return $user->getEmail();
        }
    }

    public function getServiceNumber(){
        return count($this->services);
    }

    public function isProfileAdminCompleted(){
        foreach ($this->users as $user){
            if ( $user->getType()->getId() == 3)
                return ( $user->getProfile()->getIsCompleted()) ? 'Oui' : 'Non';
        }
    }

    public function isLogoAdminCompleted(){
        $isLogoAdminDefined = null;
        foreach ($this->users as $user){
            if ( $user->getType()->getId() === 3) {
                $isLogoAdminDefined = ($user->getProfile()->getFileName() != '') ? 'Oui' : 'Non';
                return $isLogoAdminDefined;
            }
        }
    }

    public function isLogoDefined(){
        return $isLogoDefined = ($this->getFilename() !='') ? 'Oui' : 'Non';
    }

    //Add scores of all users company
    public function getScore()
    {
        $score = 0;
        foreach ($this->users as $user){
            if($user->getScore())
                $score += $user->getScore()->getPoints();
        }
        return $score;
    }

    public function isLabeled()
    {
        if ($this->label){
            return $this->label->isLabeled();
        }
        return false;
    }

    public function getCreatedAtFromUsers(){
        foreach ($this->users as $user){
            if ( $user->getType()->getId() == 3) {
                if($user->getCreatedAt()) {
                    return $user->getCreatedAt();
                }
            }
        }
    }

    public function getCreatedAtFormat(){
        return $this->getCreatedAt() ? $this->getCreatedAt()->format('d/m/Y') : null;
    }

    public function getFullAddress()
    {
        return $this->getAddressNumber() . ' ' . $this->getAddressStreet() . ' ' . $this->getAddressPostCode() . ', ' . $this->getCity();
    }
}
