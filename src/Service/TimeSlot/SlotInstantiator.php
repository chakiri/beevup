<?php


namespace App\Service\TimeSlot;


use App\Entity\Slot;
use App\Repository\SlotRepository;
use Doctrine\ORM\EntityManagerInterface;

class SlotInstantiator
{
    private EntityManagerInterface $manager;
    private SlotRepository $slotRepository;

    public function __construct(EntityManagerInterface $manager, SlotRepository $slotRepository)
    {
        $this->manager = $manager;
        $this->slotRepository = $slotRepository;
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
            $time = $timeSlot->getStartTime();
            while ($time <= $timeSlot->getEndTime()) {
                //Instantiate object
                $slot = new Slot();
                $slot
                    ->setTimeSlot($timeSlot)
                    ->setStatus(false)
                    ->setStartTime($time);

                $this->manager->persist($slot);
                $this->manager->flush();

                //Increase time by duration
                $time = $timeSlot->getStartTime()->add(new \DateInterval('PT' . $totalDuration . 'M'));
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
            $this->manager->remove($slot);
        }
        $this->manager->flush();
    }
}