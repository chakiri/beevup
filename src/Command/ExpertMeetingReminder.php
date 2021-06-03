<?php


namespace App\Command;

use App\Service\ExpertMeeting\HandleMeeting;
use App\Service\Mail\Mailer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Repository\ExpertBookingRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ExpertMeetingReminder extends Command
{
    protected static $defaultName = 'app:expert-meeting-reminder';
    private ExpertBookingRepository $expertBookingRepository;
    private HandleMeeting $handleMeeting;
    private Mailer $mailer;
    private UrlGeneratorInterface $generator;


    public function __construct(ExpertBookingRepository $expertBookingRepository, HandleMeeting $handleMeeting, Mailer $mailer, UrlGeneratorInterface $generator)
    {
        parent::__construct(null);
        $this->expertBookingRepository = $expertBookingRepository;
        $this->handleMeeting = $handleMeeting;
        $this->mailer = $mailer;
        $this->generator = $generator;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //Get 1 hour after now
        $dateTimeAfter = new \DateTime('now +1 hours');
        //Get 1 hour before now
        $dateTimeBefore = new \DateTime('now -1 hours');

        $expertsBookingPlanned = $this->expertBookingRepository->findComingMeetings($dateTimeAfter);
        $expertsBookingPassed = $this->expertBookingRepository->findComingMeetings($dateTimeBefore);

        foreach ($expertsBookingPlanned as $expertsBooking){
            $params = [
                'booking' => $this->handleMeeting->getInfoBooking($expertsBooking),
                'meeting' => $this->handleMeeting->getInfoMeeting($expertsBooking),
                'url' => $this->generator->generate('expert_booking_list', ['status' => 'confirmed'], UrlGeneratorInterface::ABSOLUTE_URL)
            ];
            //Send reminder email to expert
            $this->mailer->sendEmailWithTemplate($expertsBooking->getExpertMeeting()->getUser()->getEmail(), $params, 'expert_booking_reminder_expert');
            //Send reminder email to user
            $this->mailer->sendEmailWithTemplate($expertsBooking->getUser()->getEmail(), $params, 'expert_booking_reminder_user');
        }

        foreach ($expertsBookingPassed as $expertsBooking){
            $params = [
                'booking' => $this->handleMeeting->getInfoBooking($expertsBooking),
                'meeting' => $this->handleMeeting->getInfoMeeting($expertsBooking)
            ];
            //Send reminder email to expert
            $this->mailer->sendEmailWithTemplate($expertsBooking->getExpertMeeting()->getUser()->getEmail(), $params, 'expert_booking_feedback_expert');
            //Send reminder email to user
            $this->mailer->sendEmailWithTemplate($expertsBooking->getUser()->getEmail(), $params, 'expert_booking_feedback_user');
        }

        return 1;
    }
}