
<div class="organizer-box">
<?php
foreach ($posts as $post) {
    if(@in_array($post->ID, $check)) $checked = true;
    else $checked = false;
                                ?>
    <input type="checkbox"  name="organizer[]" <?php if($checked) echo "checked" ?> value="<?php echo $post->ID; ?>" /> <?php echo $post->post_title; ?><br/>

<?php } ?>
 </div>
<div class='add-organizer'>
    <a id="chronosly_organizer-add-toggle">+ <?php _e("Add new Organizer", "chronosly");?></a>
    <div id="chronosly_organizer-add" class="wp-hidden-child">
        <label><?php _e("Name", "chronosly");?></label> <input id='name' type='text' name='title' />
        <a id="chronosly_organizer-add-more"><?php _e("More details", "chronosly");?></a>
        <div style="margin:0;padding:0;" class="more">

            <label><?php _e("Phone", "chronosly");?></label> <input id="phone" type="text" name="phone"  value=""  /><br/>
            <label><?php _e("Mail", "chronosly");?></label> <input id="mail" type="text" name="mail"   value=""  /><br/>
            <label><?php _e("Web", "chronosly");?></label> <input id="web" type="text" name="web"  value=""  /><br/>
        </div>
        <input type="button" id="chronosly_organizer-add-submit"  class="button organizer-add-submit" value="<?php _e("Add new Organizer", "chronosly") ?>" />
    </div>
</div>