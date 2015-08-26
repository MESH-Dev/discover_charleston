<?php
    $plugin = RPw()->getSettings()->getPluginSettings(); 
    $message = !empty($plugin['noDataMessage']) ? $plugin['noDataMessage'] : __('Ooops! Nothing was found!', 'wcp-openweather-theme');
?>
<div class="wcp-openweather-container">
    <div class="wcp-openweather-nodata-wrapper wp-open-weather-nodata">
        <span class="wcp-openweather-nodata">
            <?php echo $message; ?>
        </span>
    </div>    
</div>


