<?php

namespace App\Util;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class RequestProcessor
{
    /**
     * @var RequestStack
     */
    protected $request;

    /**
     * RequestProcessor constructor.
     * @param RequestStack $request
     */
    public function __construct(RequestStack $request, Security $security)
    {
        $this->request = $request;
        $this->security = $security;
    }

    /**
     * @param array $record
     * @return array
     */
    public function processRecord(array $record)
    {
        $req = $this->request->getCurrentRequest();
        $record['extra']['client_ip']       = $req->getClientIp();
        $record['extra']['client_port']     = $req->getPort();
        $record['extra']['uri']             = $req->getUri();
        $record['extra']['query_string']    = $req->getQueryString();
        $record['extra']['method']          = $req->getMethod();
        $record['extra']['request']         = $req->request->all();
        $record['extra']['user_email']      = $this->security->getUser()->getEmail();

        return $record;
    }
}