<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Read by 4th
 */

?>

<article <?php post_class( 'blog-post-article' ); ?>>
	<header class="entry-header">
		<?php
		$category = get_the_category($post->ID);
		$counter = 0;
		if ( is_single() ) :
			foreach ($category as $single_category) {
				if ($counter == 0) {
					echo '<h1>' . $single_category->name . '</h1>';
					$counter += 1;
				}
			}
			the_title( '<h2 class="entry-title">', '</h2>' );
		else :
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		endif;
		if ( 'post' === get_post_type() ) : ?>
		<div class="entry-meta">
			<?php me_rb4_posted_on(); ?>
		</div><!-- .entry-meta -->
		<?php
		endif; ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php if ( has_post_thumbnail() && ! is_single() ) {
			echo me_rb4_get_post_image( 'thumbnail' ); // WPCS:  XSS ok.
		} ?>

		<?php if (! is_single()) {
			echo me_rb4_get_the_excerpt( array( // WPCS: XSS ok.
				'length' => '20',
				'more' => '...',
			) );
		} else {
			the_content();
		} ?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php me_rb4_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
