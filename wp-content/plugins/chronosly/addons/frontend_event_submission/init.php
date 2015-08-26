<?php
/*
    Frontend Event Submission
*/

if(!class_exists("Chronosly_Frontend_Event_Submission")){

    class Chronosly_Frontend_Event_Submission extends Chronosly_Extend {
        public $id = "frontend_event_submission";
        public $name = "Frontend Event Submission";
        public $settings_url = "chronosly_frontend_event_submission_settings";
        public $description = "Add frontend event submission adds shortcode forms for events user submit";
        public $version = "1.8";
        public $settings;
        private $addon_page_hook;

        //construct addon, calling all filtes needed for extend Chronosly
        function __construct(){
            add_shortcode( 'chronosly-front-submit', array(&$this, 'shortcode' ));//adding shortcode for create form


            if(is_admin()){
                add_filter("chronosly_addons_settings_item", array(&$this,"addon_add_item"), 0);//add new item to chronosly addons config
                add_filter("chronosly_addons_settings_menu_item", array(&$this,"addon_add_menu_item"), 0);//add new menu item to chronosly addons config
                if(!has_action("chronosly_remove_{$this->id}")) add_action("chronosly_remove_{$this->id}", array(&$this,"remove")); //function for delete addon
                //if(!has_filter("chronosly_update_template_{$this->id}")) add_filter("chronosly_update_template_{$this->id}", array(&$this,"update_templates"), 10, 4);
                add_action("admin_menu" , array(&$this,"addon_settings"));//add new page for calling in chronosly addons setting page
                add_action("chronosly_custom_backend_css", array(&$this,"include_css"));//add custom styles  and js to frontend
                add_action("chronosly_custom_backend_css", array(&$this,"admin_js"));//add custom js to wpadmin


            }

        }

        function remove(){
            delete_option("chronosly_settings_{$this->id}");//remove settings for this addon

        }



        function addon_settings($recall=0){
            if(is_admin() and !$recall)register_setting('chronosly-group', "chronosly_settings_{$this->id}");//settings for this addon
            if(!get_option("chronosly_settings_{$this->id}")){
                $settings = array(
                    "template" => "default",
                    "user" => 2,
                    "user_id" => 1,
                    "captcha" => 0,
                    "description" => 1,
                    "image" => 0,
                    "tickets" => 0,
                    "category" => 1,
                    "organizer" => 0,
                    "organizer_description" => 0,
                    "organizer_image" => 0,
                    "organizer_phone" => 0,
                    "organizer_mail" => 0,
                    "organizer_web" => 0,
                    "place" => 0,
                    "place_description" => 0,
                    "place_image" => 0,
                    "place_address" => 0,
                    "place_city" => 0,
                    "place_country" => 0,
                    "place_state" => 0,
                    "place_pc" => 0,
                    "place_phone" => 0,
                    "place_mail" => 0,
                    "place_web" => 0,
                    "12h_format" => 0,

                    "form_to" => "",
                    "form_from_n" => "",
                    "form_from_m" => "",
                    "form_subject" => "New event submitted",
                    "form_content" => "",

                    "license" => "",
                    "autoupdate" => 0,
                    "version" => "1.8",
                    "needed_version" => "1.8"
                );
                update_option("chronosly_settings_{$this->id}", serialize($settings));
            }
            $this->settings = unserialize(get_option("chronosly_settings_{$this->id}"));


            if(is_admin() and !$recall){
                $this->addon_page_hook =   add_submenu_page(
                    null,
                    'Frontend Event Submission Settings',
                    __('Frontend Event Submission Settings',"chronosly"),
                    'manage_options',
                    $this->settings_url,
                    array(&$this, 'addon_setting_page')
                );
            }
        }

        function addon_setting_page(){
            global $Chronosly_Settings;
            do_action("chronosly-addon-head", $this->id);
            $this->settings = unserialize(get_option("chronosly_settings_{$this->id}"));
            ?>

            <h3><?php _e("Form Settings", "chronosly");?></h3>
            <p><?php _e("Configure the fields to show on form", "chronosly"); ?><br/><?php _e("Event title and date are always required", "chronosly"); ?></p>
            <p><?php echo __("Use the shortcode", "chronosly")." <b>[chronosly-front-submit]</b> ".__("where you want to display the form.", "chronosly"); ?></b></p>

            <label><?php _e("Template", "chronosly")?></label>
            <select class='template_select' name='template'>
                <?php
                $options = Chronosly_Templates::get_file_templates(0, "dad1", 1);
                $return = "";

                foreach($options as $op){
                    $sel = "";
                    if($this->settings['template'] == $op) {
                        $sel = "selected='selected'";
                    }
                    $return .= "<option value='$op' $sel>$op</option>";
                }
                echo $return; ?>
            </select><br/>
            <label><?php _e("Who can send events?", "chronosly")?></label>
            <select class='user_select' name='user'>
                <?php
                $options = array(0 => __("No one", "chronosly"), 1 => __("Registered only", "chronosly"), 2 => __("All, name and mail required", "chronosly"));
                $return = "";

                foreach($options as $id => $op){
                    $sel = "";
                    if($this->settings['user'] == $id) {
                        $sel = "selected='selected'";
                    }
                    $return .= "<option value='$id' $sel>$op</option>";
                }
                echo $return; ?>
            </select><br/>
            <label><?php _e("Default author id", "chronosly")?></label> <input type="text" name="user_id" value="<?php echo $this->settings['user_id']; ?>" /><br/>
            <label><?php _e("Really Simple CAPTCHA", "chronosly")?></label> <input type="checkbox" name="captcha" value="1" <?php if($this->settings['captcha']) echo "checked" ?> /><span class="info"></span>
            <div class="info-hide"><?php echo __("You need to install free plugin <a href='https://wordpress.org/plugins/really-simple-captcha/' target='_blank'>Really Simple CAPTCHA</a>", "chronosly"); ?></div><br/>


            <label><?php _e("Event description", "chronosly")?></label> <input type="checkbox" name="description" value="1" <?php if($this->settings['description']) echo "checked" ?> /><br/>
            <label><?php _e("Event image", "chronosly")?></label> <input type="checkbox" name="image" value="1" <?php if($this->settings['image']) echo "checked" ?> /><br/>
            <label><?php _e("12 hours time format", "chronosly")?></label> <input type="checkbox" name="12h_format" value="1" <?php if($this->settings['12h_format']) echo "checked" ?> /><br/>
            <label><?php _e("Event category", "chronosly")?></label>
            <select class='category_select' name='category'>
                <?php
                $options = array(0 => __("Hidde", "chronosly"), 1 => __("Show categories already created", "chronosly"), 2 => __("Show categories and allow to create a new one", "chronosly"));
                $return = "";
                foreach($options as $id=>$op){
                    $sel = "";
                    if($this->settings['category'] == $id) {
                        $sel = "selected='selected'";
                    }
                    $return .= "<option value='$id' $sel>$op</option>";
                }
                echo $return; ?>
            </select><br/>
            <label><?php _e("Event organizer", "chronosly")?></label>
            <select class='organizer_select' name='organizer'>
                <?php
                $options = array(0 => __("Hidde", "chronosly"), 1 => __("Show organizers already created", "chronosly"), 2 => __("Show organizers and allow to create a new one", "chronosly"));
                $return = "";
                foreach($options as $id=>$op){
                    $sel = "";
                    if($this->settings['organizer'] == $id) {
                        $sel = "selected='selected'";
                    }
                    $return .= "<option value='$id' $sel>$op</option>";
                }
                echo $return; ?>
            </select><br/>
            <div class="organizer_hide">
                <p><?php _e("Extra fields to show on organizer creation", "chronosly");?> </p>
                <label><?php _e("Organizer description", "chronosly")?></label> <input type="checkbox" name="organizer_description" value="1" <?php if($this->settings['organizer_description']) echo "checked" ?> /><br/>
                <label><?php _e("Organizer image", "chronosly")?></label> <input type="checkbox" name="organizer_image" value="1" <?php if($this->settings['organizer_image']) echo "checked" ?> /><br/>
                <label><?php _e("Organizer phone", "chronosly")?></label> <input type="checkbox" name="organizer_phone" value="1" <?php if($this->settings['organizer_phone']) echo "checked" ?> /><br/>
                <label><?php _e("Organizer mail", "chronosly")?></label> <input type="checkbox" name="organizer_mail" value="1" <?php if($this->settings['organizer_mail']) echo "checked" ?> /><br/>
                <label><?php _e("Organizer web", "chronosly")?></label> <input type="checkbox" name="organizer_web" value="1" <?php if($this->settings['organizer_web']) echo "checked" ?> /><br/>
            </div>
            <label><?php _e("Event place", "chronosly")?></label>
            <select class='place_select' name='place'>
                <?php
                $options = array(0 => __("Hidde", "chronosly"), 1 => __("Show places already created", "chronosly"), 2 => __("Show places and allow to create a new one", "chronosly"));
                $return ="";
                foreach($options as $id=>$op){
                    $sel = "";
                    if($this->settings['place'] == $id) {
                        $sel = "selected='selected'";
                    }
                    $return .= "<option value='$id' $sel>$op</option>";
                }
                echo $return; ?>
            </select><br/>
            <div class="place_hide">
                <p><?php _e("Extra fields to show on place creation", "chronosly");?> </p>
                <label><?php _e("Place description", "chronosly")?></label> <input type="checkbox" name="place_description" value="1" <?php if($this->settings['place_description']) echo "checked" ?> /><br/>
                <label><?php _e("Place image", "chronosly")?></label> <input type="checkbox" name="place_image" value="1" <?php if($this->settings['place_image']) echo "checked" ?> /><br/>
                <label><?php _e("Place address", "chronosly")?></label> <input type="checkbox" name="place_address" value="1" <?php if($this->settings['place_address']) echo "checked" ?> /><br/>
                <label><?php _e("Place city", "chronosly")?></label> <input type="checkbox" name="place_city" value="1" <?php if($this->settings['place_city']) echo "checked" ?> /><br/>
                <label><?php _e("Place country", "chronosly")?></label> <input type="checkbox" name="place_country" value="1" <?php if($this->settings['place_country']) echo "checked" ?> /><br/>
                <label><?php _e("Place state", "chronosly")?></label> <input type="checkbox" name="place_state" value="1" <?php if($this->settings['place_state']) echo "checked" ?> /><br/>
                <label><?php _e("Place Postal Code", "chronosly")?></label> <input type="checkbox" name="place_pc" value="1" <?php if($this->settings['place_pc']) echo "checked" ?> /><br/>
                <label><?php _e("Place phone", "chronosly")?></label> <input type="checkbox" name="place_phone" value="1" <?php if($this->settings['place_phone']) echo "checked" ?> /><br/>
                <label><?php _e("Place mail", "chronosly")?></label> <input type="checkbox" name="place_mail" value="1" <?php if($this->settings['place_mail']) echo "checked" ?> /><br/>
                <label><?php _e("Place web", "chronosly")?></label> <input type="checkbox" name="place_web" value="1" <?php if($this->settings['place_web']) echo "checked" ?> /><br/>

            </div>
            <label><?php _e("Tickets", "chronosly")?></label> <input type="checkbox" name="tickets" value="1" <?php if($this->settings['tickets']) echo "checked" ?> /><br/>
            <br/><br/>

            <h3><?php _e("Mail Settings", "chronosly");?></h3>
            <p><?php _e("Configure the mail recibed when one user submits an envent","chronosly"); ?></p>
            <label><?php _e("From name", "chronosly")?></label> <input type="text" name="form_from_n" value="<?php echo $this->settings['form_from_n']; ?>" /><br/>
            <label><?php _e("From email", "chronosly")?></label> <input type="text" name="form_from_m" value="<?php echo $this->settings['form_from_m']; ?>" /><br/>
            <label><?php _e("To (comma separated emails)", "chronosly")?></label> <input type="text" name="form_to" value="<?php echo $this->settings['form_to']; ?>" /><br/>
            <label><?php _e("Subject", "chronosly")?></label> <input type="text" name="form_subject" value="<?php echo $this->settings['form_subject']; ?>" /><br/>
            <label><?php _e("Mail content", "chronosly")?></label><span class="info"></span>
            <div class="info-hide"><?php echo __("Preconfigured mail is sent when you let it blank", "chronosly")."<br/><br/>".__("Allowed tags are:", "chronosly")."<br/>".__("#user : username and email", "chronosly")."<br/>".__("#event-link : admin event link to validate", "chronosly")."<br/>".__("#category-link : admin category link to validate", "chronosly")."<br/>".__("#organizer-link : admin organizer link to validate", "chronosly")."<br/>".__("#place-link : admin place link to validate", "chronosly");?></div><br/>
            <input type="hidden" class="form_content_event" value="<?php echo $this->settings['form_content_event']; ?>"/>
            <input type="hidden" class="form_content_cat" value="<?php echo $this->settings['form_content_cat']; ?>"/>
            <input type="hidden" class="form_content_organizer" value="<?php echo $this->settings['form_content_organizer']; ?>"/>
            <input type="hidden" class="form_content_place" value="<?php echo $this->settings['form_content_place']; ?>"/>
            <?php wp_editor($this->settings['form_content'], "form_content"); ?>
            <br/><br/>
            <h3><?php _e("License & Updates", "chronosly");?></h3>
            <label><?php _e("License key for updates", "chronosly")?></label> <input type="text" name="license" value="<?php echo $this->settings['license']; ?>"/> <span class="info"></span>
            <div class="info-hide"><?php _e("provide the license sent to your email for alowing future updates of this addon")?></div><br/>
            <label><?php _e("Enable auto update", "chronosly")?></label> <input type="checkbox" name="autoupdate" value="1" <?php if($this->settings['autoupdate']) echo "checked" ?> /><br/>



            <?php
            do_action("chronosly-addon-foot",$this->id);
        }



        //set the class name for calling addon in settings page
        function addon_add_item($addons){
            $merge = array_merge($addons, array($this->id => "Chronosly_Frontend_Event_Submission"));
            asort($merge);
            return $merge;
        }

        function addon_add_menu_item($addon_menu){
            $merge = array_merge($addon_menu, array($this->settings_url => "Frontend Event Submission"));
            asort($merge);
            return $merge;
        }

        function include_css($print= 0){
            if($print){
                wp_register_style( 'chronosly-frontend-event-submission', CHRONOSLY_ADDONS_URL.'/frontend_event_submission/style.css');
                wp_print_styles('chronosly-frontend-event-submission');

                wp_print_scripts('jquery-ui-datepicker');
                wp_register_style( 'chronosly-admin-jquery-ui-css', CHRONOSLY_URL.'/css/smoothness/jquery-ui-1.10.4.custom.css');
                wp_print_styles('chronosly-admin-jquery-ui-css');
                wp_register_script( 'chronosly-frontend-event-submission-front-js', CHRONOSLY_ADDONS_URL.'/frontend_event_submission/front.js');
                wp_print_scripts('chronosly-frontend-event-submission-front-js');
           } else {
                wp_register_style( 'chronosly-frontend-event-submission', CHRONOSLY_ADDONS_URL.'/frontend_event_submission/style.css');
                wp_enqueue_style('chronosly-frontend-event-submission');

                wp_enqueue_script('jquery-ui-datepicker');
                wp_register_style( 'chronosly-admin-jquery-ui-css', CHRONOSLY_URL.'/css/smoothness/jquery-ui-1.10.4.custom.css');
                wp_enqueue_style('chronosly-admin-jquery-ui-css');
                wp_register_script( 'chronosly-frontend-event-submission-front-js', CHRONOSLY_ADDONS_URL.'/frontend_event_submission/front.js');
                wp_enqueue_script('chronosly-frontend-event-submission-front-js');
            }

        }

        function include_css2(){
            $this->include_css(1);
        }

        function admin_js(){

            wp_register_script( 'chronosly-frontend-event-submission-admin-js', CHRONOSLY_ADDONS_URL.'//frontend_event_submission/admin.js');
            wp_enqueue_script('chronosly-frontend-event-submission-admin-js');
        }

        function shortcode($atts){

            add_action("chronosly_custom_frontend_css", array(&$this,"include_css2"));//add custom styles  and js to frontend

            do_action("chronosly_custom_frontend_css");
            $settings = unserialize(get_option("chronosly_settings_{$this->id}"));
            $this->check_submit($settings);
            $this->print_form($settings);

         }

        function check_submit($settings){
            global $_FILES, $_POST;
            if(isset($_POST['ch-fes-nonce'])) {
                //save vars
                if ( wp_verify_nonce( $_POST['ch-fes-nonce'], "chronosly_fes_form" ) ){
                    if(!$settings["user"]) return;

                    $error = "";
                     if($settings["captcha"]){

                        $captcha_instance = new ReallySimpleCaptcha();

                        if(!$captcha_instance->check( $_POST["prefijo"], $_POST["captcha"] )){
                            $error .= __("Incorrect validation code", "chronosly")."<br/>";
                        }
                        $captcha_instance->remove( $_POST["prefijo"] );

                    }

                    //check registered required
                    if($settings["user"] == 1 and !is_user_logged_in()) {
                        $error .= __("You must login to send an event", "chronosly")."<br/>";
                        return;
                    }
                    //check registered or name and mail submit
                    $user = "";
                    if($settings["user"] == 2 and !is_user_logged_in() and (!$_POST["u-name"] or !$_POST["u-mail"])) {
                        $error .= __("You must login or provide user and mail to send an event", "chronosly")."<br/>";
                    }
                    else if($settings["user"] == 2 and !is_user_logged_in()){
                        $user = $_POST["u-name"]." (".$_POST["u-mail"].")";
                    } else {
                        $current_user = wp_get_current_user();
                        $user = $current_user->user_login." (".$current_user->user_email.")";
                    }

                    if(!isset($_POST["e-title"]) or !$_POST["e-title"]){
                        $error .= __("Event title is required","chronosly")."<br/>";
                    }
                     if(!isset($_POST["ev-from"]) or !$_POST["ev-from"]){
                        $error .= __("Event date is required","chronosly")."<br/>";
                    }



                   //future add required fields
                   if($error){
                       echo "<div class='ch-fes-message red'>".$error."</div>";

                       return;
                   }
                    //save event elements
                    $title = $_POST["e-title"];
                   $desc = (isset($_POST["e-description"])?$_POST["e-description"]:"");
                   $cats = (($settings["category"] and  isset($_POST["category"]))?$_POST["category"]:array());
                   $from = (isset($_POST["ev-from"])?$_POST["ev-from"]:"");
                   $fromh = (isset($_POST["ev-from-h"])?intval($_POST["ev-from-h"]):"");
                   $fromm = (isset($_POST["ev-from-m"])?$_POST["ev-from-m"]:"");
                   if(isset($_POST["from_am_pm"]) and $_POST["from_am_pm"] == "pm") $fromh += 12;

                   $to = (isset($_POST["ev-to"])?$_POST["ev-to"]:"");
                   $toh = (isset($_POST["ev-to-h"])?$_POST["ev-to-h"]:"");
                   $tom = (isset($_POST["ev-to-m"])?$_POST["ev-to-m"]:"");
                   if(isset($_POST["to_am_pm"]) and $_POST["to_am_pm"] == "pm") $toh += 12;

                   //add category
                    $cid = $ctitle = "";
                   if($settings["category"] == 2 and isset($_POST["c-title"]) and $_POST["c-title"]){
                       $newcat = wp_insert_term($_POST["c-title"],'chronosly_category');

                       if(!is_wp_error($newcat)) {
                           $cid = $newcat["term_id"];
                           $ctitle = $_POST["c-title"];
                           $cats[]= $cid;

                       }

                   }
                   $post = array(
                       'post_content' => $desc,
                       'post_status' => 'pending',
                       'post_title' => $_POST["e-title"],
                       'post_type' => 'chronosly',
                       'tax_input' => array(
                           'chronosly_category' => $cats
                       )
                    );

                    if(!is_user_logged_in()) $post["post_author"] = $settings["user_id"];

                    // Insert the event into the database
                    $id = wp_insert_post( $post,$wp_error );
                    if(is_wp_error($id)) {
                            echo "<div class='ch-fes-message red'>".__("An error ocurred sending your event. Please try again.")."</div>";
                            return;

                    }

                   //date
                    add_post_meta($id, "ev-from", $from);
                    add_post_meta($id, "ev-from-h", $fromh);
                    add_post_meta($id, "ev-from-m", $fromm);
                    add_post_meta($id, "ev-to", $to);
                    add_post_meta($id, "ev-to-h", $toh);
                    add_post_meta($id, "ev-to-m", $tom);

                    //ticket
                    if($settings["tickets"] and ($_POST["ticket-title"] or $_POST["ticket-link"])){
                        $tickets[]= null;
                        $tickets[] = array(
                            array("name" => "soldout", "value"=>""),
                            array("name" => "sale", "value"=>""),
                            array("name" => "title", "value"=>$_POST["ticket-title"]),
                            array("name" => "price", "value"=>$_POST["ticket-price"]),
                            array("name" => "capacity", "value"=>$_POST["ticket-capacity"]),
                            array("name" => "sales-price", "value"=>$_POST["sales-price"]),
                            // array("name" => "min-user", "value"=>$_POST["ticket-min-user"]),
                            // array("name" => "max-user", "value"=>$_POST["ticket-max-user"]),
                            // array("name" => "start-time", "value"=>$_POST["ticket-start-time"]),
                            // array("name" => "end-time", "value"=>$_POST["ticket-end-time"]),
                            array("name" => "link", "value"=>$_POST["ticket-link"]),
                            array("name" => "notes", "value"=>$_POST["ticket-notes"])
                        );
                        $enct =  json_encode(array("tickets" => $tickets),JSON_UNESCAPED_UNICODE);

			if(!$enct) $enct = json_encode(array("tickets" => $tickets));

                        add_post_meta($id, "tickets", $enct);
                    }

                   if($settings["image"]){
                       if(!empty($_FILES['e-image']["name"])) {
                            if ( ! function_exists( 'media_handle_upload' ) ) {
                                // These files need to be included as dependencies when on the front end.
                                require_once( ABSPATH . 'wp-admin/includes/image.php' );
                                require_once( ABSPATH . 'wp-admin/includes/file.php' );
                                require_once( ABSPATH . 'wp-admin/includes/media.php' );
                            }
                            $attachment_id = media_handle_upload( 'e-image',  $id);
                            //print_r($attachment_id);
                            if ( is_wp_error( $attachment_id ) ) {
                                $error .= __('There was an error uploading your event image.' , "chronosly")."<br/>";
                            } else {
                                add_post_meta($id, "_thumbnail_id", $attachment_id);
                            }
                        }
                    }


                    //save organizer element
                    $orgs = (($settings["organizer"] and  isset($_POST["organizer"]))?$_POST["organizer"]:array());
                    $oid = $otitle = "";
                    if($settings["organizer"] == 2 and isset($_POST["o-title"]) and $_POST["o-title"]){
                        $desc = (($settings["organizer_description"] and isset($_POST["o-description"]))?$_POST["o-description"]:"");
                        $post = array(
                            'post_content' => $desc,
                            'post_status' => 'pending',
                            'post_title' => $_POST["o-title"],
                            'post_type' => 'chronosly_organizer',
                         );

                         if(!is_user_logged_in()) $post["post_author"] = $settings["user_id"];


                        // Insert the event into the database
                        $oid = wp_insert_post( $post,$wp_error );
                        if(is_wp_error($oid)) {
                            $error .= __("Organizer was not created.", "chronosly")."</div>";

                        } else {
                            $orgs[] = $oid;
                            $otitle = $_POST["o-title"];

                        }


                        if($settings["organizer_image"] and !empty($_FILES['o-image']["name"]) and !is_wp_error($oid)) {

                            if ( ! function_exists( 'media_handle_upload' ) ) {
                                // These files need to be included as dependencies when on the front end.
                                require_once( ABSPATH . 'wp-admin/includes/image.php' );
                                require_once( ABSPATH . 'wp-admin/includes/file.php' );
                                require_once( ABSPATH . 'wp-admin/includes/media.php' );
                            }
                            $attachment_id = media_handle_upload( 'o-image',  1);
                            //print_r($attachment_id);
                            if ( is_wp_error( $attachment_id ) ) {
                                $error .= __('There was an error uploading your organizer image.' , "chronosly")."<br/>";
                            } else {
                                add_post_meta($oid, "_thumbnail_id", $attachment_id);
                            }
                        }

                        if($settings["organizer_phone"] and !is_wp_error($oid) and isset($_POST["o-phone"]) and $_POST["o-phone"]) add_post_meta($oid, "evo_phone", $_POST["o-phone"]);
                        if($settings["organizer_mail"] and !is_wp_error($oid) and isset($_POST["o-mail"]) and $_POST["o-phone"]) add_post_meta($oid, "evo_mail", $_POST["o-mail"]);
                        if($settings["organizer_web"] and !is_wp_error($oid) and isset($_POST["o-web"]) and $_POST["o-web"]) add_post_meta($oid, "evo_web", $_POST["o-web"]);

                    }
                    //save organizer on event
                    if(count($orgs)) add_post_meta($id, "organizer", $orgs);


                    //save place element
                    $places = (($settings["place"] and  isset($_POST["places"]))?$_POST["places"]:array());
                    $pid = $ptitle = "";
                    if($settings["place"] == 2 and isset($_POST["p-title"]) and $_POST["p-title"]){
                        $desc = (($settings["place_description"] and isset($_POST["p-description"]))?$_POST["p-description"]:"");
                        $post = array(
                            'post_content' => $desc,
                            'post_status' => 'pending',
                            'post_title' => $_POST["p-title"],
                            'post_type' => 'chronosly_places',
                        );

                        if(!is_user_logged_in()) $post["post_author"] = $settings["user_id"];


                        // Insert the event into the database
                        $pid = wp_insert_post( $post,$wp_error );
                        if(is_wp_error($pid)) {
                            $error .= __("Place was not created.", "chronosly")."</div>";

                        } else {
                            $places[] = $pid;
                            $ptitle = $_POST["p-title"];
                        }


                        if($settings["place_image"] and !empty($_FILES['p-image']["name"]) and !is_wp_error($pid)) {
                            if ( ! function_exists( 'media_handle_upload' ) ) {
                                // These files need to be included as dependencies when on the front end.
                                require_once( ABSPATH . 'wp-admin/includes/image.php' );
                                require_once( ABSPATH . 'wp-admin/includes/file.php' );
                                require_once( ABSPATH . 'wp-admin/includes/media.php' );
                            }
                            $attachment_id = media_handle_upload( 'p-image',  1);
                            //print_r($attachment_id);
                            if ( is_wp_error( $attachment_id ) ) {
                                $error .= __('There was an error uploading your place image.' , "chronosly")."<br/>";
                            }else {
                                add_post_meta($pid, "_thumbnail_id", $attachment_id);
                            }
                        }
                        if($settings["place_address"] and !is_wp_error($pid) and isset($_POST["p-dir"]) and $_POST["p-dir"]) add_post_meta($pid, "evp_dir", $_POST["p-dir"]);
                        if($settings["place_city"] and !is_wp_error($pid) and isset($_POST["p-city"]) and $_POST["p-city"]) add_post_meta($pid, "evp_city", $_POST["p-city"]);
                        if($settings["place_country"] and !is_wp_error($pid) and isset($_POST["p-country"]) and $_POST["p-country"]) add_post_meta($pid, "evp_country", $_POST["p-country"]);
                        if($settings["place_state"] and !is_wp_error($pid) and isset($_POST["p-state"]) and $_POST["p-state"]) add_post_meta($pid, "evp_state", $_POST["p-state"]);
                        if($settings["place_pc"] and !is_wp_error($pid) and isset($_POST["p-pc"]) and $_POST["p-pc"]) add_post_meta($pid, "evp_pc", $_POST["p-pc"]);
                        if($settings["place_phone"] and !is_wp_error($pid) and isset($_POST["p-phone"]) and $_POST["p-phone"]) add_post_meta($pid, "evp_phone", $_POST["p-phone"]);
                        if($settings["place_mail"] and !is_wp_error($pid) and isset($_POST["p-mail"]) and $_POST["p-phone"]) add_post_meta($pid, "evp_mail", $_POST["p-mail"]);
                        if($settings["place_web"] and !is_wp_error($pid) and isset($_POST["p-web"]) and $_POST["p-web"]) add_post_meta($pid, "evp_web", $_POST["p-web"]);

                    }
                    //save place on event
                    if(count($places)) add_post_meta($id, "places", $places);
                    echo "<div class='ch-fes-message green'>".__("Your event has been sent and is awaiting approval", "chronosly")."</div>";
                    if($error){

                        echo "<div class='ch-fes-message red'>".$error."</div>";

                    }

                    $this->send_mail($settings, $user, $id, $cid, $oid, $pid, $title, $ctitle, $otitle, $ptitle);

                }
            }
        }



        function send_mail($settings, $user, $id, $cid, $oid, $pid, $title, $ctitle, $otitle, $ptitle){
            if(!$settings["form_to"]) $settings["form_to"] = get_settings('admin_email');
            if(!$settings["form_subject"]) $settings["form_subject"] = __("New event submitted", "chronosly");
            if(!$settings["form_content"]) {
                $settings["form_content"] = $settings["form_content_event"];
                if($cid) $settings["form_content"] .= $settings["form_content_cat"];
                if($oid) $settings["form_content"] .= $settings["form_content_organizer"];
                if($pid) $settings["form_content"] .= $settings["form_content_place"];

            }
            //check links included for revision
            if($user and !stripos($settings["form_content"], "#user"))  $settings["form_content"] .= "<p>#user ".__("has sent a new event", "chronosly")." <b>'$title'</b></p>";
            if($oid or $pid or $cid){
                $settings["form_content"] .="<p>". __("This event has been created with new organizers, places and/or categories, so these ones must be approved before event validation to link within the event.", "chronosly")."</p>";

            }
            if($cid and !stripos($settings["form_content"], "#category-link"))  $settings["form_content"] .= "<p>".__("Category", "chronosly")." #category-link ".__("is waiting for validation", "chronosly")."</p>";
            if($oid and !stripos($settings["form_content"], "#organizer-link"))  $settings["form_content"] .= "<p>".__("Organizer", "chronosly")." #organizer-link ".__("is waiting for validation", "chronosly")."</p>";
            if($pid and !stripos($settings["form_content"], "#place-link"))  $settings["form_content"] .= "<p>".__("Place", "chronosly")." #place-link ".__("is waiting for validation", "chronosly")."</p>";
            if($oid or $pid or $cid){
                $settings["form_content"] .="<br/><p>". __("When new organizers, places and/or categories are validated, ", "chronosly")."</p>";

            }
            if($id and !stripos($settings["form_content"], "#event-link"))  $settings["form_content"] .= "<p>".__("You can approve event", "chronosly")." #event-link</p>";

            //replace custom tags
            $from = array("#user","#event-link","#category-link","#organizer-link","#place-link");
            $to = array(
                $user,
                "<a href='".get_site_url()."/wp-admin/post.php?post=$id&action=edit'>".$title."</a>",
                "<a href='".get_site_url()."/wp-admin/edit-tags.php?action=edit&taxonomy=chronosly_category&tag_ID=$cid'>".$ctitle."</a>",
                "<a href='".get_site_url()."/wp-admin/post.php?post=$oid&action=edit'>".$otitle."</a>",
                "<a href='".get_site_url()."/wp-admin/post.php?post=$pid&action=edit'>".$ptitle."</a>"
            );
            $settings["form_content"] = str_replace($from, $to, $settings["form_content"]);
            $headers = array('Content-Type: text/html; charset=UTF-8');
            if($settings["form_from_n"]) $headers[]= 'From: "'. $settings["form_from_n"].'" <'. $settings["form_from_m"].'>';

            wp_mail($settings["form_to"], $settings["form_subject"], $settings["form_content"],$headers);
        }



        function print_form($settings){
           if(!$settings["user"]) return;
           if($settings["user"] == 1 and !is_user_logged_in()) {
               echo "<div class='ch-fes-nologed'>".__("You must login to send an event", "chronosly")."</div>";
               return;
           }
            echo "
            <form class='ch-fes-form ch-".$settings["template"]."' method='POST' action='".$_SERVER['REQUEST_URI']."' enctype='multipart/form-data'>";
            if($settings["user"] == 2 and !is_user_logged_in()) {
                echo "<div class='fes-2-column'><div class='column'><label>".__("Your name","chronosly")."</label><input name='u-name' type='text' value='".$_POST["u-name"]."'/></div>";
                echo "<div class='column'><label>".__("Your email","chronosly")."</label><input name='u-mail' type='text' value='".$_POST["u-mail"]."'/></div></div>";

            }

                wp_nonce_field( "chronosly_fes_form", 'ch-fes-nonce' );
                echo "<label>".__("Event title","chronosly")."</label><input name='e-title' type='text' value='".$_POST["e-title"]."'/>";
                if($settings["description"]) echo "<label>".__("Event description","chronosly")."</label><textarea name='e-description'>".$_POST["e-description"]."</textarea>";
                if($settings["image"]) echo "<label>".__("Image","chronosly")."</label> <input type='file' name='e-image' value='' size='25' />";
                $this->date_html_inputs($settings);
                if($settings["tickets"]) $this->tickets_html_inputs();
                echo "<div style='clear:both;'></div>";
                if($settings["category"]) $this->category_html_inputs($settings["category"]);
                if($settings["organizer"]) $this->organizer_html_inputs($settings["organizer"], $settings);
                if($settings["place"]) $this->place_html_inputs($settings["place"], $settings);
            echo "<div style='clear:both;'></div>";

            if($settings["captcha"]){
                $captcha_instance = new ReallySimpleCaptcha();
                // $captcha_instance->bg = array( 0, 0, 0 );
                $word = $captcha_instance->generate_random_word();
                $prefix = mt_rand();
                $url = get_option('siteurl');
                $dir = substr($captcha_instance->tmp_dir, stripos($captcha_instance->tmp_dir, "/wp-content"));
                $url .= $dir;
                echo "<label>".__("Validation code","chronosly")."</label>";
                echo "<img src='".$url."/".$captcha_instance->generate_image( $prefix, $word )."' />";
                echo "<input type='text' name='captcha' value='' />";
                echo "<input type='hidden' name='prefijo' value='$prefix' />";
                echo "<div style='clear:both;'></div>";
            }

            echo "
                <input class='ch-fes-submit' type='submit' value='".__("Submit event","chronosly")."' />
            </form>
            ";
        }

        function date_html_inputs($settings){
         ?>
            <span class="ch-fes-title"><?php echo __("Dates", "chronosly"); ?></span>
            <label for="from"><?php echo __("From", "chronosly"); ?></label>
            <input type="text" id="ev-from" name="ev-from" value="<?php echo $_POST["ev-from"];?>" />
            <label for="from-h"><?php echo __("Hour", "chronosly"); ?></label>

            <input type="text" id="ev-from-h" name="ev-from-h"  value="<?php echo $_POST["ev-from-h"];?>"  />:
            <input type="text" id="ev-from-m" name="ev-from-m"  value="<?php echo $_POST["ev-from-m"];?>" />
            <?php if($settings["12h_format"]) {
                echo "<select name='from_am_pm'><option value='am'>".__("AM", "chronosly")."</option><option value='pm'>".__("PM", "chronosly")."</option></select>";
            }?>
            <br/>
            <label for="to"><?php echo __("To", "chronosly"); ?></label>
            <input type="text" id="ev-to" name="ev-to"  value="<?php echo $_POST["ev-to"];?>"  />
            <label for="from-h"><?php echo __("Hour", "chronosly"); ?></label>

            <input type="text" id="ev-to-h" name="ev-to-h"  value="<?php echo $_POST["ev-to-h"];?>" />:
            <input type="text" id="ev-to-m" name="ev-to-m"  value="<?php echo $_POST["ev-to-m"];?>" />
             <?php if($settings["12h_format"]) {
                echo "<select name='to_am_pm'><option value='am'>".__("AM", "chronosly")."</option><option value='pm'>".__("PM", "chronosly")."</option></select>";
            }?>
        <?php
        }

        function tickets_html_inputs(){
            echo '<span class="ch-fes-title">'.__("Ticket", "chronosly").'</span>';

                echo "<ul><li class='ticket-head'><span class='title'>".__("Title","chronosly")."</span><span class='price'>".__("Price","chronosly")."</span><span class='price'>".__("Sale Price","chronosly")."</span><span class='capacity'>".__("Capacity","chronosly")."</span><span class='link'>".__("Link","chronosly")."</span></li>";
            ?>
            <li class="ticket-inputs">
                <input type='text'  class='title'  name='ticket-title' value='<?php echo $_POST["ticket-title"];?>' />
                <input    type='text' class='price' name='ticket-price' value='<?php echo $_POST["ticket-price"];?>' />
                <input    type='text' class='price' name='sales-price' value='<?php echo $_POST["sales-price"];?>' />
                <input   type='text' class='capacity' name='ticket-capacity' value='<?php echo $_POST["ticket-capacity"];?>' />
             <!--    <input   type='text' class='min-user' name='ticket-min-user' value='<?php echo $_POST["ticket-min-user"];?>' />
                <input   type='text' class='max-user' name='ticket-max-user' value='<?php echo $_POST["ticket-max-user"];?>' />
                <input   type='text' class='start-time' name='ticket-start-time' value='<?php echo $_POST["ticket-start-time"];?>' />
                <input   type='text' class='end-time' name='ticket-end-time' value='<?php echo $_POST["ticket-end-time"];?>' />
             -->    <input  type='text' class='link' name='ticket-link' value='<?php echo $_POST["ticket-link"];?>' /><br/>
                <label><?php _e("notes", "chronosly");?></label>
                <textarea  name='ticket-notes' class='ticket-notes'><?php echo $_POST["ticket-notes"];?></textarea>
            </li></ul>
           <?php
        }

        function category_html_inputs($type){
            echo '<div class="ch-fes-box"><span class="ch-fes-title">'. __("Category", "chronosly").'</span>';
            $args = array('orderby'=>'asc','hide_empty'=>false);

            $posts = get_terms("chronosly_category", $args);
            echo '<div class="ch-fes-category-box">';
            foreach ($posts as $post) {
                $checked = false;
                if(is_array($_POST["category"]) and in_array($post->term_id,$_POST["category"])) $checked = true;
                ?>
                <input type="checkbox"  name="category[]" <?php if($checked) echo "checked" ?> value="<?php echo $post->term_id; ?>" /> <?php echo $post->name; ?><br/>

            <?php }
            echo "</div>";
            if($type == 2) {
                echo "
                <a id='chronosly_category-add-toggle'>+ ".__("Add new Category", "chronosly")."</a>
                <div id='chronosly_category-add' class='wp-hidden-child'>
                     <label>".__("Category name", "chronosly")."</label><input type='text' name='c-title' value='".$_POST["c-title"]."' />
                 </div>";
            }
            echo "</div>";
        }

        function organizer_html_inputs($type, $settings){
            echo '<div class="ch-fes-box"><span class="ch-fes-title">'. __("Organizer", "chronosly").'</span>';
            echo '<div class="ch-fes-organizer-box">';

            $posts = get_posts( 'post_type=chronosly_organizer&numberposts=-1&orderby=title&order=ASC&suppress_filters=0');

            foreach ($posts as $post) {
                $checked = false;
                if(is_array($_POST["organizer"]) and in_array($post->ID,$_POST["organizer"])) $checked = true;

                ?>
                <input type="checkbox"  name="organizer[]" <?php if($checked) echo "checked" ?> value="<?php echo $post->ID; ?>" /> <?php echo $post->post_title; ?><br/>

            <?php } ?>
            </div>

            <?php if($type == 2) { ?>

                <div class='add-organizer'>
                    <a id="chronosly_organizer-add-toggle">+ <?php _e("Add new Organizer", "chronosly");?></a>
                    <div id="chronosly_organizer-add" class="wp-hidden-child">
                        <label><?php _e("Name", "chronosly");?></label> <input type='text' name='o-title' value="<?php echo $_POST["o-title"]; ?>"/>
                        <?php
                        if($settings["organizer_description"]) echo "<label>".__("Description","chronosly")."</label><textarea name='o-description'>".$_POST["o-description"]."</textarea>";
                        if($settings["organizer_image"]) echo "<label>".__("Image","chronosly")."</label> <input type='file' name='o-image' value='' size='25' />";
                        echo "<div class='fes-3-column'>";
                        if($settings["organizer_phone"]) echo '<div class="column"><label>'.__("Phone", "chronosly").'</label> <input id="phone" type="text" name="o-phone"  value="'.$_POST["o-phone"].'"  /></div>';
                        if($settings["organizer_mail"]) echo '<div class="column"><label>'.__("Mail", "chronosly").'</label> <input id="mail" type="text" name="o-mail"   value="'.$_POST["o-mail"].'"  /></div>';
                        if($settings["organizer_web"]) echo '<div class="column"><label>'.__("Web", "chronosly").'</label> <input id="web" type="text" name="o-web"  value="'.$_POST["o-web"].'"  /></div>';
                        ?>
                    </div>
                </div>
                </div> <?php
            }
            echo "</div>";
        }


        function place_html_inputs($type, $settings){

            echo '<div class="ch-fes-box"><span class="ch-fes-title">'. __("Place", "chronosly").'</span>';
            echo '<div class="ch-fes-place-box">';
            $posts = get_posts( 'post_type=chronosly_places&numberposts=-1&orderby=title&order=ASC&suppress_filters=0');

                foreach ($posts as $post) {
                    $checked = false;
                    if(is_array($_POST["places"]) and in_array($post->ID,$_POST["places"])) $checked = true;

                    ?>
                    <input type="checkbox"  name="places[]" <?php if($checked) echo "checked" ?> value="<?php echo $post->ID; ?>" /> <?php echo $post->post_title; ?><br/>

                <?php }
            echo "</div>";
            if($type == 2) { ?>
                <div class='add-place'>
                    <a id="chronosly_place-add-toggle">+ <?php _e("Add new Place", "chronosly");?></a>
                    <div id="chronosly_place-add" class="wp-hidden-child">
                        <label><?php _e("Name", "chronosly");?></label> <input type='text' name='p-title' value="<?php echo $_POST["p-title"];?>" />
                            <?php if($settings["place_description"]) echo "<label>".__("Description","chronosly")."</label><textarea name='p-description'>".$_POST["p-description"]."</textarea>";
                            if($settings["place_image"]) echo "<label>".__("Image","chronosly")."</label> <input type='file' name='p-image' value='' size='25' />";
                            if($settings["place_address"]) echo '<label>'.__("Address", "chronosly").'</label> <input id="dir" type="text" name="p-dir"  value="'.$_POST["p-dir"].'"  />';
                            echo "<div class='fes-3-column'>";
                            if($settings["place_city"]) echo '<div class="column"><label>'.__("City", "chronosly").'</label> <input id="city" type="text" name="p-city"  value="'.$_POST["p-city"].'"  /></div>';
                            if($settings["place_country"]) echo '<div class="column"><label>'.__("Country", "chronosly").'</label> <input id="country" type="text" name="p-country"  value="'.$_POST["p-country"].'"  /></div>';
                            if($settings["place_state"]) echo '<div class="column"><label>'.__("State", "chronosly").'</label> <input id="state" type="text" name="p-state"  value="'.$_POST["p-state"].'"  /></div>';
                            if($settings["place_pc"]) echo '<div class="column"><label>'.__("Postal Code", "chronosly").'</label> <input id="pc" type="text" name="p-pc"  value="'.$_POST["p-pc"].'"  /></div>';
                            if($settings["place_phone"]) echo '<div class="column"><label>'.__("Phone", "chronosly").'</label> <input id="phone" type="text" name="p-phone"  value="'.$_POST["p-phone"].'"  /></div>';
                            if($settings["place_mail"]) echo '<div class="column"><label>'.__("Mail", "chronosly").'</label> <input id="mail" type="text" name="p-mail"  value="'.$_POST["p-mail"].'"  /></div>';
                            if($settings["place_web"]) echo '<div class="column"><label>'.__("Web", "chronosly").'</label> <input id="web" type="text" name="p-web"  value="'.$_POST["p-web"].'"  /></div>';?>

                        </div>
                    </div>
                </div>
            <?php }
            echo "</div>";
        }



    }
}

//initialize addon
global $Chronosly_Frontend_Event_Submission;
$Chronosly_Frontend_Event_Submission = new Chronosly_Frontend_Event_Submission();
$Chronosly_Frontend_Event_Submission->addon_settings(1);
