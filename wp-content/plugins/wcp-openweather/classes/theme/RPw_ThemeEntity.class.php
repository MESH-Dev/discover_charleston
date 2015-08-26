<?php
use Webcodin\WCPOpenWeather\Core\Agp_Module;

class RPw_ThemeEntity extends Agp_Module {

    private $uniqueId;
    
    private $id;
    
    private $name;
    
    private $isDefaultTheme = FALSE;
    
    private $active = FALSE;
    
    private $settingsKey = '';
    
    /**
     * Settings
     * 
     * @var RPw_ThemeSettings
     */
    private $settings;    
    
    public function __construct($active = FALSE, $baseDir = NULL) {
        parent::__construct($baseDir);
        $this->active = $active;
        $this->uniqueId = 'rpw-theme-' . uniqid();
        $this->settings = new RPw_ThemeSettings( $this );
    }
    
    public function init () {
        if ($this->active) {
            add_action( 'wp_enqueue_scripts', array($this, 'enqueueScripts' ) );        
        }        
    }
    
    public function adminMenu() {
        if ($this->active) {
            add_submenu_page('wcp-weather-main', __('Theme Settings', 'wcp-openweather'), __('Theme Settings', 'wcp-openweather'), 'manage_options', 'wcp-theme' , array('RPw_Settings', 'renderThemeSettingsPage') );                    
        }
    }    
    
    public function enqueueScripts () {
        wp_enqueue_script( $this->uniqueId , $this->getAssetUrl('js/main.js'), array('rpw','iris') );  
        wp_enqueue_style( $this->uniqueId . '-css', $this->getAssetUrl('css/style.css') );    
    }            
    
    public function renderIcon ($item, $hideWeatherConditions = FALSE) {
        if ($hideWeatherConditions) {
            return '<img src="' . $item->getWeatherIconUrl() .'" alt=""  title=""/>';
        } else {
            return '<img src="' . $item->getWeatherIconUrl() .'" alt="' . $item->getWeatherDescription() . '"  title="' . $item->getWeatherDescription() . '"/>';    
        }
    }        
    
    private function _prepareAtts ($atts, $tag, $template) {
        $plugin = RPw()->getSettings()->getPluginSettings();

        $timeDiff = !empty($plugin['refreshPeriod']) ? 1 * $plugin['refreshPeriod'] : 0 ;
        RPw()->getApi()->setTimeDiff($timeDiff);
        
        $id = !empty($atts['id']) ? $atts['id'] : 'default-weather-id';
        $id = stripslashes(str_replace(' ','_', str_replace(', ', '_', $id)));
        $id = preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities($id));
        if (isset($atts['id'])) {
            unset($atts['id']);
        }

        $tag = !empty($atts['tag']) ? $atts['tag'] : $tag;    
        $atts['tag'] = $tag;
        
        $template = !empty($atts['template']) ? $atts['template'] : $template;    
        $templateList = RPw()->getCurrentTheme()->getSettings()->getFieldSet("{$tag}_templates");
        $isSingleTemplate = !empty($templateList) && is_array($templateList) && count($templateList) == 1 || empty($template);
        if ($isSingleTemplate) {
            $template = 'default';
        }
        $atts['template'] = $template;
        $atts['themeOptions'] = $this->getSettings()->getSettings($this->settingsKey);
        
        RPw()->getSettings()->setCurrentId($id);        
        RPw()->getSettings()->setCurrentTag($tag);     
        RPw()->getSettings()->setCurrentTemplate($template);        
        
        $atts = RPw()->getSettingsById($id, $atts);    
        
        $atts['tag'] = $tag;
        $atts['template'] = $template;
        $atts['themeOptions'] = $this->getSettings()->getSettings($this->settingsKey);        
        
        return $atts;
    }
    
    public function doShortcode ($atts, $content, $tag = 'wcp_weather', $template = NULL) {
        $atts = $this->_prepareAtts($atts, $tag, $template);
        return $this->getTemplate("shortcode/{$atts['tag']}/{$atts['template']}/layout", $atts);        
    }
    
    public function doWidget ($atts, $tag, $template = NULL) {
        $atts = $this->_prepareAtts($atts, $tag, $template);        
        return $this->getTemplate("widget/{$atts['tag']}/{$atts['template']}/layout", $atts);
    }       
    
    public function getExcludeUserOptions ($tag) {
        $exlude = $this->getSettings()->getAllExcludeUserOptions();
        if (!empty($exlude[$tag])) {
            return $exlude[$tag];
        }
        return array();
    }
    
    public function isLangExists( $lang ) {
        $languages = $this->settings->getFieldSet('languages');
        if (!empty($languages)) {
            return array_key_exists($lang, $languages);    
        }
    }
    
    public function isPartialLang( $lang ) {
        $languages = RPw()->getSettings()->getFieldSet('partial_languages');
        if (!empty($languages) && array_key_exists($lang, $languages)) {
            return $languages[$lang];    
        }
    }    
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    
    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }
 
    public function getIsDefaultTheme() {
        return $this->isDefaultTheme;
    }

    public function setIsDefaultTheme($isDefaultTheme) {
        $this->isDefaultTheme = $isDefaultTheme;
        return $this;
    }
    
    public function getUniqueId() {
        return $this->uniqueId;
    }
    
    public function getSettings() {
        return $this->settings;
    }
    
    public function getActive() {
        return $this->active;
    }

    public function setActive($active) {
        $this->active = $active;
        return $this;
    }
    
    public function getSettingsKey() {
        return $this->settingsKey;
    }

    public function setSettingsKey($settingsKey) {
        $this->settingsKey = $settingsKey;
        $this->settings->setSettingsKey($settingsKey);
        return $this;
    }


}