<?php
/**
 * Plugin Name: Read by 4th Always On Functionality
 * Description: Contains code that is necessary for all site functionality.
 *
 * @package Read by 4th
 */

/**
 * Required files to include
 */
require_once( 'rb4-required-plugins/rb4-required-plugins.php' );


/**
 * Add required plugins to rb4_Required_Plugins
 *
 * @param  array $required Array of required plugins in `plugin_dir/plugin_file.php` form
 *
 * @return array           Modified array of required plugins
 */
add_filter( 'rb4_required_plugins', function ( $required ) {

	$site_required = array(
		// Add any required plugins here.
		'advanced-custom-fields-pro/acf.php',
		'password-protected-child-pages/password-protect-children-pages.php',
		'rb4-related-resources/rb4-related-resources.php',
		'rb4-show-seasonal-block/rb4-show-seasonal-block.php',
		'rb4-per-page-messages/rb4-per-page-messages.php',
		'rb4-featured-hero/rb4-featured-hero.php',
		'rb4-featured-partner/rb4-featured-partner.php',
	);

	return array_merge( $required, $site_required );
} );

/*
 * Prevent all autoupdates from happening. All security updates will need
 * to be applied by hand. While this adds additional work it also prevents
 * breaking changes from occurring or blocks to a git pull
 */
add_filter( 'automatic_updater_disabled', '__return_true' );
