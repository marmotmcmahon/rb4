<?php
/**
 * Template Name: Portal Page
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
		<div class="portal primary content-area">
			<main id="main" class="site-main" role="main">

				<?php
				if ( have_posts() ) :

					if ( ! post_password_required() && function_exists( 'ft_password_protect_children_page_contents' ) ) : ?>

						<header <?php post_class( 'entry-header' ); ?>>
							<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
							<nav id="site-navigation" class="portal-navigation">
								<?php echo me_rb4_list_child_pages(); // WPCS: XSS ok. ?>
							</nav><!-- #site-navigation -->
						</header><!-- .entry-header -->

					<?php endif;

					// Start standard loop.
					while ( have_posts() ) : the_post();

						get_template_part( 'template-parts/content', 'page' );

					endwhile;

				else :

					get_template_part( 'template-parts/content', 'none' );

				endif; ?>

			</main><!-- #main -->
		</div><!-- .primary -->

		<?php if ( ! post_password_required() && function_exists( 'ft_password_protect_children_page_contents' ) ) :
			get_sidebar();
		endif; ?>

	</div><!-- .wrap -->

<?php get_footer(); ?>
