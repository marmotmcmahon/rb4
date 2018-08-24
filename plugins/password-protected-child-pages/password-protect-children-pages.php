<?php
/**
 * Plugin Name
 *
 * @package     Password_Protect_Children_Pages
 * @author      jomurgel, fullthrottledevelopment, blepoxp
 * @copyright   2010 FullThrottleDevelopment.com
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: Password Protect Children Pages
 * Plugin URI:  https://github.com/jomurgel/ft-password-protect-children-pages
 * Description: Password protect children of parent if parent is password protected.
 * Version:     0.9
 * Author: jomurgel
 * Author URI: http://jomurgel.com
 * Primary Developer: Glenn Ansley (glenn@glennansley.com)
 * Text Domain: password-protect-children-pages
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

/**
 * This function prints the password form if the parent page is password protected. It is called whenever 'the_content' is invoked.
 *
 * @param mixed[] $org_content Array.
 * @return string
 */
function ft_password_protect_children_page_contents( $org_content ) {

	if ( is_page() && post_password_required() ) {
		global $post;

		// Grab ancestors.
		$ancestors = $post->ancestors;

		// Loop through ancestors, grab first one that is password protected.
		foreach ( $ancestors as $ancestor ) {

			if ( post_password_required( $ancestor ) ) {
				$real_post = $post;
				$post = get_post( $ancestor ); // WPCS: override ok.

				echo get_the_password_form(); // WPCS: XSS ok.
				$post = $real_post; // WPCS: override ok.
				return;
			}
		}
	}
	return $org_content;
}
add_filter( 'the_content', 'ft_password_protect_children_page_contents' );

/**
 * This function prints the "excerpt can't be displayed" message if the parent post is protected. It is called whenever 'get_the_excerpt' is invoked (which gets invoked by get_excerpt() ).
 *
 * @param mixed[] $org_excerpt Array.
 * @return string
 */
function ft_password_protect_children_page_excerpts( $org_excerpt ) {

	if ( is_page() && post_password_required() ) {
		global $post;

		// Grab ancestors.
		$ancestors = $post->ancestors;

		// Loop through ancestors, grab first one that is password protected.
		foreach ( $ancestors as $ancestor ) {
			if ( post_password_required( $ancestor ) ) {
				$output = wpautop( esc_html__( 'There is no excerpt because this is a protected post.' ) );
				return $output;
			}
		}
	}
	return $org_excerpt;
}
add_filter( 'get_the_excerpt', 'ft_password_protect_children_page_excerpts' , 9 );

/**
 * This function alter's the Post Title to include the protected_title_format.
 *
 * @param string $org_title to get protected title.
 * @param string $title_id  to generate title.
 * @return titles.
 */
function ft_password_protect_children_page_titles( $org_title, $title_id = '' ) {

	if ( is_page() && in_the_loop() && post_password_required() ) {
		global $post;

		// Grab ancestors.
		$ancestors = $post->ancestors;

		// Loop through ancestors, grab first one that is password protected.
		foreach ( $ancestors as $ancestor ) {

			$ancestor_post = get_post( $ancestor );

			if ( post_password_required( $ancestor ) || ( isset( $ancestor_post->post_password ) && ! empty( $ancestor_post->post_password ) ) ) {

				$protected_title_format = apply_filters( 'protected_title_format', esc_html__( 'Protected: %s' ) );
				$title = sprintf( $protected_title_format, $org_title );

				return $title;
			}
		}
	}
	return $org_title;
}
add_filter( 'the_title', 'ft_password_protect_children_page_titles', 10 , 2 );
