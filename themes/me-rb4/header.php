<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Read by 4th
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<?php
	// @codingStandardsIgnoreStart
	global $is_IE;
	if ( $is_IE ) :
	// @codingStandardsIgnoreEnd ?>
	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />

	<?php endif; ?>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'me-rb4' ); ?></a>

	<header class="site-header">
		
		<?php if ( is_front_page() ) :
			echo me_rb4_seasonal_messages_header(); // WPCS: XSS ok.
		endif; ?>

		<div class="padded-wrap">
			<button class="menu-button" id="menuButton">
				<span class="burger-icon"></span>
			</button><!-- .menu-button -->
			<div class="site-branding">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="logo-mark"><?php echo me_rb4_get_svg( array( 'icon' => 'logo', 'title' => 'Read by 4th' ) ); ?></a><!-- .logo-mark -->
			</div><!-- .site-branding -->

			<nav id="site-navigation" class="main-navigation">
				<?php if ( is_front_page() ) :
					wp_nav_menu( array(
						'theme_location' => 'homepage',
						'menu_id'        => 'primary-menu',
						'menu_class'     => 'menu',
					) );
				else :
					wp_nav_menu( array(
						'theme_location' => 'primary',
						'menu_id'        => 'primary-menu',
						'menu_class'     => 'menu',
					) );
				endif; ?>
			</nav><!-- #site-navigation -->
		</div><!-- .padded-wrap -->

		<?php if ( ! is_front_page() && ! is_page_template( 'template-portal.php' ) && ! is_404() ) { ?>
			<div class="page-child-nav">
				<?php echo me_rb4_list_child_pages(); ?>
			</div>
		<?php } ?>
	</header><!-- #masthead -->

	<?php if ( ! is_front_page() && get_field( 'header_image' ) ) :

		// ACF Fields.
		$desktop_url = get_field( 'header_image' );
		$mobile_url = get_field( 'mobile_header_image' ); ?>

		<div class="header-image full-width">
			<?php if ( $desktop_url && $mobile_url ) : ?>
				<img src="<?php echo esc_url( $mobile_url ); ?>" alt="<?php echo get_the_title(); ?>" class="mobile" />
				<img src="<?php echo esc_url( $desktop_url ); ?>" alt="<?php echo get_the_title(); ?>" class="desktop" />
			<?php elseif ( $desktop_url && ! $mobile_url ) : ?>
				<img src="<?php echo esc_url( $desktop_url ); ?>" alt="<?php echo get_the_title(); ?>" />
			<?php else :
				// Do nothing.
			endif; ?>
		</div><!-- .header_image -->

	<?php endif; ?>

	<div id="content" class="site-content">
