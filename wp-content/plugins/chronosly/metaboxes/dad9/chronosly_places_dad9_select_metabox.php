
<div id="dad9" class="main_box">
    <?php
    if($this->settings["chronosly_dad_show_load_template"] and !$this->settings["chronosly_template_default_active"]){ ?>
        <label class="top"><?php _e("Load template", "chronosly");?></label>

        <select class="tdad_select">
            <?php echo $select_options; ?>
        </select><br/>
    <?php }

    if($this->settings["chronosly_dad_show_load_view"]){
    ?>

    <label class="top"><?php _e("Select view", "chronosly");?></label>
    <select class="dad_select">
        <?php foreach($vistas as $k=>$c) { ?>
            <option value="<?php echo $k?>"><?php echo $c?></option>
        <?php } ?>
    </select><span class="info"></span>
    <div class="info-hide">
        <?php
        _e("A view is how the place is shown in each website navigation section.", "chronosly");
        _e("It allows you to thoroughly customize how each place behaves in each section.", "chronosly");
        echo "<br/>";
        echo "<br/>";

        _e("In your website the places have 2 different views", "chronosly");
        echo "<br/>";
        _e("1- General View: customize how the place is displayed in the places list", "chronosly");
        echo "<br/>";
        _e("2- Inner page view: it is the inner view of your place, a single page on the place, where all the information is shown", "chronosly");
        echo "<br/>";

        ?>
    </div>
<?php } ?>



