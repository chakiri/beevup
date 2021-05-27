<?php


namespace App\Service\TimeSlot;


class handleDatetime
{
    /**
     * Return unique date format in array
     * @param $timesSlot
     * @return array
     */
    public function getUniqueDates($timesSlot): array
    {
        $dates = [];
        foreach($timesSlot as $timeSlot){
            if (!in_array($timeSlot->getDate()->format('d/m/Y'), $dates))
                $dates [$timeSlot->getId()] = $timeSlot->getDate()->format('d/m/Y');
        }

        return $dates;
    }

    /**
     * Return 2 dimensions array corresponding to times of each date
     */
    public function getTimesById($timesSlot, $dates, $expertBookingSlot): array
    {
        $startsTimes = [];
        foreach($dates as $date){
            //Create array containing date key and value times
            $startsTimes [$date] = [];
            foreach($timesSlot as $timeSlot){
                if ($timeSlot->getDate()->format('d/m/Y') === $date){
                    foreach ($timeSlot->getSlots() as $slot){
                        //Display only available slots and in edition mode display also slot selected
                        if ($slot === $expertBookingSlot || $slot->getStatus() == false){
                            $startsTimes [$date][$slot->getId()] =  $slot->getStartTime()->format('H:i');
                        }
                    }
                }
            }
        }

        return $startsTimes;
    }
}