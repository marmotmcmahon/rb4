<?php
/**
 * Read by 4th functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Read by 4th
 */

if ( ! function_exists( 'me_rb4_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function me_rb4_setup() {
		/**
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on Read by 4th, use a find and replace
		 * to change 'me-rb4' to the name of your theme in all the template files.
		 * You will also need to update the Gulpfile with the new text domain
		 * and matching destination POT file.
		 */
		load_theme_textdomain( 'me-rb4', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/**
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/**
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// Full Width image.
		add_image_size( 'full-width', 1600, 9999 );

		// Full Width image with max height.
		add_image_size( 'header-image', 1600, 500 );

				// Full Width image with max height.
		add_image_size( 'mobile-header-image', 800, 400 );

		// Large Seasonal Background Images.
		add_image_size( 'seasonal-large', 900, 800, array( 'center', 'center' ) );

		// Medium Seasonal Background Images.
		add_image_size( 'seasonal-medium', 490, 630, array( 'center', 'center' ) );

		// Google Map Image.
		add_image_size( 'map-image', 252, 100, array( 'center', 'center' ) );

		// Material Thumb.
		add_image_size( 'material-thumb', 420, 420, array( 'center', 'center' ) );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'homepage'   => esc_html__( 'Homepage Menu', 'me-rb4' ),
			'primary'   => esc_html__( 'Primary Menu', 'me-rb4' ),
			'footer'    => esc_html__( 'Footer Menu', 'me-rb4' ),
		) );

		/**
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'gallery',
			'caption',
		) );

		// Set up the WordPress core custom background feature.
		add_theme_support( 'custom-background', apply_filters( 'me_rb4_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		) ) );

	}
endif; // me_rb4_setup.
add_action( 'after_setup_theme', 'me_rb4_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function me_rb4_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'me_rb4_content_width', 640 );
}
add_action( 'after_setup_theme', 'me_rb4_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function me_rb4_widgets_init() {

	// Define sidebars.
	$sidebars = array(
		'global-sidebar'                => esc_html__( 'Global Sidebar', 'me-rb4' ),
		'blog-sidebar'                  => esc_html__( 'Blog Sidebar', 'me-rb4' ),
		'portal-sidebar'                => esc_html__( 'Portal Sidebar', 'me-rb4' ),
		'for-every-parent'              => esc_html__( 'For Every Parent Sidebar', 'me-rb4' ),
		'0-2'                           => esc_html__( 'Kids 0-2 Sidebar', 'me-rb4' ),
		'3-4'                           => esc_html__( 'Kids 3-4 Sidebar', 'me-rb4' ),
		'5-9'                           => esc_html__( 'Kids 5-9 Sidebar', 'me-rb4' ),
		'featured-partner'              => esc_html__( 'Featured Partner Sidebar', 'me-rb4' ),
		'reading-is-everywhere-sidebar' => esc_html__( 'Reading is Everywhere Sidebar', 'me-rb4' ),
		'more-resources-sidebar'        => esc_html__( 'More Family Resources Sidebar', 'me-rb4' ),
		'attendance-matters-sidebar'    => esc_html__( 'Attendance Matters Sidebar', 'me-rb4' ),
		'get-involved-sidebar'          => esc_html__( 'Get Involved Sidebar', 'me-rb4' ),
		'contact-us-sidebar'            => esc_html__( 'Contact us Sidebar', 'me-rb4' ),
	);

	// Loop through each sidebar and register.
	foreach ( $sidebars as $sidebar_id => $sidebar_name ) {
		register_sidebar( array(
			'name'          => $sidebar_name,
			'id'            => $sidebar_id,
			'description'   => sprintf( esc_html__( 'Widget area for %s', 'me-rb4' ), $sidebar_name ),
			'before_widget' => '<aside class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );
	}

}
add_action( 'widgets_init', 'me_rb4_widgets_init' );

/**
 * Register ACF Google Maps API.
 */
function me_rb4_acf_init() {

	acf_update_setting( 'google_api_key', 'AIzaSyDF77cQHQDqDKakwlmAFKymMhZmjZWEwcE' );
}
add_action( 'acf/init', 'me_rb4_acf_init' );

/**
 * Unregister all widgets.
 */
function me_rb4_unregister_default_widgets() {
	unregister_widget( 'WP_Widget_Recent_Comments' ); // Remove Comments.
	unregister_widget( 'WP_Widget_RSS' ); // Remove RSS Feed.
	unregister_widget( 'WP_Widget_Tag_Cloud' ); // Remove Tag Cloud.
}
add_action( 'widgets_init', 'me_rb4_unregister_default_widgets', 11 );


/**
 * Remove Category prefix from categorically-viewed blog landing page.
 */
add_filter( 'get_the_archive_title', function( $title ) {
	if ( is_category() ) {
		$title = single_cat_title( '', false );
	} elseif ( is_tag() ) {
		$title = single_tag_title( '', false );
	} elseif ( is_author() ) {
		$title = '<span class="vcard">' . get_the_author() . '</span>' ;
	}
	return $title;
});


/**
 * Removes default title from default archive & category widget.
 */
function me_rb4_cat_archive_empty_title( $widget_title ) {
	if ( '!' == substr( $widget_title, 0, 1 ) ) {
		return;
	} else {
		return ( $widget_title );
	}
}
add_filter( 'widget_title', 'me_rb4_cat_archive_empty_title', 10, 3 );


/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom Post Types & Taxonomies.
 */
require get_template_directory() . '/inc/cpt-taxonomy.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load styles and scripts.
 */
require get_template_directory() . '/inc/scripts.php';

/**
 * Advanced Custom Fields
 */
require get_template_directory() . '/inc/acf/acf-fields.php';
