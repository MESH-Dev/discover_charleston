<div class="place-inputs" style="float:left;">
<label><?php _e("Address", "chronosly");?></label> <input id="evp_dir" type="text" name="evp_dir"  value="<?php echo (isset($vars['evp_dir'][0])?$vars['evp_dir'][0]:"");?>"  /><br/>
<label><?php _e("City", "chronosly");?></label> <input id="evp_city" type="text" name="evp_city"  value="<?php echo (isset($vars['evp_city'][0])?$vars['evp_city'][0]:"");?>"  /><br/>
<label><?php _e("Country", "chronosly");?></label> <input id="evp_country" type="text" name="evp_country"  value="<?php echo (isset($vars['evp_country'][0])?$vars['evp_country'][0]:"");?>"  /><br/>
<label><?php _e("State", "chronosly");?></label> <input id="evp_state" type="text" name="evp_state"  value="<?php echo (isset($vars['evp_state'][0])?$vars['evp_state'][0]:"");?>"  /><br/>
<label><?php _e("Postal Code", "chronosly");?></label> <input id="evp_pc" type="text" name="evp_pc"  value="<?php echo (isset($vars['evp_pc'][0])?$vars['evp_pc'][0]:"");?>"  /><br/>
<label><?php _e("Lat. & Long.", "chronosly");?></label> <input id="latlong" type="text" name="latlong"  value="<?php echo (isset($vars['latlong'][0])?$vars['latlong'][0]:"");?>"  /><br/>
<label><?php _e("Phone", "chronosly");?></label> <input id="evp_phone" type="text" name="evp_phone"  value="<?php echo (isset($vars['evp_phone'][0])?$vars['evp_phone'][0]:"");?>"  /><br/>
<label><?php _e("Mail", "chronosly");?></label> <input id="evp_mail" type="text" name="evp_mail"  value="<?php echo (isset($vars['evp_mail'][0])?$vars['evp_mail'][0]:"");?>"  /><br/>
<label><?php _e("Web", "chronosly");?></label> <input id="evp_web" type="text" name="evp_web"  value="<?php echo (isset($vars['evp_web'][0])?$vars['evp_web'][0]:"");?>"  /><br/>
</div>
<div id="place-gmap" style="width:50%;float:right;height:300px;"></div>
<div style="clear:both;"></div>
<span class="info"></span>
<div class="info-hide">
    <?php _e("Guided process do step by step letting you to confirm or update all templates and views", "chronosly");?><br/>
    <?php _e("Auto Update do the job for you, but this is not full suported on custom edited templates that could need your work", "chronosly");?><br/>
    <?php _e("For the single edited templates (previously edited directly inside the events page)  you can also update template using the new drag and drop 'Share' bubble", "chronosly");?><br/><br/>
</div>
