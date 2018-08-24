<?php
/**
 * Custom Post Types & Taxonomies
 *
 * @link https://codex.wordpress.org/Function_Reference/register_post_type
 *
 * @package Read by 4th
 */

/**
 * Init Custom Post Types
 */
function me_rb4_custom_post_types_init() {

	// Partners CPT.
	$partner_labels = array(
		'name'               => _x( 'Partners', 'post type general name' ),
		'singular_name'      => _x( 'Partner', 'post type singular name' ),
		'add_new'            => _x( 'Add New', 'book' ),
		'add_new_item'       => __( 'Add New Partner' ),
		'edit_item'          => __( 'Edit Partner' ),
		'new_item'           => __( 'New Partner' ),
		'all_items'          => __( 'All Partners' ),
		'view_item'          => __( 'View Partner' ),
		'search_items'       => __( 'Search Partners' ),
		'not_found'          => __( 'No Partners found' ),
		'not_found_in_trash' => __( 'No Partners found in the Trash' ),
		'parent_item_colon'  => '',
		'menu_name'          => 'Partners',
	);

	$partner_args = array(
		'labels'        => $partner_labels,
		'description'   => 'Holds our Partners and Partner specific data',
		'public'        => true,
		'menu_position' => 5, // '5' places menu item directly below Posts
		'menu_icon'     => 'dashicons-universal-access', // https://developer.wordpress.org/resource/dashicons/
		'taxonomies'    => array( '' ), // associates with custom taxonomy
		'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		'has_archive'   => false,
	);
	register_post_type( 'partner', $partner_args );

	// Sponsors CPT.
	$sponsor_labels = array(
		'name'               => _x( 'Sponsors', 'post type general name' ),
		'singular_name'      => _x( 'Sponsor', 'post type singular name' ),
		'add_new'            => _x( 'Add New', 'book' ),
		'add_new_item'       => __( 'Add New Sponsor' ),
		'edit_item'          => __( 'Edit Sponsor' ),
		'new_item'           => __( 'New Sponsor' ),
		'all_items'          => __( 'All Sponsors' ),
		'view_item'          => __( 'View Sponsor' ),
		'search_items'       => __( 'Search Sponsors' ),
		'not_found'          => __( 'No Sponsors found' ),
		'not_found_in_trash' => __( 'No Sponsors found in the Trash' ),
		'parent_item_colon'  => '',
		'menu_name'          => 'Sponsors',
	);

	$sponsor_args = array(
		'labels'        => $sponsor_labels,
		'description'   => 'Holds our Sponsors and Sponsor specific data',
		'public'        => true,
		'menu_position' => 5, // '5' places menu item directly below Posts
		'menu_icon'     => 'dashicons-carrot', // https://developer.wordpress.org/resource/dashicons/
		'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		'has_archive'   => false,
	);
	register_post_type( 'sponsor', $sponsor_args );

	// Staff/Leadership CPT.
	$staff_labels = array(
		'name'               => _x( 'Staff/Leaderships', 'post type general name' ),
		'singular_name'      => _x( 'Staff/Leadership', 'post type singular name' ),
		'add_new'            => _x( 'Add New', 'book' ),
		'add_new_item'       => __( 'Add New Staff/Leadership' ),
		'edit_item'          => __( 'Edit Staff/Leadership' ),
		'new_item'           => __( 'New Staff/Leadership' ),
		'all_items'          => __( 'All Staff/Leaderships' ),
		'view_item'          => __( 'View Staff/Leadership' ),
		'search_items'       => __( 'Search Staff/Leaderships' ),
		'not_found'          => __( 'No Staff/Leaderships found' ),
		'not_found_in_trash' => __( 'No Staff/Leaderships found in the Trash' ),
		'parent_item_colon'  => '',
		'menu_name'          => 'Staff & Leaders',
	);

	$staff_args = array(
		'labels'        => $staff_labels,
		'description'   => 'Holds our Staff/Leaderships and Staff/Leadership specific data',
		'public'        => true,
		'menu_position' => 5, // '5' places menu item directly below Posts
		'menu_icon'     => 'dashicons-groups', // https://developer.wordpress.org/resource/dashicons/
		'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		'has_archive'   => false,
	);
	register_post_type( 'staff-leadership', $staff_args );

	// Impact Locations CPT.
	$impact_labels = array(
		'name'               => _x( 'Impact Locations', 'post type general name' ),
		'singular_name'      => _x( 'Impact Location', 'post type singular name' ),
		'add_new'            => _x( 'Add New', 'book' ),
		'add_new_item'       => __( 'Add New Impact Location' ),
		'edit_item'          => __( 'Edit Impact Location' ),
		'new_item'           => __( 'New Impact Location' ),
		'all_items'          => __( 'All Impact Locations' ),
		'view_item'          => __( 'View Impact Location' ),
		'search_items'       => __( 'Search Impact Location' ),
		'not_found'          => __( 'No Impact Locations found' ),
		'not_found_in_trash' => __( 'No Impact Locations found in the Trash' ),
		'parent_item_colon'  => '',
		'menu_name'          => 'Impact Loc.',
	);

	$impact_args = array(
		'labels'        => $impact_labels,
		'description'   => 'Holds our Impact Locations and Impact Location specific data',
		'public'        => true,
		'menu_position' => 5, // '5' places menu item directly below Posts
		'menu_icon'     => 'dashicons-location', // https://developer.wordpress.org/resource/dashicons/
		'taxonomies'    => array( 'location-type' ), // associates with custom taxonomy
		'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		'has_archive'   => false,
	);
	register_post_type( 'impact-locations', $impact_args );

	// Materials CPT.
	$material_labels = array(
		'name'               => _x( 'Materials', 'post type general name' ),
		'singular_name'      => _x( 'Material', 'post type singular name' ),
		'add_new'            => _x( 'Add New', 'book' ),
		'add_new_item'       => __( 'Add New Material' ),
		'edit_item'          => __( 'Edit Material' ),
		'new_item'           => __( 'New Material' ),
		'all_items'          => __( 'All Materials' ),
		'view_item'          => __( 'View Materials' ),
		'search_items'       => __( 'Search Materials' ),
		'not_found'          => __( 'No Materials found' ),
		'not_found_in_trash' => __( 'No Materials found in the Trash' ),
		'parent_item_colon'  => '',
		'menu_name'          => 'Materials',
	);

	$material_args = array(
		'labels'        => $material_labels,
		'description'   => 'Holds our Material and Material specific data',
		'public'        => true,
		'menu_position' => 5, // '5' places menu item directly below Posts
		'menu_icon'     => 'dashicons-cloud', // https://developer.wordpress.org/resource/dashicons/
		'taxonomies'    => array( 'material-type' ), // associates with custom taxonomy
		'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		'has_archive'   => false,
	);
	register_post_type( 'material', $material_args );
}
add_action( 'init', 'me_rb4_custom_post_types_init' );


/**
 * Custom Taxonomies
 */
function me_rb4_custom_taxonomies() {
	 $taxonomies = array(
		array(
			'slug'         => 'material-type',
			'single_name'  => 'Material Type',
			'plural_name'  => 'Material Types',
			'post_type'    => 'material',
		),
		 array(
			'slug'         => 'location-type',
			'single_name'  => 'Location Type',
			'plural_name'  => 'Location Types',
			'post_type'    => 'impact-locations',
		),
	);

	foreach ( $taxonomies as $taxonomy ) {
		 $labels = array(
			'name'              => $taxonomy['plural_name'],
			'singular_name'     => $taxonomy['single_name'],
			'search_items'      => 'Search ' . $taxonomy['plural_name'],
			'all_items'         => 'All ' . $taxonomy['plural_name'],
			'parent_item'       => 'Parent ' . $taxonomy['single_name'],
			'parent_item_colon' => 'Parent ' . $taxonomy['single_name'] . ':',
			'edit_item'         => 'Edit ' . $taxonomy['single_name'],
			'update_item'       => 'Update ' . $taxonomy['single_name'],
			'add_new_item'      => 'Add New ' . $taxonomy['single_name'],
			'new_item_name'     => 'New ' . $taxonomy['single_name'] . ' Name',
			'menu_name'         => $taxonomy['plural_name'],
		 );

		$rewrite = isset( $taxonomy['rewrite'] ) ? $taxonomy['rewrite'] : array( 'slug' => $taxonomy['slug'] );
		$hierarchical = isset( $taxonomy['hierarchical'] ) ? $taxonomy['hierarchical'] : true;

		register_taxonomy( $taxonomy['slug'], $taxonomy['post_type'], array(
			'hierarchical' => $hierarchical,
			'labels'       => $labels,
			'show_ui'      => true,
			'query_var'    => true,
			'rewrite'      => $rewrite,
		));
	}
}
add_action( 'init', 'me_rb4_custom_taxonomies', 0 );

/**
 * Define default terms for custom taxonomies
 *
 * @author    Michael Fields     http://wordpress.mfields.org/
 * @props     John P. Bloch      http://www.johnpbloch.com/
 *
 * @since     2010-09-13
 * @alter     2010-09-14
 *
 * @license   GPLv2
 */
function me_rb4_material_set_default_object_terms( $post_id, $post, $update ) {
	if ( ! $update ) {
		$defaults = array(
			'material-type' => array( 'request' ),
		);
		$taxonomies = get_object_taxonomies( $post->post_type );
		foreach ( (array) $taxonomies as $taxonomy ) {
			$terms = wp_get_post_terms( $post_id, $taxonomy );
			if ( empty( $terms ) && array_key_exists( $taxonomy, $defaults ) ) {
				wp_set_object_terms( $post_id, $defaults[ $taxonomy ], $taxonomy );
			}
		}
	}
}
add_action( 'save_post', 'me_rb4_material_set_default_object_terms', 100, 3 );
