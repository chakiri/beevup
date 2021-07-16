<?php

namespace App\Service\Map;

class GeocodeAddress
{

    /**
     * Get latitude and longitude from address entity
     * @param $entity
     * @return array|null
     */
    public function geocode($entity)
    {
        //Get address
        $address = $entity->getAddressNumber() . ' ' . $entity->getAddressStreet() . ' ' . $entity->getAddressPostCode() . ' ' . $entity->getCity() . ' ' . $entity->getCountry();

        return $this->geolocate($address);
    }

    /**
     * Get lat and lon from postal code and name
     */
    public function geocodePostalCode($code)
    {
        return $this->geolocate($code);
    }

    public function geolocate($address)
    {
        $opts = array('http'=>array('header'=>"User-Agent:TPE"));
        $context = stream_context_create($opts);
        $address = urlencode($address);
        $url = "http://nominatim.openstreetmap.org/?format=json&addressdetails=1&q={$address}&format=json&limit=1" ;
        $resp_json = file_get_contents($url, false, $context);
        $resp = json_decode($resp_json, true);
        if(count($resp)> 0) {
            return [$resp[0]['lat'], $resp[0]['lon']];
        }else {
            return null;
        }
    }
}