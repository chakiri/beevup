<?php


namespace App\Service\ExpertMeeting;


use App\Entity\ExpertMeeting;
use App\Entity\Slot;
use App\Repository\ExpertBookingRepository;
use App\Repository\ExpertMeetingRepository;
use App\Repository\SlotRepository;
use App\Service\TimeSlot\SlotInstantiator;
use Symfony\Component\Security\Core\Security;

class GetExpertMeeting
{
    private ExpertMeetingRepository $expertMeetingRepository;
    private ExpertBookingRepository $expertBookingRepository;
    private Security $security;
    private SlotRepository $slotRepository;
    private SlotInstantiator $slotInstantiator;

    public function __construct(ExpertMeetingRepository $expertMeetingRepository, ExpertBookingRepository $expertBookingRepository, SlotRepository $slotRepository, Security $security, SlotInstantiator $slotInstantiator)
    {

        $this->expertMeetingRepository = $expertMeetingRepository;
        $this->expertBookingRepository = $expertBookingRepository;
        $this->security = $security;
        $this->slotRepository = $slotRepository;
        $this->slotInstantiator = $slotInstantiator;
    }

    /**
     * Function to get Experts meetings in controllers
     * @param $allCompanies
     * @return array
     */
    public function list($allCompanies): array
    {
        //Current User
        $user = $this->security->getUser();

        //Find expert meeting proposed by current user
        $expertMeeting = $this->expertMeetingRepository->findOneBy(['user' => $user]);

        //Experts meetings
        $expertsMeetings = $this->expertMeetingRepository->findLocal($allCompanies);

        //Unset experts meetings witch not containing slots
        foreach ($expertsMeetings as $key => $meeting){
            if (!$this->hasAvailableSlots($meeting)){
                unset($expertsMeetings[$key]);
            }
        }

        //Get expert meetings booked by current user
        $expertsBooking = $this->expertBookingRepository->findBy(['user' => $user]);

        $expertsMeetingsBookedByUser = [];
        foreach($expertsBooking as $expertBooking){
            if ($expertBooking->getStatus() !== 'canceled')
                $expertsMeetingsBookedByUser [] = $expertBooking->getExpertMeeting();
        }

        //Get experts booking waiting confirmation
        $expertsBookingWaiting = $this->expertBookingRepository->findByStatus($expertMeeting, 'waiting');

        return [
            'expertsMeetings' => $expertsMeetings,
            'expertMeetingCurrentUser' => $expertMeeting,
            'expertsMeetingsBookedByUser' => $expertsMeetingsBookedByUser,
            'expertsBookingWaiting' => $expertsBookingWaiting,
        ];
    }

    /**
     * Function return if expert meeting has available slots
     */
    public function hasAvailableSlots(ExpertMeeting $expertMeeting)
    {
        $slots = $this->slotRepository->findAvailableSlots($expertMeeting->getId());

        foreach ($slots as $key => $slot){
            //If slot passed unset it from array
            if ($this->slotInstantiator->availableSlot($slot) == false){
                unset($slots[$key]);
            }
        }

        return !empty($slots);
    }


}