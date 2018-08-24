<?php

/*
Plugin Name: wpStickies
Plugin URI: http://codecanyon.net/user/kreatura/
Description: Premium Image Tagging Plugin for WordPress
Version: 2.1.4
Author: Kreatura Media
Author URI: https://kreaturamedia.com/
Text Domain: wpStickies
*/

if(defined('WPS_PLUGIN_VERSION') || isset($GLOBALS['wpsPluginPath'])) {
	die('ERROR: It looks like you already have one instance of wpStickies installed. WordPress cannot activate and handle two instance at the same time, you need to remove the old version first.');
}


/********************************************************/
/*                        Actions                       */
/********************************************************/

	if(!defined('ABSPATH')) {
		header('HTTP/1.0 403 Forbidden');
		exit;
	}

	// Legacy, will be dropped
	$GLOBALS['wpsPluginPath'] = plugins_url('', __FILE__);
	$GLOBALS['wpsAutoUpdateBox'] = true;

	// Constants
	define('WPS_ROOT_FILE', __FILE__);
	define('WPS_ROOT_PATH', dirname(__FILE__));
	define('WPS_ROOT_URL', plugins_url('', __FILE__));
	define('WPS_PLUGIN_VERSION', '2.1.4');
	define('WPS_PLUGIN_SLUG', basename(dirname(__FILE__)));
	define('WPS_PLUGIN_BASE', plugin_basename(__FILE__));
	define('WPS_DATABASE_VERSION', '1.0');
	define('WPS_DB_TABLE', 'wpstickies');
	define('WPS_DB_IMAGES_TABLE', 'wpstickies_images');
	define('WPS_TEXTDOMAIN', 'wpStickies');

	if(!defined('NL')) { define("NL", "\r\n"); }
	if(!defined('TAB')) { define("TAB", "\t"); }

	// Activation hook for creating the initial DB table
	register_activation_hook(__FILE__, 'wpstickes_activation_scripts');

	// Run activation scripts when adding new sites to a multisite installation
	add_action('wpmu_new_blog', 'wpstickes_new_site');

	// Auto update
	if(!class_exists('KM_PluginUpdates')) {
		require_once WPS_ROOT_PATH.'/classes/class.km.autoupdate.plugins.php';
	}

	if(get_option('wpstickies-validated', '0')) {
		new KM_PluginUpdates(array(
			'root' => WPS_ROOT_FILE,
			'version' => WPS_PLUGIN_VERSION,
			'repo' => 'https://updates.kreaturamedia.com/plugins/',
			'channel' => get_option('wpstickies-release-channel', 'stable'),
			'license' => get_option('wpstickies-purchase-code', '')
		));
	}

	// Update notice
	if(strpos($_SERVER['REQUEST_URI'], '?page=wpstickies') !== false) {
		add_action('admin_notices', 'wpstickies_update_notice');
	}

	// Get current page
	global $pagenow;
	if($pagenow == 'plugins.php' || $pagenow == 'index.php' || strpos($_SERVER['REQUEST_URI'], '?page=wpstickies') !== false) {
		if(get_option('wps-permission-notice') === false) {
			add_action('admin_notices', 'wpstickies_permission_notice');
		}
	}

	// Include wpStikcies DB abstraction class
	require_once WPS_ROOT_PATH.'/classes/class.wps.db.php';



	// Register custom settings menu
	add_action('admin_menu', 'wpstickies_settings_menu');

	// Link content resources
	add_action('wp_enqueue_scripts', 'wpstickies_enqueue_content_res');

	// Link admin resources
	add_action('admin_enqueue_scripts', 'wpstickies_enqueue_admin_res');

	// Init wpStickies
	add_action('wp_head', 'wpstickies_js');

	// Help menu
	add_filter('contextual_help', 'wpstickies_help', 10, 3);
	add_action('add_meta_boxes', 'wpstickies_add_meta_boxes');

	// Load plugin locale
	add_action('plugins_loaded', 'wpstickes_load_lang');

	// Admin AJAX actions
	add_action('wp_ajax_wpstickies_accept', 'wpstickies_accept');
	add_action('wp_ajax_wpstickies_reject', 'wpstickies_reject');
	add_action('wp_ajax_wpstickies_restore', 'wpstickies_restore');
	add_action('wp_ajax_wpstickies_delete', 'wpstickies_delete');
	add_action('wp_ajax_wps_media_editor', 'wps_media_editor');

	// Front-end AJAX actions
	add_action('wp_ajax_wpstickies_insert', 'wpstickies_insert');
	add_action('wp_ajax_nopriv_wpstickies_insert', 'wpstickies_insert');

	add_action('wp_ajax_wpstickies_update', 'wpstickies_update');
	add_action('wp_ajax_nopriv_wpstickies_update', 'wpstickies_update');

	add_action('wp_ajax_wpstickies_get', 'wpstickies_get');
	add_action('wp_ajax_nopriv_wpstickies_get', 'wpstickies_get');

	add_action('wp_ajax_wpstickies_remove', 'wpstickies_remove');
	add_action('wp_ajax_nopriv_wpstickies_remove', 'wpstickies_remove');

	add_action('wp_ajax_wpstickies_image_settings', 'wpstickies_image_settings');
	add_action('wp_ajax_nopriv_wpstickies_image_settings', 'wpstickies_image_settings');


/********************************************************/
/*                   wpStickies locale                  */
/********************************************************/
function wpstickes_load_lang() {
	load_plugin_textdomain('wpStickies', false, WPS_PLUGIN_SLUG . '/locales/' );
}

/********************************************************/
/*             wpStickies activation scripts            */
/********************************************************/

function wpstickes_activation_scripts() {

	// Multi-site
	if(is_multisite()) {

		// Get WPDB Object
		global $wpdb;

		// Get current site
		$old_site = $wpdb->blogid;

		// Get all sites
		$sites = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

		// Iterate over the sites
		foreach($sites as $site) {
			switch_to_blog($site);
			wpstickes_create_db_tables();
		}

		// Switch back the old site
		switch_to_blog($old_site);

	// Single-site
	} else {
		wpstickes_create_db_tables();
	}
}


/********************************************************/
/*            wpStickies new site activation            */
/********************************************************/

function wpstickes_new_site($blog_id) {

    // Get WPDB Object
    global $wpdb;

    // Get current site
	$old_site = $wpdb->blogid;

	// Switch to new site
	switch_to_blog($blog_id);

	// Run activation scripts
	wpstickes_create_db_tables();

	// Switch back the old site
	switch_to_blog($old_site);

}


/********************************************************/
/*                 Activation Scripts                   */
/********************************************************/
function wpstickes_create_db_tables() {

	// Create a new role for users who has capability to
	// manage and create stickes
	add_role( 'wpstickiesadmins', 'wpStickies Admins', array(
		'read' => true, 'level_0' => true, 'edit-wpstickies' => true
	));

	// Get WPDB Object and WP Stickies DB version
	global $wpdb;
	$charset_collate = '';
	$table_name = $wpdb->prefix.WPS_DB_TABLE;

	// Get DB collate
	if(!empty($wpdb->charset)) {
		$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
	}

	if(!empty($wpdb->collate)) {
		$charset_collate .= " COLLATE $wpdb->collate";
	}

	// Building the query
	$sql = "CREATE TABLE $table_name (
				id INT(12) NOT NULL AUTO_INCREMENT,
				image VARCHAR(190) NOT NULL,
				image_original VARCHAR(190) NOT NULL,
				data TEXT NOT NULL,
				user_id INT(12) NOT NULL,
				user_name VARCHAR(50) NOT NULL,
				date_c INT(10) NOT NULL,
				date_m INT(10) NOT NULL,
				flag_hidden TINYINT(1) NOT NULL DEFAULT  1,
				flag_deleted TINYINT(1) NOT NULL DEFAULT  0,
				PRIMARY KEY  (id)
			) $charset_collate;";

	// Table name
	$table_name = $wpdb->prefix.WPS_DB_IMAGES_TABLE;

	// Building the query for images settings
	$sql2 = "CREATE TABLE $table_name (
				id INT(12) NOT NULL AUTO_INCREMENT,
				image VARCHAR(190) NOT NULL,
				image_original VARCHAR(190) NOT NULL,
				data TEXT NOT NULL,
				date_c INT(10) NOT NULL,
				date_m INT(10) NOT NULL,
				PRIMARY KEY  (id),
				UNIQUE KEY image (image)
			) $charset_collate;";

	// Executing the query
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
	dbDelta($sql2);

	// Save DB version
	update_option('wpstickies-db-version', WPS_DATABASE_VERSION);
	update_option('wpstickies-plugin-version', WPS_PLUGIN_VERSION);
	update_option('wps-permission-notice', 1);
}

function wpstickies_update_notice() {

	// Get plugin updates
	$updates = get_plugin_updates();

	// Check for update
	if(isset($updates[WPS_PLUGIN_BASE]) && isset($updates[WPS_PLUGIN_BASE]->update)) {
		$update = $updates[WPS_PLUGIN_BASE];
		add_thickbox();
		?>
		<div class="wps-notice">
			<img src="<?php echo WPS_ROOT_URL.'/img/wps_80x80.png' ?>" alt="wpStickies icon">
			<h1><?php _e('Update available for wpStickies!', WPS_TEXTDOMAIN) ?></h1>
			<p>
				<?php echo sprintf(__('You have version %1$s. Update to version %2$s.', WPS_TEXTDOMAIN), $update->Version, $update->update->new_version); ?><br>
				<i><?php echo $update->update->upgrade_notice ?></i>
				<a href="plugin-install.php?tab=plugin-information&plugin=wpStickies&section=changelog&TB_iframe=true&width=640&height=747" class="thickbox">
					<?php _e('Review changes & Install', WPS_TEXTDOMAIN) ?>
				</a>
			</p>
			<div class="clear"></div>
		</div>
		<?php
	}
}

function wpstickies_permission_notice() { ?>
	<div class="wps-notice">
		<img src="<?php echo WPS_ROOT_URL.'/img/wps_80x80.png' ?>" alt="wpStickies icon">
		<h1><?php _e('The new version of wpStickies is almost ready', WPS_TEXTDOMAIN) ?></h1>
		<p>
			<?php _e("wpStickies 2.1.0 is a huge update with lots of new features and improvements. While we have targeted 100% compatibility with older versions, unfortunately there are some areas where this was not possible. As a result, we have reseted the options dealing with user permissions, and are asking you to review your plugin settings again. If you have updated via FTP, a pluign re-activation may also be needed.", WPS_TEXTDOMAIN) ?>
			<a href="<?php echo wp_nonce_url('?page=wpstickies&wps-permission-notice=1', 'wps-permission-notice') ?>">
				<?php _e('Okay, I understand', WPS_TEXTDOMAIN) ?>
			</a>
		</p>
		<div class="clear"></div>
	</div>
<?php
}


/********************************************************/
/*               Convert to relative URLs               */
/********************************************************/
function wpstickies_convert_absolute_urls() {

	global $wpdb;

	// STICKIES
	// ---------------------
	$table_name = $wpdb->prefix.WPS_DB_TABLE;
	$stickies = $wpdb->get_results("SELECT * FROM $table_name ORDER BY date_c DESC");

	foreach($stickies as $item) {

		$url = $item->image;
		$new_url = parse_url($url, PHP_URL_PATH);

		if($url == $new_url) { continue; }

		// Save
		$wpdb->query($wpdb->prepare("UPDATE $table_name SET image = '%s'
			WHERE id = '%d' ORDER BY date_m DESC LIMIT 1",
			$new_url, $item->id
		));
	}


	// IMAGE SETTINGS
	// ---------------------
	$table_name = $wpdb->prefix.WPS_DB_IMAGES_TABLE;
	$images = $wpdb->get_results("SELECT * FROM $table_name ORDER BY date_c DESC");

	foreach($images as $item) {

		$url = $item->image;
		$new_url = parse_url($url, PHP_URL_PATH);

		if($url == $new_url) { continue; }

		// Save
		$wpdb->query($wpdb->prepare("UPDATE $table_name SET image = '%s'
			WHERE id = '%d' ORDER BY date_m DESC LIMIT 1",
			$new_url, $item->id
		));
	}

}

/********************************************************/
/*               Convert to absolute URLs               */
/********************************************************/
function wpstickies_convert_relative_urls() {

	global $wpdb;


	// STICKIES
	// ---------------------
	$table_name = $wpdb->prefix.WPS_DB_TABLE;
	$stickies = $wpdb->get_results("SELECT * FROM $table_name ORDER BY date_c DESC");

	foreach($stickies as $item) {

		// Get url
		$url = $item->image_original;

		// Save
		$wpdb->query($wpdb->prepare("UPDATE $table_name SET image = '%s'
			WHERE id = '%d' ORDER BY date_m DESC LIMIT 1",
			$url, $item->id
		));
	}


	// IMAGE SETTINGS
	// ---------------------
	$table_name = $wpdb->prefix.WPS_DB_IMAGES_TABLE;
	$images = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY date_c DESC" );

	foreach($images as $item) {

		// Get url
		$url = $item->image_original;

		// Save
		$wpdb->query($wpdb->prepare("UPDATE $table_name SET image = '%s'
			WHERE id = '%d' ORDER BY date_m DESC LIMIT 1",
			$url, $item->id
		));
	}

}

/********************************************************/
/*                 Check purchase code                  */
/********************************************************/
add_action('wp_ajax_wpstickies_verify_purchase_code', 'wpstickies_verify_purchase_code');
function wpstickies_verify_purchase_code() {
	if(check_admin_referer('wps-save-autoupdate-settings')) {

		// Get options and test user permissions
		$options = WPS_DB::getPluginSettings();
		if(!current_user_can('manage_options') && !isset($options['allow_settings_change'])) {
			die(json_encode(array('success' => false, 'message' => __("You don't have permission to change plugin settings!", WPS_TEXTDOMAIN))));
		}

		// Get data
		global $wp_version;
		$code = trim($_POST['purchase_code']);
		$oldCode = get_option('wpstickies-purchase-code', '');
		$validated = get_option('wpstickies-validated', '0');
		$channel = ($_POST['channel'] === 'beta') ? 'beta' : 'stable';

		// Save release channel
		update_option('wpstickies-release-channel', $channel);

		// Release channel
		if($validated == 1) {
			if(!preg_match('/^[a-z0-9]+$/i', $code[0]) || $oldCode == $code) {
				die(json_encode(array('success' => true, 'message' => __('Your settings were successfully saved.', WPS_TEXTDOMAIN) . '<a href="update-core.php">' . __('Check for update', WPS_TEXTDOMAIN) . '</a>' )));
			}
		}

		// Save purchase code
		update_option('wpstickies-purchase-code', $code);

		// Verify license
		$response = wp_remote_post('https://activate.kreaturamedia.com/', array(
			'user-agent' => 'WordPress/'.$wp_version.'; '.get_bloginfo('url'),
			'body' => array(
				'plugin' => urlencode('wpStickies'),
				'license' => urlencode($code)
			)
		));


		if($response['body'] == 'valid') {
			update_option('wpstickies-validated', '1');
			die(json_encode(array('success' => true, 'message' => __('Thank you for purchasing wpStickies.', WPS_TEXTDOMAIN) . '<a href="update-core.php">' . __('Check for update', WPS_TEXTDOMAIN) . '</a>')));

		// Invalid
		} else {
			update_option('wpstickies-validated', '0');
			die(json_encode(array('success' => false, 'message' => __("Your purchase code doesn't appear to be valid.", WPS_TEXTDOMAIN))));
		}
	}
}


/********************************************************/
/*               Enqueue Content Scripts                */
/********************************************************/

	function wpstickies_enqueue_content_res() {

		// Include in the footer?
		$footer = get_option('wps_include_at_footer', false) ? true : false;

		// Use Gogole CDN version of jQuery
		if(get_option('wps_use_custom_jquery', false)) {
			wp_deregister_script('jquery');
			wp_enqueue_script('jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js', false, '1.8.3');
		}

		wp_enqueue_script('wpstickies_js', WPS_ROOT_URL.'/js/wpstickies.kreaturamedia.jquery.js', array('jquery'), WPS_PLUGIN_VERSION, $footer );
		wp_localize_script( 'wpstickies_js', 'WPStickies', array( 'ajaxurl' => admin_url('admin-ajax.php') ) );

		wp_enqueue_script('jquery_easing', WPS_ROOT_URL.'/js/jquery-easing-1.3.js', array('jquery'), '1.3.0', $footer );
		wp_enqueue_style('wpstickies_css', WPS_ROOT_URL.'/css/wpstickies.css', false, WPS_PLUGIN_VERSION );

		// User CSS
		$uploads = wp_upload_dir();
		if(file_exists($uploads['basedir'].'/wpstickies.custom.css')) {
			wp_enqueue_style('wps-user', $uploads['baseurl'].'/wpstickies.custom.css', false, WPS_PLUGIN_VERSION );
		}
	}


/********************************************************/
/*                Enqueue Admin Scripts                 */
/********************************************************/

	function wpstickies_enqueue_admin_res() {

		// Use Gogole CDN version of jQuery
		if(get_option('wps_use_custom_jquery', false)) {
			wp_deregister_script('jquery');
			wp_enqueue_script('jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js', false, '1.8.3');
		}

		// Global CSS
		wp_enqueue_style('wpstickies-global', WPS_ROOT_URL.'/css/global.css', array(), WPS_PLUGIN_VERSION );

		// Main wpStickies admin page
		if(strpos($_SERVER['REQUEST_URI'], 'wpstickies')) {

			// Preview
			include WPS_ROOT_PATH . '/init.php';
			$selector = empty($options['selector']) ? 'img[class*="wp-image"],.wpstickies' : stripslashes($options['selector']);

			wp_enqueue_script('thickbox');
			wp_enqueue_style('thickbox');

			wp_register_script('wpstickies_admin', WPS_ROOT_URL.'/js/admin.js', array('jquery'), WPS_PLUGIN_VERSION );
			wp_localize_script('wpstickies_admin', 'wpsGlobals', array('selector' => $selector, 'json' => $json));
			wp_enqueue_script('wpstickies_admin');


			wp_enqueue_style('wpstickies_admin', WPS_ROOT_URL.'/css/admin.css', array(), WPS_PLUGIN_VERSION );
			wp_enqueue_script('tags_input_js', WPS_ROOT_URL.'/js/jquery.tagsinput.min.js', array('jquery'), WPS_PLUGIN_VERSION );
			wp_enqueue_style('tags_input_css', WPS_ROOT_URL.'/css/jquery.tagsinput.css', array(), WPS_PLUGIN_VERSION );

			wp_enqueue_script('wpstickies_js', WPS_ROOT_URL.'/js/wpstickies.kreaturamedia.jquery.js', array('jquery'), WPS_PLUGIN_VERSION );
			wp_localize_script( 'wpstickies_js', 'WPStickies', array( 'ajaxurl' => admin_url('admin-ajax.php') ) );

			wp_enqueue_script('jquery_easing', WPS_ROOT_URL.'/js/jquery-easing-1.3.js', array('jquery'), '1.3.0' );
			wp_enqueue_style('wpstickies_css', WPS_ROOT_URL.'/css/wpstickies.css', false, WPS_PLUGIN_VERSION );

			// Dashicons
			if(version_compare(get_bloginfo('version'), '3.8', '<')) {
				wp_enqueue_style('dashicons', WPS_ROOT_URL.'/css/dashicons.css', false, WPS_PLUGIN_VERSION );
			}

			// User CSS
			$uploads = wp_upload_dir();
			if(file_exists($uploads['basedir'].'/wpstickies.custom.css')) {
				wp_enqueue_style('wps-user', $uploads['baseurl'].'/wpstickies.custom.css', false, WPS_PLUGIN_VERSION );
			}
		}

		// CSS editor
		if(strpos($_SERVER['REQUEST_URI'], 'wpstickies-media-editor')) {
			wp_enqueue_style('wpstickies-media-editor', WPS_ROOT_URL.'/css/media-editor.css', false, WPS_PLUGIN_VERSION );
		}

		// CSS editor
		if(strpos($_SERVER['REQUEST_URI'], 'wpstickies-css-editor')) {

			// 3rd-party: CodeMirror
			wp_enqueue_style('codemirror', WPS_ROOT_URL.'/codemirror/lib/codemirror.css', false, WPS_PLUGIN_VERSION );
			wp_enqueue_script('codemirror', WPS_ROOT_URL.'/ codemirror/lib/codemirror.js', array('jquery'), WPS_PLUGIN_VERSION );
			wp_enqueue_style('codemirror-solarized', WPS_ROOT_URL.'/codemirror/theme/solarized.mod.css', false, WPS_PLUGIN_VERSION );
			wp_enqueue_script('codemirror-syntax-css', WPS_ROOT_URL.'/codemirror/mode/css/css.js', array('jquery'), WPS_PLUGIN_VERSION );
			wp_enqueue_script('codemirror-foldcode', WPS_ROOT_URL.'/codemirror/addon/fold/foldcode.js', array('jquery'), WPS_PLUGIN_VERSION );
			wp_enqueue_script('codemirror-foldgutter', WPS_ROOT_URL.'/codemirror/addon/fold/foldgutter.js', array('jquery'), WPS_PLUGIN_VERSION );
			wp_enqueue_script('codemirror-brace-fold', WPS_ROOT_URL.'/codemirror/addon/fold/brace-fold.js', array('jquery'), WPS_PLUGIN_VERSION );
			wp_enqueue_script('codemirror-active-line', WPS_ROOT_URL.'/codemirror/addon/selection/active-line.js', array('jquery'), WPS_PLUGIN_VERSION );
		}
	}

/********************************************************/
/*                 Loads settings menu                  */
/********************************************************/
function wpstickies_settings_menu() {

	// Retrieve options
	$options = WPS_DB::getPluginSettings();
	$icon = version_compare(get_bloginfo('version'), '3.8', '>=') ? 'dashicons-tag' : WPS_ROOT_URL.'/img/icon_16x16.png';

	// Get custom capabilities
	$options['capability'] = empty($options['capability']) ? 'manage_options' : $options['capability'];

	// Create new top-level menu
	$GLOBALS['options_page'] = add_menu_page('wpStickies', 'wpStickies', $options['capability'], 'wpstickies', 'wpstickies_router', $icon);

	// Add "wpStickies" submenu
 	add_submenu_page(
		'wpstickies', 'wpStickies', __('wpStickies', WPS_TEXTDOMAIN),
		$options['capability'], 'wpstickies', 'wpstickies_router'
	);

	// Add "My Media" submenu
	add_submenu_page(
		'wpstickies', 'wpStickies Media Editor', __('My Media', WPS_TEXTDOMAIN),
		$options['capability'], 'wpstickies-media-editor', 'wpstickies_router');

	// Add "CSS Editor" submenu
	if(current_user_can('manage_options') || isset($options['allow_settings_change'])) {
		add_submenu_page(
			'wpstickies', 'wpStickies CSS Editor', __('CSS Editor', WPS_TEXTDOMAIN),
			$options['capability'], 'wpstickies-css-editor', 'wpstickies_router');
	}

	// Call register settings function
	add_action( 'admin_init', 'wpstickies_register_settings' );
}


function wpstickies_router() {

	// Get current screen details
	$screen = get_current_screen();

	if(strpos($screen->base, 'wpstickies-css-editor') !== false) {
		include(WPS_ROOT_PATH.'/css_editor.php');
	} elseif(strpos($screen->base, 'wpstickies-media-editor') !== false) {
		include(WPS_ROOT_PATH.'/media_editor.php');
	} else {
		include(WPS_ROOT_PATH.'/settings.php');
	}
}

/********************************************************/
/*                    wpStickies Help                   */
/********************************************************/
function wpstickies_help($contextual_help, $screen_id, $screen) {


	if(strstr($_SERVER['REQUEST_URI'], 'wpstickies')) {

		if(function_exists('file_get_contents')) {

			// Pages to be added
			$pages = array(
				array('title' => 'Overview', 'file' => '/docs/overview.html'),
				array('title' => 'What\'s new?', 'file' => '/docs/whatsnew.html'),
				array('title' => 'Getting started','file' => '/docs/gettingstarted.html'),
				array('title' => 'About selectors', 'file' => '/docs/selectors.html'),
				array('title' => 'Permission control', 'file' => '/docs/permissions.html'),
				array('title' => 'Language support', 'file' => '/docs/language.html'),
				array('title' => 'Other features', 'file' => '/docs/other.html'),
				array('title' => 'Plugin updates', 'file' => '/docs/updates.html'),
				array('title' => 'Troubleshooting', 'file' => '/docs/troubleshooting.html')
			);

			// Add pages
			foreach($pages as $item) {
				$screen->add_help_tab(array(
					'id' => sanitize_title($item['title']),
					'title' => $item['title'],
					'content' => file_get_contents(WPS_ROOT_PATH.$item['file'])
				));
			}

		} else {

			// Error
			$screen->add_help_tab(array(
				'id' => 'error',
				'title' => 'Error',
				'content' => 'This help section couldn\'t show you the documentation because your server don\'t support the "file_get_contents" function'
			));
		}
	}
}


function wpstickies_add_meta_boxes() {
	add_meta_box('wps-media-meta-box', 'wpStickies', 'wpstickies_add_media_meta_box', 'post', 'side');
	add_meta_box('wps-media-meta-box', 'wpStickies', 'wpstickies_add_media_meta_box', 'page', 'side');
}


function wpstickies_add_media_meta_box() {
	global $post;
	echo '<a href="admin.php?page=wpstickies-media-editor&post='.$post->ID.'" target="_blank">Tag images used in this post</a>';
}

/********************************************************/
/*                  Register settings                   */
/********************************************************/
function wpstickies_register_settings() {

	// Custom CSS editor
	if(isset($_POST['wps-user-css'])) {
		if(check_admin_referer('wps-save-user-css')) {
			wps_save_user_css();
		}
	}

	if(isset($_GET['wps-permission-notice'])) {
		if(check_admin_referer('wps-permission-notice')) {
			update_option('wps-permission-notice', 1);
			header('Location: admin.php?page=wpstickies');
			die();
		}
	}

	// Save settings
	if(isset($_POST['posted']) && strpos($_SERVER['REQUEST_URI'], 'wpstickies')) {
		if(check_admin_referer('wps-save-globals')) {

			// Retrieve options
			$options = WPS_DB::getPluginSettings();
			$newOptions = $_POST['wpstickies-options'];

			// Test user role and permission to change settings
			if(!current_user_can('manage_options') && !isset($options['allow_settings_change'])) {
				die(__("You don't have permission to change plugin settings!", WPS_TEXTDOMAIN));
			}

			// Admin capability
			$newOptions['capability'] = ($newOptions['custom_role'] == 'custom') ? $newOptions['capability'] : $newOptions['custom_role'];
			if(empty($newOptions['capability']) || !current_user_can($newOptions['capability'])) {
				$newOptions['capability'] = !empty($options['capability']) ? $options['capability'] : 'manage_options';
			}

			// Sticky capabilities
			$newOptions['create_capability'] = ($newOptions['create_role'] == 'custom') ? $newOptions['create_capability'] : $newOptions['create_role'];
			$newOptions['modify_capability'] = ($newOptions['modify_role'] == 'custom') ? $newOptions['modify_capability'] : $newOptions['modify_role'];

			// Advanced & Troubleshooting
			$checkboxes = array('wps_use_custom_jquery', 'wps_include_at_footer', 'wps_concatenate_output', 'wps_put_js_to_body');
			foreach($checkboxes as $item) { update_option($item, array_key_exists($item, $_POST)); }

			// Save settings
			update_option('wpstickies-options', serialize($newOptions));

			// WPML
			if(function_exists('icl_register_string')) {
				if(!empty($newOptions) && is_array($newOptions)) {
					foreach($newOptions as $key => $val) {
						if(strpos($key, 'lang_') === 0) {
							icl_register_string('wpStickies', $key, $val);
						}
					}
				}
			}

			// Convert URLs?
			if(
				(isset($options['relative_urls']) && !isset($_POST['wpstickies-options']['relative_urls'])) ||
				(!isset($options['relative_urls']) && isset($_POST['wpstickies-options']['relative_urls']))
			) {
				//echo 'convert';
				if(isset($_POST['wpstickies-options']['relative_urls'])) {
					wpstickies_convert_absolute_urls();
				} else {
					wpstickies_convert_relative_urls();
				}
			}

			die('SUCCESS');
		}
	}
}

function wps_save_user_css() {

	// Retrieve options and test user permissions
	$options = WPS_DB::getPluginSettings();
	if(!current_user_can('manage_options') && !isset($options['allow_settings_change'])) {
		die(__("You don't have permission to change plugin settings!", WPS_TEXTDOMAIN));
	}

	// Get target file and content
	$upload_dir = wp_upload_dir();
	$file = $upload_dir['basedir'].'/wpstickies.custom.css';

	// Attempt to save changes
	if(is_writable($upload_dir['basedir'])) {
		file_put_contents($file, stripslashes($_POST['contents']));
		header('Location: admin.php?page=wpstickies-css-editor&edited=1');
		die();

	// File isn't writable
	} else {
		wp_die(__("It looks like your files isn't writable, so PHP couldn't make any changes (CHMOD).", WPS_TEXTDOMAIN), __('Cannot write to file', WPS_TEXTDOMAIN), array('back_link' => true) );
	}
}


/********************************************************/
/*                    Head init code                    */
/********************************************************/

function wpstickies_js() {

	// Get init code JSON
	include(WPS_ROOT_PATH.'/init.php');

	// Fix multiple jQuery issue
	$data = '<script type="text/javascript">var wpsjQuery = jQuery;</script>';

	// Include JS files to body option
	if(get_option('wps_put_js_to_body', false)) {
	    $data .= '<script type="text/javascript" src="'.WPS_ROOT_URL.'/js/jquery-easing-1.3.js?ver=1.3.0"></script>' . NL;
	    $data .= '<script type="text/javascript" src="'.WPS_ROOT_URL.'/js/wpstickies.kreaturamedia.jquery.js?ver='.WPS_PLUGIN_VERSION.'"></script>' . NL;
	}

	$options['selector'] = empty($options['selector']) ? 'img[class*="wp-image"],.wpstickies' : stripslashes($options['selector']);
	$data .= "<script type=\"text/javascript\">
		wpsjQuery(document).ready(function() {
			wpsjQuery('{$options['selector']}').wpStickies(".json_encode($json).");
		});
	</script>";


	// Concatenate output
	if(get_option('wps_concatenate_output', true)) {
		$data = trim(preg_replace('/\s+/u', ' ', $data));
	}

	echo $data;
}



function wpstickies_allow_creatation() {

	// Gather data
	$options = WPS_DB::getPluginSettings();
	$capability = !empty($options['create_capability']) ? $options['create_capability'] : 'manage_options';
	$allowCreate = false;
	$hidden = 1;

	// Check user permissions
	if($capability === 'visitor' || current_user_can($capability)) {
		$allowCreate = true;
	}

	// Will be instantly visible when needed
	if(current_user_can('manage_options') || isset($options['create_auto_accept'])) {
		$hidden = 0;
	}

	return array($allowCreate, $hidden);
}


function wpstickies_allow_modification($uid, $sid) {

	// Gather data
	$options = WPS_DB::getPluginSettings();
	$capability = !empty($options['modify_capability']) ? $options['modify_capability'] : 'manage_options';
	$reconfirmation = !empty($options['requirereconfirmation']) ? $options['requirereconfirmation'] : 'yes';
	$allowModify = false;
	$hidden = 1;

	// Get sticky data
	$data = WPS_DB::find( (int) $sid );

	// Check user permissions
	if(current_user_can('manage_options')) {
		$allowModify = true;

	} elseif($data['user_id'] == $uid && $data['flag_hidden'] == 1) {
		$allowModify = true;

	} elseif($data['user_id'] == $uid && current_user_can($capability)) {
		$allowModify = true;
	}

	// Re-add pending list
	if(current_user_can('manage_options') || $reconfirmation == 'auto_accept' || ($reconfirmation == 'no' && $data['flag_hidden'] == 0)) {
		$hidden = 0;
	}

	return array($allowModify, $hidden);
}


/********************************************************/
/*         Action to accept pending stickies            */
/********************************************************/
function wpstickies_accept() {

	// Permission check
	if(!current_user_can(WPS_DB::getCapability())) {
		die('ERROR');
	}

	// Accept
	WPS_DB::accept( (int) $_POST['id'] );
	die('SUCCESS');
}

/********************************************************/
/*         Action to restore removed stickies           */
/********************************************************/
function wpstickies_restore() {

	// Permission check
	if(!current_user_can(WPS_DB::getCapability())) {
		die('ERROR');
	}

	// Restore
	WPS_DB::restore( (int) $_POST['id'] );
	die('SUCCESS');
}


/********************************************************/
/*         Action to reject pending stickies            */
/********************************************************/
function wpstickies_reject() {

	// Permission check
	if(!current_user_can(WPS_DB::getCapability())) {
		die('ERROR');
	}

	// Reject
	WPS_DB::reject( (int) $_POST['id'] );
	die('SUCCESS');
}

/********************************************************/
/*        Action to delete stickies permanently         */
/********************************************************/
function wpstickies_delete() {

	// Permission check
	if(!current_user_can(WPS_DB::getCapability())) {
		die('ERROR');
	}

	// Delete
	WPS_DB::delete( (int) $_POST['id'] );
	die('SUCCESS');
}

/********************************************************/
/*               Action to remove stickies              */
/********************************************************/
function wpstickies_remove() {

	// Gather data
	$id = (int) $_POST['id'];
	$user_id = get_current_user_id();
	$options = WPS_DB::getPluginSettings();
	$image = esc_sql($_POST['image']);

	// Relative URL?
	if(isset($options['relative_urls'])) {
		$image = parse_url($image, PHP_URL_PATH);
	}

	// Permission check
	list($allowModify, $hidden) = wpstickies_allow_modification($user_id, $id);
	if(current_user_can(WPS_DB::getCapability())) {
		$allowModify = true;
	}

	// Check per image settings
	$data = WPS_DB::getImageSettings( $image );
	if($data['disabled'] == 'true') {
		$allowModify = 0;
	}

	if(!$allowModify) {
		$options['lang_err_remove'] = empty($options['lang_err_remove']) ? 'wpStickies: The following error occurred during remove: You don\'t have permission to remove this sticky' : stripslashes($options['lang_err_remove']);
		die(json_encode(array('message' => $options['lang_err_remove'], 'errorCount' => 1)));
	}

	// Remove and response
	WPS_DB::remove( $id );
	die(json_encode(array('message' => '', 'errorCount' => 0)));
}


/********************************************************/
/*              Action to add new stickies              */
/********************************************************/

function wpstickies_insert() {

	// Get users data
	$user_id = get_current_user_id();
	$options = WPS_DB::getPluginSettings();
	$original = esc_sql($_POST['image']);
	$image = isset($options['relative_urls']) ? parse_url($original, PHP_URL_PATH) : $original;

	// Get permissions
	list($allowCreate, $hidden) = wpstickies_allow_creatation();

	// Check per image settings
	$data = WPS_DB::getImageSettings( $image );
	if($data['disabled'] == 'true') {
		$allowCreate = 0;
		$hidden = 1;
	}

	// Permission test
	if(!$allowCreate) {
		$options['lang_err_create'] = empty($options['lang_err_create']) ? 'wpStickies: The following error occurred during save: You don\'t have permission to create new stickies!' : stripslashes($options['lang_err_create']);
		die(json_encode(array('message' => $options['lang_err_create'], 'errorCount' => 1)));
	}

	// Insert
	$id = WPS_DB::add(array(
		'image' => $image,
		'original' => $original,
		'data' => json_encode($_POST['data']),
		'hidden' => $hidden
	));

	// Spot contents
	$title = stripslashes($_POST['data']['spot']['titleRaw']);
	$content = stripslashes($_POST['data']['spot']['contentRaw']);
	$caption = stripslashes($_POST['data']['area']['captionRaw']);

	// Register translatable strings in WPML
	if(function_exists('icl_register_string')) {
		icl_register_string('wpStickies', 'Spot title #'.$id, $title);
		icl_register_string('wpStickies', 'Spot content #'.$id, $content);
		icl_register_string('wpStickies', 'Area caption #'.$id, $caption);
	}

	// Response
	$allowToModify = wpstickies_allow_modification($user_id, $id);

	die(json_encode(array(
		'message' => '',
		'errorCount' => 0,
		'id' => $id,
		'allowToModify' => $allowToModify[0],
		'title' => $title,
		'content' => do_shortcode(nl2br($content)),
		'caption' => $caption
	)));
}

/********************************************************/
/*               Action to modify stickies              */
/********************************************************/

function wpstickies_update() {

	// Get sticky ID
	$id = (int) $_POST['id'];
	$user_id = get_current_user_id();
	$image = esc_sql($_POST['image']);
	$options = WPS_DB::getPluginSettings();

	// Get permissions
	list($allowModify, $hidden) = wpstickies_allow_modification($user_id, $id);

	// Relative URL?
	if(isset($options['relative_urls'])) {
		$image = parse_url($image, PHP_URL_PATH);
	}

	// Check per image settings
	$data = WPS_DB::getImageSettings( $image );
	if($data['disabled'] == 'true') {
		$allowModify = 0;
		$hidden = 1;
	}

	// Permission test
	if(!$allowModify) {
		$options['lang_err_modify'] = empty($options['lang_err_modify']) ? 'wpStickies: The following error occurred during save: You don\'t have permission to modify this sticky!' : stripslashes($options['lang_err_modify']);
		die(json_encode(array('message' => $options['lang_err_modify'], 'errorCount' => 1)));
	}

	// Update
	WPS_DB::update($id, array(
		'data' => json_encode($_POST['data']),
		'hidden' => $hidden
	));

	// Spot contents
	$title = stripslashes($_POST['data']['spot']['titleRaw']);
	$content = stripslashes($_POST['data']['spot']['contentRaw']);
	$caption = stripslashes($_POST['data']['area']['captionRaw']);

	// Get translatable string from WPML
	if(function_exists('icl_t')) {
		$title = icl_t('wpStickies', 'Spot title #'.$id, $title);
		$content = icl_t('wpStickies', 'Spot content #'.$id, $content);
		$caption = icl_t('wpStickies', 'Area caption #'.$id, $caption);
	}


	// Response
	die(json_encode(array(
		'message' => '',
		'errorCount' => 0,
		'title' => $title,
		'content' => do_shortcode(nl2br($content)),
		'caption' => $caption
	)));

}

/********************************************************/
/*                Action to get stickies                */
/********************************************************/

function wpstickies_get() {

	// Gather data
	global $wpdb;
	$user_id = get_current_user_id();
	$options = WPS_DB::getPluginSettings();
	$table_name = $wpdb->prefix.WPS_DB_TABLE;
	$url = esc_sql($_GET['image']);

	// Relative URL?
	if(isset($options['relative_urls'])) {
		$url = parse_url($url, PHP_URL_PATH);
	}

	// Get latest stickies
	if(isset($options['display_pending_stickies']) && $options['display_pending_stickies'] == 'visible') {
		$stickies = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name
										WHERE image = %s AND flag_deleted = '0'
										ORDER BY date_c DESC LIMIT 50", $url), ARRAY_A);
	} else {
		$stickies = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name
						WHERE
							( image = %s AND flag_hidden = '0' AND flag_deleted = '0' ) OR
							( image = %s AND user_id = %d AND user_id != '0' AND flag_deleted = '0' )
						ORDER BY date_c DESC LIMIT 100", $url, $url, $user_id), ARRAY_A);
	}

	// Set an empty array for results
	$ret = array('settings' => array(), 'stickies' => array());

	// Build an array
	foreach($stickies as $key => $val) {

		// Get the data
		$data = json_decode($val['data'], true);
		if(!$data) { $data = json_decode(stripslashes($val['data']), true); }

		$ret['stickies'][$key] = $data;
		$ret['stickies'][$key]['sticky']['id'] = $val['id'];

		$allowToModify = wpstickies_allow_modification($user_id, $val['id']);
		$ret['stickies'][$key]['sticky']['allowToModify'] = $allowToModify[0];

		// Get raw contents
		$titleRaw = $ret['stickies'][$key]['spot']['title'] = $ret['stickies'][$key]['spot']['titleRaw'] = !empty($data['spot']['titleRaw']) ? stripslashes($data['spot']['titleRaw']) : stripslashes($data['spot']['title']);
		$contentRaw = $ret['stickies'][$key]['spot']['content'] = $ret['stickies'][$key]['spot']['contentRaw'] = !empty($data['spot']['contentRaw']) ? stripslashes($data['spot']['contentRaw']) : stripslashes($data['spot']['content']);
		$captionRaw = $ret['stickies'][$key]['area']['caption'] = $ret['stickies'][$key]['area']['captionRaw'] = !empty($data['area']['captionRaw']) ? stripslashes($data['area']['captionRaw']) : stripslashes($data['area']['caption']);

		// Get translatable string from WPML
		if(function_exists('icl_t')) {
			$ret['stickies'][$key]['spot']['title'] = icl_t('wpStickies', 'Spot title #'.$val['id'], $titleRaw);
			$ret['stickies'][$key]['spot']['content'] = icl_t('wpStickies', 'Spot content #'.$val['id'], $contentRaw);
			$ret['stickies'][$key]['area']['caption'] = icl_t('wpStickies', 'Area caption #'.$val['id'], $captionRaw);
		}

		// Parse shortcodes (if any)
		$ret['stickies'][$key]['spot']['content'] = do_shortcode(nl2br($contentRaw));
	}


	// Query down image settings
	$settings = WPS_DB::getImageSettings( $url );

	if(empty($settings)) {
		$ret['settings']['disabled'] = 'false';
	} else {
		$ret['settings'] = $settings;
	}

	die(json_encode($ret));
}


/********************************************************/
/*           Per image settings for admins              */
/********************************************************/
function wpstickies_image_settings() {

	// Only admins
	if(!current_user_can('manage_options')) {
		die(json_encode(array('message' => 'You don\'t have permission to edit this image settings', 'errorCount' => 1)));
	}

	// Gather data
	$original = esc_sql($_POST['image']);
	$image = WPS_DB::getPluginOption('relative_urls') ? parse_url($original, PHP_URL_PATH) : $original;
	$data = addslashes(json_encode(esc_sql($_POST['data'])));

	// Save
	WPS_DB::updateImageSettings( $image, $data);
	die(json_encode(array('message' => '', 'errorCount' => 0)));
}



/********************************************************/
/*            Load more content in My Media             */
/********************************************************/
function wps_media_editor() {

	// Defaults
	$ret = array('items' => array());
	$paged = !empty($_GET['paged']) ? ((int) $_GET['paged'] + 1) : 1;
	$args = array(
		'post_type' => 'attachment',
		'post_mime_type' => 'image',
		'posts_per_page' => 100,
		'post_status' => 'any',
		'paged' => $paged
	);

	if(!empty($_GET['own'])) {
		$args['author'] = get_current_user_id();
	}

	if(!empty($_GET['terms'])) {
		$args['s'] = $_GET['terms'];
	}


	$query = new WP_Query( $args );
	while($query->have_posts()) {
		$query->the_post();
		$thumb = wp_get_attachment_image_src($query->post->ID);
		$ret['items'][] = array(
			'url' => wp_get_attachment_url($query->post->ID),
			'thumb' => $thumb[0],
			'title' => $query->post->post_title,
			'date' => get_the_date(null, $query->post->ID),
			'author' => get_the_author_meta('user_nicename', $query->post->post_author)
		);
	}


	$ret['lastPage'] = ($query->max_num_pages > $paged) ? false : true;
	$ret['currPage'] = $paged;

	die(json_encode($ret));
}

?>