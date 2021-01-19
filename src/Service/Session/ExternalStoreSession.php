<?php


namespace App\Service\Session;


use App\Entity\Store;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ExternalStoreSession
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function setReference(Store $store): void
    {
        $reference = $this->session->get('store-reference');
        if (empty($reference) || $reference !== $store->getReference()){
            $this->session->set('store-reference', $store->getReference());
        }
    }
}