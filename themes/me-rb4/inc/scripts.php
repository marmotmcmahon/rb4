<?php
/**
 * Custom scripts and styles.
 *
 * @package Read by 4th
 */

/**
 * Register Google font.
 *
 * @link http://themeshaper.com/2014/08/13/how-to-add-google-fonts-to-wordpress-themes/
 */
function me_rb4_font_url() {

	$fonts_url = '';

	/**
	 * Translators: If there are characters in your language that are not
	 * supported by the following, translate this to 'off'. Do not translate
	 * into your own language.
	 */
	$roboto = _x( 'on', 'Roboto font: on or off', 'me-rb4' );
	$open_sans = _x( 'on', 'Open Sans font: on or off', 'me-rb4' );

	if ( 'off' !== $roboto || 'off' !== $open_sans ) {
		$font_families = array();

		if ( 'off' !== $roboto ) {
			$font_families[] = 'Roboto:400,700';
		}

		if ( 'off' !== $open_sans ) {
			$font_families[] = 'Open Sans:400,300,700';
		}

		$query_args = array(
			'family' => urlencode( implode( '|', $font_families ) ),
		);

		$fonts_url = add_query_arg( $query_args, '//fonts.googleapis.com/css' );
	}

	return $fonts_url;
}

/**
 * Enqueue scripts and styles.
 */
function me_rb4_scripts() {
	/**
	 * If WP is in script debug, or we pass ?script_debug in a URL - set debug to true.
	 */
	$debug = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG == true ) || ( isset( $_GET['script_debug'] ) ) ? true : false;

	/**
	 * If we are debugging the site, use a unique version every page load so as to ensure no cache issues.
	 */
	$version = '1.0.0';

	/**
	 * Should we load minified files?
	 */
	$suffix = ( true === $debug ) ? '' : '.min';

	// Register styles.
	wp_register_style( 'me-rb4-google-font', me_rb4_font_url(), array(), null );

	// Enqueue styles.
	wp_enqueue_style( 'me-rb4-google-font' );
	wp_enqueue_style( 'me-rb4-style', get_stylesheet_directory_uri() . '/style' . $suffix . '.css', array(), $version );

	wp_enqueue_script( 'me-rb4-typekit', '//use.typekit.net/ijc3jrc.js' );

	// Enqueue scripts.
	wp_enqueue_script( 'me-rb4-scripts', get_template_directory_uri() . '/assets/scripts/project' . $suffix . '.js', array( 'jquery' ), $version, true );

	// ACF Google Map
	if ( is_page_template( 'template-map.php' ) ) {
		wp_enqueue_script( 'me-rb4-google-map', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyDF77cQHQDqDKakwlmAFKymMhZmjZWEwcE', '', true );
		wp_enqueue_script( 'me-rb4-acf-map', get_stylesheet_directory_uri() . '/assets/scripts/acf-map.min.js', array( 'jquery' ), true );
	}
}
add_action( 'wp_enqueue_scripts', 'me_rb4_scripts' );

/**
 * Add Typekit Inline Script.
 */
function wds_il_typekit_inline() {
	if ( wp_script_is( 'il-typekit', 'done' ) ) : ?>
		<script>try{Typekit.load();}catch(e){}</script>
	<?php endif;
}
add_action( 'wp_head', 'wds_il_typekit_inline' );

/**
 * Add SVG definitions to footer.
 */
function me_rb4_include_svg_icons() {

	// Define SVG sprite file.
	$svg_icons = get_template_directory() . '/assets/images/svg-icons.svg';

	// If it exists, include it.
	if ( file_exists( $svg_icons ) ) {
		require_once( $svg_icons );
	}
}
add_action( 'wp_footer', 'me_rb4_include_svg_icons', 9999 );
