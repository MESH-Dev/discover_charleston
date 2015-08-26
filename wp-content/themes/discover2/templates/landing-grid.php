<?php /*
* Template Name: Landing - Grid
*/
get_header(); ?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<main id="main" class="grid site-main" role="main">

  <div class="">
    <div class="row">
      <div class="intro">
        <h1 class="title">
            <?php the_title(); // Display the page title ?>
        </h1>
        <div class="intro-statement">
          <h2><?php the_field('intro_statement'); ?></h2> 
        </div>
      </div>
      <?php  if (have_rows('grid_blocks') ):
        while (have_rows('grid_blocks')) : the_row();

        //variables
        $image = get_sub_field('image');
        $imageURL = $image['sizes']['large'];
        $label = get_sub_field('block_text');
        $link = get_sub_field('link');
        //var_dump($image);
        //var_dump($imageURL);
        //var_dump($link);
        ?>
        <a href="<?php echo $link ?>">
        <div class="grid-block" style="background-image:url('<?php echo $image['url'] ?>')">
            <!-- <img src="<?php echo $image['url'] ?>" /> -->
            <div class="grid-overlay"></div>
              <div class="grid-block-inner">
                <div class="grid-block-content">
                  <div class="grid-label">
                    <h3>
                        <?php echo $label ?>
                    </h3>
                  </div>
                </div>
              </div>
        </div></a>

      <?php endwhile; endif; ?>

    </div>
  </div>

</main>
<?php endwhile; endif; ?>

<!-- #main -->

<?php get_footer(); ?>
