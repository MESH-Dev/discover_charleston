<?php
namespace Webcodin\WCPOpenWeather\Theme\DefaultTheme;

use Webcodin\WCPOpenWeather\Theme\DefaultTheme\RPw_Theme;

class RPw_WeatherMini extends RPw_Weather {
    
    public $widgetTag = 'wcp_weather_mini_widget';    
    
	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
        $this->setWidgetTemplates( RPw()->getSettings()->getShortcodeTemplates($this->widgetTag));
		$widget_ops = array( 'description' => __( "Adds mini weather to sidebar", 'wcp-openweather-theme') );
		\WP_Widget::__construct( $this->widgetTag, __('WCP Weather Mini', 'wcp-openweather-theme'), $widget_ops);
	}
    
}

    