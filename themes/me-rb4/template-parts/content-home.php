<?php
/**
 * Template part for displaying page content in front-page.php
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Read by 4th
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php // Get Flip Cards.
	echo me_rb4_get_flip_cards_homepage(); // WPCS: XSS ok. ?>

	<?php // Get Learning Map.
	echo me_rb4_get_learning_map(); // WPCS: XSS ok. ?>

	<?php // Start Resources Masonry Block.
	echo me_rb4_get_resources_block(); // WPCS: XSS ok. ?>

	<?php // Get Infographic. Added back for new infographic
	echo me_rb4_get_infographic(); // WPCS: XSS ok. ?>

</article><!-- #post-## -->
