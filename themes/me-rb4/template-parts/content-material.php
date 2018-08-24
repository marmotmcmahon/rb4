<?php
/**
 * Template part for displaying page content in front-page.php
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Read by 4th
 */

// ACF Fields
$price = get_field( 'price' );
$limitations = get_field( 'limitations' );
$additional = get_field( 'additional_notes' );
$type = get_field( 'product_type' );
$apply = get_field( 'apply' );
$download = get_field( 'download' );
$purchase = get_field( 'purchase' );
$request = get_field( 'request' );
$term_array = get_the_terms( get_the_ID(), 'material-type' )[0];
$term = $term_array->slug;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-material-container' ); ?>>

	<div class="material-title">
		<div class="back-to-stuff">
			<a href="/fun-stuff">
				<?php echo esc_html( 'Back to Fun Stuff' ); ?>
			</a>
		</div><!-- .back-to-stuff -->
		<h1><?php the_title(); ?></h1>
	</div><!-- .material-title -->

	<div class="material-focus">
		<?php the_content(); ?>
	</div><!-- .material-focus -->

	<div class="material-sidebar">
		<?php if ( $limitations ) : ?>
			<h3><?php echo esc_html( 'Limitations' ); ?></h3>
			<?php echo esc_html( $limitations ); ?><br/><br/>
		<?php endif; ?>

		<?php if ( $additional ) : ?>
			<h3><?php echo esc_html( 'Additional Notes' ); ?></h3>
			<?php echo esc_html( $additional ); ?><br/><br/>
		<?php endif; ?>

		<?php if ( 'download' === $term ) : ?>
			<a href="<?php echo esc_url( $download ); ?>" class="button button-violet">
				<?php echo esc_html( $type['label'] ); ?>
			</a>
		<?php endif; ?>
	</div><!-- .material-sidebar -->

</article><!-- #post-## -->
