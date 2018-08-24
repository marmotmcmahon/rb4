<?php
/**
 * Template for displaying the Homepage Seasonal Block
 *
 * @package ME RB4th
 * @version 0.0.0
 */

// Get front page id
$id = get_option( 'page_on_front' );

$title = get_field( 'medium_title', $id );
$link = get_field( 'medium_link', $id );
$link_text = get_field( 'medium_link_text', $id );
$image_array = get_field( 'medium_image', $id );
$image_size = $image_array['sizes'];
$image_url = $image_size['seasonal-medium'];

echo $args['before_widget'];
if ( ! empty( $instance['title'] ) ) {
	 echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
} ?>

<div class="seasonal-widget">
	<div class="seasonal-message message-medium image-as-background" style="background-image: url( <?php echo esc_url( $image_url ); ?> );">
		<div class="float-to-bottom left">
			<h2><?php echo esc_html( $title ); ?></h2>
			<a href="<?php echo esc_url( $link ); ?>">
				<?php echo esc_html( $link_text ) . " »"; ?>
			</a>
		</div><!-- .float-to-bottom -->
	</div><!-- .seasonal-message .message-large -->
</div><!-- .seasonal-widget -->

<?php echo $args['after_widget'];
