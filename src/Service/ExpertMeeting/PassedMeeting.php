<?php


namespace App\Service\ExpertMeeting;


use Doctrine\ORM\EntityManagerInterface;

class PassedMeeting
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

            if ($now > $dateTime){
                $expertBooking->setStatus('archived');

                $this->manager->persist($expertBooking);
                $this->manager->flush();
            }
        }
    }
}