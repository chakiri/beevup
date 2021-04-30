<?php


namespace App\Service\Mail;


use App\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DailyEmail
{
    private Mailer $mailer;
    private UrlGeneratorInterface $generator;

    public function __construct(Mailer $mailer, UrlGeneratorInterface $generator)
    {
        $this->mailer = $mailer;
        $this->generator = $generator;
    }

    /**
     * Send daily email
     * @param User $user
     * @param int $notificationNumber
     */
    public function send(User $user, $notificationNumber): void
    {
        $url = $this->generator->generate('chat_topic', ['name' => 'general-' . $user->getStore()->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL);

        if ($notificationNumber == 1) $message = "Vous avez 1 nouveau message non lu.";
        else $message = "Vous avez " . $notificationNumber . " nouveaux messages non lus.";

        $params = ['message' => $message, 'url' => $url];
        $this->mailer->sendEmailWithTemplate($user->getEmail(), $params, 'daily_chat');
    }
}