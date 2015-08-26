<?php
use Webcodin\WCPOpenWeather\Core\Agp_Entity;

class RPw_CityEntity extends Agp_Entity {
    
    private $name;
    
    private $coord;
    
    private $country;
    
    private $extendedInfo;
        
    public function __construct($data) {
        $default = array(
            'ID' => NULL, 
        );

        parent::__construct($data, $default); 
    }
    
    public function getDisplayName () {
        if (empty($this->extendedInfo) && empty($this->extendedInfo['city'])) {
            return $this->name . ', ' . $this->country;
        } else {
            $data = $this->extendedInfo;
            $result = $data['city'];
//            if (!empty($data['state_short']) && strlen($data['state_short']) < 5) {
//                $result = $result . ', ' . $data['state_short'] ;
//            }
            if (!empty($data['country'])) {
                $result = $result . ', ' . $data['country'] ;
            }
            return $result;
        }
    }
    
    public function getName() {
        return $this->name;
    }

    public function getCoord() {
        return $this->coord;
    }

    public function getCountry() {
        return $this->country;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function setCoord($coord) {
        $this->coord = $coord;
        return $this;
    }

    public function setCountry($country) {
        $this->country = $country;
        return $this;
    }

    public function getExtendedInfo() {
        return $this->extendedInfo;
    }

    public function setExtendedInfo($extendedInfo) {
        $this->extendedInfo = $extendedInfo;
        return $this;
    }

}
