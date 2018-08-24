<?php

interface iWPS_DB {

	// Table: wp_wpstickies
	public static function add( $data = array() );
	public static function update( $id, $data = array() );
	public static function remove( $id );
	public static function delete( $id );
	public static function restore( $id );
	public static function accept( $id );
	public static function reject( $id );
	public static function find( $args = array() );


	// Table: wp_wpstickies_image
	public static function getImageSettings( $image );
	public static function updateImageSettings( $image, $data = array() );

	// Table: wp_options
	public static function getPluginSettings();
	public static function getPluginOption( $key );
	public static function getCapability();
}

class WPS_DB implements iWPS_DB {


	/**
	 * @var array $results Array containing the result of the last DB query
	 * @access public
	 */
	public static $results = array();



	/**
	 * @var int $count Count of found tags in the last DB query
	 * @access public
	 */
	public static $count = null;

	/**
	 * @var array $settings Global plugin settings for caching
	 * @access public
	 */
	protected static $settings = array();


	/**
	 * Adds tags with the provided data to images
	 *
	 * @since 2.1.0
	 * @access public
	 * @param string $image The image URL where the tab belongs
	 * @param array $data Tag data
	 * @return int The tab database ID inserted
	 */
	public static function add( $data = array() ) {

		global $wpdb;
		$user = wp_get_current_user();

		// Insert tag, WPDB will escape data automatically
		$wpdb->insert($wpdb->prefix.WPS_DB_TABLE, array(
			'image' => $data['image'],
			'image_original' => $data['original'],
			'data' => $data['data'],
			'user_id' => $user->ID,
			'user_name' => $user->user_login,
			'date_c' => time(),
			'date_m' => time(),
			'flag_hidden' => $data['hidden'],
			'flag_deleted' => 0

		), array(
			'%s', '%s', '%s', '%d', '%s', '%d', '%d', '%d', '%d'
		));

		// Return insert database ID
		return $wpdb->insert_id;
	}



	/**
	 * Updates tags
	 *
	 * @since 2.1.0
	 * @access public
	 * @param int $id The database ID of the tag to be updated
	 * @param array $data The new settings of the tag
	 * @return mixed Returns $wpdb's response
	 */
	public static function update( $id, $data = array() ) {

		// Check ID
		if(empty($id) || !is_int($id)) { return false; }

		global $wpdb;
		return $wpdb->update($wpdb->prefix.WPS_DB_TABLE, array(
				'data' => $data['data'],
				'date_m' => time(),
				'flag_hidden' => $data['hidden']
			), 
			array('id' => $id), 
			array('%s', '%d', '%d')
		);
	}
	

	/**
	 * Marking the tag as removed by its DB ID without deleting it
	 * from the database
	 *
	 * @since 2.1.0
	 * @access public
	 * @param int $id The database ID of the tag to be removed
	 * @return mixed Returns $wpdb's response
	 */
	public static function remove( $id ) {

		// Check ID
		if(empty($id) || !is_int($id)) { return false; }

		global $wpdb;
		return $wpdb->update(
			$wpdb->prefix.WPS_DB_TABLE,
			array('flag_deleted' => 1),
			array('id' => $id),
			'%d', '%d'
		);

	}




	/**
	 * Delete a tag by its database ID
	 *
	 * @since 2.1.0
	 * @access public
	 * @param int $id The database ID of the tag to be deleted
	 * @return mixed Returns $wpdb's response
	 */
	public static function delete( $id ) {

		// Check ID
		if(empty($id) || !is_int($id)) { return false; }

		global $wpdb;
		return $wpdb->delete($wpdb->prefix.WPS_DB_TABLE, array('id' => $id), '%d');
	}




	/**
	 * Restores a tag marked as removed previously by its database ID.
	 *
	 * @since 2.1.0
	 * @access public
	 * @param int $id The database ID of the tag to be restored
	 * @return mixed Returns $wpdb's response
	 */
	public static function restore( $id ) {

		// Check ID
		if(empty($id) || !is_int($id)) { return false; }

		// Remove
		global $wpdb;
		return $wpdb->update($wpdb->prefix.WPS_DB_TABLE,
			array('flag_deleted' => 0),
			array('id' => $id),
			'%d', '%d'
		);
	}




	/**
	 * Accepts a pending tag by its database ID.
	 *
	 * @since 2.1.0
	 * @access public
	 * @param int $id The database ID of the tag to be accepted
	 * @return mixed Returns $wpdb's response
	 */
	public static function accept( $id ) {

		// Check ID
		if(empty($id) || !is_int($id)) { return false; }

		// Remove
		global $wpdb;
		return $wpdb->update($wpdb->prefix.WPS_DB_TABLE,
			array('flag_hidden' => 0, 'flag_deleted' => 0),
			array('id' => $id),
			array('%d', '%d'), '%d'
		);
	}


	/**
	 * Rejects a pending tag by its database ID.
	 *
	 * @since 2.1.0
	 * @access public
	 * @param int $id The database ID of the tag to be rejected
	 * @return mixed Returns $wpdb's response
	 */
	public static function reject( $id ) {

		// Check ID
		if(empty($id) || !is_int($id)) { return false; }

		// Remove
		global $wpdb;
		return $wpdb->update($wpdb->prefix.WPS_DB_TABLE,
			array('flag_hidden' => 0, 'flag_deleted' => 1),
			array('id' => $id),
			array('%d', '%d'), '%d'
		);
	}


	/**
	 * Finds tags with the provided filters
	 *
	 * @since 2.1.0
	 * @access public
	 * @param mixed $args Find any tag with the provided filters
	 * @return mixed Array on success, false otherwise
	 */
	public static function find( $args = array() ) {

		// Find by ID
		if(is_int($args) || is_numeric($args)) {
			return self::_getById( (int) $args );
		
		} else {

			// Defaults
			$defaults = array(
				'columns' => '*',
				'where' => '',
				'exclude' => array('hidden', 'removed'),
				'orderby' => 'date_c',
				'order' => 'DESC',
				'limit' => 50,
				'page' => 1,
				'data' => true
			);

			// User data
			foreach($defaults as $key => $val) {
				if(!isset($args[$key])) { $args[$key] = $val; } }

			// Escape user data
			foreach($args as $key => $val) {
				$args[$key] = esc_sql($val); }

			// Exclude
			if(!empty($args['exclude'])) {
				if(in_array('hidden', $args['exclude'])) {
					$exclude[] = "flag_hidden = '0'"; }

				if(in_array('removed', $args['exclude'])) {
					$exclude[] = "flag_deleted = '0'"; }

				$args['exclude'] = implode(' AND ', $exclude);
			}

			// Where
			$where = '';
			if(!empty($args['where']) && !empty($args['exclude'])) {
				$where = "WHERE ({$args['exclude']}) AND ({$args['where']}) ";

			} elseif(!empty($args['where'])) {
				$where = "WHERE {$args['where']} ";

			} elseif(!empty($args['exclude'])) {
				$where = "WHERE {$args['exclude']} ";
			}

			// Some adjustments
			$args['limit'] = ($args['limit'] * $args['page'] - $args['limit']).', '.$args['limit'];

			// Build the query
			global $wpdb;
			$table = $wpdb->prefix.WPS_DB_TABLE;
			$tags = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS {$args['columns']} FROM $table $where
									ORDER BY {$args['orderby']} {$args['order']} LIMIT {$args['limit']}", ARRAY_A);

			// Set counter
			$found = $wpdb->get_col("SELECT FOUND_ROWS()");
			self::$count = (int) $found[0];

			// Return original value on error
			if(!is_array($tags)) { return $tags; };

			// Parse tag data
			if($args['data']) {
				foreach($tags as $key => $val) {
					$tags[$key]['data'] = json_decode($val['data'], true);
					if(!$tags[$key]['data']) { $tags[$key]['data'] = json_decode(stripslashes($val['data']), true); }
				}
			}

			// Return tags
			return $tags;
		}
	}


	/**
	 * Return per image settings
	 *
	 * @since 2.1.0
	 * @access public
	 * @param string $image The corresponding image 
	 * @return mixed Array on success, false otherwise
	 */
	public static function getImageSettings( $image ) {

		global $wpdb;
		$table = $wpdb->prefix.WPS_DB_IMAGES_TABLE;
		$settings = $wpdb->get_row($wpdb->prepare("SELECT data FROM $table WHERE image = %s LIMIT 1", $image), ARRAY_A);
		return json_decode(stripslashes($settings['data']), true);
	}




	/**
	 * Adds or updates per image settings with the provided data
	 *
	 * @since 2.1.0
	 * @access public
	 * @param string $image The image URL to save settings for
	 * @param array $data Per image settings
	 * @return int The inserted database ID
	 */
	public static function updateImageSettings( $image, $data = array() ) {

		global $wpdb; $time = time();
		$table = $wpdb->prefix.WPS_DB_IMAGES_TABLE;
		return $wpdb->query($wpdb->prepare(
			"INSERT INTO $table (image, image_original, data, date_c, date_m) 
				VALUES (%s, %s, %s, %d, %d)
			  ON DUPLICATE KEY UPDATE data = %s, date_m = %d"

		, $image, $image, $data, $time, $time, $data, $time));
	}


	/**
	 * Returns wpStickies' global settings
	 *
	 * @since 2.1.0
	 * @access public
	 * @return array Array holding the plugin settings
	 */
	public static function getPluginSettings() {
		
		$options = get_option('wpstickies-options');
		$options = empty($options) ? array() : $options;
		self::$settings = is_array($options) ? $options : unserialize($options);
		return self::$settings;
	}



	/**
	 * Returns a global plugin option by its key
	 *
	 * @since 2.1.0
	 * @access public
	 * @param string $key The option key to be returned
	 * @return array Array holding the plugin settings
	 */
	public static function getPluginOption( $key ) {
	
		// Request settings if its empty
		if(empty(self::$settings)) { self::getPluginSettings(); }

		// Check setting key
		if(!isset($key) || !isset(self::$settings[$key])) { return false; }

		// Return setting
		return self::$settings[$key];
	}



	/**
	 * Returns the capability set in plugin options
	 *
	 * @since 2.1.0
	 * @access public
	 * @return string The capability
	 */
	public static function getCapability() {
		$cap = self::getPluginOption('capability');
		return !empty($cap) ? $cap : 'manage_options'; 
	}


	private static function _getById($id = null) {

		// Check ID
		if(empty($id) || !is_int($id)) { return false; }

		// Get tag
		global $wpdb;
		$table = $wpdb->prefix.WPS_DB_TABLE;
		$result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = '%d' ORDER BY id DESC LIMIT 1", $id), ARRAY_A);

		// Check return value
		if(!is_array($result)) { return false; }

		// Return result
		$result['data'] = json_decode($result['data'], true);
		return $result;
	}

}