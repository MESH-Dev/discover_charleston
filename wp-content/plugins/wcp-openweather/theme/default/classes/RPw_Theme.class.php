<?php
namespace Webcodin\WCPOpenWeather\Theme\DefaultTheme;

class RPw_Theme extends \RPw_ThemeEntity {

    /**
     * The single instance of the class 
     * 
     * @var object 
     */
    protected static $_instance = null;    
    
	/**
	 * Main Instance
	 *
     * @return object
	 */
	public static function instance($active = FALSE) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self($active);
		}
		return self::$_instance;
	}    
    
	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
    }        
    
    public function __construct($active = FALSE) {
        parent::__construct($active, dirname(dirname(__FILE__)));
        $this->setId('default');
        $this->setName(__('Default Theme', 'wcp-openweather-theme-' . $this->getUniqueId()));
        $this->setIsDefaultTheme(TRUE);
        $this->setSettingsKey('rpw-theme-default-settings');
        add_action( 'init', array($this, 'loadTranslation' ));        
    }
    
    public function init() {
        parent::init();
        
        if ($this->getActive()) {
            load_plugin_textdomain('wcp-openweather-theme', FALSE, basename( dirname(dirname($this->getBaseDir())) ) . '/theme/default/languages');            
            
            add_action( 'admin_menu', array( $this, 'adminMenu' ), 999 );             
            add_shortcode( 'wcp_weather', array($this, 'doShortcode') ); 
            add_action( 'widgets_init', array($this, 'initWidgets' ) ); 
        }   
    }
    
    public function loadTranslation() {
        $domain = 'wcp-openweather-theme-' . $this->getUniqueId();
        $locale = apply_filters( 'plugin_locale', get_locale(), 'wcp-openweather-theme' );        
        $mofile = $this->getBaseDir() . '/languages/wcp-openweather-theme-' . $locale . '.mo';
        load_textdomain( $domain, $mofile );
    }
    
    public static function getThemeName () {
        return self::$_instance->getName();
    }

    public function getName() {
        return __(parent::getName(), 'wcp-openweather-theme-' . $this->getUniqueId());
    }        
    
    public function initWidgets() {
        register_widget(__NAMESPACE__ . '\RPw_Weather');
        register_widget(__NAMESPACE__ . '\RPw_WeatherMini');
    }        


}