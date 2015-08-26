
<div class="place-box">

    <?php

    foreach ($posts as $post) {
        if(@in_array($post->ID, $check)) $checked = true;
        else $checked = false;
        ?>
        <input type="checkbox"  name="places[]" <?php if($checked) echo "checked" ?> value="<?php echo $post->ID; ?>" /> <?php echo $post->post_title; ?><br/>

    <?php } ?>
</div>
<div class='add-place'>
    <a id="chronosly_place-add-toggle">+ <?php _e("Add new Place", "chronosly");?></a>
    <div id="chronosly_place-add" class="wp-hidden-child">
        <label><?php _e("Name", "chronosly");?></label> <input id='name' type='text' name='title' />
        <a id="chronosly_place-add-more"><?php _e("More details", "chronosly");?></a>
        <div style="margin:0;padding:0;" class="more">
            <label><?php _e("Address", "chronosly");?></label> <input id="dir" type="text" name="dir"  value=""  /><br/>
            <label><?php _e("City", "chronosly");?></label> <input id="city" type="text" name="city"  value=""  /><br/>
            <label><?php _e("Country", "chronosly");?></label> <input id="country" type="text" name="country"  value=""  /><br/>
            <label><?php _e("State", "chronosly");?></label> <input id="state" type="text" name="state"  value=""  /><br/>
            <label><?php _e("Postal Code", "chronosly");?></label> <input id="pc" type="text" name="pc"  value=""  /><br/>
            <label><?php _e("Phone", "chronosly");?></label> <input id="phone" type="text" name="phone"  value=""  /><br/>
            <label><?php _e("Mail", "chronosly");?></label> <input id="mail" type="text" name="mail"  value=""  /><br/>
            <label><?php _e("Web", "chronosly");?></label> <input id="web" type="text" name="web"  value=""  /><br/>
        </div>
        <input type="button" id="chronosly_place-add-submit"  class="button place-add-submit" value="<?php _e("Add new Place", "chronosly") ?>" />

    </div>
</div>

