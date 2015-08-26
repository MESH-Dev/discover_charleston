<?php
/*
    Filter and Sort
*/

if(!class_exists("Chronosly_Filter_and_Sort")){

    class Chronosly_Filter_and_Sort extends Chronosly_Extend {
        public $id = "filter_and_sort";
        public $name = "Filter and Sort";
        public $settings_url = "chronosly_filter_and_sort_settings";
        public $description = "Allow you to show your content based on given set of criteria";
        public $version = "1.9";
        public $settings;
        private $addon_page_hook;

        //construct addon, calling all filtes needed for extend Chronosly
        function __construct(){

                add_action('chronosly-before-events', array(&$this, 'print_filter_bar'));
                add_action( 'wp_ajax_nopriv_chronosly_filter_and_sort', array(&$this, 'js_filter_and_sort' ));
                add_action( 'wp_ajax_chronosly_filter_and_sort', array(&$this, 'js_filter_and_sort' ));
                add_action("chronosly_custom_frontend_css", array(&$this,"include_css"));//add custom styles  and js to frontend

            add_filter("chronosly-shortcode-extra-params", array(&$this,"extend_shortcode_params"));
            add_filter("chronosly-shortcode-extra-params-run", array(&$this,"extend_shortcode_params_run"));




             if(is_admin()){
                 add_filter("chronosly_addons_settings_item", array(&$this,"addon_add_item"), 0);//add new item to chronosly addons config
                 add_filter("chronosly_addons_settings_menu_item", array(&$this,"addon_add_menu_item"), 0);//add new menu item to chronosly addons config
                 if(!has_action("chronosly_remove_{$this->id}")) add_action("chronosly_remove_{$this->id}", array(&$this,"remove")); //function for delete addon
                 add_action("admin_menu" , array(&$this,"addon_settings"));//add new page for calling in chronosly addons setting page
                 add_action("chronosly_custom_backend_css", array(&$this,"include_css"));//add custom styles  and js to frontend
             }

        }

        public function extend_shortcode_params($args){
            $ret= array(
                "filter" => "",
                "from" => "",
                "to" => "",
                "event_name" => "",
                "price_min" => "",
                "price_max" => "",
                "organizer_name" => "",
                "place_name" => "",
                "place_address" => "",
                "place_city" => "",
                "place_country" => "",
                "place_state" => "",
                "place_pc" => "",
                "place_near" => "",
                "author" => "",
                "orderby" => "",
                "orderdir" => "",
            );
            return array_merge($args,$ret);
        }
        public function extend_shortcode_params_run($code){

            if($code["filter"]) {
                $_REQUEST["before_events"] = 1;
            }
            if($code["from"]) {
                $_REQUEST["ch_from"] = $code["from"];
            }
            if($code["to"]) {
                $_REQUEST["ch_to"] = $code["to"];
            }
            if($code["event_name"]) {
                if($code["id"]) $code["id"] .= ",".$this->get_events("name", $code["event_name"]);
                else $code["id"] = $this->get_events("name", $code["event_name"]);
            }
            if($code["organizer_name"]) {
                if($code["organizer"]) $code["organizer"] .= ",".$this->get_organizers("name", $code["organizer_name"]);
                else $code["organizer"] = $this->get_organizers("name", $code["organizer_name"]);
            }
            if($code["place_name"]) {
                if($code["place"]) $code["place"] .= ",".$this->get_places("name", $code["place_name"]);
                else $code["place"] = $this->get_places("name", $code["place_name"]);
            }
            if($code["place_address"]) {
                if($code["place"]) $code["place"] .= ",".$this->get_places("address", $code["place_address"]);
                else $code["place"] = $this->get_places("address", $code["place_address"]);
            }
            if($code["place_city"]) {
                if($code["place"]) $code["place"] .= ",".$this->get_places("city", $code["place_city"]);
                else $code["place"] = $this->get_places("city", $code["place_city"]);
            }
            if($code["place_country"]) {
                if($code["place"]) $code["place"] .= ",".$this->get_places("country", $code["place_country"]);
                else $code["place"] = $this->get_places("country", $code["place_country"]);
            }
            if($code["place_state"]) {
                if($code["place"]) $code["place"] .= ",".$this->get_places("state", $code["place_state"]);
                else $code["place"] = $this->get_places("state", $code["place_state"]);
            }
            if($code["place_pc"]) {
                if($code["place"]) $code["place"] .= ",".$this->get_places("cp", $code["place_pc"]);
                else $code["place"] = $this->get_places("cp", $code["place_pc"]);
            }
            if($code["place_near"] and $_REQUEST["latlong"]) {
                if($code["place"]) $code["place"] .= ",".$this->get_places("near", $code["place_near"]);
                else $code["place"] = $this->get_places("near", $code["place_near"]);
            }

            return $code;
        }

        private function get_events($type, $value){
            $value = explode(",", $value);
            switch($type){
                case "name":
                    foreach($value as $name){
                        $args = array(
                            'post_title_like' => $name,
                            'post_type' => "chronosly"
                        );
                        $res = new WP_Query($args);

                       foreach($res->posts as $r) $ret[] = $r->ID;
                    }
                    if(count($ret)) return implode(",", $ret);
                    return "none";
                break;
            }
        }

        private function get_organizers($type, $value){
            $value = explode(",", $value);
            switch($type){
                case "name":
                    foreach($value as $name){
                        $args = array(
                            'post_title_like' => $name,
                            'post_type' => "chronosly_organizer"
                        );
                        $res = new WP_Query($args);

                       foreach($res->posts as $r) $ret[] = $r->ID;
                    }
                    if(count($ret)) return implode(",", $ret);
                    return "none";
                break;
            }
        }

        private function get_places($type, $value){
            $value = explode(",", $value);
            switch($type){
                case "name":
                    foreach($value as $name){
                        $args = array(
                            'post_title_like' => $name,
                            'post_type' => "chronosly_places"
                        );
                        $res = new WP_Query($args);

                       foreach($res->posts as $r) $ret[] = $r->ID;
                    }
                    if(count($ret))return implode(",", $ret);
                    return "none";
                break;
                case "address":
                    foreach($value as $name){
                        $args = array(
                            'post_meta_like' => array("evp_dir" => $name),
                            'post_type' => "chronosly_places"
                        );
                        $res = new WP_Query($args);
                       foreach($res->posts as $r) $ret[] = $r->ID;
                    }
                    if(count($ret))return implode(",", $ret);
                    return "none";
                break;
                case "city":
                    foreach($value as $name){
                        $args = array(
                            'post_meta_like' => array("evp_city" => $name),
                            'post_type' => "chronosly_places"
                        );
                        $res = new WP_Query($args);
                       foreach($res->posts as $r) $ret[] = $r->ID;
                    }
                    if(count($ret))return implode(",", $ret);
                    return "none";
                break;
                case "country":
                    foreach($value as $name){
                        $args = array(
                            'post_meta_like' => array("evp_country"=> $name),
                            'post_type' => "chronosly_places"
                        );
                        $res = new WP_Query($args);
                       foreach($res->posts as $r) $ret[] = $r->ID;
                    }
                    if(count($ret))return implode(",", $ret);
                    return "none";
                break;
                case "state":
                    foreach($value as $name){
                        $args = array(
                            'post_meta_like' => array("evp_state"=> $name),
                            'post_type' => "chronosly_places"
                        );
                        $res = new WP_Query($args);
                       foreach($res->posts as $r) $ret[] = $r->ID;
                    }
                    if(count($ret))return implode(",", $ret);
                    return "none";
                break;
                case "cp":
                    foreach($value as $name){
                        $args = array(
                            'post_meta_like' => array("evp_pc"=> $name),
                            'post_type' => "chronosly_places"
                        );
                        $res = new WP_Query($args);
                       foreach($res->posts as $r) $ret[] = $r->ID;
                    }
                    if(count($ret))return implode(",", $ret);
                    return "none";
                break;
                case "near":
                    $latlong = explode(",",$_REQUEST["latlong"]);
                    $distance = $_REQUEST["distance"];
                    $box = $this->getBoundaries($latlong[0], $latlong[1], $distance);

                    $args = array(
                        'post_meta_location' => $box,
                        'post_type' => "chronosly_places"
                    );
                    $res = new WP_Query($args);

                    foreach($res->posts as $r) $ret[] = $r->ID;

                    if(count($ret))return implode(",", $ret);
                    return "none";
                break;
            }
        }



        function print_filter_bar($stilo){
            global $wp_query;


            $this->include_css();
            $stilo1 = str_replace("padding:30px;", "", $stilo);
            $stilo = str_replace("padding:30px;", "margin-bottom:15px;", $stilo);
            $this->settings = unserialize(get_option("chronosly_settings_{$this->id}"));
            $id = $wp_query->get_queried_object_id();
            $type = get_post_type();
            if($_REQUEST["shortcode"] and $_REQUEST["before_events"]) {
                //print_r($wp_query);
               $type = $wp_query->query["post_type"];
               if($_REQUEST["shortcode_categories"]) $type = "chronosly_categories";
               if($_REQUEST["shortcode_category"]) $type = "chronosly_category";
            }
            switch($type){
                case "post":
                    if(is_tax("chronosly_category") and $this->settings["categories-filter-show"]) $this->print_filter("categories", $stilo);
                break;
                case "chronosly_categories":
                    if($this->settings["categories-filter-show"]) $this->print_filter("categories", $stilo);
                break;
                case "chronosly_category":
                    if($this->settings["category-filter-show"]) $this->print_filter("category", $stilo);
                break;
                case "chronosly":
                    if(is_tax("chronosly_category") ){
                        if( $this->settings["category-filter-show"])$this->print_filter("category", $stilo, $id);
                        break;
                    }
                    if(!is_archive()) break; //aqui hay que mirar los shortcodes porq son archives tmbn
                    if( $this->settings["events-filter-show"]) {
                        if($this->settings["month-nav"]) $this->print_navigation($stilo1);
                        $this->print_filter("events", $stilo);
                    }
                break;
                case "chronosly_organizer":
                    if(!is_archive()) break; //aqui hay que mirar los shortcodes porq son archives tmbn
                    if( $this->settings["organizers-filter-show"])$this->print_filter("organizers", $stilo);
                break;
                case "chronosly_places":
                    if(!is_archive()) break; //aqui hay que mirar los shortcodes porq son archives tmbn
                    if($this->settings["places-filter-show"]) $this->print_filter("places", $stilo);
                break;
                case "chronosly_calendar":
                    if($this->settings["calendar-filter-show"]) {

                        // $this->print_navigation("calendar");
                        $this->print_filter("calendar", $stilo);
                    }
                break;
                default:

            }
            if( is_main_query() and get_query_var("chronosly_category") == "list_all_cats") $this->print_filter("categories", $stilo);
        }

        function print_filter($type, $stilo, $id = 0){
            $this->settings = unserialize(get_option("chronosly_settings_{$this->id}"));
            //echo ' <script src="//code.jquery.com/jquery-1.10.2.js"></script><script src="//code.jquery.com/ui/1.11.3/jquery-ui.js"></script>';
            echo "<div class='ch-fas-container' style='$stilo'><a href='ch_form'></a><form class='ch-fas-form' action=''>";
            $places["data"] = get_posts('post_type=chronosly_places&posts_per_page=-1&orderby=title&order=ASC&suppress_filters=0');
            foreach($places["data"] as $p){
                $places["metas"][$p->ID] = get_post_meta($p->ID);
            }
            $status = 0;
            switch($type){
                case "events":

                    echo "<input type='hidden' name='type' value='events' />";
                    foreach(unserialize($this->settings["events-filter"]) as $field){
                        $this->print_filter_field($type, $field, $places, $status);
                    }
                    if(!in_array("event_date", unserialize($this->settings["events-filter"]))) {
                      $this->print_filter_field($type, "event_date_hidden", $places, $status);
                    }
                    $order = unserialize($this->settings["events-order"]);
                    if(count($order) > 1){
                        echo "</div></div><div class='fas-filter-container'>";
                        echo "<div class='fas-select'>".__("Order", "chronosly")."<div class='arrow-down'></div></div>";
                        echo "<div class='fas-event-block'>";
                        echo "<select name='orderby'>";
                        foreach($order as $field){
                            $this->print_order_field($type, $field, $status);
                        }
                        echo "</select>&nbsp;&nbsp;";
                        $this->print_orderdir_field();
                    }

                break;
                case "organizers":
                    echo "<input type='hidden' name='type' value='organizers' />";
                    foreach(unserialize($this->settings["organizers-filter"]) as $field){
                        $this->print_filter_field($type, $field, $places, $status);
                    }
                     if(!in_array("event_date", unserialize($this->settings["organizers-filter"]))) {
                      $this->print_filter_field($type, "event_date_hidden", $places, $status);
                    }
                    $order = unserialize($this->settings["organizers-order"]);
                    if(count($order) > 1){
                        echo "</div></div><div class='fas-filter-container'>";
                        echo "<div class='fas-select'>".__("Order", "chronosly")."<div class='arrow-down'></div></div>";
                        echo "<div class='fas-event-block'>";
                        echo "<select name='orderby'>";
                        foreach($order as $field){
                            $this->print_order_field($type, $field, $status);
                        }
                        echo "</select>&nbsp;&nbsp;";
                        $this->print_orderdir_field();
                    }
                break;
                case "places":
                    echo "<input type='hidden' name='type' value='places' />";
                    foreach(unserialize($this->settings["places-filter"]) as $field){
                        $this->print_filter_field($type, $field, $places, $status);
                    }
                     if(!in_array("event_date", unserialize($this->settings["places-filter"]))) {
                      $this->print_filter_field($type, "event_date_hidden", $places, $status);
                    }
                    $order = unserialize($this->settings["places-order"]);
                    if(count($order) > 1){
                        echo "</div></div><div class='fas-filter-container'>";
                        echo "<div class='fas-select'>".__("Order", "chronosly")."<div class='arrow-down'></div></div>";
                        echo "<div class='fas-event-block'>";
                        echo "<select name='orderby'>";
                        foreach($order as $field){
                            $this->print_order_field($type, $field, $status);
                        }
                        echo "</select>&nbsp;&nbsp;";
                        $this->print_orderdir_field();
                    }

                    break;
                case "categories":
                    echo "<input type='hidden' name='type' value='categories' />";
                    foreach(unserialize($this->settings["categories-filter"]) as $field){
                        $this->print_filter_field($type, $field, $places, $status);
                    }
                     if(!in_array("event_date", unserialize($this->settings["categories-filter"]))) {
                      $this->print_filter_field($type, "event_date_hidden", $places, $status);
                    }
                    $order = unserialize($this->settings["categories-order"]);
                    if(count($order) > 1){
                        echo "</div></div><div class='fas-filter-container'>";
                        echo "<div class='fas-select'>".__("Order", "chronosly")."<div class='arrow-down'></div></div>";
                        echo "<div class='fas-event-block'>";
                        echo "<select name='orderby'>";
                        foreach($order as $field){
                            $this->print_order_field($type, $field, $status);
                        }
                        echo "</select>&nbsp;&nbsp;";
                        $this->print_orderdir_field();
                    }

                    break;
                case "category":
                    echo "<input type='hidden' name='type' value='category' />";
                    echo "<input type='hidden' name='cid' value='$id' />";
                    foreach(unserialize($this->settings["category-filter"]) as $field){
                        $this->print_filter_field($type, $field, $places, $status);
                    }
                     if(!in_array("event_date", unserialize($this->settings["category-filter"]))) {
                      $this->print_filter_field($type, "event_date_hidden", $places, $status);
                    }
                    $order = unserialize($this->settings["category-order"]);
                    if(count($order) > 1){
                        echo "</div></div><div class='fas-filter-container'>";
                        echo "<div class='fas-select'>".__("Order", "chronosly")."<div class='arrow-down'></div></div>";
                        echo "<div class='fas-event-block'>";
                        echo "<select name='orderby'>";
                        foreach($order as $field){
                            $this->print_order_field($type, $field, $status);
                        }
                        echo "</select>&nbsp;&nbsp;";
                        $this->print_orderdir_field();
                    }

                    break;
                case "calendar":
                    echo "<input type='hidden' name='type' value='calendar' />";
                    echo "<input type='hidden' name='y' value='".$_REQUEST["y"]."' />";
                    echo "<input type='hidden' name='mo' value='".$_REQUEST["mo"]."' />";
                    echo "<input type='hidden' name='week' value='".$_REQUEST["week"]."' />";
                    foreach(unserialize($this->settings["calendar-filter"]) as $field){
                        $this->print_filter_field($type, $field, $places, $status);
                    }
                     if(!in_array("event_date", unserialize($this->settings["calendar-filter"]))) {
                      $this->print_filter_field($type, "event_date_hidden", $places, $status);
                    }
                break;
            }
            if($status) echo "</div></div>";//cerramos el filter block
            echo "</form><div class='ch-spinner'></div></div>";
            echo "<br/>";

        }

        function print_filter_field($type, $field, $places, &$status){
            global $ch_fas_date;
            switch($field){
                case "event_name":
                    if(!$status) {
                        $status = "event_in";
                        echo "<div class='fas-filter-container'>";
                        echo "<div class='fas-select'>".__("Events", "chronosly")."<div class='arrow-down'></div></div>";
                        echo "<div class='fas-event-block'>";
                    }
                    $value = "";
                    if($_REQUEST["event_name"]) $value = $_REQUEST["event_name"];
                    echo "<label>".__("Find event", "chronosly")."</label><input class='ch-field-full' type='text' name='$field' value='$value' />";
                    echo  "<br/>";
                    break;
                case "event_date":
                    if(!$status) {
                        $status = "event_in";
                        echo "<div class='fas-filter-container'>";
                        echo "<div class='fas-select'>".__("Events", "chronosly")."<div class='arrow-down'></div></div>";
                        echo "<div class='fas-event-block'>";
                    }
                    if($ch_fas_date["from"]) $from =$ch_fas_date["from"];
                    if($ch_fas_date["to"]) $to =$ch_fas_date["to"];
                    echo "<label>".__("From", "chronosly")."</label><input class='ch-field-date' type='text' name='from' value='$from' />";
                    echo "<label>".__("To", "chronosly")."</label><input class='ch-field-date' type='text' name='to' value='$to' />";
                    echo  "<br/>";
                    break;
                case "event_date_hidden":

                    if($ch_fas_date["from"]) $from =$ch_fas_date["from"];
                    if($ch_fas_date["to"]) $to =$ch_fas_date["to"];
                    echo "<input class='ch-field-date' type='hidden' name='from' value='$from' />";
                    echo "<input class='ch-field-date' type='hidden' name='to' value='$to' />";
                    break;
                case "event_price":
                    if(!$status) {
                        $status = "event_in";
                        echo "<div class='fas-filter-container'>";
                        echo "<div class='fas-select'>".__("Events", "chronosly")."<div class='arrow-down'></div></div>";
                        echo "<div class='fas-event-block'>";
                    }
                    $pmin = "";
                    $pmax = "";
                    if($_REQUEST["price_min"]) $pmin = $_REQUEST["price_min"];
                    if($_REQUEST["price_max"]) $pmax = $_REQUEST["price_max"];
                    echo "<label>".__("Price", "chronosly")."</label>
                    <input class='ch-field-range-min' type='text' name='price_min' value='$pmin' />-&nbsp;&nbsp;&nbsp;<input class='ch-field-range-max' type='text' name='price_max' value='$pmax' />";
                    echo '<div class="slider-range"></div>';
                    echo  "<br/>";
                    break;
                case "author":

                    if(!$status) {
                        $status = "event_in";
                        echo "<div class='fas-filter-container'>";
                        echo "<div class='fas-select'>".__("Event", "chronosly")."<div class='arrow-down'></div></div>";
                        echo "<div class='fas-event-block'>";
                    }
                    echo "<label>".__("Author", "chronosly")."</label>";
                    $users = get_users( 'orderby=nicename&who=auhtor' );
                    echo "<ul class='ch-input-list'>";
                    $i = 0;
                    $extra = "";
                    if($_REQUEST["author"]) $catsSet = explode(",", $_REQUEST["author"]);
                    foreach($users as $p){
                        $i++;
                        $checked ="";
                        if(in_array($p->data->ID, $catsSet)) $checked= "checked";
                        // if($i > 5) $extra = "noshow";
                        echo '<li class="'.$extra.'"><input value="'.$p->data->ID.'" type="checkbox" name="author[]" '.$checked.'> '.$p->data->user_nicename.'</li>';
                    }
                    // if($extra){
                    //     echo "<li class='more'><b>".__("More", "chronosly")."</b></li>";
                    //     echo "<li class='less'><b>".__("Less", "chronosly")."</b></li>";
                    // }
                    echo "</ul>";
                    echo  "<br/>";

                break;
                case "category":
                    if($status and $status != "category_in"){
                        $status = 0;
                        echo "</div>";
                        echo "</div>";
                    }
                    if(!$status){
                        $status = "category_in";
                        echo "<div class='fas-filter-container'>";
                        echo "<div class='fas-select'>".__("Category", "chronosly")."<div class='arrow-down'></div></div>";
                        echo "<div class='fas-event-block'>";
                    }
                    $args = array('hide_empty'=>true);
                    $posts = get_terms("chronosly_category", $args);
                    if(!count($posts)) break;
                    $posts = $this->order_terms_by_hierarchy($posts);
                    // print_r($posts);
                   // echo "<label>".__("Category", "chronosly")."</label>";
                    //$posts = get_posts('post_type=chronosly_category&posts_per_page=-1&orderby=title&order=ASC&suppress_filters=0');

                    echo "<ul class='ch-input-list'>";
                    $i = 0;
                    $extra = "";
                    $level = 0;
                    $parent = 0;
                    $parents = array("0");
                    $ps = array();
                    if($_REQUEST["category"]) $catsSet = explode(",", $_REQUEST["category"]);
                    foreach($posts as $p){
                        $ps[] = $p->name;
                        if($p->level != $level){
                            while($p->level != $level){
                                if($p->level > $level){
                                     echo "<ul style='margin:0px;padding:0 0 0 20px'>";
                                     ++$level;
                                } else {
                                     echo "</ul>";
                                     --$level;
                                }
                            }
                        }
                       // if($parent != $p->parent){

                       //      // if(!in_array($p->parent, $parents)) {
                       //      //     echo "<ul style='margin:0px;padding:0 0 0 20px'>";
                       //      //     $parents[]= $p->parent;
                       //      // } else {
                       //      //     $find = 0;
                       //      //     while(!$find and $par = array_pop($parents)){
                       //      //         if($par == $p->parent) {
                       //      //             $find = 1;
                       //      //             $parents[] = $par;
                       //      //         }
                       //      //         else echo "</ul>";
                       //      //     }
                       //      // }
                       //      $parent = $p->parent;
                       //      $level = $p->level;
                       // }
                        $i++;
                        if($i > 50) $extra = "noshow";
                        $checked ="";
                        if(in_array($p->term_id, $catsSet)) $checked= "checked";
                        echo '<li class="'.$extra.'"><input value="'.$p->term_id.'" type="checkbox" name="category[]" '.$checked.'> '.$p->name.'</li>';
                       //  if($parent != $p->parent){
                       //      echo "<ul>";
                       // }
                    }
                    // while($par = array_pop($parents)){
                    //     echo "</ul>";
                    // }
                    while(!$level){
                        echo "</ul>";
                        --$level;
                    }
                    if($extra) {
                        echo "<li class='more'><b>".__("More", "chronosly")."</b></li>";
                        echo "<li class='less'><b>".__("Less", "chronosly")."</b></li>";

                    }
                    echo "</ul>";
                    echo  "<br/>";
                    // print_r($ps);
                    break;
                case "organizer":
                    if($status and $status != "organizer_in") {
                        $status = 0;
                        echo "</div>";
                        echo "</div>";
                    }
                    if(!$status){
                        $status = "organizer_in";
                        echo "<div class='fas-filter-container'>";
                        echo "<div class='fas-select'>".__("Organizer", "chronosly")."<div class='arrow-down'></div></div>";
                        echo "<div class='fas-event-block'>";
                    }
                    $posts = get_posts('post_type=chronosly_organizer&posts_per_page=-1&orderby=title&order=ASC&suppress_filters=0');
                    if(!count($posts)) break;
                    //echo "<label>".__("Organizer", "chronosly")."</label>";


                    echo "<ul class='ch-input-list'>";
                    $i = 0;
                    $extra = "";
                    if($_REQUEST["organizer"]) $catsSet = explode(",", $_REQUEST["organizer"]);

                    foreach($posts as $p){
                        $i++;
                        $checked ="";
                        if(in_array($p->ID, $catsSet)) $checked= "checked";
                        // if($i > 5) $extra = "noshow";
                        echo '<li class="'.$extra.'"><input value="'.$p->ID.'" '.$checked.' type="checkbox" name="organizer[]"> '.$p->post_title.'</li>';
                    }
                    // if($extra) {
                    //     echo "<li class='more'><b>".__("More", "chronosly")."</b></li>";
                    //     echo "<li class='less'><b>".__("Less", "chronosly")."</b></li>";
                    // }
                    echo "</ul>";
                    echo  "<br/>";
                    break;
                case "place_name":

                     if($status and $status != "place_in") {
                         $status = 0;
                         echo "</div>";
                         echo "</div>";
                     }
                    if(!$status){
                        $status = "place_in";
                        echo "<div class='fas-filter-container'>";
                        echo "<div class='fas-select'>".__("Place", "chronosly")."<div class='arrow-down'></div></div>";
                        echo "<div class='fas-event-block'>";
                    }
                    echo "<ul class='ch-input-list'>";
                    $i = 0;
                    $extra = "";
                    if($_REQUEST["place"]) $catsSet = explode(",", $_REQUEST["place"]);

                    foreach($places["data"] as $p){
                        $i++;
                        $checked ="";
                        if(in_array($p->ID, $catsSet)) $checked= "checked";
                        echo '<li class="'.$extra.'"><input value="'.$p->ID.'" '.$checked.' type="checkbox" name="place[]"> '.$p->post_title.'</li>';
                    }
                    // if($extra) {
                    //     echo "<li class='more'><b>".__("More", "chronosly")."</b></li>";
                    //     echo "<li class='less'><b>".__("Less", "chronosly")."</b></li>";
                    // }
                    echo "</ul>";
                    echo  "<br/>";

                    break;
                case "place_state":
                     if($status and $status != "place_in") {
                         $status = 0;
                         echo "</div>";
                         echo "</div>";
                     }
                    if(!$status) {
                        $status = "place_in";
                        echo "<div class='fas-filter-container'>";
                        echo "<div class='fas-select'>".__("Place", "chronosly")."<div class='arrow-down'></div></div>";
                        echo "<div class='fas-event-block'>";
                    }
                    $ret = "<label>".__("State", "chronosly")."</label>";
                    $ret .= "<ul class='ch-input-list'>";
                    $previos = array();
                    $i = 0;
                    $extra = "";
                    if($_REQUEST["place"]) $catsSet = explode(",", $_REQUEST["place"]);
                    foreach($places["metas"] as $k=>$p){
                        if($p["evp_state"][0] and !in_array($p["evp_state"][0], $previos)) {
                            $i++;
                            $checked ="";
                            if(in_array($k, $catsSet)) $checked= "checked";
                            $ret .= '<li class="'.$extra.'"><input value="'.$k.'" '.$checked.' type="checkbox" name="place[]"> '.$p["evp_state"][0].'</li>';
                            $previos[] = $p["evp_state"][0];
                        }
                    }
                    // if($extra) {
                    //     $ret .="<li class='more'><b>".__("More", "chronosly")."</b></li>";
                    //     $ret .="<li class='less'><b>".__("Less", "chronosly")."</b></li>";
                    // }
                    $ret .= "</ul>";
                    $ret .= "<br/>";
                    if(count($previos)) echo $ret;
                break;
                case "place_country":
                     if($status and $status != "place_in") {
                         $status = 0;
                         echo "</div>";
                         echo "</div>";
                     }
                    if(!$status) {
                        $status = "place_in";
                        echo "<div class='fas-filter-container'>";
                        echo "<div class='fas-select'>".__("Place", "chronosly")."<div class='arrow-down'></div></div>";
                        echo "<div class='fas-event-block'>";
                    }
                    $ret = "<label>".__("Country", "chronosly")."</label>";
                    $ret .= "<ul class='ch-input-list'>";
                    $previos = array();
                    $i = 0;
                    $extra = "";
                    if($_REQUEST["place"]) $catsSet = explode(",", $_REQUEST["place"]);
                    foreach($places["metas"] as $k=>$p){
                        if($p["evp_country"][0] and !in_array($p["evp_country"][0], $previos)) {
                            $i++;
                            $checked ="";
                            if(in_array($k, $catsSet)) $checked= "checked";
                            $ret .= '<li class="'.$extra.'"><input value="'.$k.'" '.$checked.' type="checkbox" name="place[]"> '.$p["evp_country"][0].'</li>';
                            $previos[] = $p["evp_country"][0];
                        }
                    }
                    // if($extra) {
                    //     $ret .="<li class='more'><b>".__("More", "chronosly")."</b></li>";
                    //     $ret .="<li class='less'><b>".__("Less", "chronosly")."</b></li>";
                    // }
                    $ret .= "</ul>";
                    $ret .= "<br/>";
                    if(count($previos)) echo $ret;

                    break;
                case "place_city":
                    if($status and $status != "place_in") {
                        $status = 0;
                        echo "</div>";
                        echo "</div>";
                    }
                    if(!$status) {
                        $status = "place_in";
                        echo "<div class='fas-filter-container'>";
                        echo "<div class='fas-select'>".__("Place", "chronosly")."<div class='arrow-down'></div></div>";
                        echo "<div class='fas-event-block'>";
                    }
                    $ret = "<label>".__("City", "chronosly")."</label>";
                    $ret .= "<ul class='ch-input-list'>";
                    $previos = array();
                    $i = 0;
                    $extra = "";
                    if($_REQUEST["place"]) $catsSet = explode(",", $_REQUEST["place"]);
                    foreach($places["metas"] as $k=>$p){
                        if($p["evp_city"][0] and !in_array($p["evp_city"][0], $previos)) {
                            $i++;
                            $checked ="";
                            if(in_array($k, $catsSet)) $checked= "checked";
                            $ret .= '<li class="'.$extra.'"><input value="'.$k.'" '.$checked.' type="checkbox" name="place[]"> '.$p["evp_city"][0].'</li>';
                            $previos[] = $p["evp_city"][0];
                        }
                    }
                    // if($extra) {
                    //     $ret .="<li class='more'><b>".__("More", "chronosly")."</b></li>";
                    //     $ret .="<li class='less'><b>".__("Less", "chronosly")."</b></li>";
                    // }
                    $ret .= "</ul>";
                    $ret .= "<br/>";

                    if(count($previos)) echo $ret;

                    break;
                case "place_pc":
                    if($status and $status != "place_in") {
                        $status = 0;
                        echo "</div>";
                        echo "</div>";
                    }
                    if(!$status) {
                        $status = "place_in";
                        echo "<div class='fas-filter-container'>";
                        echo "<div class='fas-select'>".__("Place", "chronosly")."<div class='arrow-down'></div></div>";
                        echo "<div class='fas-event-block'>";
                    }
                    $ret = "<label>".__("Postal Code", "chronosly")."</label> ";
                    $ret .= "<ul class='ch-input-list'>";
                    $previos = array();
                    $i = 0;
                    $extra = "";
                    if($_REQUEST["place"]) $catsSet = explode(",", $_REQUEST["place"]);
                    foreach($places["metas"] as $k=>$p){
                        if($p["evp_pc"][0] and !in_array($p["evp_pc"][0], $previos)) {
                            $i++;
                            $checked ="";
                            if(in_array($k, $catsSet)) $checked= "checked";
                            $ret .= '<li class="'.$extra.'"><input value="'.$k.'" '.$checked.' type="checkbox" name="place[]"> '.$p["evp_pc"][0].'</li>';
                            $previos[] = $p["evp_pc"][0];
                        }
                    }
                    // if($extra) {
                    //     $ret .="<li class='more'><b>".__("More", "chronosly")."</b></li>";
                    //     $ret .="<li class='less'><b>".__("Less", "chronosly")."</b></li>";
                    // }
                    $ret .= "</ul>";
                    $ret .= "<br/>";
                    if(count($previos)) echo $ret;

                    break;
                case "place_address":
                    if($status and $status != "place_in") {
                        $status = 0;
                        echo "</div>";
                        echo "</div>";
                    }
                    if(!$status) {
                        $status = "place_in";
                        echo "<div class='fas-filter-container'>";
                        echo "<div class='fas-select'>".__("Place", "chronosly")."<div class='arrow-down'></div></div>";
                        echo "<div class='fas-event-block'>";
                    }
                    $value = "";
                    if($_REQUEST["place_address"]) $value = $_REQUEST["place_address"];
                    echo "<label>".__("Address", "chronosly")."</label><input class='ch-field-full' type='text' name='$field' value='$value' />";
                    echo "<br/>";
                    break;

                case "place_near":
                    if($status and $status != "place_in") {
                        $status = 0;
                        echo "</div>";
                        echo "</div>";
                    }
                    if(!$status) {
                        $status = "place_in";
                        echo "<div class='fas-filter-container'>";
                        echo "<div class='fas-select'>".__("Place", "chronosly")."<div class='arrow-down'></div></div>";
                        echo "<div class='fas-event-block'>";
                    }
                    $unit = __("km", "chronosly");
                    if($this->settings["distance-unit"] == "miles") $unit = __("miles", "chronosly");
                    ?>

                    <script>
                            function success(position) {

                            var latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                            jQuery("#ch-geo").val(latlng.toString().replace("(", "").replace(" ", "").replace(")", ""));

                            }

                            function error(msg) {

                                alert("Error [" + error.code + "]: " + error.message);
                            }

                            jQuery(document).ready(function(){
                                jQuery("#ch-near").change(function (){
                                    if(jQuery(this).is(":checked")){
                                        if (navigator.geolocation) {
                                            navigator.geolocation.getCurrentPosition(success, error,{maximumAge:60000, timeout: 4000});
                                        } else {

                                            alert('<?php _e("Required geolocation to find events near you", "chronosly")?>');
                                        }
                                    }
                                });
                            });

                            </script>
                            <input type="hidden" id="ch-geo" name="latlong" value="" />
                            <?php 
                            $value = "10";
                            $checked = "";
                            if($_REQUEST["distance"]) $value = $_REQUEST["distance"]; 
                            if($_REQUEST["place_near"]) $checked = "checked"; 
                            ?>
                            <input type="checkbox" id="ch-near" name="place_near" value="1" <?php echo $checked; ?>/> <label><?php _e("Events near to me", "chronosly");?></label>
                            <input class="ch-distance" type="text" name="distance" value="<?php echo $value;?>"/> <label><?php echo $unit; ?></label>


                    <?php
                    echo "<br/>";



                    break;
            }

        }

    function order_terms_by_hierarchy($terms){

        $ordered_list = array();
        foreach($terms as $term){
            if($term->parent ==  0){
                $term->level = 0;
                $ordered_list[] = $term;
            }
        }


        foreach($ordered_list as $parent){
            $this->insert_child_terms_in_list($terms, $ordered_list, $parent->term_id);
        }
        // echo "<pre>";
        // print_r($ordered_list);
        return $ordered_list;

    }

     function insert_child_terms_in_list($terms, &$ordered_list, $parent, $level = 0){

        $children = array();
        foreach($terms as $term){
            if($term->parent ==  $parent){
                $children[] = $term;
            }
        }

        // get index of parent
        $parent_index = -1;
        foreach($ordered_list as $k => $term){
            if($term->term_id == $parent){
                $parent_index = $k;
                break;
            }
        }

        if($children && $parent_index >= 0){
            array_splice($ordered_list, $parent_index+1, 0, $children);

            foreach($children as $child){
                $child->level = $level + 1;
                $this->insert_child_terms_in_list($terms, $ordered_list, $child->term_id, $level + 1);
            }
        }



    }

        function print_order_field($type, $field){

            switch($field){
                case "event":
                   echo "<option value='$field'>".__("Event", "chronosly")."</option>";
                break;
                case "date":
                   echo "<option value='$field'>".__("Date", "chronosly")."</option>";
                break;
                case "price":
                   echo "<option value='$field'>".__("Price", "chronosly")."</option>";
                break;
                case "category":
                   echo "<option value='$field'>".__("Category", "chronosly")."</option>";
                break;
                case "organizer":
                   echo "<option value='$field'>".__("Organizer", "chronosly")."</option>";
                break;
                case "place":
                   echo "<option value='$field'>".__("Place", "chronosly")."</option>";
                break;


            }

        }

        function print_orderdir_field(){
            echo "<select name='orderdir'><option value='ASC' selected>".__("ASC", "chronosly")."</option><option value='DESC'>".__("DESC", "chronosly")."</option></select>";
        }

        function print_navigation($stilo){
            global $ch_fas_date;
            if($stilo == "calendar"){
                 if($_REQUEST["mo"]){
                    if($_REQUEST["mo"] < 10) $_REQUEST["mo"] = "0".$_REQUEST["mo"];
                    $_REQUEST["from"] = $_REQUEST["y"]."-".$_REQUEST["mo"]."-01";
                    $_REQUEST["to"] = date("Y-m-t", strtotime($_REQUEST["from"]));
                }
                else if($_REQUEST["week"]){
                    $w =strtotime($year."W".str_pad($week, 2, '0', STR_PAD_LEFT));
                    if($settings["chronosly_week_start"] == 1) {
                        $w -= (60*60*24);
                    }
                    $_REQUEST["from"] = date("Y-m-d", $w);
                    $_REQUEST["to"] = date("Y-m-d", strtotime($_REQUEST["from"]." + 6 day"));
                }
                else {
                    $_REQUEST["from"] = $_REQUEST["y"]."-01-01";
                    $_REQUEST["to"] = $_REQUEST["y"]."-12-31";
                }

                $ch_fas_date = array("from" => $_REQUEST["from"], "to" => $_REQUEST["to"]);

                return;

            }
            $settings = unserialize(get_option("chronosly-settings"));
            $listado = ((isset($_REQUEST["chronosly_event_list_format"]) and $_REQUEST["chronosly_event_list_format"])?$_REQUEST["chronosly_event_list_format"]:$settings["chronosly_event_list_format"]);
            $time =  ((isset($_REQUEST["chronosly_event_list_time"]) and $_REQUEST["chronosly_event_list_time"])?$_REQUEST["chronosly_event_list_time"]:$settings["chronosly_event_list_time"]);
            switch($listado){
                case "year":
                    if(!$time or $time == "current"){
                        $fromc =  date("Y-12-31");
                        $toc = date("Y-01-01");
                    } else {
                        if($pastformat){
                            $fromc =  date("Y-m-d", time() - 60 * 60 * 24);
                            $toc = date("Y")."-01-01";
                        }
                        else{
                            $fromc =  "$time-12-31";
                            $toc = "$time-01-01";
                        }
                    }

                    $ch_fas_date = array("from" => $toc, "to" => $fromc);

                    $d1 = date("Y-m-d", strtotime($toc. " - 1 year"));
                    $d2 = date("Y-12-t", strtotime($d1));
                    $d3 = date("Y-m-d", strtotime($fromc. " + 1 day"));
                    $d4 = date("Y-12-t", strtotime($d3));

                    $name1 = date("Y", strtotime($fromc. " - 1 year"));
                    $name2 = date("Y", strtotime($fromc. " + 1 year"));

                    echo "<div class='ch-fas-nav'  style='$stilo'><a class='nav1' href='#ch_form' onclick='ch_filter(this, {\"from\": \"$d1\", \"to\": \"$d2\"})'><div class='arrow-down'></div>$name1</a><a class='nav2' href='#ch_form' onclick='ch_filter(this, {\"from\": \"$d3\", \"to\": \"$d4\"})'>$name2<div class='arrow-up'></div></a></div>";
                    break;
                case "month":
                    if(!$time  or $time == "current"){
                        $fromc =  date("Y-m-t");
                        $toc = date("Y-m-01");
                    } else {
                        if($pastformat){
                            $fromc =  date("Y-m-d", time() - 60 * 60 * 24);
                            $toc = date("Y-m-01");
                        }
                        else{
                            if((int)$time < 10) $time = "0".$time;
                            $y = "Y";
                            if($_REQUEST["y"]) $y = $_REQUEST["y"];
                            $fromc =  date("Y-m-t", strtotime(date("$y-$time-01")));
                            $toc = date("$y-$time-01");
                        }
                    }
                    $ch_fas_date = array("from" => $toc, "to" => $fromc);

                    $d1 = date("Y-m-d", strtotime($toc. " - 1 month"));
                    $d2 = date("Y-m-t", strtotime($d1));
                    $d3 = date("Y-m-d", strtotime($fromc. " + 1 day"));
                    $d4 = date("Y-m-t", strtotime($d3));
                    $name1 = date("n", strtotime($toc. " - 1 month"));
                    $name2 = date("n", strtotime($toc. " + 1 month"));
                    $m = array(__("January"), __("February"),__("March"), __("April"),__("May"), __("June"),__("July"),__("August"),__("September"),__("October"),__("November"),__("December"));

                    // echo "<div class='ch-fas-nav'  style='$stilo'><a href='?post_type=chronosly&y=$y1&chronosly_event_list_time=$name1&separator='><div class='arrow-down'></div>".$m[$name1-1]."</a><a href='?post_type=chronosly&y=$y2&chronosly_event_list_time=$name2&separator='>".$m[$name2-1]."<div class='arrow-up'></div></a></div>";
                    echo "<div class='ch-fas-nav'  style='$stilo'><a class='nav1' href='#ch_form' onclick='ch_filter(this, {\"from\": \"$d1\", \"to\": \"$d2\"})'><div class='arrow-down'></div>".$m[$name1-1]."</a><a class='nav2' href='#ch_form' onclick='ch_filter(this, {\"from\": \"$d3\", \"to\": \"$d4\" })'>".$m[$name2-1]."<div class='arrow-up'></div></a></div>";


                    break;

                case "week":
                    if(!$time  or $time == "current"){
                        $monday = strtotime('last monday', strtotime('tomorrow'));
                        if($settings["chronosly_week_start"] == 1) {
                            $monday -= (60*60*24);
                        }
                        $fromc =  date("Y-m-d",$monday+(6*60*60*24));
                        $toc = date("Y-m-d",$monday);

                    } else {
                        if($pastformat){
                            $monday = strtotime('last monday', strtotime('tomorrow'));
                            if($settings["chronosly_week_start"] == 1) {
                                $monday -= (60*60*24);
                            }
                            $fromc =  date("Y-m-d", time() - 60 * 60 * 24);
                            $toc = date("Y-m-d",$monday);
                        }
                        else {
                            $y = date("Y");
                            if($_REQUEST["y"]) $y = $_REQUEST["y"];
                            $monday = strtotime($y . 'W' . str_pad($time, 2, '0', STR_PAD_LEFT));
                            if($settings["chronosly_week_start"] == 1) {
                                $monday -= (60*60*24);
                            }
                            $fromc =  date("Y-m-d",$monday+(6*60*60*24));
                            $toc = date("Y-m-d", $monday);
                        }
                    }
                    $ch_fas_date = array("from" => $toc, "to" => $fromc);

                    $d1 = date("Y-m-d", strtotime($toc. " - 1 week"));
                    $d2 = date("Y-m-d", strtotime($toc." - 1 day"));
                    $d3 = date("Y-m-d", strtotime($fromc. " + 1 day"));
                    $d4 = date("Y-m-d", strtotime($fromc. " +1 week"));
                    $name1 = date("n", strtotime($toc. " - 1 week"));
                    $name2 = date("n", strtotime($toc. " + 1 week"));
                    $m = array(__("January"), __("February"),__("March"), __("April"),__("May"), __("June"),__("July"),__("August"),__("September"),__("October"),__("November"),__("December"));

                    // echo "<div class='ch-fas-nav'  style='$stilo'><a href='?post_type=chronosly&y=$y1&chronosly_event_list_time=$name1&separator='><div class='arrow-down'></div>".$m[$name1-1]."</a><a href='?post_type=chronosly&y=$y2&chronosly_event_list_time=$name2&separator='>".$m[$name2-1]."<div class='arrow-up'></div></a></div>";
                    echo "<div class='ch-fas-nav'  style='$stilo'><a class='nav1' href='#ch_form' onclick='ch_filter(this, {\"from\": \"$d1\", \"to\": \"$d2\"})'><div class='arrow-down'></div>".date("d", strtotime($d1))."-".date("d", strtotime($d2))." ".$m[$name1-1]."</a><a class='nav2' href='#ch_form' onclick='ch_filter(this, {\"from\": \"$d3\", \"to\": \"$d4\" })'>".date("d", strtotime($d3))."-".date("d", strtotime($d4))." ".$m[$name2-1]."<div class='arrow-up'></div></a></div>";

                break;
                case "day":
                    if(!$time  or $time == "current"){
                            $fromc = $toc = date("Y-m-d");
                    } else {
                        $fromc = $toc = $time;

                    }

                    $ch_fas_date = array("from" => $toc, "to" => $fromc);

                    $time = strtotime($toc);
                    $name1 = date("Y-m-d", $time-(1*60*60*24));
                    $name2 = date("Y-m-d", $time+(1*60*60*24));

                    echo "<div class='ch-fas-nav'  style='$stilo'><a href='?post_type=chronosly&chronosly_event_list_time=$name1&separator='><div class='arrow-down'></div>".__("Previous day", "chronosly")."</a><a href='?post_type=chronosly&chronosly_event_list_time=$name2&separator='>".__("Next day", "chronosly")."<div class='arrow-up'></div></a></div>";

                    break;
//                case "upcoming":
//                    if(!$time){
//                        $fromc =  date("Y-m-d", strtotime("+1 week"));
//                        $toc = date("Y-m-d");
//                    } else {
//                        $fromc =  date("Y-m-d",strtotime("+$time day"));
//                        $toc = date("Y-m-d");
//                    }
//
//                    $ch_fas_date = array("from" => $toc, "to" => $fromc);
//
//                    $time = strtotime($toc);
//                    $name1 = date("Y-m-d", $time-(1*60*60*24));
//                    $name2 = date("Y-m-d", $time+(1*60*60*24));
//
//                    echo "<div class='ch-fas-nav'  style='$stilo'><a href='?post_type=chronosly&chronosly_event_list_time=$name1'><div class='arrow-down'></div>".__("Previous day", "chronosly")."</a><a href='?post_type=chronosly&chronosly_event_list_time=$name2'>".__("Next day", "chronosly")."<div class='arrow-up'></div></a></div>";
//
//                    break;
            }


        }


        function js_filter_and_sort(){
            $shortcode = "";
            switch($_REQUEST["type"]){
                case "events":
                    $shortcode = "chronosly type='event' ";
                break;
                case "organizers":
                    $shortcode = "chronosly type='organizer' ";
                break;
                case "places":
                    $shortcode = "chronosly type='place' ";
                break;
                case "categories":
                    $shortcode = "chronosly type='event' ";
                break;
                case "category":
                    $shortcode = "chronosly type='event' ";
                break;
                case "calendar":
                    $shortcode = "chronosly type='calendar' ";
                break;
            }
            if($_REQUEST["category"]) $shortcode .= "category='".$_REQUEST["category"]."' ";
            if($_REQUEST["y"]) $shortcode .= "year='".$_REQUEST["y"]."' ";
            if($_REQUEST["mo"]) $shortcode .= "month='".$_REQUEST["mo"]."' ";
            if($_REQUEST["week"]) $shortcode .= "week='".$_REQUEST["week"]."' ";
            if($_REQUEST["event_name"]) $shortcode .= "event_name='".$_REQUEST["event_name"]."' ";
            if($_REQUEST["from"]) $shortcode .= "from='".$_REQUEST["from"]."' ";
            if($_REQUEST["to"]) $shortcode .= "to='".$_REQUEST["to"]."' ";
            if(isset($_REQUEST["price_min"])) $shortcode .= "price_min='".$_REQUEST["price_min"]."' ";
            if(isset($_REQUEST["price_max"])) $shortcode .= "price_max='".$_REQUEST["price_max"]."' ";
            if($_REQUEST["organizer"]) $shortcode .= "organizer='".$_REQUEST["organizer"]."' ";
            if($_REQUEST["organizer_name"]) $shortcode .= "organizer_name='".$_REQUEST["organizer_name"]."' ";
            if($_REQUEST["place"]) $shortcode .= "place='".$_REQUEST["place"]."' ";
            if($_REQUEST["place_name"]) $shortcode .= "place_name='".$_REQUEST["place_name"]."' ";
            if($_REQUEST["place_address"]) $shortcode .= "place_address='".$_REQUEST["place_address"]."' ";
            if($_REQUEST["place_city"]) $shortcode .= "place_city='".$_REQUEST["place_city"]."' ";
            if($_REQUEST["place_country"]) $shortcode .= "place_country='".$_REQUEST["place_country"]."' ";
            if($_REQUEST["place_state"]) $shortcode .= "place_state='".$_REQUEST["place_state"]."' ";
            if($_REQUEST["place_pc"]) $shortcode .= "place_pc='".$_REQUEST["place_pc"]."' ";
            if($_REQUEST["place_near"]) $shortcode .= "place_near='".$_REQUEST["place_near"]."' ";
            if(!$_REQUEST["place_near"] and $_REQUEST["latlong"]) $_REQUEST["latlong"] = "";
            if($_REQUEST["author"]) $shortcode .= "author='".$_REQUEST["author"]."' ";
            if($_REQUEST["type"] == "categories" and $shortcode == "chronosly type='event' ") $shortcode = "chronosly type='category'";
            else if($_REQUEST["type"] == "category"){
                if( $shortcode == "chronosly type='event' ") $shortcode = "chronosly type='category' single='1' id='".$_REQUEST["cid"]."'";
                else  $shortcode .= "category='".$_REQUEST["cid"]."' ";
            }
            $shortcode .= " pagination='1' navigation='1' ";

            $settings = unserialize(get_option("chronosly-settings"));



            if($_REQUEST["type"] == "calendar"){
                //ponemos el from to
                if($_REQUEST["mo"]){
                    $_REQUEST["from"] = $_REQUEST["y"]."-".$_REQUEST["mo"]."-01";
                    $_REQUEST["to"] = date("Y-m-t", strtotime($_REQUEST["from"]));
                }
                else if($_REQUEST["week"]){
                    $w =strtotime($year."W".str_pad($week, 2, '0', STR_PAD_LEFT));
                    if($settings["chronosly_week_start"] == 1) {
                        $w -= (60*60*24);
                    }
                    $_REQUEST["from"] = date("Y-m-d", $w);
                    $_REQUEST["to"] = date("Y-m-d", strtotime($_REQUEST["from"]." + 6 day"));
                }
                else {
                    $_REQUEST["from"] = $_REQUEST["y"]."-01-01";
                    $_REQUEST["to"] = $_REQUEST["y"]."-12-31";
                }
            }
            //echo "[$shortcode]";
             // ob_start();
            echo do_shortcode("[$shortcode]");


            $listado = ((isset($_REQUEST["chronosly_event_list_format"]) and $_REQUEST["chronosly_event_list_format"])?$_REQUEST["chronosly_event_list_format"]:$settings["chronosly_event_list_format"]);

             switch($listado){
              case "year":
                    $d1 = date("Y-m-d", strtotime($_REQUEST["from"]. " - 1 year"));
                    $d2 = date("Y-12-t", strtotime($d1));
                    $d3 = date("Y-m-d", strtotime($_REQUEST["to"]. " + 1 day"));
                    $d4 = date("Y-12-t", strtotime($d3));
                    $name1 = date("Y", strtotime($_REQUEST["from"]. " - 1 year"));
                    $name2 = date("Y", strtotime($_REQUEST["from"]. " + 1 year"));

                    // echo "<div class='ch-fas-nav'  style='$stilo'><a href='?post_type=chronosly&y=$y1&chronosly_event_list_time=$name1&separator='><div class='arrow-down'></div>".$m[$name1-1]."</a><a href='?post_type=chronosly&y=$y2&chronosly_event_list_time=$name2&separator='>".$m[$name2-1]."<div class='arrow-up'></div></a></div>";
                    echo "<div><div class='ch-fas-nav'  style='$stilo'><a class='nav1' href='#ch_form' onclick='ch_filter(this, {\"from\": \"$d1\", \"to\": \"$d2\"})'><div class='arrow-down'></div>".$name1."</a><a class='nav2' href='#ch_form' onclick='ch_filter(this, {\"from\": \"$d3\", \"to\": \"$d4\" })'>".$name2."<div class='arrow-up'></div></a></div></div>";
                break;
                case "month":


                    $d1 = date("Y-m-d", strtotime($_REQUEST["from"]. " - 1 month"));
                    $d2 = date("Y-m-t", strtotime($d1));
                    $d3 = date("Y-m-d", strtotime($_REQUEST["to"]. " + 1 day"));
                    $d4 = date("Y-m-t", strtotime($d3));
                    $name1 = date("n", strtotime($_REQUEST["from"]. " - 1 month"));
                    $name2 = date("n", strtotime($_REQUEST["from"]. " + 1 month"));
                    $m = array(__("January"), __("February"),__("March"), __("April"),__("May"), __("June"),__("July"),__("August"),__("September"),__("October"),__("November"),__("December"));

                    // echo "<div class='ch-fas-nav'  style='$stilo'><a href='?post_type=chronosly&y=$y1&chronosly_event_list_time=$name1&separator='><div class='arrow-down'></div>".$m[$name1-1]."</a><a href='?post_type=chronosly&y=$y2&chronosly_event_list_time=$name2&separator='>".$m[$name2-1]."<div class='arrow-up'></div></a></div>";
                    echo "<div><div class='ch-fas-nav'  style='$stilo'><a class='nav1' href='#ch_form' onclick='ch_filter(this, {\"from\": \"$d1\", \"to\": \"$d2\"})'><div class='arrow-down'></div>".$m[$name1-1]."</a><a class='nav2' href='#ch_form' onclick='ch_filter(this, {\"from\": \"$d3\", \"to\": \"$d4\" })'>".$m[$name2-1]."<div class='arrow-up'></div></a></div></div>";


                    break;
                case "week":

                     $d1 = date("Y-m-d", strtotime($_REQUEST["from"]. " - 1 week"));
                    $d2 = date("Y-m-d", strtotime($_REQUEST["from"]." - 1 day"));
                    $d3 = date("Y-m-d", strtotime($_REQUEST["to"]. " + 1 day"));
                    $d4 = date("Y-m-d", strtotime($_REQUEST["to"]. " +1 week"));
                    $name1 = date("n", strtotime($_REQUEST["from"]. " - 1 week"));
                    $name2 = date("n", strtotime($_REQUEST["from"]. " + 1 week"));
                    $m = array(__("January"), __("February"),__("March"), __("April"),__("May"), __("June"),__("July"),__("August"),__("September"),__("October"),__("November"),__("December"));

                    // echo "<div class='ch-fas-nav'  style='$stilo'><a href='?post_type=chronosly&y=$y1&chronosly_event_list_time=$name1&separator='><div class='arrow-down'></div>".$m[$name1-1]."</a><a href='?post_type=chronosly&y=$y2&chronosly_event_list_time=$name2&separator='>".$m[$name2-1]."<div class='arrow-up'></div></a></div>";
                    echo "<div><div class='ch-fas-nav'  style='$stilo'><a class='nav1' href='#ch_form' onclick='ch_filter(this, {\"from\": \"$d1\", \"to\": \"$d2\"})'><div class='arrow-down'></div>".date("d", strtotime($d1))."-".date("d", strtotime($d2))." ".$m[$name1-1]."</a><a class='nav2' href='#ch_form' onclick='ch_filter(this, {\"from\": \"$d3\", \"to\": \"$d4\" })'>".date("d", strtotime($d3))."-".date("d", strtotime($d4))." ".$m[$name2-1]."<div class='arrow-up'></div></a></div></div>";
                    break;
                }


            echo "<div><div class='ch_from'>".$_REQUEST["from"]."</div>";
            echo "<div class='ch_to'>".$_REQUEST["to"]."</div></div>";
            // $args["html"] =    ob_get_clean();
            // $args["title"] = $_REQUEST["from"];
            // $args["toc"] = $_REQUEST["from"];
            // $args["fromc"] = $_REQUEST["to"];
            // echo json_encode($args);
            die();

        }



        function getBoundaries($lat, $lng, $distance = 10, $earthRadius = 6371)
        {
            $return = array();

            // Los angulos para cada dirección
            $cardinalCoords = array('north' => '0',
                'south' => '180',
                'east' => '90',
                'west' => '270');

            $this->settings = unserialize(get_option("chronosly_settings_{$this->id}"));
            if($this->settings["distance-unit"] == "miles") $earthRadius *= 0.62137;

            $rLat = deg2rad($lat);
            $rLng = deg2rad($lng);
            $rAngDist = $distance/$earthRadius;

            foreach ($cardinalCoords as $name => $angle)
            {
                $rAngle = deg2rad($angle);
                $rLatB = asin(sin($rLat) * cos($rAngDist) + cos($rLat) * sin($rAngDist) * cos($rAngle));
                $rLonB = $rLng + atan2(sin($rAngle) * sin($rAngDist) * cos($rLat), cos($rAngDist) - sin($rLat) * sin($rLatB));

                $return[$name] = array('lat' => (float) rad2deg($rLatB),
                    'lng' => (float) rad2deg($rLonB));
            }

            return array('min_lat'  => $return['south']['lat'],
                'max_lat' => $return['north']['lat'],
                'min_lng' => $return['west']['lng'],
                'max_lng' => $return['east']['lng']);
        }



        function include_css(){
            $this->settings = unserialize(get_option("chronosly_settings_{$this->id}"));
            wp_register_style( 'chronosly-filter-and-sort', CHRONOSLY_ADDONS_URL.'/filter_and_sort/style.css');
            wp_enqueue_style('chronosly-filter-and-sort');
           // wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-datepicker');
           // wp_enqueue_script('jquery-ui-slider');
            wp_register_style( 'chronosly-admin-jquery-ui-css', CHRONOSLY_URL.'/css/smoothness/jquery-ui-1.10.4.custom.css');
            wp_enqueue_style('chronosly-admin-jquery-ui-css');
            wp_register_script( 'chronosly-filter-and-sort-js', CHRONOSLY_ADDONS_URL.'/filter_and_sort/front.js');
            // This will localize the link for the ajax url to your 'my-script' js file (above). You can retreive it in 'script.js' with 'myAjax.ajaxurl'
            wp_localize_script( 'chronosly-filter-and-sort-js', 'translated',
                array(
                    'ajaxurl' => admin_url( 'admin-ajax.php' ),
                    'pricemax' => $this->settings["pricemax"]
                ));
            wp_enqueue_script('chronosly-filter-and-sort-js');
        }

        function remove(){
            delete_option("chronosly_settings_{$this->id}");//remove settings for this addon

        }



        function addon_settings($recall=0){
            if(is_admin() and !$recall)register_setting('chronosly-group', "chronosly_settings_{$this->id}");//settings for this addon
            if(!get_option("chronosly_settings_{$this->id}")){
                $settings = array(
                    "events-filter" => serialize(array(
                    'void',
                    1 => 'event_name',
                    2 => 'event_date',
                    3 => 'event_price',
                    5 => 'category',
                    6 => 'organizer',
                    7 => 'place_name',
                    10 => 'place_city',
                    13 => 'place_near')),
                    "organizers-filter" => serialize(array("void", "organizer")),
                    "places-filter" => serialize(array(
                            7 => 'place_name',
                            10 => 'place_city',
                            13 => 'place_near')),
                    "categories-filter" => serialize(array(
                            'void',
                            1 => 'event_name',
                            2 => 'event_date',
                            3 => 'event_price',
                            5 => 'category',
                            6 => 'organizer',
                            7 => 'place_name',
                            10 => 'place_city',
                            13 => 'place_near')),
                    "category-filter" => serialize(array(
                            'void',
                            1 => 'event_name',
                            2 => 'event_date',
                            3 => 'event_price',
                            5 => 'category',
                            6 => 'organizer',
                            7 => 'place_name',
                            10 => 'place_city',
                            13 => 'place_near'
                        )),
                    "calendar-filter" => serialize(array(
                            'void',
                            1 => 'event_name',
                            2 => 'event_date',
                            3 => 'event_price',
                            5 => 'category',
                            6 => 'organizer',
                            7 => 'place_name',
                            10 => 'place_city',
                            13 => 'place_near'
                        )),

                    "events-order" => serialize(array("void")),
                    "organizers-order" => serialize(array("void")),
                    "places-order" => serialize(array("void")),
                    "categories-order" => serialize(array("void")),
                    "category-order" => serialize(array("void")),

                    "distance-unit" => "km",
                    "distance-unit" => 100,
                    "month-nav" => 1,
                    "events-filter-show" => 1,
                    "organizers-filter-show" => 1,
                    "places-filter-show" => 1,
                    "categories-filter-show" => 1,
                    "category-filter-show" => 1,
                    "calendar-filter-show" => 1,
                    "using-settings" => 1,

                    "license" => "",
                    "autoupdate" => 0,
                    "version" => "1.9",
                    "needed_version" => "1.9"
                );
                update_option("chronosly_settings_{$this->id}", serialize($settings));
            }
            $this->settings = unserialize(get_option("chronosly_settings_{$this->id}"));


            if(is_admin() and !$recall){
                $this->addon_page_hook =   add_submenu_page(
                    null,
                    'Filter and Sort Settings',
                    __('Filter and Sort Settings',"chronosly"),
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
            $events_filter = unserialize($this->settings['events-filter']);
            $events_order = unserialize($this->settings['events-order']);
            $organizers_filter = unserialize($this->settings['organizers-filter']);
            $organizers_order = unserialize($this->settings['organizers-order']);
            $places_filter = unserialize($this->settings['places-filter']);
            $places_order = unserialize($this->settings['places-order']);
            $categories_filter = unserialize($this->settings['categories-filter']);
            $categories_order = unserialize($this->settings['categories-order']);
            $category_filter = unserialize($this->settings['category-filter']);
            $category_order = unserialize($this->settings['category-order']);
            $calendar_filter = unserialize($this->settings['calendar-filter']);


            ?>

            <h3><?php _e("Filter Bars Settings", "chronosly");?></h3>

            <label><?php _e("Show filter on Events list", "chronosly")?></label> <input type="checkbox" name="events-filter-show" value="1" <?php if($this->settings['events-filter-show']) echo "checked" ?> /><br/>
            <label><?php _e("Show filter on Organizers list", "chronosly")?></label> <input type="checkbox" name="organizers-filter-show" value="1" <?php if($this->settings['organizers-filter-show']) echo "checked" ?> /><br/>
            <label><?php _e("Show filter on Places list", "chronosly")?></label> <input type="checkbox" name="places-filter-show" value="1" <?php if($this->settings['places-filter-show']) echo "checked" ?> /><br/>
            <label><?php _e("Show filter on Categories list", "chronosly")?></label> <input type="checkbox" name="categories-filter-show" value="1" <?php if($this->settings['categories-filter-show']) echo "checked" ?> /><br/>
            <label><?php _e("Show filter on single Category", "chronosly")?></label> <input type="checkbox" name="category-filter-show" value="1" <?php if($this->settings['category-filter-show']) echo "checked" ?> /><br/>
            <label><?php _e("Show filter on Calendar", "chronosly")?></label> <input type="checkbox" name="calendar-filter-show" value="1" <?php if($this->settings['calendar-filter-show']) echo "checked" ?> /><br/>
            <label><?php _e("Max Price range", "chronosly")?></label> <input style="width:60px;" type="text" name="pricemax" value="<?php echo $this->settings['pricemax']; ?>" /><br/>
            <label><?php _e("Unit of length", "chronosly")?></label>
            <select name="distance-unit" style="width:60px;">
                <option value="km" <?php if($this->settings['distance-unit'] == "km") echo "selected";?> > <?php _e("km", "chronosly");?></option>
                <option value="miles" <?php if($this->settings['distance-unit'] == "miles") echo "selected";?>><?php _e("miles", "chronosly");?></option>
            </select>
            <?php /*<label><?php _e("Event list filter taking into account main settings", "chronosly")?></label> <input type="checkbox" name="using-settings" value="1" <?php if($this->settings['using-settings']) echo "checked" ?> />
            <span class="info"></span><div class="info-hide"><?php _e("Main eventlist settings like 'Main event list display' or 'Show past events' will be taken into account to display and execute filters.", "chronosly")?><br/> <?php _e("Uncheck to show all possible filters, including filters for past events (and its categories, prices, names...) for different dates not listed in main settings.", "chronosly");?></div><br/>
            */ ?>
            <br/>
            <h3><?php _e("Events list Filter and Sort", "chronosly");?></h3>
            <label></label><?php echo "<b>".__("Filter", "chronosly")."&nbsp;&nbsp;&nbsp;".__("Sort", "chronosly")."</b>"?><br/>
            <input style="display:none;" type="checkbox" name="events-filter[]" value="void"  checked /> <input style="display:none;" type="checkbox" name="events-order[]" value="void" checked />
            <label><?php _e("Week/Month/Year navigation", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="month-nav" value="1" <?php if($this->settings['month-nav']) echo "checked" ?> />
            <span class="info"></span><div class="info-hide"><?php _e("Show a week,month or year navigation, based on event list settings, to navigate previous or next.", "chronosly")?></div><br/>
            <label><?php _e("Event Name", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="events-filter[]" value="event_name" <?php if(in_array("event_name", $events_filter)) echo "checked" ?> /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="events-order[]" value="event" <?php if(in_array("event", $events_order)) echo "checked" ?> />
            <span class="info"></span><div class="info-hide"><?php _e("This filter will show as search input box.", "chronosly")?></div><br/>
            <label><?php _e("Event Date", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="events-filter[]" value="event_date" <?php if(in_array("event_date", $events_filter)) echo "checked" ?> /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="events-order[]" value="date" <?php if(in_array("date", $events_order)) echo "checked" ?> /><br/>
            <label><?php _e("Event Price", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="events-filter[]" value="event_price" <?php if(in_array("event_price", $events_filter)) echo "checked" ?> /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="events-order[]" value="price" <?php if(in_array("price", $events_order)) echo "checked" ?> />
            <span class="info"></span><div class="info-hide"><?php _e("This filter will show as range input box.", "chronosly")?></div><br/>
            <label><?php _e("Author", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="events-filter[]" value="author" <?php if(in_array("author", $events_filter)) echo "checked" ?> /><br/>
            <label><?php _e("Category", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="events-filter[]" value="category" <?php if(in_array("category", $events_filter)) echo "checked" ?> /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="events-order[]" value="category" <?php if(in_array("category", $events_order)) echo "checked" ?> /><br/>
            <label><?php _e("Organizer", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="events-filter[]" value="organizer" <?php if(in_array("organizer", $events_filter)) echo "checked" ?> /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="events-order[]" value="organizer" <?php if(in_array("organizer", $events_order)) echo "checked" ?> /><br/>
            <label><?php _e("Place Name", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="events-filter[]" value="place_name" <?php if(in_array("place_name", $events_filter)) echo "checked" ?> /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="events-order[]" value="place" <?php if(in_array("place", $events_order)) echo "checked" ?> /><br/>
            <label><?php _e("Place State", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="events-filter[]" value="place_state" <?php if(in_array("place_state", $events_filter)) echo "checked" ?> /> <br/>
            <label><?php _e("Place Country", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="events-filter[]" value="place_country" <?php if(in_array("place_country", $events_filter)) echo "checked" ?> /><br/>
            <label><?php _e("Place City", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="events-filter[]" value="place_city" <?php if(in_array("place_city", $events_filter)) echo "checked" ?> /><br/>
            <label><?php _e("Place Postal Code", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="events-filter[]" value="place_pc" <?php if(in_array("place_pc", $events_filter)) echo "checked" ?> /><br/>
            <label><?php _e("Place Address", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="events-filter[]" value="place_address" <?php if(in_array("place_address", $events_filter)) echo "checked" ?> />
            <span class="info"></span><div class="info-hide"><?php _e("This filter will show as search input box.", "chronosly")?></div><br/>
            <label><?php _e("Place near to user", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="events-filter[]" value="place_near" <?php if(in_array("place_near", $events_filter)) echo "checked" ?> /><br/>
            <br/>

            <h3><?php _e("Organizers list Filter and Sort", "chronosly");?></h3>
            <label></label><?php echo "<b>".__("Filter", "chronosly")."&nbsp;&nbsp;&nbsp;".__("Sort", "chronosly")."</b>"?><br/>
            <input style="display:none;" type="checkbox" name="organizers-filter[]" value="void"  checked /> <input style="display:none;" type="checkbox" name="organizers-order[]" value="void" checked />
            <label><?php _e("Author", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="organizers-filter[]" value="author" <?php if(in_array("author", $organizers_filter)) echo "checked" ?> /><br/>
            <label><?php _e("Organizer", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="organizers-filter[]" value="organizer" <?php if(in_array("organizer", $organizers_filter)) echo "checked" ?> /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="organizers-order[]" value="organizer" <?php if(in_array("organizer", $organizers_order)) echo "checked" ?> /><br/>
            <br/>

            <h3><?php _e("Places list Filter and Sort", "chronosly");?></h3>
            <label></label><?php echo "<b>".__("Filter", "chronosly")."&nbsp;&nbsp;&nbsp;".__("Sort", "chronosly")."</b>"?><br/>
            <input style="display:none;" type="checkbox" name="places-filter[]" value="void"  checked /> <input style="display:none;" type="checkbox" name="places-order[]" value="void" checked />
            <label><?php _e("Author", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="places-filter[]" value="author" <?php if(in_array("author", $places_filter)) echo "checked" ?> /><br/>
            <label><?php _e("Place Name", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="places-filter[]" value="place_name" <?php if(in_array("place_name", $places_filter)) echo "checked" ?> /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="places-order[]" value="place" <?php if(in_array("place", $places_order)) echo "checked" ?> /><br/>
            <label><?php _e("Place State", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="places-filter[]" value="place_state" <?php if(in_array("place_state", $places_filter)) echo "checked" ?> /><br/>
            <label><?php _e("Place Country", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="places-filter[]" value="place_country" <?php if(in_array("place_country", $places_filter)) echo "checked" ?> /><br/>
            <label><?php _e("Place City", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="places-filter[]" value="place_city" <?php if(in_array("place_city", $places_filter)) echo "checked" ?> /><br/>
            <label><?php _e("Place Postal Code", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="places-filter[]" value="place_pc" <?php if(in_array("place_pc", $places_filter)) echo "checked" ?> /><br/>
            <label><?php _e("Place Address", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="places-filter[]" value="place_address" <?php if(in_array("place_address", $places_filter)) echo "checked" ?> />
            <span class="info"></span><div class="info-hide"><?php _e("This filter will show as search input box.", "chronosly")?></div><br/>
            <label><?php _e("Place near to user", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="places-filter[]" value="place_near" <?php if(in_array("place_near", $places_filter)) echo "checked" ?> /><br/>
            <br/>

            <h3><?php _e("Categories list Filter and Sort", "chronosly");?></h3>
            <label></label><?php echo "<b>".__("Filter", "chronosly")."&nbsp;&nbsp;&nbsp;".__("Sort", "chronosly")."</b>"?><br/>
            <input style="display:none;" type="checkbox" name="categories-filter[]" value="void"  checked /> <input style="display:none;" type="checkbox" name="categories-order[]" value="void" checked />
            <label><?php _e("Event Name", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="categories-filter[]" value="event_name" <?php if(in_array("event_name", $categories_filter)) echo "checked" ?> /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="categories-order[]" value="event" <?php if(in_array("event", $categories_order)) echo "checked" ?> />
            <span class="info"></span><div class="info-hide"><?php _e("This filter will show as search input box.", "chronosly")?></div><br/>
            <label><?php _e("Event Date", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="categories-filter[]" value="event_date" <?php if(in_array("event_date", $categories_filter)) echo "checked" ?> /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="categories-order[]" value="date" <?php if(in_array("date", $categories_order)) echo "checked" ?> /><br/>
            <label><?php _e("Event Price", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="categories-filter[]" value="event_price" <?php if(in_array("event_price", $categories_filter)) echo "checked" ?> /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="categories-order[]" value="price" <?php if(in_array("price", $categories_order)) echo "checked" ?> />
            <span class="info"></span><div class="info-hide"><?php _e("This filter will show as range input box.", "chronosly")?></div><br/>
            <label><?php _e("Author", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="categories-filter[]" value="author" <?php if(in_array("author", $categories_filter)) echo "checked" ?> /><br/>
            <label><?php _e("Category", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="categories-filter[]" value="category" <?php if(in_array("category", $categories_filter)) echo "checked" ?> /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="categories-order[]" value="category" <?php if(in_array("category", $categories_order)) echo "checked" ?> /><br/>
            <label><?php _e("Organizer", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="categories-filter[]" value="organizer" <?php if(in_array("organizer", $categories_filter)) echo "checked" ?> /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="categories-order[]" value="organizer" <?php if(in_array("organizer", $categories_order)) echo "checked" ?> /><br/>
            <label><?php _e("Place Name", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="categories-filter[]" value="place_name" <?php if(in_array("place_name", $categories_filter)) echo "checked" ?> /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="categories-order[]" value="place" <?php if(in_array("place", $categories_order)) echo "checked" ?> /><br/>
            <label><?php _e("Place State", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="categories-filter[]" value="place_state" <?php if(in_array("place_state", $categories_filter)) echo "checked" ?> /><br/>
            <label><?php _e("Place Country", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="categories-filter[]" value="place_country" <?php if(in_array("place_country", $categories_filter)) echo "checked" ?> /><br/>
            <label><?php _e("Place City", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="categories-filter[]" value="place_city" <?php if(in_array("place_city", $categories_filter)) echo "checked" ?> /><br/>
            <label><?php _e("Place Postal Code", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="categories-filter[]" value="place_pc" <?php if(in_array("place_pc", $categories_filter)) echo "checked" ?> /><br/>
            <label><?php _e("Place Address", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="categories-filter[]" value="place_address" <?php if(in_array("place_address", $categories_filter)) echo "checked" ?> />
            <span class="info"></span><div class="info-hide"><?php _e("This filter will show as search input box.", "chronosly")?></div><br/>
            <label><?php _e("Place near to user", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="categories-filter[]" value="place_near" <?php if(in_array("place_near", $categories_filter)) echo "checked" ?> /><br/>
            <br/>

            <h3><?php _e("Single Category Filter and Sort", "chronosly");?></h3>
            <label></label><?php echo "<b>".__("Filter", "chronosly")."&nbsp;&nbsp;&nbsp;".__("Sort", "chronosly")."</b>"?><br/>
            <input style="display:none;" type="checkbox" name="category-filter[]" value="void"  checked /> <input style="display:none;" type="checkbox" name="category-order[]" value="void" checked />
            <label><?php _e("Event Name", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="category-filter[]" value="event_name" <?php if(in_array("event_name", $category_filter)) echo "checked" ?> /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="category-order[]" value="event" <?php if(in_array("event", $category_order)) echo "checked" ?> />
            <span class="info"></span><div class="info-hide"><?php _e("This filter will show as search input box.", "chronosly")?></div><br/>
            <label><?php _e("Event Date", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="category-filter[]" value="event_date" <?php if(in_array("event_date", $category_filter)) echo "checked" ?> /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="category-order[]" value="date" <?php if(in_array("date", $category_order)) echo "checked" ?> /><br/>
            <label><?php _e("Event Price", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="category-filter[]" value="event_price" <?php if(in_array("event_price", $category_filter)) echo "checked" ?> /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="category-order[]" value="price" <?php if(in_array("price", $category_order)) echo "checked" ?> />
            <span class="info"></span><div class="info-hide"><?php _e("This filter will show as range input box.", "chronosly")?></div><br/>
            <label><?php _e("Author", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="category-filter[]" value="author" <?php if(in_array("author", $category_filter)) echo "checked" ?> /><br/>

            <label><?php _e("Organizer", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="category-filter[]" value="organizer" <?php if(in_array("organizer", $category_filter)) echo "checked" ?> /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="category-order[]" value="organizer" <?php if(in_array("organizer", $category_order)) echo "checked" ?> /><br/>
            <label><?php _e("Place Name", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="category-filter[]" value="place_name" <?php if(in_array("place_name", $category_filter)) echo "checked" ?> /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="category-order[]" value="place" <?php if(in_array("place", $category_order)) echo "checked" ?> /><br/>
            <label><?php _e("Place State", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="category-filter[]" value="place_state" <?php if(in_array("place_state", $category_filter)) echo "checked" ?> /><br/>
            <label><?php _e("Place Country", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="category-filter[]" value="place_country" <?php if(in_array("place_country", $category_filter)) echo "checked" ?> /><br/>
            <label><?php _e("Place City", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="category-filter[]" value="place_city" <?php if(in_array("place_city", $category_filter)) echo "checked" ?> /><br/>
            <label><?php _e("Place Postal Code", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="category-filter[]" value="place_pc" <?php if(in_array("place_pc", $category_filter)) echo "checked" ?> /><br/>
            <label><?php _e("Place Address", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="category-filter[]" value="place_address" <?php if(in_array("place_address", $category_filter)) echo "checked" ?> />
            <span class="info"></span><div class="info-hide"><?php _e("This filter will show as search input box.", "chronosly")?></div><br/>
            <label><?php _e("Place near to user", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="category-filter[]" value="place_near" <?php if(in_array("place_near", $category_filter)) echo "checked" ?> /><br/>
            <br/><br/>
            <h3><?php _e("Calendar Filter", "chronosly");?></h3>
            <label></label><?php echo "<b>".__("Filter", "chronosly")."</b>"?><br/>
            <input style="display:none;" type="checkbox" name="calendar-filter[]" value="void"  checked />
            <label><?php _e("Event Name", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="calendar-filter[]" value="event_name" <?php if(in_array("event_name", $calendar_filter)) echo "checked" ?> />
            <span class="info"></span><div class="info-hide"><?php _e("This filter will show as search input box.", "chronosly")?></div><br/>
            <label><?php _e("Event Price", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="calendar-filter[]" value="event_price" <?php if(in_array("event_price", $calendar_filter)) echo "checked" ?> />
            <span class="info"></span><div class="info-hide"><?php _e("This filter will show as range input box.", "chronosly")?></div><br/>
            <label><?php _e("Author", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="calendar-filter[]" value="author" <?php if(in_array("author", $calendar_filter)) echo "checked" ?> /><br/>
            <label><?php _e("Category", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="calendar-filter[]" value="category" <?php if(in_array("category", $calendar_filter)) echo "checked" ?> /><br/>
            <label><?php _e("Organizer", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="calendar-filter[]" value="organizer" <?php if(in_array("organizer", $calendar_filter)) echo "checked" ?> /><br/>
            <label><?php _e("Place Name", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="calendar-filter[]" value="place_name" <?php if(in_array("place_name", $calendar_filter)) echo "checked" ?> /><br/>
            <label><?php _e("Place State", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="calendar-filter[]" value="place_state" <?php if(in_array("place_state", $calendar_filter)) echo "checked" ?> /><br/>
            <label><?php _e("Place Country", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="calendar-filter[]" value="place_country" <?php if(in_array("place_country", $calendar_filter)) echo "checked" ?> /><br/>
            <label><?php _e("Place City", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="calendar-filter[]" value="place_city" <?php if(in_array("place_city", $calendar_filter)) echo "checked" ?> /><br/>
            <label><?php _e("Place Postal Code", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="calendar-filter[]" value="place_pc" <?php if(in_array("place_pc", $calendar_filter)) echo "checked" ?> /><br/>
            <label><?php _e("Place Address", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="calendar-filter[]" value="place_address" <?php if(in_array("place_address", $calendar_filter)) echo "checked" ?> />
            <span class="info"></span><div class="info-hide"><?php _e("This filter will show as search input box.", "chronosly")?></div><br/>
            <label><?php _e("Place near to user", "chronosly")?></label> &nbsp;&nbsp;&nbsp;<input type="checkbox" name="calendar-filter[]" value="place_near" <?php if(in_array("place_near", $calendar_filter)) echo "checked" ?> /><br/>
            <br/><br/>
            <h3><?php _e("License & Updates", "chronosly");?></h3>
            <label><?php _e("License key for updates", "chronosly")?></label> <input type="text" name="license" value="<?php echo $this->settings['license']; ?>"/> <span class="info"></span>
            <div class="info-hide"><?php _e("provide the license sent to your email for alowing future updates of this addon", "chronosly")?></div><br/>
            <label><?php _e("Enable auto update", "chronosly")?></label> <input type="checkbox" name="autoupdate" value="1" <?php if($this->settings['autoupdate']) echo "checked" ?> /><br/>
            <?php
            do_action("chronosly-addon-foot",$this->id);
        }



        //set the class name for calling addon in settings page
        function addon_add_item($addons){
            $merge = array_merge($addons, array($this->id => "Chronosly_Filter_and_Sort"));
            asort($merge);
            return $merge;
        }

        function addon_add_menu_item($addon_menu){
            $merge = array_merge($addon_menu, array($this->settings_url => "Filter and Sort"));
            asort($merge);
            return $merge;
        }



        function admin_js(){

            wp_register_script( 'chronosly-filter-and-sort-admin-js', CHRONOSLY_ADDONS_URL.'/filter_and_sort/admin.js');
            wp_enqueue_script('chronosly-filter-and-sort-admin-js');
        }

        function shortcode($atts){
            // add_action("chronosly_custom_backend_css", array(&$this,"admin_js"));//add custom js to wpadmin
            // add_action("chronosly_custom_backend_css", array(&$this,"include_css"));//add custom js to wpadmin
            add_action("chronosly_custom_frontend_css", array(&$this,"include_css"));//add custom styles  and js to frontend
            do_action("chronosly_custom_frontend_css");
            $settings = unserialize(get_option("chronosly_settings_{$this->id}"));
            $this->check_submit($settings);
            $this->print_form($settings);

         }



    }
}

//initialize addon
global $Chronosly_Filter_and_Sort;
$Chronosly_Filter_and_Sort = new Chronosly_Filter_and_Sort();
$Chronosly_Filter_and_Sort->addon_settings(1);
