<?php


namespace App\Service;



class Utility
{
    public function updateName(string $name)
    {
        $updateName ='';
        $pieces = explode("-", $name);
        foreach ($pieces as $piece){
            $val = ucfirst(strtolower($piece));
            $updateName =  ($updateName=='') ? $val :$updateName.'-'.$val;

        }
         return $updateName;
    }

    public function getEmailsList(string $emailsList){

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