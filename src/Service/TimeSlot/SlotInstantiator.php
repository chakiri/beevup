<?php


namespace App\Service\TimeSlot;


use App\Entity\Slot;
use App\Repository\ExpertBookingRepository;
use App\Repository\SlotRepository;
use Doctrine\ORM\EntityManagerInterface;

class SlotInstantiator
{
    private EntityManagerInterface $manager;
    private SlotRepository $slotRepository;
    private ExpertBookingRepository $expertBookingRepository;

    public function __construct(EntityManagerInterface $manager, SlotRepository $slotRepository, ExpertBookingRepository $expertBookingRepository)
    {
        $this->manager = $manager;
        $this->slotRepository = $slotRepository;
        $this->expertBookingRepository = $expertBookingRepository;
    }

    /**
     * Function to instantiate slot from time slots
     */
    public function instantiate($breakTime, $timeSlots)
    {
        $duration = 30;
        $totalDuration = $duration + $breakTime;
        foreach ($timeSlots as $timeSlot) {
            //Clear slots attached to timeSlot
            $this->clearSlots($timeSlot);

            //Instantiate slots only for timeSlots deleted false
            if ($timeSlot->getIsDeleted() == false){
                $time = $timeSlot->getStartTime();
                $limit = $timeSlot->getEndTime()->sub(new \DateInterval('PT' . $totalDuration . 'M'));

                while ($time <= $limit) {
                    //Check if slot exist
                    $sl = $this->slotRepository->findExistingSlot($timeSlot->getDate(), $time);
                    if (!$sl){
                        //Instantiate object
                        $slot = new Slot();
                        $slot
                            ->setTimeSlot($timeSlot)
                            ->setStartTime($time);

                        $this->manager->persist($slot);
                        $this->manager->flush();
                    }

                    //Increase time by duration
                    $time = $timeSlot->getStartTime()->add(new \DateInterval('PT' . $totalDuration . 'M'));
                }
            }
        }
    }

    /**
     * Function to remove all slots of timeSlot before creating new ones
     */
    public function clearSlots($timeSlot)
    {
        $slots = $this->slotRepository->findBy(['timeSlot' => $timeSlot]);

        foreach ($slots as $slot){
            if ($slot->getStatus() == false)
                $this->manager->remove($slot);
        }
        $this->manager->flush();
    }

    /**
     * Function to clear passed slots
     */
    public function clearPassedSlots($timeSlot)
    {
        $slots = $this->slotRepository->findBy(['timeSlot' => $timeSlot]);
        $now = new \Datetime();

        foreach ($slots as $slot){
            //Get complete datetime slot
            $dateTimeSlot = $this->getDateTimeOfSlot($slot);

            //Get expertBookings with this slot
            $expertBookings = $this->expertBookingRepository->findBy(['slot' => $slot]);

            if (empty($expertBookings) && $dateTimeSlot <= $now && $slot->getStatus() == false){
                $this->manager->remove($slot);
            }
        }
        $this->manager->flush();
    }

    /**
     * Get complete datetime slot
     */
    public function getDateTimeOfSlot($slot): \DateTime
    {
        //Get complete datetime slot
        $date = $slot->getTimeSlot()->getDate()->format('d-m-Y');
        $time = $slot->getStartTime()->format('H:i');

        //Instantiate object DateTime with slot date and time
        return new \DateTime($date . ' ' . $time);
    }

    /**
     * Return if slot is always available
     */
    public function availableSlot(Slot $slot): bool
    {
        $dateSlot = $this->getDateTimeOfSlot($slot);
        $dateNow = new \DateTime();

        return $dateSlot > $dateNow;
    }
}