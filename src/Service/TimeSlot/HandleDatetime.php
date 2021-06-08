<?php


namespace App\Service\TimeSlot;


class HandleDatetime
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

        usort($dates, [$this, 'cmp']);

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
            foreach($timesSlot as $timeSlot){
                if ($timeSlot->getDate()->format('d/m/Y') === $date){
                    foreach ($timeSlot->getSlots() as $slot){
                        //Display only available slots and in edition mode display also slot selected
                        if ($slot === $expertBookingSlot || $slot->getStatus() == false){
                            if (!isset($startsTimes [$date])){
                                $startsTimes [$date] = [];
                            }
                            $startsTimes [$date][$slot->getId()] =  $slot->getStartTime()->format('H:i');
                        }
                    }
                }
            }
        }

        return $this->startsTimeByDate($startsTimes);
    }

    /**
     * Function to sort times
     */
    private function startsTimeByDate ($startsTimes)
    {
        //Sort startsTimes by times ASC
        foreach($startsTimes as $key => $startTime) {
            uasort($startsTimes[$key], [$this, 'cmp']);
        }
        return $startsTimes;
    }

    /**
     * Function to used in usort
     */
    private function cmp($a, $b)
    {
        return $a <=> $b;
    }
}