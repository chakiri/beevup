<?php

namespace App\Service\Session;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CookieAcceptedSession
{
    private $session;

    public function  __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function addSession()
    {
        //Set accepted cookie in session
        if (!$this->session->get('cookie')){

            $this->session->set('cookie', ['isAccepted' => true]);

        }

        return $this->session->get('cookie');
    }

    public function addCookie($request): void
    {
        //Set accepted cookie in Cookie
        if(!$request->cookies->has('cguAccepted')){
            $cookie = new Cookie('cguAccepted', true, time() + (24 * 60 * 60) ); //Expire 24h

            $response = new Response();
            $response->headers->setCookie($cookie);
            $response->send();
        }
    }
}