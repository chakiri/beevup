<?php

namespace App\Service;

class Map
{


    function geocode($address){
        $opts = array('http'=>array('header'=>"User-Agent:TPE"));
        $context = stream_context_create($opts);
        $address = urlencode($address);
        $url = "http://nominatim.openstreetmap.org/?format=json&addressdetails=1&q={$address}&format=json&limit=1" ;
        $resp_json = file_get_contents($url, false, $context);
         $resp = json_decode($resp_json, true);
         if(count($resp)> 0) {
             return array($resp[0]['lat'], $resp[0]['lon']);
         }
         else {
             return null;
         }

    }
}