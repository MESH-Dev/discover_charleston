<?php
if(!isset($_REQUEST["shortcode"]) or !$_REQUEST["shortcode"])  {
    get_header();

    ?>
        <section id="primary" class="content-area">
            <div id="content" class="site-content" role="main">

                <?php
}

echo '<div class="chronosly-closure">';

$limit = (isset($_REQUEST["count"]) and $_REQUEST["count"])?$_REQUEST["count"]:$Post_Type_Chronosly->settings["chronosly_events_x_page"];
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                $Post_Type_Chronosly->template->templates_tabs("dad1", 1);
            $stilo = "margin:auto;padding:30px;";
            if($Post_Type_Chronosly->settings["chronosly_template_max"]) $stilo .= "max-width:".$Post_Type_Chronosly->settings["chronosly_template_max"]."px;";
            if($Post_Type_Chronosly->settings["chronosly_template_min"]) $stilo .= "min-width:".$Post_Type_Chronosly->settings["chronosly_template_min"]."px;";

       if(!isset($_REQUEST["shortcode"]) or !$_REQUEST["shortcode"] or (isset($_REQUEST["navigation"]) and $_REQUEST["navigation"])){
         ?>


            <div class="ch-header ch-<?php echo $Post_Type_Chronosly->settings["chronosly_titles_template_default"];?>" style="<?php echo $stilo;?>">
<?php   if($Post_Type_Chronosly->settings["chronosly-places-list-url"]){ ?>
            <a href="<?php  echo $Post_Type_Chronosly->settings["chronosly-places-list-url"];?>" class="back"><i class="fa fa-chevron-left"></i> <?php _e("Places list","chronosly") ?></a>
<?php } else { ?>
            <a href="<?php  echo (get_option('permalink_structure')?get_post_type_archive_link( 'chronosly_places' ):get_site_url()."/index.php?post_type=chronosly_places") ;?>" class="back"><i class="fa fa-chevron-left"></i> <?php _e("Places list","chronosly") ?></a>
<?php }  if($Post_Type_Chronosly->settings["chronosly-calendar-url"]){ ?>
         <a href="<?php  echo $Post_Type_Chronosly->settings["chronosly-calendar-url"]; ?>" class="icon-calendar"></a></div>

    <?php } else { ?>
            <a href="<?php  echo (get_option('permalink_structure')?Post_Type_Chronosly_Calendar::get_permalink()."year_".date("Y")."/month_".date("n")."/":get_site_url()."/?post_type=chronosly_calendar&y=".date("Y")."&mo=".date("n")); ?>" class="icon-calendar"></a></div>
<?php    }
          }

if(!$_REQUEST["shortcode"] or ($_REQUEST["shortcode"] and $_REQUEST["before_events"])) do_action("chronosly-before-events", $stilo);
echo "<div class='chronosly-content-block' style='".$stilo.";clear:both;'>";

                if (have_posts() ) {
                // Start the Loop.
                    the_post();
                    $xid = get_the_ID();
                    $feats = 0;


                    ob_start();
                    $Post_Type_Chronosly->template->print_template($xid, "dad10", "", "", "front");

                    $content = ob_get_clean();

                    if(stripos($content, "#event_list#")){
                        //get the events for this organizer
                        add_action( 'pre_get_posts', array("Post_Type_Chronosly",'add_custom_post_vars')  );
                        $events = Post_Type_Chronosly_Places::get_events_from_place($xid);
                        $res = "";
                        remove_action( 'pre_get_posts', array("Post_Type_Chronosly",'add_custom_post_vars')  );

                        if($settings["chronosly-show-repeats-place"]) {
                            $repeated = Post_Type_Chronosly::get_events_repeated_by_date($limit, $paged, array("meta_query"=> array(
                                    'key' => 'places',
                                    'value' => ':"'.$xid.'";',
                                    'compare' => 'LIKE'
                                )));
                        }
                        $elementos = Post_Type_Chronosly::get_days_by_date($events, $repeated,$limit, $paged);
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
                                    $Post_Type_Chronosly->template->print_template($el["id"], "dad6", "", "", "front", array("id" => $ide, "start" => $el["start"], "end" => $el["end"]));
                                    $events_list[$ide]= ob_get_clean();
                                }
                                else {
                                    $xid = $ide = $el;
                                    ob_start();
                                    $Post_Type_Chronosly->template->print_template($el, "dad6", "", "", "front");
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