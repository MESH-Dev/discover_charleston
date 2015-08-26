<?php
if(!isset($_REQUEST["shortcode"]) or !$_REQUEST["shortcode"])  {

    get_header();
}


$settings = unserialize(get_option("chronosly-settings"));

$limit = (isset($_REQUEST["count"]) and $_REQUEST["count"])?$_REQUEST["count"]:$Post_Type_Chronosly->settings["chronosly_organizers_x_page"];
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
if($_REQUEST["page"]) $paged = $_REQUEST["page"];

if(!isset($_REQUEST["shortcode"]) or !$_REQUEST["shortcode"])  {


        $args  = array(
            'posts_per_page'   => $limit,
            'numberposts'       => $limit,

            'paged'           => $paged,
            'category'         => '',
            'include'          => '',
            'exclude'          => '',
            'meta_key'         => '',
            'meta_value'       => '',
            'post_type'        => 'chronosly_organizer',
            'post_mime_type'   => '',
            'post_parent'      => '',
            'post_status'      => 'publish'
             );
    if ( is_user_logged_in() ) $args["post_status"] = array('publish', 'private');

    if(!has_action( 'posts_orderby', array("Post_Type_Chronosly_Organizer",'add_custom_organizers_orderby') )) add_action( 'posts_orderby', array("Post_Type_Chronosly_Organizer",'add_custom_organizers_orderby') );

        $query = new WP_Query( $args );
    if(has_action( 'posts_orderby', array("Post_Type_Chronosly_Organizer",'add_custom_organizers_orderby') )) remove_action( 'posts_orderby', array("Post_Type_Chronosly_Organizer",'add_custom_organizers_orderby') );
    ?>
    <section id="primary" class="content-area">
        <div id="content" class="site-content" role="main">

                <?php
} else $query = $wp_query;

echo '<div class="chronosly-closure">';

                $Post_Type_Chronosly->template->templates_tabs("dad1", 1);
                $stilo = "margin:auto;padding:30px;";
                if($Post_Type_Chronosly->settings["chronosly_template_max"]) $stilo .= "max-width:".$Post_Type_Chronosly->settings["chronosly_template_max"]."px;";
                if($Post_Type_Chronosly->settings["chronosly_template_min"]) $stilo .= "min-width:".$Post_Type_Chronosly->settings["chronosly_template_min"]."px;";
if(!isset($_REQUEST["shortcode"]) or !$_REQUEST["shortcode"] or (isset($_REQUEST["navigation"]) and $_REQUEST["navigation"])){

                ?>


                <div class="ch-header ch-<?php echo $Post_Type_Chronosly->settings["chronosly_titles_template_default"];?>" style="<?php echo $stilo;?>"><span class="title"><?php _e("Organizers", "chronosly"); ?></span>
    <?php   if($Post_Type_Chronosly->settings["chronosly-calendar-url"]){ ?>
         <a href="<?php  echo $Post_Type_Chronosly->settings["chronosly-calendar-url"]; ?>" class="icon-calendar"></a></div>

    <?php } else { ?>
            <a href="<?php  echo (get_option('permalink_structure')?Post_Type_Chronosly_Calendar::get_permalink()."year_".date("Y")."/month_".date("n")."/":get_site_url()."/?post_type=chronosly_calendar&y=".date("Y")."&mo=".date("n")); ?>" class="icon-calendar"></a></div>
<?php    }

}
if(!$_REQUEST["shortcode"] or ($_REQUEST["shortcode"] and $_REQUEST["before_events"])) do_action("chronosly-before-events", $stilo);
echo "<div class='chronosly-content-block' style='".$stilo.";clear:both;'>";

            if ( $query->have_posts() ){
					// Start the Loop.
					while ( $query->have_posts() ) {
                        $query->the_post();
                        $xid = get_the_ID();
                        $feats = 0;

                        ob_start();
                        $Post_Type_Chronosly->template->print_template($xid, "dad7", "", "", "front");

                        $content = ob_get_clean();

                        if(stripos($content, "#event_list#")){
                            //get the events for this organizer
                            add_action( 'pre_get_posts', array("Post_Type_Chronosly",'add_custom_post_vars')  );

                            $events = Post_Type_Chronosly_Organizer::get_events_from_organizer($xid);
                            remove_action( 'pre_get_posts', array("Post_Type_Chronosly",'add_custom_post_vars')  );

                            if($settings["chronosly-show-repeats-organizer"]) {
                                $repeated = Post_Type_Chronosly::get_events_repeated_by_date($limit, $paged, array("meta_query"=> array(
                                        'key' => 'organizer',
                                        'value' => ':"'.$xid.'";',
                                        'compare' => 'LIKE'
                                    )));
                            }
                            $elementos = Post_Type_Chronosly::get_days_by_date($events, $repeated, $limit, $paged);
                            $elements = $elementos[0];
                            $res = "";

                            $repeats = array();
                            if(count($elements)){
                                foreach($elements as $el){
                                    $xid = $ide = 0;
                                    if(is_array($el)){
                                        $xid = $ide =  $el["id"];
                                        if(isset($repeats[$xid])) $ide .= "_".$repeats[$xid];
                                        ob_start();
                                        $Post_Type_Chronosly->template->print_template($el["id"], "dad5", "", "", "front", array("id" => $ide, "start" => $el["start"], "end" => $el["end"]));
                                        $events_list[$ide]= ob_get_clean();
                                    }
                                    else {
                                        $xid = $ide = $el;
                                        ob_start();
                                        $Post_Type_Chronosly->template->print_template($el, "dad5", "", "", "front");
                                        $events_list[$ide]= ob_get_clean();
                                    }
                                    if(isset($repeats[$xid])) ++$repeats[$xid];
                                    else $repeats[$xid] = 1;




                                    $is_feat = stripos($events_list[$ide], " ch-featured ");
                                    if($feats and !$is_feat){
                                        $res .= "<span class='feat-sep'></span>";
                                        $feats = 0;
                                    }
                                    else if($is_feat) $feats = 1;
                                    $res .= $events_list[$ide];
                                }
                            }
                            echo str_replace("#event_list#", $res, $content);
                        } else   echo $content;
                    }
                  if(!isset($_REQUEST["shortcode"]) or $_REQUEST["pagination"]) {
                    if(!isset($_REQUEST["shortcode"])) {

                        if(!$_REQUEST["ch_code"]) {
                            $_REQUEST["ch_code"] = array("type" => "organizer", "pagination" => 1, "count" => $limit);
                           

                            $_REQUEST["ch_code"] = json_encode($_REQUEST["ch_code"]);
                        }
                      }
                      echo "<div class='pagination'  style='$stilo'>";
                     if( $paged > 1) echo '<a onclick="javascript:ch_prev_page('.$limit.','.$paged.', \''. urlencode($_REQUEST["ch_code"]) .'\', this)"><< '.__("Previous page").'</a> &nbsp;&nbsp;&nbsp;';
                     
                      if($paged < $query->max_num_pages) echo '<a onclick="javascript:ch_next_page('.$limit.','.$paged.', \''. urlencode($_REQUEST["ch_code"]) .'\', this)">'.__("Next page").' >></a>';
                      echo "</div>";
                  }
            } else {
                echo '<div class="ch-error" style="'.$stilo.'">';
                _e("No events found");
                echo '</div>';
            }
if(!$_REQUEST["shortcode"] or ($_REQUEST["shortcode"] and $_REQUEST["after_events"])) do_action("chronosly-after-events");
echo "</div>"; //close chronosly block
echo "</div>"; //close chronosly closure

if(!isset($_REQUEST["shortcode"]) or !$_REQUEST["shortcode"])  {

                ?>
        </div><!-- #content -->
    </section><!-- #primary -->

    <?php
    wp_reset_postdata();


    get_footer();

}