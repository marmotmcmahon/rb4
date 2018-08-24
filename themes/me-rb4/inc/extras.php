<?php
/**
 * Custom functions that act independently of the theme templates.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Read by 4th
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function me_rb4_body_classes( $classes ) {

	// @codingStandardsIgnoreStart
	// Allows for incorrect snake case like is_IE to be used without throwing errors.
	global $is_IE;

	// If it's IE, add a class.
	if ( $is_IE ) {
		$classes[] = 'ie';
	}
	// @codingStandardsIgnoreEnd

	// Give all pages a unique class.
	if ( is_page() ) {
		$classes[] = 'page-' . basename( get_permalink() );
	}

	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	// Are we on mobile?
	// PHP CS wants us to use jetpack_is_mobile instead, but what if we don't have Jetpack installed?
	// Allows for using wp_is_mobile without throwing an error.
	// @codingStandardsIgnoreStart
	if ( wp_is_mobile() ) {
		$classes[] = 'mobile';
	}
	// @codingStandardsIgnoreEnd

	// Adds "no-js" class. If JS is enabled, this will be replaced (by javascript) to "js".
	$classes[] = 'no-js';

	return $classes;
}
add_filter( 'body_class', 'me_rb4_body_classes' );


/**
 * Add custom image sizes attribute to enhance responsive image functionality
 * for content images
 *
 * @package Read by 4th
 *
 * @param string $sizes A source size value for use in a 'sizes' attribute.
 * @param array  $size  Image size. Accepts an array of width and height
 *                      values in pixels (in that order).
 * @return string A source size value for use in a content image 'sizes' attribute.
 */
function me_rb4_content_image_sizes_attr( $sizes, $size ) {
	$width = $size[0];

	840 <= $width && $sizes = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 1362px) 62vw, 840px';

	if ( 'page' === get_post_type() ) {
		840 > $width && $sizes = '(max-width: ' . $width . 'px) 85vw, ' . $width . 'px';
	} else {
		840 > $width && 600 <= $width && $sizes = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 984px) 61vw, (max-width: 1362px) 45vw, 600px';
		600 > $width && $sizes = '(max-width: ' . $width . 'px) 85vw, ' . $width . 'px';
	}

	return $sizes;
}
add_filter( 'wp_calculate_image_sizes', 'me_rb4_content_image_sizes_attr', 10 , 2 );


/**
 * Add custom image sizes attribute to enhance responsive image functionality
 * for post thumbnails
 *
 * @package Read by 4th
 *
 * @param array $attr Attributes for the image markup.
 * @param int   $attachment Image attachment ID.
 * @param array $size Registered image size or flat array of height and width dimensions.
 * @return string A source size value for use in a post thumbnail 'sizes' attribute.
 */
function me_rb4_post_thumbnail_sizes_attr( $attr, $attachment, $size ) {
	if ( 'post-thumbnail' === $size ) {
		is_active_sidebar( 'sidebar-1' ) && $attr['sizes'] = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 984px) 60vw, (max-width: 1362px) 62vw, 840px';
		! is_active_sidebar( 'sidebar-1' ) && $attr['sizes'] = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 1362px) 88vw, 1200px';
	}
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'me_rb4_post_thumbnail_sizes_attr', 10 , 3 );


if ( is_page() ) {
	/**
	 * Automatically change child template to match parent_tmp
	 */
	function me_rb4_give_children_parent_template() {
		global $post;

		$curr_tmp = get_post_meta( $post->ID, '_wp_page_template', true );
		$parent_tmp = get_post_meta( $post->post_parent, '_wp_page_template', true );

		if ( $post->post_parent ) :
			update_post_meta( $post->ID, '_wp_page_template', $parent_tmp,$curr_tmp );
		endif;
	}
	add_action( 'save_post', 'me_rb4_give_children_parent_template' );
}

/**
 * Change "Posts" Label to "News".
 */
function revcon_change_post_label() {
	global $menu;
	global $submenu;
	$menu[5][0] = 'News';
	$submenu['edit.php'][5][0] = 'News';
	$submenu['edit.php'][10][0] = 'Add News';
	$submenu['edit.php'][16][0] = 'News Tags';
}

/**
 * Change "Posts" objects to "News".
 */
function revcon_change_post_object() {
	global $wp_post_types;
	$labels = &$wp_post_types['post']->labels;
	$labels->name = 'News';
	$labels->singular_name = 'News';
	$labels->add_new = 'Add News';
	$labels->add_new_item = 'Add News';
	$labels->edit_item = 'Edit News';
	$labels->new_item = 'News';
	$labels->view_item = 'View News';
	$labels->search_items = 'Search News';
	$labels->not_found = 'No News found';
	$labels->not_found_in_trash = 'No News found in Trash';
	$labels->all_items = 'All News';
	$labels->menu_name = 'News';
	$labels->name_admin_bar = 'News';
}
add_action( 'admin_menu', 'revcon_change_post_label' );
add_action( 'init', 'revcon_change_post_object' );


/**
 * Output new form for password protected pages.
 *
 * @return string, new form.
 */
function me_rb4_new_password_protect_form() {

	// global.
	global $post;

	// get password box.
	$label = 'pwbox-' . ( empty( $post->ID ) ? rand() : $post->ID ); ?>

	<section class="password-protected">

		<?php // generate new form.
		$new_form = '<form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" method="post" class="new-password-proteded-form with-input">';
		$new_form .= '<h2>' . esc_html__( 'Welcome to the ' . ( empty( $post->post_title ) ? rand() : $post->post_title ), 'me-rb4' ) . '</h2>';
		$new_form .= '<span class="message">' . esc_html__( 'To view this protected post, enter the password below:', 'me-rb4' ) . '</span>';
		$new_form .= '<label for="' . $label . '">' . __( 'Password:' ) . ' </label>';
		$new_form .= '<input name="post_password" id="' . esc_html( $label ) . '" type="password" size="20" maxlength="20" /><input type="submit" name="Submit" value="' . esc_attr__( 'Submit' ) . '" />';
		$new_form .= '</form>';

		// output form.
		return $new_form; ?>

	</section><!-- .password-protected -->

	<?php
}
add_filter( 'the_password_form', 'me_rb4_new_password_protect_form' );
