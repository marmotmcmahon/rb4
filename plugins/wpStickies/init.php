<?php

if(!defined('WPS_ROOT_FILE')) { 
	header('HTTP/1.0 403 Forbidden');
	exit;
}

// Get defaults
include WPS_ROOT_PATH . '/config/lang.defaults.php';
include WPS_ROOT_PATH . '/config/settings.defaults.php';

// Retrieve options
$option = get_option('wpstickies-options');
$options = empty($option) ? array() : $option;
$options = is_array($options) ? $options : unserialize($options);
$hasOptions = empty($options) ? false : true;

// Get user role
$user = wp_get_current_user();
$role = !empty($user->roles) ? $user->roles[0] : 'non-user';

// Check permissions
$allowToCreate = wpstickies_allow_creatation();
$allowToCreate = empty($allowToCreate[0]) ? false : true;

// Var to hold json data
$json = array();
$json['settings']['role'] = $role;
$json['settings']['allowToCreate'] = $allowToCreate;
$json['settings']['allowToModify'] = true;

// Plugin settings
foreach($wpsSettingsDefaults as $key => $item) {

	if(is_bool($item['value']) && !$hasOptions) {
		$json[$item['group']][$item['name']] = $item['value'];
	
	}elseif(is_bool($item['value']) && $hasOptions) {
		if($item['value'] === true && empty($options[$key])) {
			$json[$item['group']][$item['name']] = false;
		} else {
			$json[$item['group']][$item['name']] = true;
		}

	} elseif(isset($options[$key])) {
		$json[$item['group']][$item['name']] = is_numeric($options[$key]) ? (float) $options[$key] : stripslashes($options[$key]);
	
	} else {
		$json[$item['group']][$item['name']] = $item['value'];
	}
}


// Language strings
foreach($wpsLangDefaults as $key => $item) {
	
	// Set default if needed
	if(empty($options[$key])) { $options[ $key] = $item['value']; }

	// Get WPML localized string (if any)
	if(function_exists('icl_t')) {
		$options[$key] = icl_t('wpStickies', $key, $item['value']);
	}

	// Set lang string
	$json['language'][$item['name']] = do_shortcode(__(stripslashes($options[$key])));
}