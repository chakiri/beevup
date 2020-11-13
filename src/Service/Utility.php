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
}