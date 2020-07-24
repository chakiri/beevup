<?php

namespace App\Service\Session;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CookieAccepted
{
    private $session;

    public function  __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function add()
    {
        //Set accepted cookie in session
        $this->session->get('cookie');
        if (!$this->session->get('cookie')){

            $this->session->set('cookie', ['isAccepted' => true]);

        }

        return $this->session->get('cookie');
    }
}