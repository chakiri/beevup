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

    public function addLink(string $str, string $hostname)
    {
        $str =  str_replace( 'Beevup.fr', '<a href='.$hostname.'>Beevup.fr</a>',$str);
        return $str;
    }


}