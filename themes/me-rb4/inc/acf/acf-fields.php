<?php
/**
 * ACF Custom Fields PHP Output
 *
 * @link https://codex.wordpress.org/Function_Reference/register_post_type
 *
 * @package Read by 4th
 */

/**
 * Advanced Custom Fields Template Tags if ACF exists.
 */
require get_template_directory() . '/inc/acf/acf-template-tags.php';

if( function_exists('acf_add_options_page') ) {
	
	acf_add_options_page(array(
		'page_title' 	=> 'Global Elements',
		'menu_title'	=> 'Global Elements',
		'menu_slug' 	=> 'global-elements',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));
}