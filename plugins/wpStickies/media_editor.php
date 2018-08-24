<?php

	$args = array(
		'post_type' => 'attachment',
		'post_mime_type' => 'image',
		'posts_per_page' => 100,
		'post_status' => 'any',
	);

	if(!empty($_GET['post'])) {
		$args['post_parent'] = (int) $_GET['post'];
	}

	if(!empty($_GET['own'])) {
		$args['author'] = get_current_user_id();
	}

	if(!empty($_GET['terms'])) {
		$args['s'] = $_GET['terms'];
	}

	$query = new WP_Query( $args );
?>
<div class="wrap">

	<!-- Page title -->
	<h2>
		<?php _e('My Media', WPS_TEXTDOMAIN) ?>
		<a href="?page=wpstickies" class="add-new-h2"><?php _e('Back to the list', WPS_TEXTDOMAIN) ?></a>
	</h2>

	<!-- Main menu -->
	<form action="admin.php?page=wpstickies-media-editor" method="get" id="wps-main-nav-bar" class="wps-media-editor">
		<?php if(empty($_GET['post'])) : ?>
		<input type="hidden" name="page" value="wpstickies-media-editor">
		<label><input type="checkbox" name="own" <?php echo isset($_GET['own']) ? 'checked="checked"' : '' ?>> <?php _e('Show my uploads only', WPS_TEXTDOMAIN) ?></label>

		<button type="submit" class="button"><?php _e('Update', WPS_TEXTDOMAIN); ?></button>
		<input type="search" name="terms" class="search" placeholder="Search" value="<?php echo !empty($_GET['terms']) ? $_GET['terms'] : '' ?>">
		<?php else : ?>
		<?php
			$post = get_post( (int) $_GET['post'] );
		?>
			<h4><?php _e('Listing images used in post', WPS_TEXTDOMAIN) ?> "<strong><?php echo $post->post_title ?></strong>"</h4>
			<a href="<?php echo get_edit_post_link((int) $_GET['post']) ?>" class="unselectable"><?php _e('Open post in editor', WPS_TEXTDOMAIN) ?><i class="dashicons dashicons-arrow-right-alt"></i></a>
			<a href="admin.php?page=wpstickies-media-editor" class="unselectable"><?php _e('Show all media', WPS_TEXTDOMAIN) ?></a>
		<?php endif ?>
	</form>

	<!-- Media List -->
	<div id="wps-media-editor">
		<?php if($query->have_posts()) : ?>
		<?php while($query->have_posts()) : $query->the_post(); ?>
		<div class="item">
			<a href="<?php echo wp_get_attachment_url($query->post->ID) ?>" class="wps-preview">
				<span class="dashicons dashicons-edit"></span>
				<?php echo wp_get_attachment_image($query->post->ID) ?>
			</a>
			<ul>
				<li><?php echo $query->post->post_title ?></li>
				<li><?php echo get_the_date(null, $query->post->ID) ?></li>
				<li>by <?php the_author_meta('user_nicename', $query->post->post_author); ?></li>
			</ul>
		</div>
		<?php endwhile ?>
		<?php else : ?>
		<?php _e('Sorry, no uploads found.', WPS_TEXTDOMAIN) ?>
		<?php endif ?>
	</div>
</div>
