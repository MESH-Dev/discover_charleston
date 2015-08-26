<?php /*
* Template Name: Landing - 2 column
*/
get_header(); ?>




<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<main id="main" class="package site-main" role="main">

  <div class="">
    <div class="row">
      <div class="intro">
        <h1 class="title">
            <?php the_title(); // Display the page title ?>
        </h1>
        
      </div>
      <!--column 1-->
      <div class="left column">
        <div class="intro-statement">
          <h2><?php the_field('intro_statement'); ?></h2> 
        </div>
      <?php  if (have_rows('packages') ):
        $ctr == 0;
        while (have_rows('packages')) : the_row();
        $ctr++;
        //variables
        $map = get_sub_field('map_location');
        $description = get_sub_field('description');
        $title = get_sub_field('title');
        
        // $link = get_sub_field('link');
        //var_dump($image);
        //var_dump($imageURL);
        //var_dump($link);
        ?>
        <?php if($ctr%2 == 1){ echo '<div class="row">';} ?>
 
           <div class="grid-block <?php echo $ctr;?>">
            <div class="map-location">
              <?php echo $map;?>
            </div>
            <div class="package-content">
              
              <h2><?php echo $title; ?></h2>
              <p><?php echo $description; ?></p>
            </div>
          </div><!--End Grid block-->
        
        <?php if($ctr%2 == 0){ echo "</div>";} ?>
 
      <?php endwhile; ?>
      <?php endif; ?>

      <?php  if ($ctr%1==0) : ?>
      </div>

      <?php endif;?> 
      <div class="map">
        <?php echo get_field('google_map') ?>
      </div>
    </div>
      <!--column two-->
      <div id="suggestions" class="right column">
        <?php echo the_field('package_suggestions'); ?>
      </div>
      <!--end column 2-->
    </div>
  </div>

</main>
<?php endwhile; endif; ?>

<!-- #main -->

<?php get_footer(); ?>
