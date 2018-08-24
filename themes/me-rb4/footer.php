<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Read by 4th
 */

?>

	</div><!-- #content -->

	<?php if ( is_page_template( 'template-juicer.php' ) ) : ?>
		<div class="juicer-container">
			<div class="wrap">
				<?php if ( get_field( 'juicer_title' ) ) : ?>
					<h2><?php the_field( 'juicer_title' ); ?>
				<?php endif; ?>

				<?php if ( function_exists( 'juicer_feed' ) ) :
					juicer_feed( 'name=reading' );
				endif; ?>
			</div><!-- .wrap -->
		</div><!-- .juicer-container -->
	<?php endif; ?>

	<footer class="site-footer">

		<nav id="footer-navigation" class="footer-navigation">
			<?php
				wp_nav_menu( array(
					'theme_location' => 'footer',
					'menu_id'        => 'footer-menu',
					'menu_class'     => 'menu',
					'container'      => false,
				) );
			?>
		</nav><!-- #site-navigation -->

		<div class="site-info">
			<?php echo wp_kses_post( bloginfo( 'name' ) . '. Copyright ' . date( 'Y' ) . '. ' . 'Site by <a href="http://themightyengine.com">Mighty Engine</a>' ); ?>
		</div>

	</footer><!-- .site-footer -->
</div><!-- #page -->

<?php wp_footer(); echo me_rb4_get_google_analytics_code(); ?>

</body>
</html>
