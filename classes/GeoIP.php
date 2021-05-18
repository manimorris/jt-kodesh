<?php

namespace jcal;


class GeoIP{
    public $client_ip;
  
    public function get_geo_vars(){

        $geo = unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=" .$this->client_ip));

        return $geo;
        
    }
    
}

