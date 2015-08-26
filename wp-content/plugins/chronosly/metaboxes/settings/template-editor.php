<?php if($print == 1){?>
<div class="wrapch addon" id="<?php echo (isset($_REQUEST['addon']))?$_REQUEST["addon"]:"";?>">
    <div class="save_info"></div>

    <?php
    if(!isset($_REQUEST["addon"]) or !$_REQUEST["addon"]){
         if(isset($_REQUEST['installed'])){

             echo "<div class='bubleadvice'>".__('Template successfully installed!', "chronosly")."<br/>";
             echo __("Check", "chronosly")." <a href='admin.php?page=chronosly_addons_configs'>".__("Addons Configs", "chronosly")."</a> ".__("to ensure that all addons are ready for the template.", "chronosly")."</div>";
         } ?>
        <div class="ch-box3" >
            <h3><?php _e("Upload template", "chronosly");?></h3>
            <form id="templates" method="post" action="" enctype="multipart/form-data">

                <input type="file" id="wp_custom_attachment" name="chronosly_addon" value="" size="25" />

                <?php  wp_nonce_field( "chronosly_addons_upload", 'chronosly_nonce' ); ?>
                <input class="button" type="submit" value="<?php _e("Upload", "chronosly");?>"/>
            </form>
        </div>
        <div class="ch-box3" style="width:33%">
            <h3><?php _e("Clone template", "chronosly")?></h3>
            <select class='duplicate_from'><?php echo $templates_options;?></select>
            <?php _e("to", "chronosly");?>
            <input type='text' value=""  class="duplicate_template" name="duplicate_template" />

            <span class="button" onclick="javascript:save_template('template_file_duplicate')"><?php _e("Clone", "chronosly")?></span>
        </div>
        <div class="ch-box3" style="width:25%;border:none;margin-right:0px;">
          <h3><?php _e("Create new template", "chronosly")?></h3>
            <input type='text' value=""  class="save_template" name="save_template" />

            <span class="button" onclick="javascript:save_template('template_file_new')"><?php _e("Create", "chronosly")?></span>
            <span style="display:none;" class="button" onclick="javascript:save_all_html_template()"><?php _e("Save all html templates", "chronosly")?></span>
        </div>

            <h3 style="clear:both"><?php _e("Edit templates", "chronosly");?></h3>
<?php
    } else {
        echo $mensage;
        echo "<h3>".__("Update your custom templates to integrate this addon", "chronosly")."</h3>";
        _e("Your custom edited templates have to be manually edited.", "chronosly");
        echo "<br/>".__("You have to insert where you want the new addon element via drag and drop, and customize the style(css or template editor).", "chronosly");?>
      <?php /*  <a class="ch-button warning" href="admin.php?page=chronosly_edit_templates&autoupdate=1&addon=<?php echo $_REQUEST["addon"]; ?>&viewstoedit=<?php echo $_REQUEST["viewstoedit"]; ?>"><?php _e("Auto Update", "chronosly"); ?></a>
        <span class="warning-hide">
                <?php _e("You are going to update all templates to insert this addon.\nIf you have previously edited and saved some templates is probably that after the autoupdate you also need to adjust that templates.\nCheck all changes and edit if is needed in the next page.", "chronosly");?>
            </span>*/ ?>
        <br/><br/>

    <?php } ?>

    <div id="dad1" class="main_box">
   <div class="level"><?php
    _e("Select the template you want to edit", "chronosly");
    ?>
    <select class="tdad_select">
        <?php echo $templates_options; ?>
    </select></div>
    <a class="ch-button warning" href="javascript:delete_template()"><?php _e("Delete template", "chronosly")?></a>
    <div class="warning-hide"><?php _e("Confirm to delete this template, you can't recover it", "chronosly")?></div>
    <div class='bubleadvice'><?php
            if(isset($_REQUEST["addon"])){

                 echo __("Update all templates that you are using in your events", "chronosly")."<br/>";
            }
        _e("You can find all templates that are working in your events in ","chronosly");?><a target="_blank" href="admin.php?page=chronosly_templates_status"><?php _e("Templates Status", "chronosly")?></a>
    </div><br/>
        <div class="level">
    <?php
    _e("Select the view you want to edit", "chronosly");
    ?>
    <select class="dad_select">
        <?php
        $viewstoedit = "";
        if(isset($_REQUEST["viewstoedit"])) $vistastoedit = explode("|", $_REQUEST["viewstoedit"]);
        foreach($vistas as $k=>$c) { ?>
            <option <?php if(isset($_REQUEST["viewstoedit"]) and !in_array(str_replace("dad", "", $k), $vistastoedit )) echo "style='display:none;'"; ?> value="<?php echo $k?>"><?php echo $c?></option>
        <?php }

        ?>
    </select> </div> <?php if (isset($_REQUEST["viewstoedit"])) {
        echo "<div class='bubleadvice'>";
        _e("This views needs your approval, save changes on every view when they are ready","chronosly");
        echo "</div>";
    } ?>
      <br/>

    <span class="button-red" onclick="javascript:save_template('template_file')"><?php _e("Save changes", "chronosly")?></span>
    <br/>
    <br/>
<?php
}
else if($print == 2){
    ?>


    </div>

    </div>

<?php } ?>
