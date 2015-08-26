<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php the_title(); ?></title>
<link href="<?php echo get_template_directory_uri(); ?>/fonts/fontawesome.min.css" rel="stylesheet" type="text/css">
<link href="<?php echo get_template_directory_uri(); ?>/fonts/stylesheet.css" rel="stylesheet" type="text/css">
<link href="<?php echo get_template_directory_uri(); ?>/style.css" rel="stylesheet" type="text/css">
<?php wp_head(); ?>
</head>

<?php 

$max = count( get_field('images') );
$rand = rand (0, $max-1 );
$ctr = 0;

 if ( is_front_page() ) {
 	if( have_rows('images') ):
 		
	    while ( have_rows('images') ) : the_row();
	        $image = get_sub_field('image');
	        if($ctr == $rand){
	        	$bg_img = $image['url'];	
	        }
	        $ctr++;
	    endwhile;
	endif;  

 }
else{
 
	$img = get_field('background_image'); 
	$bg_img = $img['url'];	
}




 

?>
<body class="bg-home <?php if ( is_front_page() ) {echo'homepage'; } ?>" <?php if($bg_img){?>style="background: url('<?php echo $bg_img; ?>') no-repeat center center fixed; background-size: cover;" <?php }?>>

<header class="header-block">

	<div class="">

		<div class="first-block"><!-- MY WEST VIRGINIA --></div> 

		<ul class="social">
		<li><?php echo do_shortcode('[wpc-weather id="22"]'); ?></li>
		<li><a href="https://www.facebook.com/pages/Discover-Charleston-WV/884303084981830?fref=ts"><i class="fa fa-facebook"></i></a></li>
		<li><a href="https://twitter.com/discovercwv"><i class="fa fa-twitter"></i></a></li>
		<li><a href="https://instagram.com/discovercwv"><i class="fa fa-instagram"></i></a></li>
		</ul>
	</div>

	<div class="">
		<h1>
			<a href="<?php echo get_site_url(); ?>" class="font-color1 main-logo">DISCOVER CHARLESTON</a>
		</h1>
	</div>

	<div class="clearfix"></div>

	<div class="header-sec3">
	<?php

	$defaults = array(
		'theme_location'  => '',
		'menu'            => 'Main Menu',
		'container'       => 'main-navigation',
		'container_class' => '',
		'container_id'    => '',
		'menu_class'      => '',
		'menu_id'         => '',
		'echo'            => true,
		'fallback_cb'     => 'wp_page_menu',
		'before'          => '',
		'after'           => '',
		'link_before'     => '',
		'link_after'      => '',
		'items_wrap'      => '<ul>%3$s</ul>',
		'depth'           => 0,
		'walker'          => ''
	);

	wp_nav_menu( $defaults );

	?>
	</div>


</header>



<div class="container content-block bgcol5 pad-full mar-bot-20">
