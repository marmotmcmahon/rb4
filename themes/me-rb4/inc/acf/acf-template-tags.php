<?php
/**
 * ACF Custom Fields PHP Output
 *
 * @link https://codex.wordpress.org/Function_Reference/register_post_type
 *
 * @package Read by 4th
 */

/**
 * Get Flexible Content Loop
 *
 * @return string
 */
function me_rb4_get_flexible_content() {

	ob_start();

	// check if the flexible content field has rows of data.
	if ( have_rows( 'flexible_content' ) ) :

		// loop through the rows of data.
		while ( have_rows( 'flexible_content' ) ) : the_row();

			if ( 'content' === get_row_layout() ) : ?>

				<section class="flexible-content">

					<?php // acf vars.
					$num_col = get_sub_field( 'number_of_columns' );
					$num_value = $num_col['value'];

					// acf column vars.
					$full = get_sub_field( 'content_full' );
					$col1of2 = get_sub_field( 'content_1_of_2' );
					$col2of2 = get_sub_field( 'content_2_of_2' );
					$col1of3 = get_sub_field( 'content_1_of_3' );
					$col2of3 = get_sub_field( 'content_2_of_3' );
					$col3of3 = get_sub_field( 'content_3_of_3' );

					if ( 'one' === $num_value ) :

						echo $full; // WPCS: XSS ok.

					elseif ( 'two' === $num_value ) : ?>

						<div class="half">
							<?php echo $col1of2; // WPCS: XSS ok. ?>
						</div><!-- .half -->
						<div class="half">
							<?php echo $col2of2; // WPCS: XSS ok. ?>
						</div><!-- .half -->

					<?php elseif ( 'three' === $num_value ) : ?>

						<div class="third">
							<?php echo $col1of3; // WPCS: XSS ok. ?>
						</div><!-- .third -->
						<div class="third">
							<?php echo $col2of3; // WPCS: XSS ok. ?>
						</div><!-- .third -->
						<div class="third">
							<?php echo $col3of3; // WPCS: XSS ok. ?>
						</div><!-- .third -->

					<?php endif; ?>

				</section><!-- .flexible-content -->

			<?php elseif ( 'flip_cards' === get_row_layout() ) : ?>

				<section class="flexible-flip-cards">

					<ul class="card-container">

						<?php // Flip Cards.
						$card_numbers = array( '1', '2', '3' );

						foreach ( $card_numbers as $card_number ) {

							$card_cloned = array( 'card_title', 'card_subtitle', 'card_back', 'card_image', 'card_link' );

							$card_title = get_sub_field( 'card_' . $card_number . '_' . $card_cloned[0] );
							$card_subtitle = get_sub_field( 'card_' . $card_number . '_' . $card_cloned[1] );
							$card_back = get_sub_field( 'card_' . $card_number . '_' . $card_cloned[2] );
							// get image array.
							$card_flip_image = get_sub_field( 'card_' . $card_number . '_' . $card_cloned[3] );
							$card_link = ( get_sub_field( 'card_' . $card_number . '_' . $card_cloned[4] ) ) ? get_sub_field( 'card_' . $card_number . '_' . $card_cloned[4] ) : '';

							// Let's make sure we have an array.
							if ( is_array( $card_flip_image ) ) :
								$card_flip_image_size = $card_flip_image['sizes'];
								$card_flip_image_url = $card_flip_image_size['material-thumb'];
							endif; ?>

							<li class="flip-container">
								<div class="front image-as-background <?php echo 'front-' . absint( $card_number ); ?>" style="background-image: url(<?php if ( is_array( $card_flip_image ) ) { echo esc_url( $card_flip_image_url ); } ?>);">
									<div class="content">
										<span class="top-title">
											<?php echo esc_html( $card_title ); ?>
										</span><!-- .title -->
										<span class="sub-title">
											<?php echo esc_html( $card_subtitle ); ?>
										</span><!-- .sub-title -->
									</div><!-- .content -->
								</div><!-- .front -->
								<div class="back <?php echo 'back-' . absint( $card_number ); ?>">
									<a href="<?php echo esc_url( $card_link ); ?>" class="back-link"></a>
									<div class="content">
										<div class="back-content">
											<span class="top-title">
												<?php echo esc_html( $card_title ); ?>
											</span><!-- .title -->
											<?php echo wp_kses_post( $card_back ); ?>
										</div><!-- .back-content -->
									</div><!-- .content -->
								</div><!-- .back -->
							</li><!-- .flip-container -->
						<?php } // endforeach. ?>
					</ul>
				</section><!-- .flip-cards -->

			<?php elseif ( 'full_width_image' === get_row_layout() ) :

				$image_array = get_sub_field( 'image' ); ?>

				<?php if ( ! empty( $image_array ) ) :

					$image_size = $image_array['sizes'];
					$image_url = $image_size['full-width']; ?>

					<section class="flexible-image image-as-background full-width" style="background-image: url(<?php echo esc_url( $image_url ); ?>);"></section><!-- full-width -->

				<?php endif;

			elseif ( 'column_width_image' === get_row_layout() ) :

				$image_array = get_sub_field( 'image' );
				$image_link = ( get_sub_field( 'image_url' ) ) ? get_sub_field( 'image_url' ) : '';

				if ( ! empty( $image_array ) ) :

					$image_size = $image_array['sizes'];
					$image_url = $image_size['full-width']; ?>

					<section class="flexible-call-to-action column-width">
						<a href="<?php echo esc_url( $image_link ); ?>">
							<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_html( $image_array['title'] ); ?>" />
						</a>
					</section><!-- full-width -->

				<?php endif;

			elseif ( 'faqs' === get_row_layout() ) :

				// title vars.
				$faq_title = get_sub_field( 'section_title' );

				// q & a repeater var.
				$repeater = 'q_and_a';

				if ( have_rows( $repeater ) ) : ?>

					<section class="flexible-faqs">

						<h2><?php echo esc_html( $faq_title ); ?></h2>

						<?php // loop through questions and answers.
						while ( have_rows( $repeater ) ) : the_row();

							$question = get_sub_field( 'question' );
							$answer = get_sub_field( 'answer' ); ?>

							<div class="question-and-answer">
								<div class="question">
									<?php echo wp_kses_post( $question ); ?>
								</div><!-- .question -->
								<div class="answer">
									<?php echo wp_kses_post( $answer ); ?>
								</div><!-- .answer -->
							</div><!-- .question and answer -->

						<?php endwhile; ?>

					</section><!-- .flexible-faqs -->
				<?php endif;

			elseif ( 'call_to_action' === get_row_layout() ) :

				// cta vars.
				$bg_color = get_sub_field( 'background_color' );
				$left = get_sub_field( 'left_column' );
				$right = get_sub_field( 'right_column' ); ?>

				<section class="flexible-call-to-action" style="background-color: <?php echo esc_html( $bg_color ); ?>">

					<div class="left">
						<?php echo wp_kses_post( $left ); ?>
					</div><!-- .left -->

					<div class="right">
						<?php echo wp_kses_post( $right ); ?>
					</div><!-- .right -->

				</section><!-- .flexible-call-to-action -->

			<?php endif;
		endwhile;
	endif;

	return ob_get_clean();
}

/**
 * Returns the flip cards.
 */
function me_rb4_get_flip_cards_homepage() {

	ob_start(); ?>

	<section class="flip-cards age-groups">
		<?php if ( get_field( 'section_title' ) ) : ?>
			<h2 class="title"><?php the_field( 'section_title' ); ?></h2>
		<?php endif; ?>

		<ul class="card-container">

			<?php // Flip Cards.
			$numbers = array( '1', '2', '3', '4' );

			foreach ( $numbers as $number ) {

				$cloned = array( 'card_title', 'card_subtitle', 'card_back', 'card_image', 'card_link' );

				$title = get_field( 'card_'. $number . '_' . $cloned[0] );
				$subtitle =  get_field( 'card_'. $number . '_' . $cloned[1] );
				$back =  get_field( 'card_'. $number . '_' . $cloned[2] );

				// get image array.
				$flip_image = get_field( 'card_'. $number . '_' . $cloned[3] );
				$link = ( get_field( 'card_'. $number . '_' . $cloned[4] ) ) ? get_field( 'card_'. $number . '_' . $cloned[4] ) : '';

				// Let's make sure we have an array.
				if ( is_array( $flip_image ) ) :
					$flip_image_size = $flip_image['sizes'];
					$flip_image_url = $flip_image_size['material-thumb'];
				endif; ?>

				<li class="flip-container">
					<div class="front image-as-background <?php echo 'front-' . absint( $number ); ?>" style="background-image: url(<?php if ( is_array( $flip_image ) ) { echo esc_url( $flip_image_url ); } ?>);">
						<div class="content">
							<span class="top-title">
								<?php echo esc_html( $title ); ?>
							</span><!-- .title -->
							<span class="sub-title">
								<?php echo esc_html( $subtitle ); ?>
							</span><!-- .sub-title -->
						</div><!-- .content -->
					</div><!-- .front -->
					<div class="back <?php echo 'back-' . absint( $number ); ?>">
						<a href="<?php echo esc_url( $link ); ?>" class="back-link"></a>
						<div class="content">
							<div class="back-content">
								<span class="top-title">
									<?php echo esc_html( $title ); ?>
								</span><!-- .title -->
								<?php echo wp_kses_post( $back ); ?>
							</div><!-- .back-content -->
						</div><!-- .content -->
					</div><!-- .back -->
				</li><!-- .flip-container -->
			<?php } // endforeach. ?>
		</ul>
	</section><!-- .flip-cards -->

	<?php return ob_get_clean();
}

/**
 * Returns Image Map
 */
function me_rb4_get_learning_map() {

	// Page Vars from ACF.
	$image_map = get_field( 'image_map' );
	$image_size = $image_map['sizes'];
	$image_url = $image_size['full-width'];
	$image_title = $image_map['title'];
	$image_map_content = get_field( 'image_map_content' );

	ob_start(); ?>

	<section class="learning-map full-width">
		<div class="image-map-content">
			<div class="wrap">
				<?php echo wp_kses_post( $image_map_content ); ?>
			</div><!-- .wrap -->
		</div><!-- .image-map-content -->
		<img class="image-map" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_html( $image_title ); ?>" />
	</section><!-- .learning-map -->

	<?php return ob_get_clean();
}

/**
 * Return infographic markup.
 */
function me_rb4_get_infographic() {

	// Stats Block.
	$desktop_image = get_field( 'infographic_desktop' );
	$desktop_size = $desktop_image['sizes'];
	$desktop_url = $desktop_size['full-width'];
	$mobile_image = get_field( 'infographic_mobile' );
	$mobile_size = $mobile_image['sizes'];
	$mobile_url = $mobile_size['full-width'];

	$stats_title = $desktop_image['title'];

	$link_url = get_field('link_url');

	ob_start(); ?>

	<a href="<?php echo( $link_url ); ?>"
	<section class="stats-block wrap">
		<img class="desktop" src="<?php echo esc_url( $desktop_url ); ?>" />
		<img class="mobile" src="<?php echo esc_url( $mobile_url ); ?>"  />
		<br>
	</section>
	</a>
	<!-- .stats-block -->

	<?php return ob_get_clean();
}

/**
 * Return impact infographic.
 */
function me_rb4_get_impact_infographic() {

	// Stats Block.
	$desktop_image = get_field( 'infographic_desktop' );
	$desktop_size = $desktop_image['sizes'];
	$desktop_url = $desktop_size['full-width'];
	$mobile_image = get_field( 'infographic_mobile' );
	$mobile_size = $mobile_image['sizes'];
	$mobile_url = $mobile_size['full-width'];

	$stats_title = $desktop_image['title'];


	ob_start(); ?>

	<section class="stats-block wrap">
		<img class="desktop" src="<?php echo esc_url( $desktop_url ); ?>" />
		<img class="mobile" src="<?php echo esc_url( $mobile_url ); ?>"  />
		<br>
	</section>
	<!-- .stats-block -->

	<?php return ob_get_clean();
}

/**
 * Return resources block.
 */
function me_rb4_get_resources_block() {

	ob_start(); ?>

	<section class="resources-block">
		<div class="wrap resources">

		<?php // Flip Cards.
			$locations = array( 'top_left', 'bottom_left' );
			$count = 0; ?>

			<div class="col-1" >

				<?php foreach ( $locations as $location ) {

					// Let's count.
					$count++;

					// Get cloned fields.
					$sections = array( 'background_color', 'background_image', 'card_content' );

					$color = get_field( $location . '_' . $sections[0] );
					$image_array = get_field( $location . '_' . $sections[1] );
					$content = get_field( $location . '_' . $sections[2] );

					// Let's make sure we have an array.
					if ( is_array( $image_array ) ) :
						$image_size = $image_array['sizes'];
						$image_url = $image_size['material-thumb'];
					else :
						$image_url = null;
					endif; ?>

					<div style="background-color: <?php echo esc_html( $color ); ?>;">
						<?php if ( is_array( $image_array ) ) : ?>
							<div class="image-as-background" style="background-image: url(<?php echo esc_url( $image_url ); ?>);"></div>
						<?php endif; ?>
						<?php if ( $content ) : ?>
							<div class="content">
								<?php echo wp_kses_post( $content ); ?>
							</div>
						<?php endif; ?>
					</div>

				<?php } // endforeach. ?>
		</div><!-- .col-1 -->

			<?php // Flip Cards.
			$locations = array( 'top_center', 'bottom_center' );
			$count = 0; ?>

			<div class="col-2" >

				<?php foreach ( $locations as $location ) {

					// Let's count.
					$count++;

					// Get cloned fields.
					$sections = array( 'background_color', 'background_image', 'card_content' );

					$color = get_field( $location . '_' . $sections[0] );
					$image_array = get_field( $location . '_' . $sections[1] );
					$content = get_field( $location . '_' . $sections[2] );

					// Let's make sure we have an array.
					if ( is_array( $image_array ) ) :
						$image_size = $image_array['sizes'];
						$image_url = $image_size['material-thumb'];
					else :
						$image_url = null;
					endif; ?>

					<div style="background-color: <?php echo esc_html( $color ); ?>;">
						<?php if ( is_array( $image_array ) ) : ?>
							<div class="image-as-background" style="background-image: url(<?php echo esc_url( $image_url ); ?>);"></div>
						<?php endif; ?>
						<?php if ( $content ) : ?>
							<div class="content">
								<?php echo wp_kses_post( $content ); ?>
							</div>
						<?php endif; ?>
					</div>

				<?php } // endforeach. ?>
			</div><!-- .col-2 -->

			<?php // Flip Cards.
			$locations = array( 'top_right_inner_left', 'top_right_inner_right', 'bottom_right' );
			$count = 0; ?>

			<div class="col-3" >

				<?php foreach ( $locations as $location ) {

					// Let's count.
					$count++;

					// Get cloned fields.
					$sections = array( 'background_color', 'background_image', 'card_content' );

					$color = get_field( $location . '_' . $sections[0] );
					$image_array = get_field( $location . '_' . $sections[1] );
					$content = get_field( $location . '_' . $sections[2] );

					// Let's make sure we have an array.
					if ( is_array( $image_array ) ) :
						$image_size = $image_array['sizes'];
						$image_url = $image_size['material-thumb'];
					else :
						$image_url = null;
					endif; ?>

					<div style="background-color: <?php echo esc_html( $color ); ?>;">
						<?php if ( is_array( $image_array ) ) : ?>
							<div class="image-as-background" style="background-image: url(<?php echo esc_url( $image_url ); ?>);"></div>
						<?php endif; ?>
						<?php if ( $content ) : ?>
							<div class="content">
								<?php echo wp_kses_post( $content ); ?>
							</div>
						<?php endif; ?>
					</div>

				<?php } // endforeach. ?>
			</div><!-- .col-3 -->
		</div><!-- .wrap -->
	</section><!-- .resources-block -->

	<?php return ob_get_clean();
}

/**
 * Get permalink by slug.
 *
 * @param  string $slug supply slug of page.
 * @param  string $type type of post/page.
 * @return permalink by page slug.
 */
function me_rb4_get_link_by_slug( $slug, $type = 'page' ) {
	$post = get_page_by_path( $slug, OBJECT, $type );
	return get_permalink( $post->ID );
}

/**
 * Adds global stay informed widget.
 * @return string widget for stay informed
 */
function me_rb4_display_global_stay_informed_widget() {

	$title = get_field( 'stay_informed_title', 'options' );
	$content = get_field( 'stay_informed_widget_content', 'options' );

	if ( $title && $content ) : ?>
		<aside class="widget rb4-stay-informed">

			<div class="stay-informed-widget">
				<h3 class="widget-title"><?php echo esc_html( $title ); ?></h3>
				<?php echo wp_kses_post( $content ); ?>
			</div><!-- .seasonal-widget -->

		</aside>
	<?php endif;
}
