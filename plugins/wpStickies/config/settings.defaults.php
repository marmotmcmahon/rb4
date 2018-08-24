<?php

$wpsSettingsDefaults = array(

	// OPTIONS: General
	'show_messages' => array('name' => 'showMessages', 'group' => 'settings', 'value' => true),
	'always_visible' => array('name' => 'alwaysVisible', 'group' => 'settings', 'value' => true),
	'image_min_width' => array('name' => 'imageMinWidth', 'group' => 'settings', 'value' => 150),
	'image_min_height' => array('name' => 'imageMinHeight', 'group' => 'settings', 'value' => 150),

	// OPTIONS: Positions
	'spot_bubble_direction' => array('name' => 'spotBubbleDirection', 'group' => 'position', 'value' => 'top'),
	'auto_change_direction' => array('name' => 'autoChangeDirection', 'group' => 'position', 'value' => true),
	'spot_bubble_distance' => array('name' => 'spotBubbleDistance', 'group' => 'position', 'value' => 2),
	'area_min_width' => array('name' => 'areaMinWidth', 'group' => 'position', 'value' => 25),
	'area_min_height' => array('name' => 'areaMinHeight', 'group' => 'position', 'value' => 25),
	'spot_bubble_spot_buttons_positiondirection' => array('name' => 'spotButtonsPosition', 'group' => 'position', 'value' => 'left'),

	// OPTIONS: Animation
	'directionin' => array('name' => 'directionIn', 'group' => 'animation', 'value' => 'bottom'),
	'directionout' => array('name' => 'directionOut', 'group' => 'animation', 'value' => 'fade'),
	'easingin' => array('name' => 'easingIn', 'group' => 'animation', 'value' => 'easeOutQuart'),
	'easingout' => array('name' => 'easingOut', 'group' => 'animation', 'value' => 'easeInBack'),
	'durationin' => array('name' => 'durationIn', 'group' => 'animation', 'value' => 500),
	'durationout' => array('name' => 'durationOut', 'group' => 'animation', 'value' => 250),
	'spot_bubble_easing' => array('name' => 'spotBubbleEasing', 'group' => 'animation', 'value' => 'easeOutBack'),
	'spot_bubble_duration' => array('name' => 'spotBubbleDuration', 'group' => 'animation', 'value' => 200),
	'delay' => array('name' => 'delay', 'group' => 'animation', 'value' => 30)
);