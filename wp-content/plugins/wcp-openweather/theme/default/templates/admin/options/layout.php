<?php 
    $args = new stdClass();
    $args->settings = $params;
    $args->key = isset( $_GET['tab'] ) ? $_GET['tab'] : 'rpw-theme-default-settings';
    $args->tabs = $args->settings->getTabs();
    $args->fieldSet = $args->settings->getFieldSet();
    $args->data = $args->settings->getSettings($args->key);
    $args->fields = $args->settings->getFields($args->key);
    $title = $args->settings->getRecursiveCallable( !empty($args->settings->getConfig()->admin->options->title) ? $args->settings->getConfig()->admin->options->title : '' );
?>
<?php if (!empty($title)) :?>
<div style="width: 100%; padding: 20px 0 0;">
    <table cellpading="0" cellspacing="0">
        <tr>
            <td style="padding: 0 20px 0 0; vertical-align: middle;">                                                                                               
                <img src="<?php echo RPw()->getAssetUrl( 'images/icons/icon-theme-256x256.png' ); ?>" alt="" width="100" height="100" />    
            </td>
            <td style="padding: 0; vertical-align: middle;">
                <h1 style="margin: 0px; padding: 0 0 10px;"><?php echo $title;?></h1>
                <p style="margin: 0px; padding: 0;"><?php _e('You can change current theme <a href="/wp-admin/admin.php?page=wcp-weather&tab=plugin-settings">here</a>.', 'wcp-openweather-theme'); ?></p> 
            </td>
        </tr>
    </table>
</div>
<?php endif;?>
<div class="wrap">
    <?php 
        screen_icon();
        settings_errors();
        
        echo $args->settings->getParentModule()->getTemplate('admin/options/render-tabs', $args);
    ?>
    <form method="post" action="options.php">
        <?php wp_nonce_field( 'update-options' ); ?>
        <?php settings_fields( $args->key ); ?>
        
        <?php echo $args->settings->getParentModule()->getTemplate('admin/options/render-page', $args); ?>
        
        <p class="submit">
            <input id="submit" class="button button-primary" type="submit" value="<?php _e('Save Changes','wcp-openweather-theme'); ?>" name="submit">
            <a class="button button-primary" href="?page=<?php echo $args->settings->getPage();?>&tab=<?php echo $args->key;?>&reset-settings=true" ><?php _e('Reset to Default','wcp-openweather-theme'); ?></a>
        </p>
    </form>
</div>