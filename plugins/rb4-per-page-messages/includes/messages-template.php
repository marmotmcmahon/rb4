<?php
/**
 * Template for displaying the Homepage Seasonal Block
 *
 * @package ME RB4th
 * @version 0.0.0
 */

// Get front page id
$id = get_the_ID();

$copy = get_field( 'copy', $id ); // required field.
$link = ( get_field( 'link', $id ) ) ? get_field( 'link', $id ) : '';
$link_text = ( get_field( 'link_text', $id ) ) ? get_field( 'link_text', $id ) : '';

$image_array = get_field( 'background_image', $id );
$image_size = ( $image_array ) ? $image_array['sizes'] : '';
$image_url = ( $image_size ) ? $image_size['seasonal-medium'] : '';

if ( $copy ) {

	echo $args['before_widget'];
	if ( ! empty( $instance['title'] ) ) {
		 echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
	} ?>

	<div class="message-widget">
		<div class="seasonal-message message-medium image-as-background" style="background-image: url( <?php echo esc_url( $image_url ); ?> );">
			<div class="float-to-bottom left">
				<h2><?php echo esc_html( $copy ); ?></h2>
				<a href="<?php echo esc_url( $link ); ?>">
					<?php echo esc_html( $link_text ); ?>
				</a>
			</div><!-- .float-to-bottom -->
		</div><!-- .seasonal-message .message-large -->
	</div><!-- .seasonal-widget -->

	<?php echo $args['after_widget'];
}
