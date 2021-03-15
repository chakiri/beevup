<?php


namespace App\Service;



class Utility
{


    public function getEmailsList(string $emailsList)
    {
       return  explode(';', $emailsList);
    }

    /**
     * Get array of ids from array
     * @param array $array
     * @return array
     */
    public function getIdsOfArray(array $array): array
    {
        foreach ($array as $item){
            $arrayIds [] = $item->getId();
        }

        return $arrayIds;
    }

}