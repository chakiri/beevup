<?php


namespace App\Service\ExpertMeeting;


use App\Entity\ExpertBooking;
use Doctrine\ORM\EntityManagerInterface;

class HandleMeeting
{
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Function to archive passed expert booking
     */
    public function archive(array $expertsBooking): void
    {
        foreach ($expertsBooking as $expertBooking){
            $date = $expertBooking->getSlot()->getTimeSlot()->getDate()->format('d-m-Y');
            $time = $expertBooking->getSlot()->getStartTime()->format('H:i');

            //Instantiate object DateTime with slot date and time
            $dateTime = new \DateTime($date . ' ' . $time);

            $now = new \DateTime();

            //Add 40 minutes to now
            $dateTime->add(new \DateInterval('PT2H'));

            if ($dateTime < $now){
                $expertBooking->setStatus('archived');

                $this->manager->persist($expertBooking);
                $this->manager->flush();
            }
        }
    }

    /**
     * Function used in send mail to get all infos booking
     * @param ExpertBooking $expertBooking
     * @return array
     */
    public function getInfoBooking(ExpertBooking $expertBooking): array
    {
        return [
            'date' => $expertBooking->getSlot()->getTimeSlot()->getDate()->format('d/m/Y'),
            'time' => $expertBooking->getSlot()->getStartTime()->format('H:i'),
            'way' => $expertBooking->getWay() === 'visio' ? 'En visio-conférence' : 'A l\'adresse : ' .$expertBooking->getExpertMeeting()->getAddress(),
            'description' => $expertBooking->getDescription(),
            'name' => $expertBooking->getUser()->getProfile()->getFullName(),
            'company' => $expertBooking->getUser()->getCompany()->getName(),
            'phone' => $expertBooking->getUser()->getProfile()->getPhoneNumber(),
            'email' => $expertBooking->getUser()->getEmail(),
            'visioLink' => $expertBooking->getVideoLink(),
        ];
    }

    /**
     * Function used in send mail to get all infos meeting
     * @param ExpertBooking $expertBooking
     * @return array
     */
    public function getInfoMeeting(ExpertBooking $expertBooking): array
    {
        return [
            'name' => $expertBooking->getExpertMeeting()->getUser()->getProfile()->getFullName(),
            'company' => $expertBooking->getExpertMeeting()->getUser()->getCompany()->getName(),
            'phone' => $expertBooking->getExpertMeeting()->getUser()->getProfile()->getPhoneNumber(),
            'email' => $expertBooking->getExpertMeeting()->getUser()->getEmail(),
        ];
    }
}