<?php
/**
 * RB4 Show Seasonal Block Rb4
 *
 * @since 0.0.1
 * @package RB4 Show Seasonal Block
 */

/**
 * RB4 Show Seasonal Block Rb4 class.
 *
 * @since 0.0.1
 */
class RB4PPM_Rb4 extends WP_Widget {

	/**
	 * Unique identifier for this widget.
	 *
	 * Will also serve as the widget class.
	 *
	 * @var string
	 * @since  0.0.1
	 */
	protected $widget_slug = 'rb4-per-page-messages';


	/**
	 * Widget name displayed in Widgets dashboard.
	 * Set in __construct since __() shouldn't take a variable.
	 *
	 * @var string
	 * @since  0.0.1
	 */
	protected $widget_name = '';

	/**
	 * Shortcode name for this widget
	 *
	 * @var string
	 * @since  0.0.1
	 */
	protected static $shortcode = 'rb4-per-page-messages';


	/**
	 * Construct widget class.
	 *
	 * @since  0.0.1
	 * @return void
	 */
	public function __construct() {

		$this->widget_name          = esc_html__( 'RB4 Per Page Messages', 'rb4-per-page-messages' );

		parent::__construct(
			$this->widget_slug,
			$this->widget_name,
			array(
				'classname'   => $this->widget_slug,
				'description' => esc_html__( 'ADisplay the Per Page Message Content in the Sidebar', 'rb4-per-page-messages' ),
			)
		);

		add_action( 'save_post',    array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
		add_shortcode( self::$shortcode, array( __CLASS__, 'get_widget' ) );
	}


	/**
	 * Delete this widget's cache.
	 *
	 * Note: Could also delete any transients
	 * delete_transient( 'some-transient-generated-by-this-widget' );
	 *
	 * @since  0.0.1
	 * @return void
	 */
	public function flush_widget_cache() {
		wp_cache_delete( $this->widget_slug, 'widget' );
	}


	/**
	 * Front-end display of widget.
	 *
	 * @since  0.0.1
	 * @param  array $args     The widget arguments set up when a sidebar is registered.
	 * @param  array $instance The widget settings as set by user.
	 * @return void
	 */
	public function widget( $args, $instance ) {
		include( 'messages-template.php' );
	}


	/**
	 * Update form values as they are saved.
	 *
	 * @since  0.0.1
	 * @param  array $new_instance New settings for this instance as input by the user.
	 * @param  array $old_instance Old settings for this instance.
	 * @return array               Settings to save or bool false to cancel saving.
	 */
	public function update( $new_instance, $old_instance ) {

		// Previously saved values.
		$instance = $old_instance;

		// Sanitize text before saving to database.
		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['text'] = force_balance_tags( $new_instance['text'] );
		} else {
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes( $new_instance['text'] ) ) );
		}

		// Flush cache.
		$this->flush_widget_cache();

		return $instance;
	}


	/**
	 * Back-end widget form with defaults.
	 *
	 * @since  0.0.1
	 * @param  array $instance Current settings.
	 * @return void
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance,
			array(
				'text'  => '',
			)
		);

		// edit post link vars.
		$context = 'here';
		$id = get_option( 'page_on_front' );

		?>
		<p>
			<?php echo wp_kses_post( 'This displays the Per Page ACF data if the page has data.' ); ?>
		</p>
		<?php
	}
}


/**
 * Register this widget with WordPress. Can also move this function to the parent plugin.
 *
 * @since  0.0.1
 * @return void
 */
function rb4_per_page_messages_register_rb4() {
	register_widget( 'RB4PPM_Rb4' );
}
add_action( 'widgets_init', 'rb4_per_page_messages_register_rb4' );
