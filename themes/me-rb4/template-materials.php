<?php
/**
 * Template Name: Fun Stuff
 *
 * This is the template that displays all materials by default.
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
		<div class="portal content-area">
			<main id="main" class="site-main" role="main">

				<?php
				// Default loop.
				if ( have_posts() ) : ?>

						<header <?php post_class( 'entry-header' ); ?>>
							<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
						</header><!-- .entry-header -->

					<?php // Start standard loop.
					while ( have_posts() ) : the_post();

						get_template_part( 'template-parts/content', 'page' );

					endwhile;
					wp_reset_postdata();

				else :

					get_template_part( 'template-parts/content', 'none' );

				endif;

				// Let's start the template loop.
				$args = array(
					'posts_per_page' => '100',
					'post_type'      => 'material',
				);

				$new_query = new WP_Query( $args );

				// Start Custom loop.
				if ( $new_query->have_posts() ) : ?>

					<ul class="material">

					<?php // Start standard loop.
					while ( $new_query->have_posts() ) : $new_query->the_post();

						// ACF Fields
						$price = get_field( 'price' );
						$limitations = get_field( 'limitations' );
						$additional = get_field( 'additional_notes' );
						$type = get_field( 'product_type' );
						$apply = get_field( 'apply' );
						$download = get_field( 'download' );
						$purchase = get_field( 'purchase' );
						$request = get_field( 'request' );
						$term_array = get_the_terms( get_the_ID(), 'material-type' )[0];
						$term = $term_array->slug; ?>

						<li>

							<div>
								<?php if ( 'request' === $term || 'apply' === $term ) : ?>
									<a href="<?php echo get_permalink(); ?>" >
								<?php elseif ( 'download' === $term ) : ?>
									<a href="<?php the_permalink(); ?>" >
								<?php elseif ( 'purchase' === $term ) : ?>
									<a href="<?php echo esc_url( $purchase ); ?>" >
								<?php else : ?>
									<a href="<?php the_permalink(); ?>" >
								<?php endif; ?>
								<?php if ( has_post_thumbnail() ) { ?>
									<?php echo me_rb4_get_post_image( 'material-thumb' ); // WPCS: XSS ok. ?>
								<?php } else { ?>
										<img src="<?php echo get_stylesheet_directory_uri() . '/assets/images/sprites/material-placeholder.png'; // WPCS: XSS ok. ?>" alt="This Material Has no Image" />
									<?php } ?>
								</a>
							</div>

							<h3>
							<?php if ( 'request' === $term || 'apply' === $term ) : ?>
								<a href="<?php echo get_permalink(); ?>" >
							<?php elseif ( 'download' === $term ) : ?>
								<a href="<?php the_permalink(); ?>" >
							<?php elseif ( 'purchase' === $term ) : ?>
								<a href="<?php echo esc_url( $purchase ); ?>" >
							<?php endif; ?>
									<?php the_title(); ?>
								</a>
							</h3>

							<?php if ( ! $price ) : ?>
								<div><?php echo esc_html( 'Free' ); ?></div>
							<?php else : ?>
								<div><?php echo $price; ?></div>
							<?php endif; ?>

							<?php if ( 'request' === $term || 'apply' === $term ) : ?>
								<a href="<?php echo me_rb4_get_link_by_slug( 'contact-us' ) . '?&rb4-material-type=' . get_the_title(); ?>" class="button button-teal" >
									<?php echo esc_html( $type['label'] ); ?>
								</a>
							<?php elseif ( 'download' === $term ) : ?>
								<a href="<?php the_permalink(); ?>" class="button button-violet"><?php echo esc_html( $type['label'] ); ?></a>
							<?php elseif ( 'purchase' === $term ) : ?>
								<a href="<?php echo esc_url( $purchase ); ?>" class="button"><?php echo esc_html( $type['label'] ); ?></a>
							<?php endif; ?>

						</li>

					<?php endwhile;
					wp_reset_postdata(); ?>

					</ul>

				<?php else :

					get_template_part( 'template-parts/content', 'none' );

				endif;
				?>

			</main><!-- #main -->
		</div><!-- .primary -->
	</div><!-- .wrap -->

<?php get_footer(); ?>
