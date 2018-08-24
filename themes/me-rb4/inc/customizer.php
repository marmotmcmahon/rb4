<?php
/**
 * Read by 4th Theme Customizer.
 *
 * @package Read by 4th
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function me_rb4_customize_register( $wp_customize ) {

	$wp_customize->remove_section( 'colors' );
	$wp_customize->remove_section( 'header_image' );
	$wp_customize->remove_section( 'background_image' );
	$wp_customize->remove_section( 'nav' );

	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	// Add our social link options.
	$wp_customize->add_section(
		'me_rb4_social_links_section',
		array(
			'title'       => esc_html__( 'Social Links', 'me-rb4' ),
			'description' => esc_html__( 'These are the settings for social links. Please limit the number of social links to 5.', 'me-rb4' ),
			'priority'    => 90,
		)
	);

	// Create an array of our social links for ease of setup.
	$social_networks = array( 'facebook', 'instagram', 'twitter' );

	// Loop through our networks to setup our fields.
	foreach ( $social_networks as $network ) {

		$wp_customize->add_setting(
			'me_rb4_' . $network . '_link',
			array(
				'default' => '',
				'sanitize_callback' => 'me_rb4_sanitize_customizer_url',
			)
		);
		$wp_customize->add_control(
			'me_rb4_' . $network . '_link',
			array(
				'label'   => sprintf( esc_html__( '%s Link', 'me-rb4' ), ucwords( $network ) ),
				'section' => 'me_rb4_social_links_section',
				'type'    => 'text',
			)
		);
	}

	// Google Analytics Code Field.
	$wp_customize->add_section( 'me_rb4_analytics_section',
		array(
			'title'    => esc_html__( 'Google Analytics', 'me-rb4' ),
			'priority' => 30,
		)
	);

	// Add our analtycis text field.
	$wp_customize->add_setting( 'me_rb4_analytics_section',
		array(
			'default' => '',
		)
	);

	$wp_customize->add_control( 'me_rb4_analytics_section',
		array(
			'label'       => esc_html__( 'Google Analytics Code', 'me-rb4' ),
			'description' => esc_html__( 'Add code from http://analytics.google.com', 'me-rb4' ),
			'section'     => 'me_rb4_analytics_section',
			'type'        => 'text',
			'sanitize'    => 'html',
		)
	);
}
add_action( 'customize_register', 'me_rb4_customize_register' );


/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function me_rb4_customize_preview_js() {
	wp_enqueue_script( 'me_rb4_customizer', get_template_directory_uri() . '/assets/scripts/customizer.js', array( 'customize-preview' ), '20151215', true );
}
add_action( 'customize_preview_init', 'me_rb4_customize_preview_js' );

/**
 * Sanitize our customizer text inputs.
 *
 * @param  string $input Text saved in Customizer input fields.
 * @return string        Sanitized output.
 *
 * @author Corey Collins
 */
function me_rb4_sanitize_customizer_text( $input ) {
	return sanitize_text_field( force_balance_tags( $input ) );
}

/**
 * Sanitize our customizer URL inputs.
 *
 * @param  string $input The URL we need to validate.
 * @return string        Sanitized output.
 *
 * @author Corey Collins
 */
function me_rb4_sanitize_customizer_url( $input ) {
	return esc_url( $input );
}


/**
 * Removes unecessary wordpess menu items.
 */
function me_rb4_remove_unnecessary_wordpress_menus() {
	global $submenu;
	unset( $submenu['themes.php'][20] ); // remove background image.
}
add_action( 'admin_menu', 'me_rb4_remove_unnecessary_wordpress_menus', 999 );
