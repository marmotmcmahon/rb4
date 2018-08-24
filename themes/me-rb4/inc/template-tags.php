<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Read by 4th
 */

if ( ! function_exists( 'me_rb4_posted_on' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time and author.
	 */
	function me_rb4_posted_on() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf( $time_string,
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() )
		);

		$posted_on = sprintf(
			esc_html_x( 'Posted on %s', 'post date', 'me-rb4' ),
			'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
		);

		$byline = sprintf(
			esc_html_x( 'by %s', 'post author', 'me-rb4' ),
			'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
		);

		echo '<span class="posted-on">' . $posted_on /*. '</span><span class="byline"> ' . $byline . '</span>'*/; // WPCS: XSS OK.

	}
endif;

if ( ! function_exists( 'me_rb4_entry_footer' ) ) :
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 */
	function me_rb4_entry_footer() {
		// Hide category and tag text for pages.
		if ( 'post' === get_post_type() ) {
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( esc_html__( ', ', 'me-rb4' ) );
			if ( $categories_list && me_rb4_categorized_blog() ) {
				printf( '<span class="cat-links">' . esc_html__( 'Posted in %1$s', 'me-rb4' ) . '</span>', $categories_list ); // WPCS: XSS OK.
			}

			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list( '', esc_html__( ', ', 'me-rb4' ) );
			if ( $tags_list ) {
				printf( '<span class="tags-links">' . esc_html__( 'Tagged %1$s', 'me-rb4' ) . '</span>', $tags_list ); // WPCS: XSS OK.
			}
		}

		edit_post_link(
			sprintf(
				/* translators: %s: Name of current post */
				esc_html__( 'Edit %s', 'me-rb4' ),
				the_title( '<span class="screen-reader-text">"', '"</span>', false )
			),
			'<span class="edit-link">',
			'</span>'
		);
	}
endif;

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function me_rb4_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'me_rb4_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,
			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'me_rb4_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so me_rb4_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so me_rb4_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in me_rb4_categorized_blog.
 */
function me_rb4_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return false;
	}
	// Like, beat it. Dig?
	delete_transient( 'me_rb4_categories' );
}
add_action( 'delete_category', 'me_rb4_category_transient_flusher' );
add_action( 'save_post',     'me_rb4_category_transient_flusher' );

/**
 * Return SVG markup.
 *
 * @param array $args for svg.
 * @return string SVG markup.
 */
function me_rb4_get_svg( $args = array() ) {

	// Make sure $args are an array.
	if ( empty( $args ) ) {
		return esc_html__( 'Please define default parameters in the form of an array.', 'me-rb4' );
	}

	// Define an icon.
	if ( false === array_key_exists( 'icon', $args ) ) {
		return esc_html__( 'Please define an SVG icon filename.', 'me-rb4' );
	}

	// Set defaults.
	$defaults = array(
		'icon'  => '',
		'title' => '',
		'desc'  => '',
	);

	// Parse args.
	$args = wp_parse_args( $args, $defaults );

	// Figure out which title to use.
	$title = ( $args['title'] ) ? $args['title'] : $args['icon'];

	// Set aria hidden.
	$aria_hidden = ' aria-hidden="true"';

	// Set ARIA.
	$aria_labelledby = '';
	if ( $args['title'] && $args['desc'] ) {
		$aria_labelledby = ' aria-labelledby="title-ID desc-ID"';
		$aria_hidden = '';
	}

	// Begin SVG markup.
	$svg = '<svg class="icon icon-' . esc_attr( $args['icon'] ) . '"' . $aria_hidden . $aria_labelledby . ' role="img">';

	// Add title markup.
	$svg .= '<title>' . esc_html( $title ) . '</title>';

	// If there is a description, display it.
	if ( $args['desc'] ) {
		$svg .= '<desc>' . esc_html( $args['desc'] ) . '</desc>';
	}

	// Use absolute path in the Customizer so that icons show up in there.
	if ( is_customize_preview() ) {
		$svg .= '<use xlink:href="' . get_parent_theme_file_uri( '/assets/images/svg-icons.svg#icon-' . esc_html( $args['icon'] ) ) . '"></use>';
	} else {
		$svg .= '<use xlink:href="#icon-' . esc_html( $args['icon'] ) . '"></use>';
	}

	$svg .= '</svg>';

	return $svg;
}

/**
 * Trim the title length.
 *
 * @param array $args Parameters include length and more.
 * @return string        The shortened excerpt.
 */
function me_rb4_get_the_title( $args = array() ) {

	// Set defaults.
	$defaults = array(
		'length'  => 12,
		'more'    => '...',
	);

	// Parse args.
	$args = wp_parse_args( $args, $defaults );

	// Trim the title.
	return wp_trim_words( get_the_title( get_the_ID() ), $args['length'], $args['more'] );
}

/**
 * Customize "Read More" string on <!-- more --> with the_content();
 */
function me_rb4_content_more_link() {
	return ' <a class="more-link" href="' . get_permalink() . '">' . esc_html__( 'Read More', 'me-rb4' ) . '...</a>';
}
add_filter( 'the_content_more_link', 'me_rb4_content_more_link' );

/**
 * Customize the [...] on the_excerpt();
 *
 * @param string $more The current $more string.
 * @return string Replace with "Read More..."
 */
function me_rb4_excerpt_more( $more ) {
	return sprintf( ' <a class="more-link" href="%1$s">%2$s</a>', get_permalink( get_the_ID() ), esc_html__( 'Read more...', 'me-rb4' ) );
}
add_filter( 'excerpt_more', 'me_rb4_excerpt_more' );

/**
 * Limit the excerpt length.
 *
 * @param array $args Parameters include length and more.
 * @return string The shortened excerpt.
 */
function me_rb4_get_the_excerpt( $args = array() ) {

	// Set defaults.
	$defaults = array(
		'length' => 20,
		'more'   => '...',
	);

	// Parse args.
	$args = wp_parse_args( $args, $defaults );

	// Trim the excerpt.
	return wp_trim_words( get_the_excerpt(), absint( $args['length'] ), esc_html( $args['more'] ) );
}

/**
 * Echo an image, no matter what.
 *
 * @param string $size The image size you want to display.
 */
function me_rb4_get_post_image( $size = 'thumbnail' ) {

	// If featured image is present, use that.
	if ( has_post_thumbnail() ) {
		return the_post_thumbnail( $size );
	}

	// Check for any attached image.
	$media = get_attached_media( 'image', get_the_ID() );
	$media = current( $media );

	// Set up default image path.
	$media_url = get_stylesheet_directory_uri() . '/assets/images/placeholder.png';

	// If an image is present, then use it.
	if ( is_array( $media ) && 0 < count( $media ) ) {
		$media_url = ( 'thumbnail' === $size ) ? wp_get_attachment_thumb_url( $media->ID ) : wp_get_attachment_url( $media->ID );
	}

	// Start the markup.
	ob_start(); ?>

		<img src="<?php echo esc_url( $media_url ); ?>" class="attachment-thumbnail wp-post-image" alt="<?php echo esc_html( get_the_title() ); ?>" />

	<?php
	return ob_get_clean();
}

/**
 * Return an image URI, no matter what.
 *
 * @param  string $size The image size you want to return.
 * @return string The image URI.
 */
function me_rb4_get_post_image_uri( $size = 'thumbnail' ) {

	// If featured image is present, use that.
	if ( has_post_thumbnail() ) {

		$featured_image_id = get_post_thumbnail_id( get_the_ID() );
		$media = wp_get_attachment_image_src( $featured_image_id, $size );

		if ( is_array( $media ) ) {
			return current( $media );
		}
	}

	// Check for any attached image.
	$media = get_attached_media( 'image', get_the_ID() );
	$media = current( $media );

	// Set up default image path.
	$media_url = get_stylesheet_directory_uri() . '/assets/images/placeholder.png';

	// If an image is present, then use it.
	if ( is_array( $media ) && 0 < count( $media ) ) {
		$media_url = ( 'thumbnail' === $size ) ? wp_get_attachment_thumb_url( $media->ID ) : wp_get_attachment_url( $media->ID );
	}

	return $media_url;
}

/**
 * Get an attachment ID from it's URL.
 *
 * @param string $attachment_url The URL of the attachment.
 * @return int The attachment ID.
 */
function me_rb4_get_attachment_id_from_url( $attachment_url = '' ) {

	global $wpdb;

	$attachment_id = false;

	// If there is no url, return.
	if ( '' === $attachment_url ) {
		return false;
	}

	// Get the upload directory paths.
	$upload_dir_paths = wp_upload_dir();

	// Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image.
	if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {

		// If this is the URL of an auto-generated thumbnail, get the URL of the original image.
		$attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );

		// Remove the upload path base directory from the attachment URL.
		$attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $attachment_url );

		// Do something with $result.
		$attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url ) ); // WPCS: db call ok , cache ok.
	}

	return $attachment_id;
}

/**
 * Echo the copyright text saved in the Customizer.
 */
function me_rb4_get_copyright_text() {

	// Grab our customizer settings.
	$copyright_text = get_theme_mod( 'me_rb4_copyright_text' );

	// Stop if there's nothing to display.
	if ( ! $copyright_text ) {
		return false;
	}

	// Echo the text.
	echo '<span class="copyright-text">' . wp_kses_post( $copyright_text ) . '</span>';
}

/**
 * Build social sharing icons.
 *
 * @return string
 */
function me_rb4_get_social_share() {

	// Build the sharing URLs.
	$twitter_url  = 'https://twitter.com/share?text=' . rawurlencode( html_entity_decode( get_the_title() ) ) . '&amp;url=' . rawurlencode( get_the_permalink() );
	$facebook_url = 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( get_the_permalink() );
	$linkedin_url = 'https://www.linkedin.com/shareArticle?title=' . rawurlencode( html_entity_decode( get_the_title() ) ) . '&amp;url=' . rawurlencode( get_the_permalink() );

	// Start the markup.
	ob_start(); ?>
	<div class="social-share">
		<h5 class="social-share-title"><?php esc_html_e( 'Share This', 'me-rb4' ); ?></h5>
		<ul class="social-icons menu menu-horizontal">
			<li class="social-icon">
				<a href="<?php echo esc_url( $twitter_url ); ?>" onclick="window.open(this.href, 'targetWindow', 'toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, top=150, left=0, width=600, height=300' ); return false;">
					<?php echo me_rb4_get_svg( array( 'icon' => 'twitter-square', 'title' => 'Twitter', 'desc' => __( 'Share on Twitter', 'me-rb4' ) ) ); // WPCS: XSS ok. ?>
					<span class="screen-reader-text"><?php esc_html_e( 'Share on Twitter', 'me-rb4' ); ?></span>
				</a>
			</li>
			<li class="social-icon">
				<a href="<?php echo esc_url( $facebook_url ); ?>" onclick="window.open(this.href, 'targetWindow', 'toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, top=150, left=0, width=600, height=300' ); return false;">
					<?php echo me_rb4_get_svg( array( 'icon' => 'facebook-square', 'title' => 'Facebook', 'desc' => __( 'Share on Facebook', 'me-rb4' ) ) ); // WPCS: XSS ok. ?>
					<span class="screen-reader-text"><?php esc_html_e( 'Share on Facebook', 'me-rb4' ); ?></span>
				</a>
			</li>
			<li class="social-icon">
				<a href="<?php echo esc_url( $linkedin_url ); ?>" onclick="window.open(this.href, 'targetWindow', 'toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, top=150, left=0, width=475, height=505' ); return false;">
					<?php echo me_rb4_get_svg( array( 'icon' => 'linkedin-square', 'title' => 'LinkedIn', 'desc' => __( 'Share on LinkedIn', 'me-rb4' ) ) ); // WPCS: XSS ok. ?>
					<span class="screen-reader-text"><?php esc_html_e( 'Share on LinkedIn', 'me-rb4' ); ?></span>
				</a>
			</li>
		</ul>
	</div><!-- .social-share -->

	<?php
	return ob_get_clean();
}

/**
 * Retrieve the social links saved in the customizer
 *
 * @return mixed HTML output of social links
 */
function me_rb4_get_social_network_links() {

	// Create an array of our social links for ease of setup.
	// Change the order of the networks in this array to change the output order.
	$social_networks = array( 'facebook', 'googleplus', 'instagram', 'linkedin', 'twitter' );

	// Kickoff our output buffer.
	ob_start(); ?>

	<ul class="social-icons">
	<?php
	// Loop through our network array.
	foreach ( $social_networks as $network ) :

		// Look for the social network's URL.
		$network_url = get_theme_mod( 'me_rb4_' . $network . '_link' );

		// Only display the list item if a URL is set.
		if ( isset( $network_url ) && ! empty( $network_url ) ) : ?>
			<li class="social-icon <?php echo esc_attr( $network ); ?>">
				<a href="<?php echo esc_url( $network_url ); ?>">
					<?php echo me_rb4_get_svg( array( 'icon' => $network . '-square', 'title' => sprintf( __( 'Link to %s', 'me-rb4' ), ucwords( esc_html( $network ) ) ) ) ); // WPCS: XSS ok. ?>
					<span class="screen-reader-text"><?php echo sprintf( __( 'Link to %s', 'me-rb4' ), ucwords( esc_html( $network ) ) ); // WPCS: XSS ok. ?></span>
				</a>
			</li><!-- .social-icon -->
		<?php endif;
	endforeach; ?>
	</ul><!-- .social-icons -->

	<?php
	return ob_get_clean();
}

/**
 * Add Social Links to Footer Menu
 *
 * @param array $items get navigation.
 * @param array $args get navigation.
 * @return string
 * @author jomurgel
 */
function me_rb4_add_social_to_nav( $items, $args ) {

	if ( ( 'footer' === $args->theme_location ) ) {

		// Set an array of social networks.
		$social_networks = array( 'facebook','twitter','instagram' );

		// facebook.
		$social_networks[0] = '<li class="social ' . esc_attr( $social_networks[0] ) . '"><a href="' . esc_url( get_theme_mod( 'me_rb4_' . $social_networks[0] . '_link' ) ) . '" target="_blank">' . me_rb4_get_svg( array( 'icon' => $social_networks[0] . '-square', 'title' => $social_networks[0] . '' ) ) . '</a>';

		// twitter.
		$social_networks[1] = '<li class="social ' . esc_attr( $social_networks[1] ) . '"><a href="' . esc_url( get_theme_mod( 'me_rb4_' . $social_networks[1] . '_link' ) ) . '" target="_blank">' . me_rb4_get_svg( array( 'icon' => $social_networks[1] . '-square', 'title' => $social_networks[1] . '' ) ) . '</a>';

		// instagram.
		$social_networks[2] = '<li class="social ' . esc_attr( $social_networks[2] ) . '"><a href="' . esc_url( get_theme_mod( 'me_rb4_' . $social_networks[2] . '_link' ) ) . '" target="_blank">' . me_rb4_get_svg( array( 'icon' => $social_networks[2] . '-square', 'title' => $social_networks[2] . '' ) ) . '</a>';

		// add menu itesm before menu.
		return $social_networks[0] . $social_networks[1] . $social_networks[2] . $items;
	} else {
		return $items;
	}
}
add_filter( 'wp_nav_menu_items', 'me_rb4_add_social_to_nav', 10, 2 );


/**
 * Make ACF Seasonal Messages Header.
 *
 * @return  string      Markup for the event slider slide
 *
 * @author jomurgel
 */
function me_rb4_seasonal_messages_header() {

	// Large Message ACF Fields.
	$large_title = get_field( 'large_title' );
	$large_text = get_field( 'large_text' );
	$large_link = get_field( 'large_link' );
	$btn_text = get_field( 'button_text' );
	$large_image_array = get_field( 'large_image' );
	$large_image_size = $large_image_array['sizes'];
	$large_image_url = $large_image_size['seasonal-large'];

	// Medium Message ACF Fields.
	$medium_title = get_field( 'medium_title' );
	$medium_link = get_field( 'medium_link' );
	$medium_link_text = get_field( 'medium_link_text' );
	$medium_link_text = $medium_link_text . " »";
	$medium_image_array = get_field( 'medium_image' );
	$medium_image_size = $medium_image_array['sizes'];
	$medium_image_url = $medium_image_size['seasonal-medium'];

	// Small Message ACF Fields.
	$small_text = get_field( 'small_text' );
	$small_link = get_field( 'small_link' );

	ob_start(); ?>

	<div class="main-page-seasonal-messages">
		<div class="seasonal-left">
			<a href="<?php echo esc_url( $large_link ); ?>" class="seasonal-message message-large image-as-background" style="background-image: url( <?php echo esc_url( $large_image_url ); ?> );">
				<div class="seasonal-content">
					<h2 class="large"><?php echo esc_html( $large_title ); ?></h2>
				</div><!-- .seasonal-content -->
				<div class="float-to-bottom right">
					<div class="button button-teal">
						<?php echo esc_html( $btn_text ); ?>
					</div><!-- .button .button-teal -->
					<?php echo wp_kses_post( $large_text ); ?>
				</div><!-- .float-to-bottom -->
			</a><!-- .seasonal-message .message-large -->
		</div><!-- .seasonal-left -->

		<div class="seasonal-right">
			<a href="<?php echo esc_url( $medium_link ); ?>" class="seasonal-message message-medium image-as-background" style="background-image: url( <?php echo esc_url( $medium_image_url ); ?> );">
				<div class="float-to-bottom left">
					<h2><?php echo esc_html( $medium_title ); ?></h2>
					<?php echo esc_html( $medium_link_text ); ?>
				</div><!-- .float-to-bottom -->
			</a><!-- .seasonal-message .message-medium -->

			<div class="seasonal-message message-small">
				<div claass="seasonal-content">
					<?php echo wp_kses_post( $small_text ); ?>
				</div><!-- .seasonal-content -->
			</div><!-- .seasonal-message .message-small -->
		</div><!-- .seasonal-right -->
	</div><!-- .main-page-seasonal-messages -->

	<?php
	return ob_get_clean();
}

/**
 * Remove "Protected" from title on Password Protected pages.
 *
 * @param  string $title get title.
 * @return string.
 */
function me_rb4_remove_protected_in_title( $title ) {
	return '%s';
}
add_filter( 'protected_title_format', 'me_rb4_remove_protected_in_title' );

/**
 * List page children of post_parent.
 *
 * @return list of children.
 */
function me_rb4_list_child_pages() {

	// global post var.
	global $post;
	$string = '';

	if ( is_page() && $post->post_parent ) :
		$childpages = wp_list_pages( 'sort_column=menu_order&title_li=&child_of=' . $post->post_parent . '&echo=0' );
	else :
		$childpages = wp_list_pages( 'sort_column=menu_order&title_li=&child_of=' . $post->ID . '&echo=0' );
	endif;

	if ( $childpages ) {
		$string = '<ul>' . '<li class="header"><a href="' . get_the_permalink( $post->post_parent ) . '">' . get_the_title( $post->post_parent ) . ':</a></li>' . $childpages . '</ul>';
	}
	return $string;
}


/**
 * Google Analytics in Footer
 */
function me_rb4_get_google_analytics_code() {

	// Grab our customizer settings.
	$analytics_code = get_theme_mod( 'me_rb4_analytics_section' );

	// Stop if there's nothing to display.
	if ( ! $analytics_code ) {
		return false;
	}

	$ga = '';
	$ga .= '' ?>
	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

		ga('create', '<?php echo esc_html( $analytics_code ); ?>', 'auto');
		ga('send', 'pageview');
	</script>
	<?php

	return $ga;
}
