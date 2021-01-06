<?php


namespace App\Service\Session;


use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ExternalStoreSession
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function setReference($request): void
    {
        $reference = $this->session->get('store-reference');
        if (empty($reference) || $reference !== $request->get('reference')){
            $this->session->set('store-reference', $request->get('reference'));
        }
    }
}