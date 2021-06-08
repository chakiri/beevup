<?php


namespace App\Service\ExpertMeeting;


use App\Repository\ExpertBookingRepository;
use App\Repository\ExpertMeetingRepository;
use Symfony\Component\Security\Core\Security;

class GetExpertMeeting
{
    private ExpertMeetingRepository $expertMeetingRepository;
    private ExpertBookingRepository $expertBookingRepository;
    private Security $security;

    public function __construct(ExpertMeetingRepository $expertMeetingRepository, ExpertBookingRepository $expertBookingRepository, Security $security)
    {

        $this->expertMeetingRepository = $expertMeetingRepository;
        $this->expertBookingRepository = $expertBookingRepository;
        $this->security = $security;
    }

    /**
     * Function to get Experts meetings in controllers
     * @param $allCompanies
     * @return array
     */
    public function list($allCompanies, $limit = null): array
    {
        //Current User
        $user = $this->security->getUser();

        //Find expert meeting proposed by current user
        $expertMeeting = $this->expertMeetingRepository->findOneBy(['user' => $user]);

        //Experts meetings
        $expertsMeetings = $this->expertMeetingRepository->findLocal($allCompanies, $limit);

        if (!$limit && $expertMeeting){
            //Add expertMeeting of current user on the beginning of array
            array_unshift($expertsMeetings, $expertMeeting);
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
}