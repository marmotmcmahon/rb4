<?php
/**
 * The sidebar containing the main widget area.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Read by 4th
 */

?>

<aside class="secondary widget-area" role="complementary">
	<?php if ( is_home() || is_single() || is_archive() || is_category() || is_search() ) :
		dynamic_sidebar( 'blog-sidebar' );
	elseif ( is_page_template( 'template-portal.php' ) ) :
		dynamic_sidebar( 'portal-sidebar' );
	elseif ( is_page( 'for-every-parent' ) ) :
		dynamic_sidebar( 'for-every-parent' );
	elseif ( is_page( 'kids-0-2' ) ) :
		dynamic_sidebar( '0-2' );
	elseif ( is_page( 'kids-3-4' ) ) :
		dynamic_sidebar( '3-4' );
	elseif ( is_page( 'kids-5-9' ) ) :
		dynamic_sidebar( '5-9' );
	elseif ( is_page( 'more-resources' ) ) :
		dynamic_sidebar( 'more-resources-sidebar' );
	elseif ( is_page( 'attendance-matters' ) ) :
		dynamic_sidebar( 'attendance-matters-sidebar' );
	elseif ( is_page( 'about' ) ) :
		dynamic_sidebar( 'featured-partner' );
	elseif ( is_page( 'leadership' ) ) :
		dynamic_sidebar( 'featured-partner' );
	elseif ( is_page( 'funders' ) ) :
		dynamic_sidebar( 'featured-partner' );
	elseif ( is_page( 'partners' ) ) :
		dynamic_sidebar( 'featured-partner' );
	elseif ( is_page( 'reading-is-everywhere' ) ) :
		dynamic_sidebar( 'reading-is-everywhere-sidebar' );
	elseif ( is_page( 'reading-hero' ) ) :
		dynamic_sidebar( 'reading-is-everywhere-sidebar' );
	elseif ( is_page( 'book-nook' ) ) :
		dynamic_sidebar( 'reading-is-everywhere-sidebar' );
	elseif ( is_page( 'get-involved' ) ) :
		dynamic_sidebar( 'get-involved-sidebar' );
	elseif ( is_page( 'contact-us' ) ) :
		dynamic_sidebar( 'contact-us-sidebar' );
	else :
		dynamic_sidebar( 'global-sidebar' );
	endif;

	// Add Global Stay Informed Widget
	me_rb4_display_global_stay_informed_widget();

	?>
</aside><!-- .secondary -->
