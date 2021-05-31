<?php


namespace App\Service\ExpertMeeting;


class videoConference
{
    private const LENGTH = 20;
    private const URI = 'https://meet.jit.si/';

    /**
     * Function to generate link videoconference for meeting
     * @return string
     * @throws \Exception
     */
    public function generateLink(){
        $token = bin2hex(random_bytes(self::LENGTH));

        return self::URI . $token;
    }

}