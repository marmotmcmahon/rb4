<?php
/**
 * Template Name: 301 Redirect
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package RB4th
 */

if ( function_exists( 'have_posts' ) && have_posts() ) {

	while ( have_posts() ) {

		// get the post.
		the_post();

		// get content.
		ob_start();
		the_content();
		$contents	= ob_get_contents();
		ob_end_clean();

			// grab the 'naked' link.
			$link = trim( strip_tags( $contents ) );

			// work out.
			if ( ! preg_match( '%^http://%', $link ) ) {
				$host = $_SERVER['HTTP_HOST'];
				$dir = dirname( $_SERVER['PHP_SELF'] );
				$link = 'http://' . $host . $dir . '/' . $link;
			}

		// navigate to the link.
		header( 'HTTP/1.1 301 Moved Permanently' );
		header( "Location: $link" );
		exit;
	}
}
