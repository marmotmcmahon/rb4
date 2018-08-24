
<?php
/**
 * Template for displaying the Featured Partner Widget
 *
 * @package ME RB4th
 * @version 0.0.0
 */

// Get front page id
$id = 'widget_' . $args['widget_id'];

$sec_title = (get_field( 'partner_section_title', $id )) ? get_field( 'partner_section_title', $id ) : 'Featured Partner'; // required field.
$title = get_field( 'partner_block_title', $id ); // required field.
$link = ( get_field( 'partner_block_link', $id ) ) ? get_field( 'partner_block_link', $id ) : '';
$content = get_field( 'partner_block_content', $id );

$image_array = get_field( 'partner_background_image', $id );
$image_size = ( $image_array ) ? $image_array['sizes'] : '';
$image_url = ( $image_size ) ? $image_size['seasonal-medium'] : '';
$target = ( get_field( 'partner_open_in_new_tab', $id ) ) ? ' target="_blank"' : '';

if ( $sec_title && $title && $content ) {

	echo $args['before_widget'];
	// This doesn't exist.
	if ( ! empty( $instance['title'] ) ) {
		 echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
	} ?>

	<div class="featured-partner-widget">
		<div class="featured-partner message-medium image-as-background" style="background-image: url( <?php echo esc_url( $image_url ); ?> );">
			<div class="float-to-bottom left">
				<span class="section-title"><?php echo esc_html( $sec_title ); ?></span>
				<h2><?php echo esc_html( $title ); ?></h2>
				<a href="<?php echo esc_url( $link ); ?>"<?php echo esc_html( $target )?>>
					<?php echo wp_kses_post( $content ); ?>
				</a>
			</div><!-- .float-to-bottom -->
		</div><!-- .seasonal-message .message-large -->
	</div><!-- .seasonal-widget -->

	<?php echo $args['after_widget'];
}
