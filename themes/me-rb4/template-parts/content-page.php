<?php
/**
 * Template part for displaying flexible content fields
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Read by 4th
 */

if ( post_password_required() && function_exists( 'ft_password_protect_children_page_contents' ) ) :
	the_content(); // for password-protected pages.
endif;

// get flexible content fields.
if ( ! post_password_required() ) {
	echo me_rb4_get_flexible_content(); // WPCS: XSS ok.
}
