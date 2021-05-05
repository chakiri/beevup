<?php

namespace App\Controller\Admin\Label\Traits;

trait LabelTrait
{
    public function isKbisValid(): bool
    {
        return $this->kbisStatus === 'isValid';
    }

    public function getSiret()
    {
        return $this->company->getSiret();
    }

    public function getCreatedAtFormat()
    {
        return $this->createdAt->format('d/m/Y H:i');
    }

    public function getStoreAppointmentFormat()
    {
        return $this->storeAppointment ? $this->storeAppointment->format('d/m/Y H:i') : null;
    }

    public function getContactNameAdmin()
    {
        return $this->company->getCompanyAdministratorFullName();
    }

    public function getContactName()
    {
        return $this->company->getName();
    }

    public function getContactEmail()
    {
        return $this->company->getCompanyAdministrator()->getEmail();
    }

    public function getContactAddress()
    {
        return $this->company->getFullAddress();
    }

    public function getContactPhone()
    {
        return $this->company->getPhone();
    }
}