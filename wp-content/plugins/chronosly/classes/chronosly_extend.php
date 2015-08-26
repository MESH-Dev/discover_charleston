<?php
if(!class_exists('Chronosly_Extend'))
{
    class Chronosly_Extend
    {
        /**
         * Construct the plugin object
         */

        public $core_update_url = "http://plugins.heimsveld.com/updates-core.php";
        public $addons_feed_url = "http://plugins.heimsveld.com/addons_feed.json";
        public $templates_feed_url = "http://plugins.heimsveld.com/templates_feed.json";
        public $addons_update_url = "http://plugins.heimsveld.com/updates-addons.php";
        public $templates_update_url = "http://plugins.heimsveld.com/updates-templates.php";

        public function __construct()
        {


            // register actions

            add_action('init', array(&$this, 'admin_init'), 11);
          
            add_action("chronosly_update_core", array(&$this, 'update_core'));
           add_action("chronosly_update_addons", array(&$this, 'update_addons'), 100, 1);
           add_action("chronosly_update_templates",array(&$this, 'update_templates'), 100, 1);


            add_action( 'widgets_init', array(&$this, 'register_widgets' ));
           
        //  add_action('admin_menu', array(&$this, 'extra_init'));
        //  add_action( 'admin_enqueue_scripts', array(&$this,'extra_init') );


        } // END public function __construct


        public function get_addons_feed(){
            if(false === ( $addons = get_transient( 'addons_marketplace' ))){
                $cont = $this->get_external_content($this->addons_feed_url,"json");
                if(!$cont["error"]){
                    $addons =   $cont["content"];
                    set_transient( "addons_marketplace", $addons, 60 * 60 * 24  );
                    foreach($addons as $k=>$ad){
                        if($settings = unserialize(get_option("chronosly_settings_{$k}"))) {
                            $settings["needed_version"] = $ad->version;
                            update_option("chronosly_settings_{$k}", serialize($settings));
                        }
                    }
                }
            }
            return $addons;

        }

        public function get_templates_feed(){
            if(false === ( $templates = get_transient( 'templates_marketplace' ))){
                $cont = $this->get_external_content($this->templates_feed_url,"json");
                if(!$cont["error"]){
                    $templates= $cont["content"];
                    set_transient( "templates_marketplace", $templates, 60 * 60 * 24  );
                }            }
            return $templates;

        }

        public function register_widgets() {
            register_widget( 'Chronosly_Widget' );
        }


         public function update_core($manual=0){
            $settings = unserialize(get_option("chronosly-settings"));

            if(!$manual and !$settings["chronosly_core_autoupdate"]) return;

            $cont = $this->get_external_content($this->core_update_url."?key=".$settings["chronosly_license"],"json");

            if(!$cont["error"]){
                if(CHRONOSLY_VERSION == $cont["content"]->version) return "<div class='bubblegreen'>The core is already up to date.</div>";
                if(!$this->update_code("core", $cont["content"]->url)){
                    $cont["message"] = "<div class='bubbleerror'>Its impossible to update the core, try again later. If the problem persist contact with chronosly team.</div>";
                } else {
                    $cont["message"] = "<div class='bubblegreen'>The core is up to date now.</div>";
                    Chronosly_Extend::rebuild_template_addons("default");

                    //the new code will set CHRONOSLY_VERSION code to the latest
                    //update templates and addons
                    $this->update_addons(); 
                    $this->update_templates(); 
                    Chronosly_Cache::clear_cache();
                }

            }
             return $cont["message"];


        }
       
        public function update_addons($addon=""){
            $this->get_addons_feed();//traemos el feed para ver las versiones
            if($addon){
                $addons_list = array($addon => "not important");
            }
            else {
                $addons = array();
                $addons_list = apply_filters("chronosly_addons_settings_item", $addons);
            }
            $setts = unserialize(get_option("chronosly-settings"));
            foreach($addons_list as $k=>$v){
                $settings = unserialize(get_option("chronosly_settings_{$k}"));
                if(!$addon and !$settings["autoupdate"] and !$setts["chronosly_addons_autoupdate"]) continue;//no tenemos que actualizar
                $cont = $this->get_external_content($this->addons_update_url."?addon=$k&key=".$settings["license"],"json");
                if(!$cont["error"]){
                   if($settings["version"] !=  $cont["content"]->version){
                       if(!$this->update_code("addons", $cont["content"]->url, $k)){
                           $cont["message"] = "<div class='bubbleerror'>Its impossible to update this addon, try again later. If the problem persist contact with chronosly team.</div>";
                       } else {
                           $cont["message"] = "<div class='bubblegreen'>The addon is up to date now.</div>";
                           //update version to new, recordar que el addon debe poner el version en el codigo tambien
                           $settings["version"] = $cont["content"]->version;
                           $settings["needed_version"] = $cont["content"]->version;
                           update_option("chronosly_settings_{$k}", serialize($settings));
                           Chronosly_Cache::clear_cache();
                           //seguramente haya que actualizar el template si afecta 2.0
                       }
                   } else {
                       $cont["message"] = "<div class='bubblegreen'>The addon is already up to date.</div>";
                   }



                }
            }
             return  $cont["message"];
            //si se llama desde el admin ejecutar un reload par que lo cargue bien en la pantalla
            //

        }

        public function update_templates($template=""){
            //en el caso que instalemos un addon, necesitaremos poner si necesita actualizar el template o no

            $templates = array();
            $temp = new Chronosly_Templates;
            $templates_list =  $temp->load_template_settings($temp->get_templates_options(1), 1);

            //demomento nada utiliza la accion directa lo comentamos
           /* if($template){
                $templates_list = array($template => $templates_list[$template]);
            }*/
            $settings = unserialize(get_option("chronosly-settings"));

            foreach($templates_list as $k=>$v){
                if($k == "default" or (!$template and !$settings["chronosly_templates_autoupdate"]) or !$settings["chronosly-settings_templates_license_$k"]) continue;//no tenemos que actualizar revisar la licencia 2.0
                $cont = $this->get_external_content($this->templates_update_url."?template=$k&key=".$settings["chronosly-settings_templates_license_$k"],"json");
                if(!$cont["error"]){
                    //echo $k." ".$v->version." ".$cont["content"]->version;
                    if($v->version !=  $cont["content"]->version){
                        if(!$this->update_code("templates", $cont["content"]->url)){
                            $cont["message"] = "<div class='bubbleerror'>Its impossible to update this template, try again later. If the problem persist contact with chronosly team.</div>";
                        } else {

                            $cont["message"] = "<div class='bubblegreen'>The template is up to date now.</div>";
                            //update version to new, recordar que el addon debe poner el version en el codigo tambien
                            $settings["version"] = $cont["content"]->version;
                            update_option("chronosly_settings_{$k}", serialize($settings));
                            Chronosly_Cache::clear_cache();
                           if($k != "default") Chronosly_Extend::rebuild_template_addons($k); //2.o revisar porque no se hace el default

                        }
                    } else {
                        $cont["message"] = "<div class='bubblegreen'>The template is already up to date.</div>";
                    }



                }
            }

            return  $cont["message"];
            //si se llama desde el admin ejecutar un reload par que lo cargue bien en la pantalla
            //

        }


        //Cuando un template se actualiza se pierden los añadidos de los addons
        public function rebuild_template_addons($template){
            global  $Post_Type_Chronosly;
            $addons = array();
            $addons_list = apply_filters("chronosly_addons_settings_item", $addons);
            if(!count($addons_list)) {
                Chronosly_Extend::init_addons();
                $addons_list = apply_filters("chronosly_addons_settings_item", $addons);
            }

            if(count($addons_list)){
                foreach($addons_list as $k=>$v){
                    if(has_filter("chronosly_update_template_{$k}")) $Post_Type_Chronosly->template->full_update_templates_by_addon($k,array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12), array($template));

                }
            }
        }


        public function get_external_content($url, $format=""){
            $response = wp_remote_post($url, array("timeout" => 60));
            $cont = array();
            if ( is_wp_error( $response ) ) {
                $error_message = $response->get_error_message();
                $cont["error"] = 1 ;
                $cont["message"] = "Something went wrong: $error_message";
            } else {
                if($response["response"]["code"] != 200){
                    $cont["error"] = 1 ;
                    $cont["message"] = $response["response"]["message"].": ".$response["body"];
                }
               else {
                   $cont["error"] = 0 ;
                   switch($format){
                       case "json":
                           $cont["content"] = json_decode($response["body"]);
                           break;
                       default:
                           $cont["content"] = $response["body"];
                           break;
                   }
               }
            }
           //print_r($response);
           // print_r($cont);
            return $cont;
        }

        //update code via donwloadding zip
        public function update_code($type, $url, $addon=""){
            global $Chronosly_Settings;
            switch($type){
                case "core":
                    $path = CHRONOSLY_PATH;
                break;
                case "addons":
                    $path = CHRONOSLY_ADDONS_PATH;
                break;
                case "templates":
                    $path = CHRONOSLY_TEMPLATES_PATH;
                break;
            }

            $cont = $this->get_external_content($url);
           // return;
            //save the zip temporaly
            if($cont["error"]) return 0;
            $zip = $cont["content"];
            $fp = fopen(CHRONOSLY_PATH.DIRECTORY_SEPARATOR."temp.zip", "w");
            fwrite($fp, $zip);
            fclose($fp);
            require_once(ABSPATH .'/wp-admin/includes/file.php');
            WP_Filesystem();
            if(stripos($addon, "organizers_and_places") !== FALSE) $path = CHRONOSLY_PATH;
            $unzipfile = unzip_file(CHRONOSLY_PATH.DIRECTORY_SEPARATOR."temp.zip", $path);
            @unlink( CHRONOSLY_PATH.DIRECTORY_SEPARATOR."temp.zip");
            $utils = new Chronosly_Utils();

            if(stripos($addon, "organizers_and_places") !== FALSE and $unzipfile === true) {
                $files = scandir ( $path.DIRECTORY_SEPARATOR."organizers_and_places");
                foreach ( $files as $file ) {
                    if ($file != "." && $file != ".."){
                        $utils->rcopy( $path.DIRECTORY_SEPARATOR."organizers_and_places/$file", $destination_path.DIRECTORY_SEPARATOR."$file" );
                    }
                }
            }

            if ( $unzipfile === true) return 1;
            return 0;
        }

        public static function create_dad_field($data){
            global $ch_field_create_js_code, $ch_field_modify_js_code, $ch_js_style_fields, $ch_php_style_fields;
            if(!count($data)) return; //el data tiene que ser un array valido
            if(!$data["name"] or !$data["type"] or !$data["type"]) return; //aseguramos que tenemos los minimos
            if($data["el_type"] == "var" and (!$data["php_function"] or !$data["js_function"])) return;
            else if($data["el_type"] == "style" and (!$data["style_fields"] or !$data["js_style_values"] or !$data["php_style_values"])) return;
            $return = "";
            $return .= Chronosly_Extend::create_field($data);
            //llamamos a los fields associados a este value

            if(isset($data['fields_associated'])){
                foreach($data['fields_associated'] as $field){
                    if(has_filter("chronosly_field_".$field["name"])){
                        $default = $field;

                        //if(count($fields_array) and isset($fields_array[$field["name"]])) $default = array_merge($default, $fields_array[$field["name"]]); //setting values to fields
                        $return .= apply_filters("chronosly_field_".$field["name"], $default);
                    }
                }
            }

            if(isset($data['js_function'])){
                //si no la hemos añadido ya antes

                if(stripos($ch_field_create_js_code, $data["name"]."chseparator1") === FALSE){

                    //si existe la funcion llamamos para generar el array de funciones que imprimiremos en el js al crear el elemento o eliminarlo

                    if(!is_array($data['js_function'])){
                        if(function_exists($data['js_function'])){
                                $ch_field_create_js_code .= $data["name"]."chseparator1".call_user_func($data['js_function'], "create")."chseparator2";
                                $ch_field_modify_js_code .= $data["name"]."chseparator1".call_user_func($data['js_function'], "modify")."chseparator2";


                        }
                    } else {
                        if(class_exists($data['js_function'][0]) and method_exists ($data['js_function'][0], $data['js_function'][1])){
                            $ch_field_create_js_code .= $data["name"]."chseparator1".call_user_func($data['js_function'], "create")."chseparator2";
                            $ch_field_modify_js_code .= $data["name"]."chseparator1".call_user_func($data['js_function'], "modify")."chseparator2";
                        }
                    }

                }

            }
            //creamos el filter para hacer render en el frontend

            if(isset($data['php_function'])){
                if(!has_filter("chronosly_field_render_".$data['name'])){
                   //echo "chronosly_field_render_".$data["name"]." ";
                    //print_r($data['php_function']);echo "<br/>";
                    add_filter("chronosly_field_render_".$data["name"], $data['php_function'], 10, 4);
                }
            }

            //si es una field de estilos
            if(isset($data['style_fields']) and isset($data['js_style_values']) and isset($data['php_style_values'])){
                $ch_js_style_fields[$data["name"]] = array("fields" => $data['style_fields'], "values" => $data['js_style_values']);
                $ch_php_style_fields[$data["name"]] = array("fields" => $data['style_fields'], "values" => $data['php_style_values']);
            }

            return $return;

        }



        /* recibe un array del formato
        array(
            "label" => "Event title",
            "name" => "title",
            "type" => "hidden",
            "fields_associated" => array(
                array(
                    "name" => "shorten_text",
                    "label" => "Shorten title"
                ) ,
                array(
                    "name" => "readmore_check",
                    "label" => "Link title"
                ) ,
                array(
                    "name" => "readmore_action",
                    "label" => "Action when click link"
                ) ,
                array(
                    "name" => "target_blank"
                ) ,
                array(
                    "name" => "nofollow"
                )
            ) ,
            "data_field" => "post_title",
            "data_static function" => array(
                "Chronosly_Teplates",
                "get_title_content"
            )

           @inside_template para cargar los datos hidden en el template,
            si es 0 representa un elemento a hacer dad,
             si es 1 representa un elemento ya cargado en el template  con un value
        */
        public static function create_dad_buble($data, $inside_template = 0, $fields_array = array(), $style = array()){

            global $ch_bubble_create_js_code, $ch_bubble_modify_js_code;
            if(!count($data)) return; //el data tiene que ser un array valido
            if($data['name'] != "box" and (!$data["name"]  or !$data["box_name"]  or !$data["type"])) return; //aseguramos que tenemos los minimos
            $return = "";
            if(!$inside_template and isset($data["box_info"])) $return .= "<li class='draggable info' title='".$data["box_info"]."'> <span class='title'>".__($data["box_name"], "chronosly")."</span>";//añadimos el d&d si es una box
            else if(!$inside_template) $return .= "<li class='draggable'> <span class='title'>".$data["box_name"]."</span>";//añadimos el d&d si es una box
           if($data['name'] != "box") {
               $return .= "<div class='ev-hidden'>
                            <div class='vars'>";

                 if(isset($fields_array["bubble_value"])) $data["value"] = $fields_array["bubble_value"];
                            $return .= Chronosly_Extend::create_field($data, 1); //creamos el campo

                            if($data['js_function']){
                                //si no la hemos añadido ya antes
                                if(stripos($ch_bubble_create_js_code, $data["name"]."chseparator1") === FALSE){
                                    //si existe la funcion llamamos para generar el array de funciones que imprimiremos en el js al crear el elemento o eliminarlo
                                    if(!is_array($data['js_function'])){
                                        if(function_exists($data['js_function'])){
                                            $ch_bubble_create_js_code .= $data["name"]."chseparator1".call_user_func($data['js_function'], "create")."chseparator2";
                                            $ch_bubble_modify_js_code .= $data["name"]."chseparator1".call_user_func($data['js_function'], "modify")."chseparator2";
                                        }
                                    } else {
                                        if(class_exists($data['js_function'][0]) and method_exists ($data['js_function'][0], $data['js_function'][1])){
                                            $ch_bubble_create_js_code .= $data["name"]."chseparator1".call_user_func($data['js_function'], "create")."chseparator2";
                                            $ch_bubble_modify_js_code .= $data["name"]."chseparator1".call_user_func($data['js_function'], "modify")."chseparator2";
                                        }
                                    }
                                }
                            }



                //llamamos a los fields associados a este value
                 if(isset($data['fields_associated'])){
                     foreach($data['fields_associated'] as $field){
                        if(has_filter("chronosly_field_".$field["name"])){
                                    $default = $field;

                                    if(count($fields_array) and isset($fields_array[$field["name"]])) $default = array_merge($default, $fields_array[$field["name"]]); //setting values to fields
                                     $return .= apply_filters("chronosly_field_".$field["name"], $default);
                                }
                     }
                 }
                            $return .= "</div>";
           }
            /* STYLE BOXES */
                    $styleboxes = Chronosly_Paint::default_style_boxes();
                    foreach($styleboxes as $id=>$box){
                        $return .= "<div class='$id'>";
                        $cont = "";
                        $cont .= apply_filters("chronosly_style_box_".$id."_fields", $cont, $style);//genramos los inputs de los stylers
                        $return .= $cont;
                        $return .= "</div>";
                    }
            if($data['name'] != "box"){
                $return .= "</div>";


                if(!$inside_template) $return .= "</li>";

                //creamos el filter para hacer render en el frontend
                if($data['php_function']){
                    if(!has_filter("chronosly_bubble_render_".$data['name'])) add_filter("chronosly_bubble_render_".$data["name"], $data['php_function'], 10, 3);
                }
            }
            return $return;

        }

        private static function create_field($data, $order=0){
            $return = "";
            $ord = "";
            if($order) $ord = ' order="0" ';
            if(!isset($data['value'])) $data['value'] = "";
            switch($data['type']) {

                case "hidden":
                case "cont_box":
                    $return  .= '<input '.$ord.' class="'.$data['name'].'" name="'.$data['name'].'" type="hidden" value="'.$data['value'].'" />';
                    break;
                case "input":
                    $return  .= "<label>".$data['label'].'</label> <input '.$ord.' class="'.$data['name'].'" name="'.$data['name'].'" type="text" value="'.$data['value'].'" /><br/>';
                    break;
                case "time":
                    $return  .= "<label>".$data['label'].'</label> <input $ord extra="'.$data["extra"].'" class="'.$data['name'].'" name="'.$data['name'].'" type="text" value="'.$data['value'].'" /><br/>';
                    break;
                case "checkbox":
                    $checked ="";
                    if($data['value']) $checked = 'checked="checked"';
                    $return  .= "<label>".$data['label'].'</label> <input '.$ord.' class="'.$data['name'].'" name="'.$data['name'].'" type="checkbox" value="1" '.$checked.' /><br/>';
                    break;
                case "textarea":
                    $return .= "<label class='full'>".$data['label'].'</label><br/><textarea '.$ord.'  name="'.$data['name'].'" class="textarea2 '.$data['name'].'"  rows="4" cols="50" >'.$data['value'].'</textarea><br/>';
                    break;
                case "wyswyg":
                    $return  .= "<label class='full'>".$data['label'].':</label> <textarea '.$ord.'  name="'.$data['name'].'" class="textarea '.$data['name'].'"  rows="4" cols="50" >'.$data['value'].'</textarea><br/>';

                    break;
                case "image":
                    $return .= "<label>".$data['label'].'</label><br/> <input  '.$ord.'  class="upload_image" type="text" size="36" name="'.$data['name'].'" value="'.$data['value'].'" />
                                                    <input  class="upload_image_button" type="button" value="'.__('Upload Image', 'chronosly').'" /><br/>';

                    break;
                case "gallery":
                    $return .= "<label>".$data['label'].'</label><br/> <input  '.$ord.'  class="upload_gallery" type="text" size="36" name="'.$data['name'].'" value="'.$data['value'].'" /><input  class="upload_gallery_button" type="button" value="'.__("Update Gallery", "chronosly").'" /><br/>';

                    break;
                case "select":
                    $select = 0;
                    $return .= "<label>".$data['label']."</label> <select class='".$data['name']."' name='".$data['name']."'>";
                    $return .= "<option value=''></option>";
                    foreach($data["options"] as $id=>$op){
                        $sel = "";
                        if($data['value'] == $id and $data['value'] !== "") {
                            $select = 1;
                            $sel = "selected='selected'";
                        }
                        $return .= "<option value='$id' $sel>".__($op, "chronosly")."</option>";
                    }
                    $return .= "</select><br/>";
               break;
                case "link":
                    break;

                default:
                    //extendemos los tipos llamando al hook que toca
                    $return .= apply_filters("new_dad_buble_".$data['type'], $data);//se crea un hook especifico para llamar a la variable personalizada
                    break;
            }
            return $return;
        }

        /**
         * hook into WP's admin_init action hook
         */
       public  function admin_init()
        {


        
            $this->init_addons();
            do_action("chronosly_dad_vars_fields");




        }

        //cargamos los init de la carpeta addons
        private static function init_addons(){
            $addonspath = CHRONOSLY_PATH.DIRECTORY_SEPARATOR."addons".DIRECTORY_SEPARATOR;
            if ($handle = opendir($addonspath)) {
                while (false !== ($entry = readdir($handle))) {
                    if($entry != "." and $entry != ".." and is_dir($addonspath.$entry)) {
                        if ($handle2 = opendir($addonspath.$entry)) {
                            while (false !== ($entry2 = readdir($handle2))) {
                                if($entry2 == "init.php") require_once($addonspath.$entry.DIRECTORY_SEPARATOR."init.php");
                            }
                            closedir($handle2);

                         }


                    }

                }

                closedir($handle);
            }
        }


        // END public function plugin_settings_page()



    } // END
} // END

