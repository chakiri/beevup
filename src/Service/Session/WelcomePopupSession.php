<?php

namespace App\Service\Session;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class WelcomePopupSession
{
    private $session;

    public function  __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function add(): ?array
    {
        //Set welcome popup session
        $popup = $this->session->get('popup');
        if (empty($popup)){
            $popup = $this->session->set('popup', ['isShowed' => true]);
        }

        return $popup;
    }
}