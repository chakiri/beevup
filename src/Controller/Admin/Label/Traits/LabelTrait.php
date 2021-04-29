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
        return $this->createdAt->format('d/m/Y H:II');
    }

    public function getStoreAppointmentFormat()
    {
        return $this->storeAppointment ? $this->storeAppointment->format('d/m/Y H:II') : null;
    }
}