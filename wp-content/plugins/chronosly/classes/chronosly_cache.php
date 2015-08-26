<?php
/**
 * cache
 */
if(!class_exists('Chronosly_Cache')){


    class Chronosly_Cache{

        public function save_item($id, $vista, $html){
            if (!Chronosly_Utils::validate_closure($html))  { 
                return;
            }
            if($_REQUEST["small"]) $vista .= "_small";
            if(function_exists("qtrans_getLanguage")) $vista .= qtrans_getLanguage();

            update_option("chronosly_template_{$id}_{$vista}", $html);
        }

        public function load_item($id, $vista){
            $settings = unserialize(get_option("chronosly-settings"));
            if($settings["disable_cache"]) return false;
            if($_REQUEST["small"]) $vista .= "_small";
            if(function_exists("qtrans_getLanguage")) $vista .= qtrans_getLanguage();
            $html = get_option("chronosly_template_{$id}_{$vista}");
            if (!Chronosly_Utils::validate_closure($html))  { 
                return false;
            }

            return $html;
        }

        public function delete_item($id){
            global $wpdb;
            // find list of states in DB
            $qry = "DELETE FROM $wpdb->options where option_name like 'chronosly_template_{$id}_%'";
            $wpdb->get_results( $qry );
        }

        public function clear_cache(){
            global $wpdb;
            // find list of states in DB
            $qry = "DELETE FROM $wpdb->options where option_name like 'chronosly_template_%'";
            $wpdb->get_results( $qry );
        }

       
    }


}
