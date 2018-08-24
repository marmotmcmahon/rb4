<?php
/**
 *
 * Template Name: Full Width
 *
 * The template for displaying pages without a sidebar
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Read by 4th
 */

get_header(); ?>

	<div class="wrap">
		<div class="content-area">
			<main id="main" class="site-main" role="main">

				<?php
				if ( have_posts() ) : ?>

					<header <?php post_class( 'entry-header' ); ?>>
						<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
					</header><!-- .entry-header -->

				<?php
				// Start standard loop.
				while ( have_posts() ) : the_post();

							get_template_part( 'template-parts/content', 'page' );

				endwhile;

				else :

					get_template_part( 'template-parts/content', 'none' );

				endif; 
				
				if ( is_page( 'impact' ) ) {
					// Get Infographic.
					echo me_rb4_get_impact_infographic(); // WPCS: XSS ok.
				}
				
				?>

			</main><!-- #main -->
		</div><!-- .primary -->

	</div><!-- .wrap -->

<?php get_footer(); ?>
