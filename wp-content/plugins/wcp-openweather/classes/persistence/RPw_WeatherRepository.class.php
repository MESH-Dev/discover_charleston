<?php
use Webcodin\WCPOpenWeather\Core\Agp_RepositoryAbstract;

class RPw_WeatherRepository extends Agp_RepositoryAbstract {

    public $entityClass = 'RPw_WeatherEntity';    
    
    /**
     * City
     * 
     * @var RPw_CityEntity
     */
    private $city;
    
    public function __construct($data = NULL) {
        parent::__construct($data['items']);
        $this->city = new RPw_CityEntity($data['city']);
    }
    
    public function init () {
        
    }    
    
    /**
     * City
     * 
     * @return RPw_CityEntity
     */
    public function getCity() {
        return $this->city;
    }

    public function applySettings($settings) {
        foreach ($this->getAll() as $entity) {
            $entity->applySettings($settings);
        }
    }

    public function applyThemeUrl($themeUrl) {
        foreach ($this->getAll() as $entity) {
            $entity->applyThemeUrl($themeUrl);
        }
    }    
    
}
