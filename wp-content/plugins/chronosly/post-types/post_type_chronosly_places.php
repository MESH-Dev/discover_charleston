<?php
if (!class_exists('Post_Type_Chronosly_Places')) {
    /**
     * A PostTypeTemplate class that provides 3 additional meta fields
     */
    class Post_Type_Chronosly_Places
    {
        const POST_TYPE	= "chronosly_places";
        private $_meta	= array(
            'evp_phone',
            'evp_mail',
            'evp_web',
            'evp_dir',
            'evp_city',
            'evp_country',
            'evp_state',
            'evp_pc',
            'latlong',
            'featured',
            'order'

        );

        public $template, $settings;


        /**
         * The Constructor
         */
        public function __construct()
        {
            global $Post_Type_Chronosly;
            // register actions
            add_action('init', array(&$this, 'init'));
            add_action('admin_init', array(&$this, 'admin_init'));
            if(is_admin()) add_action( 'wp_ajax_chronosly_add_place', array(&$this, 'js_add_place' ));

            $this->template = $Post_Type_Chronosly->template;

        } // END public function __construct()

        /**
         * hook into WP's init action hook
         */
        public function init()
        {
            // Initialize Post Type
            $this->settings = unserialize(get_option("chronosly-settings"));
            $this->create_post_type();
            add_action('save_post', array(&$this, 'save_post'));
            $this->set_latlongs();
        } // END public function init()

        /**
         * Create the post type
         */
        public function create_post_type()
        {
            global $Post_Type_Chronosly;
            $slug = "chronosly-place";
            if($Post_Type_Chronosly->settings['chronosly-place-slug']) $slug = $Post_Type_Chronosly->settings['chronosly-place-slug'];
            register_post_type(self::POST_TYPE,
                array(
                    'labels' => array(
                        'name' => __("Places", "chronosly"),
                        'singular_name' => __("Place", "chronosly"),
                        'add_new' =>  __("Add new place", "chronosly"),
                        'add_new_item' =>  __("Add new place", "chronosly"),
                        'view_item' =>  __("View place", "chronosly"),
                        'search_items' =>  __("Search place", "chronosly"),


                    ),
                    'rewrite' => array('slug' => $slug, 'with_front' => false, 'feeds' => true),
                    'public' => true,
                    'show_ui' => true,
                    'map_meta_cap'	=> true,
                    'capability_type' => 'chronosly',
                    'capabilities' => array(
                        'publish_posts' => 'publish_chronoslys',
                        'edit_posts' => 'edit_chronoslys',
                        'edit_others_posts' => 'edit_others_chronoslys',
                        'edit_private_posts' => 'edit_private_chronoslys',
                        'edit_published_posts' => 'edit_published_chronoslys',
                        'delete_posts' => 'delete_chronoslys',
                        'delete_others_posts' => 'delete_others_chronoslya',
                        'read_private_posts' => 'read_private_chronoslys',
                        'delete_private_posts' => 'delete_private_chronoslys',
                        'delete_published_posts' => 'delete_published_chronoslys',
                        'edit_post' => 'edit_chronosly',
                        'delete_post' => 'delete_chronosly',
                        'read_post' => 'read_chronosly',
                    ),
                    'hierarchical' => true,
                    'has_archive'	=> $slug,
                    'menu_position' => 1,
                    'menu_icon' => 'dashicons-admin-site',
                    'show_in_menu'  => 'edit.php?post_type=chronosly',

                    'capability' => 'chronosly_author',
                    'has_archive' => true,
                    'description' => __("Events type for managing your events", "chronosly"),
                    'supports' => array(
                        'title', 'editor','excerpt','thumbnail'
                    ),
                )
            );
            if($Post_Type_Chronosly->settings['chronosly-allow-flush']and !$Post_Type_Chronosly->settings['chronosly-places-flushed']) {
                flush_rewrite_rules();
                $Post_Type_Chronosly->settings['chronosly-places-flushed'] = 1;
                update_option('chronosly-settings', serialize($Post_Type_Chronosly->settings));

            }
           // add_filter( 'map_meta_cap', array("Post_Type_Chronosly",'chronosly_map_meta_cap'), 10, 4 );
            add_filter( 'template_include', array("Post_Type_Chronosly",'chronosly_templates') );
        }



        public static function add_custom_places_orderby( $orderby ) {
            global $wpdb;
            if(has_action( 'posts_orderby', array("Post_Type_Chronosly_Places",'add_custom_places_orderby'))) remove_action( 'posts_orderby', array("Post_Type_Chronosly_Places",'add_custom_places_orderby') );

            //set the order
            $order = array('orderby' => 'title');
            $settings = unserialize(get_option("chronosly-settings"));

            $limit =  ((isset($_REQUEST["count"]) and $_REQUEST["count"])?$_REQUEST["count"]:$settings["chronosly_places_x_page"]);
            $orderdir = "ASC";
            if($_REQUEST["orderby"] == "place" and $_REQUEST["orderdir"]) $orderdir = $_REQUEST["orderdir"];

            //if featured on top
            if($settings["chronosly_featured_first"] and !$_REQUEST["featured"]){


                $metaquery = array();


                $metaquery[] = array(
                    'key' => 'featured',
                    'value' => "",
                    'compare' => '='
                );

                $args = array(
                        'post_type' => 'chronosly_places',
                        'post_status'      => 'publish',
                        'order' => $orderdir,
                        //'posts_per_page'   => $limit,
                        'numberposts'       => -1,
                        "meta_query" => $metaquery

                    )+$order;
                if ( is_user_logged_in() ) $args["post_status"] = array('publish', 'private');

                $normal_args = $args;

                $metaquery = array();

                $metaquery[] = array(
                    'key' => 'featured',
                    'value' => 1,
                    'compare' => '='
                );

                $args = array(
                        'post_type' => 'chronosly_places',
                        'post_status'      => 'publish',
                        'order' => $orderdir,
                       'posts_per_page'   => -1,
                        'numberposts'       => -1,
                        "meta_query" => $metaquery
                    )+$order;
                if ( is_user_logged_in() ) $args["post_status"] = array('publish', 'private');

                $featured_args = $args;
                $normal = get_posts( $normal_args );
                $featured = get_posts( $featured_args );
                $posts =  (array) array_merge((array) $featured,(array) $normal);
                //echo "<pre>";print_r($posts);
                if ( $posts ) {

                    // add custom ordering
                    $sql = ' CASE';
                    $i = count( $posts );
                    foreach ( $posts as $post ) {
                        $sql .= " WHEN $wpdb->posts.ID = $post->ID THEN $i";
                        $i--;
                    }
                    $sql .= ' ELSE 0 END DESC';

                    $orderby = $sql ;
                }

            }
            else {
                $metaquery = array();


                $args = array(
                        'post_type' => 'chronosly_places',
                        'post_status'      => 'publish',
                        'order' => $orderdir,
                        //'posts_per_page'   => $limit,
                        'numberposts'       => -1,
                        "meta_query" => $metaquery

                    )+$order;
                if ( is_user_logged_in() ) $args["post_status"] = array('publish', 'private');

                $normal_args = $args;



                $normal = get_posts( $normal_args );
                $posts =  (array)  $normal;
                //echo "<pre>";print_r($posts);
                if ( $posts ) {

                    // add custom ordering
                    $sql = ' CASE';
                    $i = count( $posts );
                    foreach ( $posts as $post ) {
                        $sql .= " WHEN $wpdb->posts.ID = $post->ID THEN $i";
                        $i--;
                    }
                    $sql .= ' ELSE 0 END DESC';

                    $orderby = $sql ;
                }

            }


            //echo $orderby;
            return $orderby;
        }


        /**
         * Save the metaboxes for this custom post type
         */
        public function save_post($post_id)
        {
            // verify if this is an auto save routine.
            // If it is our form has not been submitted, so we dont want to do anything
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return;
            }
            // handle the case when the custom post is quick edited
            // otherwise all custom meta fields are cleared out
            if (wp_verify_nonce($_POST['_inline_edit'], 'inlineeditnonce'))
                return;

            if (isset($_POST['post_type']) && $_POST['post_type'] == self::POST_TYPE && current_user_can('edit_post', $post_id)) {
                Chronosly_Cache::delete_item($post_id);
                foreach ($this->_meta as $field_name) {
                    // Update the post's meta field
                    update_post_meta($post_id, $field_name, $_POST[$field_name]);
                }
            } else {
                return;
            } // if($_POST['post_type'] == self::POST_TYPE && current_user_can('edit_post', $post_id))
        } // END public function save_post($post_id)

        public function js_add_place(){
            $name = (isset($_REQUEST['name'])?$_REQUEST['name']:"");
            $phone = (isset($_REQUEST['phone'])?$_REQUEST['phone']:"");
            $mail = (isset($_REQUEST['mail'])?$_REQUEST['mail']:"");
            $web = (isset($_REQUEST['web'])?$_REQUEST['web']:"");
            $dir = (isset($_REQUEST['dir'])?$_REQUEST['dir']:"");
            $city = (isset($_REQUEST['city'])?$_REQUEST['city']:"");
            $state = (isset($_REQUEST['state'])?$_REQUEST['state']:"");
            $country= (isset($_REQUEST['country'])?$_REQUEST['country']:"");
            $pc = (isset($_REQUEST['pc'])?$_REQUEST['pc']:"");

            $args = array(
                "post_title" => $name,
                'post_status'      => 'publish',
                "post_type" => self::POST_TYPE
            );
            if($id = wp_insert_post($args)){
                add_post_meta($id, "evp_phone", $phone);
                add_post_meta($id, "evp_web", $web);
                add_post_meta($id, "evp_mail", $mail);
                add_post_meta($id, "evp_dir", $dir);
                add_post_meta($id, "evp_city", $city);
                add_post_meta($id, "evp_country", $country);
                add_post_meta($id, "evp_state", $state);
                add_post_meta($id, "evp_pc", $pc);
                echo '<input type="checkbox" checked name="places[]" value="'.$id.'" />'.$name."<br>";

            }

            die();
        }

        /**
         * hook into WP's admin_init action hook
         */
        public function admin_init()
        {
            // Add metaboxes
            add_action('add_meta_boxes', array(&$this, 'add_meta_boxes'));
        } // END public function admin_init()

        /**
         * hook into WP's add_meta_boxes action hook
         */
        public function add_meta_boxes()
        {
            // Add this metabox to every selected post
            global $post;

            add_meta_box(
                sprintf('chronosly_%s_vars_section', self::POST_TYPE),
                __('Place details', 'chronosly'),
                array(&$this, 'add_inner_meta_boxes'),
                self::POST_TYPE,
                'normal',
                'high',
                array('type' => 'place', "post" => $post)

            );
            if( $this->settings["chronosly_template_editor"]){
                add_meta_box(
                    sprintf('chronosly_%s_dad9_section', self::POST_TYPE),
                    __('Template', 'chronosly'),
                    array(&$this, 'add_inner_meta_boxes'),
                    self::POST_TYPE,
                    'normal',
                    'high',
                    array('type' => 'dad9', "post" => $post)

                );
            }
            add_meta_box(
                sprintf('chronosly_place_%s_vars_section', self::POST_TYPE),
                __('Options', 'chronosly'),
                array(&$this, 'add_inner_meta_boxes'),
                self::POST_TYPE,
                'side',
                'core',
                array('type' => 'vars', "post" => $post)

            );
        } // END public function add_meta_boxes()

        /**
         * called off of the add meta box
         */
        public function add_inner_meta_boxes($post, $metabox)
        {
            global $Post_Type_Chronosly;
            $post = $metabox['args']['post'];//solve problematical posts ids

            // Render the job order metabox
            if (count($metabox['args']) and isset($metabox['args']['type'])) {
                //set de defaults vars for custmize contents

                 if ('place' == $metabox['args']['type']) {
                    $vars = @get_post_meta($post->ID);
                    require_once(CHRONOSLY_PATH.DIRECTORY_SEPARATOR."metaboxes".DIRECTORY_SEPARATOR.self::POST_TYPE."_details_metabox.php");

                } elseif ('vars' == $metabox['args']['type']) {
                    $vars = @get_post_meta($post->ID);
                    require_once(CHRONOSLY_PATH.DIRECTORY_SEPARATOR."metaboxes".DIRECTORY_SEPARATOR."chronosly_vars_metabox.php");

                } elseif ('dad9' == $metabox['args']['type']) {
                     //cargando vistas

                     $vistas = array(
                         "dad9" => __("All places list view", "chronosly"),
                         "dad10" => __("Single place view", "chronosly")

                     );

                     //drag and drop 9
                    //get custom templates
                    if ($handle = opendir(CHRONOSLY_TEMPLATES_PATH.DIRECTORY_SEPARATOR."dad9".DIRECTORY_SEPARATOR)) {

                        while (false !== ($entry = readdir($handle))) {
                            if($entry != "." and $entry != "..") $custom_templates[] = str_replace(".php", "",$entry);

                        }

                        closedir($handle);
                    }

                    $dadcats = array();


                     $perfil = $this->settings['chronosly_tipo_perfil'];
                     $selected_template = $this->template->get_tipo_template($post->ID, "dad9");
                     if($selected_template == "chbd")  $selected_template = "template_edited";
                     $select_options =  $this->template->build_templates_select($this->template->get_file_templates($post->ID, "dad9",$perfil), $selected_template);
                     require_once(CHRONOSLY_PATH.DIRECTORY_SEPARATOR."metaboxes".DIRECTORY_SEPARATOR."dad9".DIRECTORY_SEPARATOR.self::POST_TYPE."_dad9_select_metabox.php");
                     //load custom or default template
                     //$this->template->set_post($post);
                     $template = $this->template->print_template($post->ID, "dad9", $dadcats);

                    //save or overwrite template
                    require_once(CHRONOSLY_PATH.DIRECTORY_SEPARATOR."metaboxes".DIRECTORY_SEPARATOR."dad9".DIRECTORY_SEPARATOR.self::POST_TYPE."_dad9_save_metabox.php");
                    //print_r($GLOBALS);

                }

            }
        } // END public function add_inner_meta_boxes($post)

        public static function get_events_from_place($id){
            $metaquery = array(
                array(
                    'key' => 'places',
                    'value' => ':"'.$id.'";',
                    'compare' => 'LIKE'
                )
            );

            $args  = array(
                'posts_per_page'   => -1,
                'numberposts'       => -1,

                'offset'           => 0,
                'category'         => '',

                'include'          => '',
                'exclude'          => '',
                'meta_query' => $metaquery,
                'post_type'        => 'chronosly',
                'post_mime_type'   => '',
                'post_parent'      => '',
                'post_status'      => 'publish'
            );
            if ( is_user_logged_in() ) $args["post_status"] = array('publish', 'private');

            if(!has_action( 'posts_orderby', array("Post_Type_Chronosly",'add_custom_orderby') )) add_action( 'posts_orderby', array("Post_Type_Chronosly",'add_custom_orderby') );

            $ret =  new WP_Query( $args );
            if(has_action( 'posts_orderby', array("Post_Type_Chronosly",'add_custom_orderby') )) remove_action( 'posts_orderby', array("Post_Type_Chronosly",'add_custom_orderby') );
            return $ret;
        }



        function geocode_place($string){
            $string = str_replace (" ", "+", urlencode($string));
            $details_url = "http://maps.googleapis.com/maps/api/geocode/json?address=".$string."&sensor=false";
            //echo $details_url;
            $res = wp_remote_get($details_url);
            if(!is_wp_error($res)) $response = $res["body"];
            else return null;
            // If Status Code is ZERO_RESULTS, OVER_QUERY_LIMIT, REQUEST_DENIED or INVALID_REQUEST
            if (!isset($response) or !is_array($response) or @$response['status'] != 'OK') {
                return null;
            }

           // print_r($response);
            $geometry = $response['results'][0]['geometry'];

            $latitude = $geometry['location']['lat'];
            $longitude = $geometry['location']['lng'];

            return "($latitude,$longitude)";



        }


        function set_latlongs(){
            $args = array(
                'post_type' => "chronosly_places",
                'meta_query' => array(array("key" => "latlong", "value" => "", 'compare' => '=')),
                "satus" => "published"
            );
            $res = new WP_Query($args);
            /*echo "<pre>";
            print_r($res);
            echo "</pre>";*/
            foreach($res as $r){
                //echo $r->ID;
                $vars = @get_post_meta($r->ID);

                $dir = $vars["evp_dir"][0]." "
                 .$vars["evp_city"][0]." "
                 .$vars["evp_pc"][0]." "
                 .$vars["evp_state"][0]." "
                 .$vars["evp_country"][0];
                if(str_replace(" ", "",$dir)) {
                    $geo = "";
                    $geo =$this->geocode_place($dir);
                    //echo " $geo ";
                    update_post_meta($r->ID, "latlong", $geo);
                }
            }
        }


    } // END class Post_Type_Template
} // END if(!class_exists('Post_Type_Template'))
