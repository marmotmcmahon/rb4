<?php
/**
 *
 * Template Name: Impact Map
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

				<header <?php post_class( 'entry-header' ); ?>>
					<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
				</header><!-- .entry-header -->

				<?php the_content(); ?>

			<?php // Start New Query.
			$args = array(
				'post_type'      => 'impact-locations',
				'posts_per_page' => '-1',
			);

			// Creates new loop for impact locations.
			$query = new WP_Query( $args );

			if ( $query->have_posts() ) :

				// get only location-type taxonomy.
				$cat_args = array(
					'taxonomy' => 'location-type',
					'show_hidden' => 'true',
				);

				// let's get those categories.
				$categories = get_categories( $cat_args ); ?>

				<?php // Print Map Key ?>
				<div class="map-key">
					<ul>
						<?php // loop through each categories and define an svg.
						foreach ( $categories as $category ) { ?>
							<li>
								<img src="<?php echo get_stylesheet_directory_uri() . '/assets/images/svg-icons/map-marker-' . esc_html( $category->slug ) . '.svg'; ?>" /><?php echo esc_html( $category->name ); ?>
							</li>
						<?php } ?>
					</ul>
				</div><!-- .map-key -->

				<?php // Start map location. ?>
				<div class="acf-map">

					<?php // Start standard loop.
					while ( $query->have_posts() ) : $query->the_post();

						$location = get_field( 'address' );

						if ( ! empty( $location ) ) : ?>

							<?php // let's get all those categories fomr the post only for the data-icon.
							$taxonomy = get_the_terms( $post->ID, 'location-type' );

							// assign a default if undefied category attached.
							if ( ! $taxonomy ) {
								$tax_slug = 'default';
							} else {
								$tax_slug = $taxonomy[0]->slug;
							} ?>

							<div class="marker <?php echo esc_html( $tax_slug ); ?>" data-lat="<?php echo esc_html( $location['lat'] ); ?>" data-lng="<?php echo esc_html( $location['lng'] ); ?>" data-icon="<?php echo get_stylesheet_directory_uri() . '/assets/images/svg-icons/map-marker-' . esc_html( $tax_slug ) . '.svg'; ?>">

								<?php // Start Info Window Info.
								if ( has_post_thumbnail() ) :
									the_post_thumbnail( 'map-image' );
								endif; ?>
								<div class="info">
									<h4><?php the_title(); ?></h4>
									<address>
										<span class="glyphicon glyphicon-map-marker"></span>
										<?php 
										$address = str_replace( ', United States', '', $location['address'] );
										echo esc_html( $address ); ?>
									</address>
									<div class="phone">
										<?php // phone number.
										$phone = get_field( 'phone_number' );
										if ( $phone ) :
											echo '<a href="tel:' . esc_html( $phone ) . '">' . esc_html( $phone ) . '</a>';
										endif; ?>
									</div><!-- .phone -->
									<div class="email">
										<?php // phone number.
										$email = get_field( 'email' );
										if ( $email ) :
											echo '<a href="mailto:' . esc_html( $email ) . '">' . esc_html( $email ) . '</a>';
										endif; ?>
									</div><!-- .email -->
									<div class="website">
										<?php // phone number.
										$website = get_field( 'website' );
										if ( $website ) :
											echo '<a href="' . esc_html( $website ) . '">' . esc_html( 'Website' ) . '</a>';
										endif; ?>
									</div><!-- .email -->
								</div><!-- .info -->
							</div><!-- .marker -->
						<?php endif;

					endwhile;
					wp_reset_postdata(); ?>

				</div><!-- .acf-map -->

			<?php endif; ?>

				<?php if ( get_field( 'lower_content' ) ) :
					echo get_field( 'lower_content' );
				endif; ?>

			</main><!-- #main -->
		</div><!-- .primary -->

	</div><!-- .wrap -->

<?php get_footer(); ?>
