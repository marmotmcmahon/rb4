<?php
/**
 * Template for displaying Related Resources.
 *
 * @package ME RB4th
 * @version 0.0.0
 */

// ACF Vars.
$resources = get_field( 'related_resources', 'widget_' . $args['widget_id'] );

if ( $resources ) :

	echo $args['before_widget'];

	if ( $instance['title'] ) {
		 echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
	}
		echo $resources;

	echo $args['after_widget'];

endif;
