<?php get_header(); ?>

<main id="content">

	
		<div class="row">
			<div class="">
				<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

					<h1><?php the_title(); ?></h1>
					<div class="default-page-content">
						<?php the_content(); ?>
					</div>

				<?php endwhile; ?>
			</div>

			

		</div>
	</div>

</main><!-- End of Content -->

<?php get_footer(); ?>