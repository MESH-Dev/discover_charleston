<?php

return array(
    'wcp-weather-main' => array (
        'page_title' => __('WCP Weather', 'wcp-openweather'), 
        'menu_title' => __('WCP Weather', 'wcp-openweather'), 
        'capability' => 'manage_options',
        'function' => array('RPw_Settings', 'renderSettingsPage'),
        'icon_url' => '', 
        'position' => '99', 
        'hideInSubMenu' => TRUE,
        'submenu' => array(
            'wcp-weather' => array(
                'page_title' => __('Settings', 'wcp-openweather'), 
                'menu_title' => __('Settings', 'wcp-openweather'), 
                'capability' => 'manage_options',
                'function' => array('RPw_Settings', 'renderSettingsPage'),                         
            ),
        ),        
    ),
);
    