<?php
/**
 * The main template file.
 * @package WordPress
 * @subpackage Discover Charleston  V2.0
 */
get_header(); 
?>




<h1><?php the_title(); ?></h1>
<div class="is_this_my_page"><?php the_content(); ?></div>




<?php get_footer(); ?>