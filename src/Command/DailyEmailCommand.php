<?php

namespace App\Command;

use App\Controller\WebsocketController;
use App\Entity\MessageNotification;
use App\Repository\MessageNotificationRepository;
use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DailyEmailCommand extends Command
{
    protected static $defaultName = 'app:daily-email';
    private $notificationMessage;
    private $users;
    private $mailer;


    public function __construct(MessageNotificationRepository $messageNotificationRepository, UserRepository $userRepository, WebsocketController $websocketController, \Swift_Mailer $mailer )
    {
        parent::__construct(null);
        $this->notificationMessage = $messageNotificationRepository;
        $this->users =$userRepository;
        $this->websoketContoller = $websocketController;
        $this->mailer = $mailer;
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $users = $this->users->findBy(['isValid'=>true]);
        $notifications = $this->notificationMessage->findNotifications();
        foreach ($users as $user)
        {

            $notificationNumber = count($this->notificationMessage->findByUser($user));
            if($notificationNumber > 0) {
                $this->websoketContoller->sendDaillyEmail($user, $this->mailer, $notificationNumber);
            }

        }
        return 1;
    }
}
