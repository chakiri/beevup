<?php

namespace App\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Twig\Environment;

class MaintenanceListener
{
    private $container, $maintenance, $ipAuthorized, $twig, $event;

    public function __construct($maintenance, ContainerInterface $container,  Environment $twig)
    {
        $this->container = $container;
        $this->maintenance = $maintenance["statut"];
        $this->ipAuthorized = $maintenance["ipAuthorized"];
        $this->twig = $twig;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $maintenance = $this->maintenance ? $this->maintenance : false;
        $currentIP = $_SERVER['REMOTE_ADDR'];
        if ($maintenance AND !in_array($currentIP, $this->ipAuthorized)) {
            // We load our maintenance template

            $template =  $this->twig->render('maintenance/maintenance.html.twig');
            // We send our response with a 503 response code (service unavailable)
            $event->setResponse(new Response($template, 503));
            $event->stopPropagation();
        }

    }
}