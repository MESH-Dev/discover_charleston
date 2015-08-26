<?php
    use Webcodin\WCPOpenWeather\Theme\DefaultTheme\RPw_Theme;

    $settings = $params;
    $plugin = RPw()->getSettings()->getPluginSettings();        
    $template = RPw()->getSettings()->getCurrentTemplatePath();
    
    $data = RPw()->getWeatherById($settings['id']);
    $weather = !empty($data['weather']) ? $data['weather'] : NULL;
    $forecast = !empty($data['forecast']) ? $data['forecast'] : NULL;

    $hasData = !empty($weather) || !empty($forecast);

    if (!empty($weather)) {
        $city = $weather->getCity()->getDisplayName();
    } elseif (!empty($forecast)) {
        $city = $forecast->getCity()->getDisplayName();
    } else {
        $city = $settings['city'];
    }
    
    $city = stripcslashes($city);

    $background = isset($settings['themeOptions']['background_color']) ? $settings['themeOptions']['background_color'] : NULL;
    $secondary_background = isset($settings['themeOptions']['secondary_background_color']) ? $settings['themeOptions']['secondary_background_color'] : NULL;
    $color = isset($settings['themeOptions']['text_color']) ? $settings['themeOptions']['text_color'] : NULL;
    $secondary_color = isset($settings['themeOptions']['secondary_text_color']) ? $settings['themeOptions']['secondary_text_color'] : NULL;    
    
    $currentDate = RPw()->getDate( '%b %e, %Y - %a' ) ;
?>
<style>
    .wcp-openweather-primary-background { <?php if (!empty($background)): echo "background: $background !important;"; endif;?> }
    .wcp-openweather-primary-color { <?php if (!empty($color)): echo "color: $color !important;"; endif;?> }
</style>

<div class="wcp-openweather-default-widget wp-open-weather wpw-widget wcp-openweather-primary-background wcp-openweather-primary-color">
    <div class="wcp-openweather-header">
        <div class="wcp-openweather-header-wrapper">
            <div class="wcp-openweather-container">
                <div class="wcp-openweather-day-wrapper">
                    <span class="wcp-openweather-day wcp-openweather-primary-color">
                        <?php echo $currentDate; ?>
                    </span>
                </div>
                <div class="wcp-openweather-options-wrapper">
                    <div class="wcp-openweather-refresh wp-open-weather-refresh">
                        <a class="wcp-openweather-refresh-icon wp-open-weather-refresh-now wcp-openweather-primary-color" data-id="<?php echo $settings['id'];?>" data-tag="<?php echo $settings['tag'];?>" data-template="<?php echo $settings['template'];?>" href="javascript:void(0);" onclick="return false;"><span class="wcp-ow-icon-refresh wcp-openweather-primary-color"></span></a>
                    </div>
                    <?php 
                        if (!empty($settings['enableUserSettings'])) : 
                            echo RPw()->getTemplate('user/user-options', $params); 
                        endif;
                    ?> 
                </div>
            </div>
        </div>
    </div>
    <div class="wcp-openweather-content wp-open-weather-data">
        <?php 
            if ( !$hasData ) : 
                echo RPw_Theme::instance()->getTemplate("widget/{$template}/nodata");  
            else: 
        ?>
        <div class="wcp-openweather-container">
            <div class="wcp-openweather-city-wrapper">
                <span class="wcp-openweather-city"><?php echo $city;?></span>
            </div>        
            <?php 
                if ( !empty($weather) ) :
                    $weather->hideWeatherConditions = $settings['hideWeatherConditions'];
                    echo RPw_Theme::instance()->getTemplate("widget/{$template}/now", $weather); 
                endif;
            ?>        
        </div>
        <?php endif; ?> 
        
        <?php 
            if (!empty($forecast)) :
                $forecast->hideWeatherConditions = $settings['hideWeatherConditions'];
                echo RPw_Theme::instance()->getTemplate("widget/{$template}/forecast", $forecast);
            endif;                
        ?>        
    </div>
</div>
