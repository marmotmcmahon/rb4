<?php

	if(!defined('WPS_ROOT_FILE')) {
		header('HTTP/1.0 403 Forbidden');
		exit;
	}

	// Retrieve options
	$options = WPS_DB::getPluginSettings();

	$pending = WPS_DB::find(array(
		'where' => 'flag_hidden=1',
		'exclude' => array('removed'),
		'limit' => 100
	));

	$latest = WPS_DB::find(array());

	$removed = WPS_DB::find(array(
		'where' => 'flag_deleted=1',
		'exclude' => array('hidden')
	));

	// Custom capability
	$custom_capability = $custom_role = !empty($options['capability']) ? $options['capability'] : 'manage_options';
	$default_capabilities = array('manage_network', 'manage_options', 'publish_pages', 'publish_posts', 'edit_posts');
	$permission_caps = array('manage_options', 'publish_pages', 'publish_posts', 'edit_posts', 'edit-wpstickies', 'read', 'visitor');

	if(in_array($custom_capability, $default_capabilities)) {
		$custom_capability = '';
	} else {
		$custom_role = 'custom';
	}

	// Auto-updates
	$code = get_option('wpstickies-purchase-code', '');
	if(!empty($code)) {
		$start = substr($code, 0, -6);
		$end = substr($code, -6);
		$code = preg_replace("/[a-zA-Z0-9]/", '●', $start) . $end;
		$code = str_replace('-', ' ', $code);
	}

	$validity = get_option('wpstickies-validated', '0');
	$channel = get_option('wpstickies-release-channel', 'stable');

	// Defaults
	$relativeURLDefault = false;
	$allowSettingsChangeDefault = false;
	$createAutoAcceptDefault = false;
	$directionInDefault = 'bottom';
	$showMessagesDefault = true;
	$alwaysVisibleDefault = true;
	$autoChangeDirectionDefault = true;

	$create_capability = $create_role = !empty($options['create_capability']) ? $options['create_capability'] : 'manage_options';
	if(in_array($create_capability, $permission_caps)) { $create_capability = '';
		} else { $create_role = 'custom'; }


	$modify_capability = $modify_role = !empty($options['modify_capability']) ? $options['modify_capability'] : 'manage_options';
	if(in_array($modify_capability, $permission_caps)) { $modify_capability = '';
		} else { $modify_role = 'custom'; }

	if(empty($options['display_pending_stickies'])) { $options['display_pending_stickies'] = 'invisible'; }
	if(empty($options['requirereconfirmation'])) { $options['requirereconfirmation'] = 'yes'; }
	if(empty($options['spot_bubble_direction'])) { $options['spot_bubble_direction'] = 'top'; }

	$hasAdminPermissions = (current_user_can('manage_options') || isset($options['allow_settings_change'])) ? true : false;
?>
<div class="wrap">

	<h2><?php _e('wpStickies', WPS_TEXTDOMAIN) ?></h2>

	<!-- Main menu -->
	<div id="wps-main-nav-bar">
		<?php if($hasAdminPermissions) : ?>
		<a href="#" class="settings active"><i class="dashicons dashicons-admin-tools"></i> <?php _e('Plugin Settings', WPS_TEXTDOMAIN) ?></a>
		<a href="#" class="stickies"><i class="dashicons dashicons-tag"></i> <?php _e('Stickies', WPS_TEXTDOMAIN) ?></a>
		<a href="#" class="language"><i class="dashicons dashicons-translation"></i> <?php _e('Language', WPS_TEXTDOMAIN) ?></a>
		<a href="#" class="updates"><i class="dashicons dashicons-update"></i> <?php _e('News & Updates', WPS_TEXTDOMAIN) ?></a>
		<?php else : ?>
		<a href="#" class="stickies active"><i class="dashicons dashicons-tag"></i> <?php _e('Stickies', WPS_TEXTDOMAIN) ?></a>
		<?php endif ?>
		<a href="https://support.kreaturamedia.com/faq/6/wpstickies/" target="_blank" class="faq right unselectable"><i class="dashicons dashicons-sos"></i> FAQ</a>
		<a href="#" class="support right unselectable"><i class="dashicons dashicons-editor-help"></i> <?php _e('Documentation', WPS_TEXTDOMAIN) ?></a>
		<span class="right help">Need help? Try these:</span>
		<a href="#" class="clear unselectable"></a>
	</div>

	<!-- Pages -->
	<div id="wps-pages">

		<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post" id="wps-form">
			<input type="hidden" name="posted" value="1">
			<?php wp_nonce_field('wps-save-globals'); ?>

			<!-- Plugin settings -->
			<?php if($hasAdminPermissions) : ?>
			<div class="wps-page wps-slider-settings active">
				<div class="wps-box wps-settings">
					<h3 class="header medium"><?php _e('Plugin Settings', WPS_TEXTDOMAIN) ?></h3>
					<div class="inner">
						<ul class="wps-settings-sidebar">
							<li class="active"><i class="dashicons dashicons-admin-tools"></i><?php _e('General', WPS_TEXTDOMAIN) ?></li>
							<li><i class="dashicons dashicons-admin-users"></i><?php _e('Permissions', WPS_TEXTDOMAIN) ?></li>
							<li><i class="dashicons dashicons-admin-appearance"></i><?php _e('Appearance', WPS_TEXTDOMAIN) ?></li>
							<li><i class="dashicons dashicons-lightbulb"></i><?php _e('Troubleshooting', WPS_TEXTDOMAIN) ?></li>
						</ul>
						<div class="wps-settings-contents">
							<table>

								<!-- Plugin settings: General -->
								<tbody class="active">
									<tr class="subheader">
										<th colspan="3">
											<?php _e('General', WPS_TEXTDOMAIN) ?>
										</th>
									</tr>
									<tr>
										<td valign="top" style="padding-top: 12px;"><?php _e('Selectors', WPS_TEXTDOMAIN) ?></td>
										<td colspan="2" class="desc">
											<input type="text" name="selector" class="selector input" value="<?php echo !empty($options['selector']) ? htmlspecialchars(stripslashes($options['selector'])) : 'img[class*=&quot;wp-image&quot;], .wpstickies' ?>">
											<?php _e('You can apply wpStickies on any content, even on DIVs with background with the right selector. Please read the documentation for more information about selectors.', WPS_TEXTDOMAIN) ?>
										</td>
									</tr>
									<tr>
										<td><?php _e('Use relative URLs', WPS_TEXTDOMAIN) ?></td>
										<td><input type="checkbox" name="relative_urls"<?php echo ((empty($option) && $relativeURLDefault == true) || isset($options['relative_urls'])) ? ' checked="checked"' : '' ?>></td>
										<td class="desc"><?php _e("wpStickies will try to find images locally by ignoring the domain name in their paths. This option helps if you've moved your site to another server, but it can also cause problems for images loaded externally.", WPS_TEXTDOMAIN) ?></td>
									</tr>
									<tr class="subheader">
										<th colspan="3">
											<?php _e('Permissions', WPS_TEXTDOMAIN) ?>
										</th>
									</tr>
									<tr>
										<td><?php _e('Allow plugin access to', WPS_TEXTDOMAIN) ?></td>
										<td>
											<select name="custom_role">
												<?php if(is_multisite()) : ?>
												<option value="manage_network" <?php echo ($custom_role == 'manage_network') ? 'selected="selected"' : '' ?>> <?php _e('Super Admin', WPS_TEXTDOMAIN) ?></option>
												<?php endif; ?>
												<option value="manage_options" <?php echo ($custom_role == 'manage_options') ? 'selected="selected"' : '' ?>> <?php _e('Admins', WPS_TEXTDOMAIN) ?></option>
												<option value="publish_pages" <?php echo ($custom_role == 'publish_pages') ? 'selected="selected"' : '' ?>> <?php _e('Editors, Admins', WPS_TEXTDOMAIN) ?></option>
												<option value="publish_posts" <?php echo ($custom_role == 'publish_posts') ? 'selected="selected"' : '' ?>> <?php _e('Authors, Editors, Admins', WPS_TEXTDOMAIN) ?></option>
												<option value="edit_posts" <?php echo ($custom_role == 'edit_posts') ? 'selected="selected"' : '' ?>> <?php _e('Contributors, Authors, Editors, Admins', WPS_TEXTDOMAIN) ?></option>
												<option value="custom" <?php echo ($custom_role == 'custom') ? 'selected="selected"' : '' ?>> <?php _e('Custom', WPS_TEXTDOMAIN) ?></option>
											</select>
										</td>
										<td class="desc"><?php _e("You can allow non-administrator users to access wpStickies' admin area.", WPS_TEXTDOMAIN) ?></td>
									</tr>
									<tr>
										<td><?php _e('Custom capability', WPS_TEXTDOMAIN) ?></td>
										<td><input type="text" name="capability" class="input" value="<?php echo $custom_capability ?>"></td>
										<td class="desc"><?php _e('Alternatively, you can provide a custom capability instead of the predefined roles. <a href="http://codex.wordpress.org/Roles_and_Capabilities" target="_blank">See the Codex</a> for more information.', WPS_TEXTDOMAIN) ?></td>
									</tr>
									<tr>
										<td><?php _e('Allow changing plugin settings', WPS_TEXTDOMAIN) ?></td>
										<td><input type="checkbox" name="allow_settings_change"<?php echo ((empty($option) && $allowSettingsChangeDefault == true) || isset($options['allow_settings_change'])) ? ' checked="checked"' : '' ?>></td>
										<td class="desc"><?php _e('Non-administrator users will be able to change plugin settings, including permission preferences.', WPS_TEXTDOMAIN) ?></td>
									</tr>
									<tr class="subheader">
										<th colspan="3">
											<?php _e('Other', WPS_TEXTDOMAIN) ?>
										</th>
									</tr>
									<tr>
										<td><?php _e('Minimum image width & height', WPS_TEXTDOMAIN) ?></td>
										<td>
											<input type="text" name="image_min_width" class="input mini" value="<?php echo !empty($options['image_min_width']) ? $options['image_min_width'] : '150' ?>"> x
											<input type="text" name="image_min_height" class="input mini" value="<?php echo !empty($options['image_min_height']) ? $options['image_min_height'] : '150' ?>">
										</td>
										<td class="desc"><?php _e('Prevent wpStickies to be applied on images with smaller dimensions that you specify here.', WPS_TEXTDOMAIN) ?></td>
									</tr>

									<tr>
										<td><?php _e('Minimum area width & height', WPS_TEXTDOMAIN) ?></td>
										<td>
											<input type="text" name="area_min_width" class="input mini" value="<?php echo !empty($options['area_min_width']) ? $options['area_min_width'] : '25' ?>"> x
											<input type="text" name="area_min_height" class="input mini" value="<?php echo !empty($options['area_min_height']) ? $options['area_min_height'] : '25' ?>">
										</td>
										<td class="desc"><?php _e('Prevent unintentionally adding area tags by specifying the minimum dimensions that needs to be drawn.', WPS_TEXTDOMAIN) ?></td>
									</tr>
								</tbody>

								<!-- Plugin settings: Permissions -->
								<tbody>
									<tr class="subheader">
										<th colspan="3">
											<?php _e('Adding new stickies', WPS_TEXTDOMAIN) ?>
										</th>
									</tr>
									<tr>
										<td><?php _e('Allow adding stickies to', WPS_TEXTDOMAIN) ?></td>
										<td>
											<select name="create_role">
												<option value="manage_options" <?php echo ($create_role == 'manage_options') ? 'selected="selected"' : '' ?>> <?php _e('Admins', WPS_TEXTDOMAIN) ?></option>
												<option value="publish_pages" <?php echo ($create_role == 'publish_pages') ? 'selected="selected"' : '' ?>> <?php _e('Editors, Admins', WPS_TEXTDOMAIN) ?></option>
												<option value="publish_posts" <?php echo ($create_role == 'publish_posts') ? 'selected="selected"' : '' ?>> <?php _e('Authors, Editors, Admins', WPS_TEXTDOMAIN) ?></option>
												<option value="edit_posts" <?php echo ($create_role == 'edit_posts') ? 'selected="selected"' : '' ?>> <?php _e('Contributors, Authors, Editors, Admins', WPS_TEXTDOMAIN) ?></option>
												<option value="edit-wpstickies" <?php echo ($create_role == 'edit-wpstickies') ? 'selected="selected"' : '' ?>> <?php _e('wpStickies admins, Admins', WPS_TEXTDOMAIN) ?></option>
												<option value="read" <?php echo ($create_role == 'read') ? 'selected="selected"' : '' ?>> <?php _e('Every registered member', WPS_TEXTDOMAIN) ?></option>
												<option value="visitor" <?php echo ($create_role == 'visitor') ? 'selected="selected"' : '' ?>> <?php _e('Every visitor', WPS_TEXTDOMAIN) ?></option>
												<option value="custom" <?php echo ($create_role == 'custom') ? 'selected="selected"' : '' ?>> <?php _e('Custom', WPS_TEXTDOMAIN) ?></option>
											</select>
										</td>
										<td class="desc"><?php _e('You can control here who can create new sticikes. The "wpStickies admins" group created by the plugin with subscriber permissions.', WPS_TEXTDOMAIN) ?></td>
									</tr>
									<tr>
										<td><?php _e('Custom capability', WPS_TEXTDOMAIN) ?></td>
										<td>
											<input type="text" name="create_capability" class="input" value="<?php echo $create_capability ?>">
										</td>
										<td class="desc"><?php _e('Alternatively, you can provide a custom capability instead of the predefined roles. <a href="http://codex.wordpress.org/Roles_and_Capabilities" target="_blank">See the Codex</a> for more information.', WPS_TEXTDOMAIN) ?></td>
									</tr>
									<tr>
										<td><?php _e('Pending stickies displayed as', WPS_TEXTDOMAIN) ?></td>
										<td>
											<select name="display_pending_stickies">
												<option value="visible"<?php echo ($options['display_pending_stickies'] == 'visible') ? ' selected="selected"' : '' ?>><?php _e('Visible', WPS_TEXTDOMAIN) ?></option>
												<option value="invisible"<?php echo (!isset($options['display_pending_stickies']) || $options['display_pending_stickies'] == 'invisible') ? ' selected="selected"' : '' ?>><?php _e('Invisible', WPS_TEXTDOMAIN) ?></option>
											</select>
										</td>
										<td class="desc"><?php _e('Show or hide pending tags until an administrator approves them.', WPS_TEXTDOMAIN) ?></td>
									</tr>
									<tr>
										<td><?php _e('Auto-accept pending stickies', WPS_TEXTDOMAIN) ?></td>
										<td><input type="checkbox" name="create_auto_accept"<?php echo ((empty($option) && $createAutoAcceptDefault == true) || isset($options['create_auto_accept'])) ? ' checked="checked"' : '' ?>></td>
										<td class="desc"><?php _e('You can bypass the pending list and all the tags will be accepted automatically.', WPS_TEXTDOMAIN) ?></td>
									</tr>

									<tr class="subheader">
										<th colspan="3">
											<?php _e('Modifying stickies', WPS_TEXTDOMAIN) ?>
										</th>
									</tr>
									<tr>
										<td><?php _e('Allow modifying stickies to', WPS_TEXTDOMAIN) ?></td>
										<td>
											<select name="modify_role">
												<option value="manage_options" <?php echo ($modify_role == 'manage_options') ? 'selected="selected"' : '' ?>> <?php _e('Admins', WPS_TEXTDOMAIN) ?></option>
												<option value="publish_pages" <?php echo ($modify_role == 'publish_pages') ? 'selected="selected"' : '' ?>> <?php _e('Editors, Admins', WPS_TEXTDOMAIN) ?></option>
												<option value="publish_posts" <?php echo ($modify_role == 'publish_posts') ? 'selected="selected"' : '' ?>> <?php _e('Authors, Editors, Admins', WPS_TEXTDOMAIN) ?></option>
												<option value="edit_posts" <?php echo ($modify_role == 'edit_posts') ? 'selected="selected"' : '' ?>> <?php _e('Contributors, Authors, Editors, Admins', WPS_TEXTDOMAIN) ?></option>
												<option value="edit-wpstickies" <?php echo ($modify_role == 'edit-wpstickies') ? 'selected="selected"' : '' ?>> <?php _e('wpStickies admins, Admins', WPS_TEXTDOMAIN) ?></option>
												<option value="read" <?php echo ($modify_role == 'read') ? 'selected="selected"' : '' ?>> <?php _e('Every registered member', WPS_TEXTDOMAIN) ?></option>
												<option value="custom" <?php echo ($modify_role == 'custom') ? 'selected="selected"' : '' ?>> <?php _e('Custom', WPS_TEXTDOMAIN) ?></option>
											</select>
										</td>
										<td class="desc"><?php _e('You can control here who can modify stickies. Non-administrator users can only edit their own stickies.', WPS_TEXTDOMAIN) ?></td>
									</tr>
									<tr>
										<td><?php _e('Custom capability', WPS_TEXTDOMAIN) ?></td>
										<td>
											<input type="text" name="modify_capability" class="input" value="<?php echo $modify_capability ?>">
										</td>
										<td class="desc"><?php _e('Alternatively, you can provide a custom capability instead of the predefined roles. <a href="http://codex.wordpress.org/Roles_and_Capabilities" target="_blank">See the Codex</a> for more information.', WPS_TEXTDOMAIN) ?></td>
									</tr>
									<tr>
										<td><?php _e('After a modification', WPS_TEXTDOMAIN) ?></td>
										<td>
											<select name="requirereconfirmation">
												<option value="yes"<?php echo ($options['requirereconfirmation'] == 'yes') ? ' selected="selected"' : '' ?>><?php _e('Add sticky to the pending list for re-confirmation', WPS_TEXTDOMAIN) ?></option>
												<option value="no"<?php echo ($options['requirereconfirmation'] == 'no') ? ' selected="selected"' : '' ?>><?php _e('Treat as confirmed if it was previously approved', WPS_TEXTDOMAIN) ?></option>
												<option value="auto_accept"<?php echo ($options['requirereconfirmation'] == 'auto_accept') ? ' selected="selected"' : '' ?>><?php _e('Ignore pending list, approve them automatically', WPS_TEXTDOMAIN) ?></option>
											</select>
										</td>
										<td class="desc"><?php _e('Choose an action to be performed after editing tags. You can send modified stickies for revaluation, treat them as confirmed if they were previously approved, or just completely ignore the pending list in all cases.', WPS_TEXTDOMAIN) ?></td>
									</tr>
								</tbody>


								<!-- Plugin settings: Appearance -->
								<tbody>
									<tr class="subheader">
										<th colspan="3">
											<?php _e('Animation settings', WPS_TEXTDOMAIN) ?>
										</th>
									</tr>
									<tr>
										<td colspan="3">
											<table class="removestyle">
												<tbody>
													<tr>
														<th></th>
														<th><?php _e('Direction', WPS_TEXTDOMAIN) ?></th>
														<th><?php _e('Easing', WPS_TEXTDOMAIN) ?></th>
														<th><?php _e('Duration', WPS_TEXTDOMAIN) ?></th>
													</tr>
													<tr>
														<td><?php _e('Mouse enter animation', WPS_TEXTDOMAIN) ?></td>
														<td>
															<?php $options['directionin'] = empty($options['directionin']) ? 'bottom' : $options['directionin']; ?>
															<select name="directionin">
																<option value="fade"<?php echo ($options['directionin'] == 'fade' ) ? ' selected="selected"' : '' ?>><?php _e('fade', WPS_TEXTDOMAIN) ?></option>
																<option value="top"<?php echo ($options['directionin'] == 'top') ? ' selected="selected"' : '' ?>><?php _e('top', WPS_TEXTDOMAIN) ?></option>
																<option value="bottom" <?php echo ($options['directionin'] == 'bottom') ? ' selected="selected"' : '' ?>><?php _e('bottom', WPS_TEXTDOMAIN) ?></option>
																<option value="left"<?php echo ($options['directionin'] == 'left') ? ' selected="selected"' : '' ?>><?php _e('left', WPS_TEXTDOMAIN) ?></option>
																<option value="right"<?php echo ($options['directionin'] == 'right') ? ' selected="selected"' : '' ?>><?php _e('right', WPS_TEXTDOMAIN) ?></option>
															</select>
														</td>
														<td>
															<?php $options['easingin'] = empty($options['easingin']) ? 'easeOutQuart' : $options['easingin']; ?>
															<select name="easingin">
																<option<?php echo ($options['easingin'] == 'linear' ) ? ' selected="selected"' : '' ?>>linear</option>
																<option<?php echo ($options['easingin'] == 'swing' ) ? ' selected="selected"' : '' ?>>swing</option>
																<option<?php echo ($options['easingin'] == 'easeInQuad' ) ? ' selected="selected"' : '' ?>>easeInQuad</option>
																<option<?php echo ($options['easingin'] == 'easeOutQuad' ) ? ' selected="selected"' : '' ?>>easeOutQuad</option>
																<option<?php echo ($options['easingin'] == 'easeInOutQuad' ) ? ' selected="selected"' : '' ?>>easeInOutQuad</option>
																<option<?php echo ($options['easingin'] == 'easeInCubic' ) ? ' selected="selected"' : '' ?>>easeInCubic</option>
																<option<?php echo ($options['easingin'] == 'easeOutCubic' ) ? ' selected="selected"' : '' ?>>easeOutCubic</option>
																<option<?php echo ($options['easingin'] == 'easeInOutCubic' ) ? ' selected="selected"' : '' ?>>easeInOutCubic</option>
																<option<?php echo ($options['easingin'] == 'easeInQuart' ) ? ' selected="selected"' : '' ?>>easeInQuart</option>
																<option<?php echo ($options['easingin'] == 'easeOutQuart' ) ? ' selected="selected"' : '' ?>>easeOutQuart</option>
																<option<?php echo ($options['easingin'] == 'easeInOutQuart' ) ? ' selected="selected"' : '' ?>>easeInOutQuart</option>
																<option<?php echo ($options['easingin'] == 'easeInQuint' ) ? ' selected="selected"' : '' ?>>easeInQuint</option>
																<option<?php echo ($options['easingin'] == 'easeOutQuint' ) ? ' selected="selected"' : '' ?>>easeOutQuint</option>
																<option<?php echo ($options['easingin'] == 'easeInOutQuint' ) ? ' selected="selected"' : '' ?>>easeInOutQuint</option>
																<option<?php echo ($options['easingin'] == 'easeInSine' ) ? ' selected="selected"' : '' ?>>easeInSine</option>
																<option<?php echo ($options['easingin'] == 'easeOutSine' ) ? ' selected="selected"' : '' ?>>easeOutSine</option>
																<option<?php echo ($options['easingin'] == 'easeInOutSine' ) ? ' selected="selected"' : '' ?>>easeInOutSine</option>
																<option<?php echo ($options['easingin'] == 'easeInExpo' ) ? ' selected="selected"' : '' ?>>easeInExpo</option>
																<option<?php echo ($options['easingin'] == 'easeOutExpo' ) ? ' selected="selected"' : '' ?>>easeOutExpo</option>
																<option<?php echo ($options['easingin'] == 'easeInOutExpo' ) ? ' selected="selected"' : '' ?>>easeInOutExpo</option>
																<option<?php echo ($options['easingin'] == 'easeInCirc' ) ? ' selected="selected"' : '' ?>>easeInCirc</option>
																<option<?php echo ($options['easingin'] == 'easeOutCirc' ) ? ' selected="selected"' : '' ?>>easeOutCirc</option>
																<option<?php echo ($options['easingin'] == 'easeInOutCirc' ) ? ' selected="selected"' : '' ?>>easeInOutCirc</option>
																<option<?php echo ($options['easingin'] == 'easeInElastic' ) ? ' selected="selected"' : '' ?>>easeInElastic</option>
																<option<?php echo ($options['easingin'] == 'easeOutElastic' ) ? ' selected="selected"' : '' ?>>easeOutElastic</option>
																<option<?php echo ($options['easingin'] == 'easeInOutElastic' ) ? ' selected="selected"' : '' ?>>easeInOutElastic</option>
																<option<?php echo ($options['easingin'] == 'easeInBack' ) ? ' selected="selected"' : '' ?>>easeInBack</option>
																<option<?php echo ($options['easingin'] == 'easeOutBack' ) ? ' selected="selected"' : '' ?>>easeOutBack</option>
																<option<?php echo ($options['easingin'] == 'easeInOutBack' ) ? ' selected="selected"' : '' ?>>easeInOutBack</option>
																<option<?php echo ($options['easingin'] == 'easeInBounce' ) ? ' selected="selected"' : '' ?>>easeInBounce</option>
																<option<?php echo ($options['easingin'] == 'easeOutBounce' ) ? ' selected="selected"' : '' ?>>easeOutBounce</option>
																<option<?php echo ($options['easingin'] == 'easeInOutBounce' ) ? ' selected="selected"' : '' ?>>easeInOutBounce</option>
															</select>
														</td>
														<td><input type="text" name="durationin" value="<?php echo !empty($options['durationin']) ? $options['durationin'] : '500' ?>"> ms</td>
													</tr>
													<tr>
														<td><?php _e('Mouse out animation', WPS_TEXTDOMAIN) ?></td>
														<td>
															<?php $options['directionout'] = empty($options['directionout']) ? 'fade' : $options['directionout']; ?>
															<select name="directionout">
																<option value="fade"<?php echo ($options['directionout'] == 'fade' ) ? ' selected="selected"' : '' ?>><?php _e('fade', WPS_TEXTDOMAIN) ?></option>
																<option value="top"<?php echo ($options['directionout'] == 'top') ? ' selected="selected"' : '' ?>><?php _e('top', WPS_TEXTDOMAIN) ?></option>
																<option value="bottom" <?php echo ($options['directionout'] == 'bottom') ? ' selected="selected"' : '' ?>><?php _e('bottom', WPS_TEXTDOMAIN) ?></option>
																<option value="left"<?php echo ($options['directionout'] == 'left') ? ' selected="selected"' : '' ?>><?php _e('left', WPS_TEXTDOMAIN) ?></option>
																<option value="right"<?php echo ($options['directionout'] == 'right') ? ' selected="selected"' : '' ?>><?php _e('right', WPS_TEXTDOMAIN) ?></option>
															</select>
														</td>
														<td>
															<?php $options['easingout'] = empty($options['easingout']) ? 'easeInBack' : $options['easingout']; ?>
															<select name="easingout">
																<option<?php echo ($options['easingout'] == 'linear' ) ? ' selected="selected"' : '' ?>>linear</option>
																<option<?php echo ($options['easingout'] == 'swing' ) ? ' selected="selected"' : '' ?>>swing</option>
																<option<?php echo ($options['easingout'] == 'easeInQuad' ) ? ' selected="selected"' : '' ?>>easeInQuad</option>
																<option<?php echo ($options['easingout'] == 'easeOutQuad' ) ? ' selected="selected"' : '' ?>>easeOutQuad</option>
																<option<?php echo ($options['easingout'] == 'easeInOutQuad' ) ? ' selected="selected"' : '' ?>>easeInOutQuad</option>
																<option<?php echo ($options['easingout'] == 'easeInCubic' ) ? ' selected="selected"' : '' ?>>easeInCubic</option>
																<option<?php echo ($options['easingout'] == 'easeOutCubic' ) ? ' selected="selected"' : '' ?>>easeOutCubic</option>
																<option<?php echo ($options['easingout'] == 'easeInOutCubic' ) ? ' selected="selected"' : '' ?>>easeInOutCubic</option>
																<option<?php echo ($options['easingout'] == 'easeInQuart' ) ? ' selected="selected"' : '' ?>>easeInQuart</option>
																<option<?php echo ($options['easingout'] == 'easeOutQuart' ) ? ' selected="selected"' : '' ?>>easeOutQuart</option>
																<option<?php echo ($options['easingout'] == 'easeInOutQuart' ) ? ' selected="selected"' : '' ?>>easeInOutQuart</option>
																<option<?php echo ($options['easingout'] == 'easeInQuint' ) ? ' selected="selected"' : '' ?>>easeInQuint</option>
																<option<?php echo ($options['easingout'] == 'easeOutQuint' ) ? ' selected="selected"' : '' ?>>easeOutQuint</option>
																<option<?php echo ($options['easingout'] == 'easeInOutQuint' ) ? ' selected="selected"' : '' ?>>easeInOutQuint</option>
																<option<?php echo ($options['easingout'] == 'easeInSine' ) ? ' selected="selected"' : '' ?>>easeInSine</option>
																<option<?php echo ($options['easingout'] == 'easeOutSine' ) ? ' selected="selected"' : '' ?>>easeOutSine</option>
																<option<?php echo ($options['easingout'] == 'easeInOutSine' ) ? ' selected="selected"' : '' ?>>easeInOutSine</option>
																<option<?php echo ($options['easingout'] == 'easeInExpo' ) ? ' selected="selected"' : '' ?>>easeInExpo</option>
																<option<?php echo ($options['easingout'] == 'easeOutExpo' ) ? ' selected="selected"' : '' ?>>easeOutExpo</option>
																<option<?php echo ($options['easingout'] == 'easeInOutExpo' ) ? ' selected="selected"' : '' ?>>easeInOutExpo</option>
																<option<?php echo ($options['easingout'] == 'easeInCirc' ) ? ' selected="selected"' : '' ?>>easeInCirc</option>
																<option<?php echo ($options['easingout'] == 'easeOutCirc' ) ? ' selected="selected"' : '' ?>>easeOutCirc</option>
																<option<?php echo ($options['easingout'] == 'easeInOutCirc' ) ? ' selected="selected"' : '' ?>>easeInOutCirc</option>
																<option<?php echo ($options['easingout'] == 'easeInElastic' ) ? ' selected="selected"' : '' ?>>easeInElastic</option>
																<option<?php echo ($options['easingout'] == 'easeOutElastic' ) ? ' selected="selected"' : '' ?>>easeOutElastic</option>
																<option<?php echo ($options['easingout'] == 'easeInOutElastic' ) ? ' selected="selected"' : '' ?>>easeInOutElastic</option>
																<option<?php echo ($options['easingout'] == 'easeInBack' ) ? ' selected="selected"' : '' ?>>easeInBack</option>
																<option<?php echo ($options['easingout'] == 'easeOutBack' ) ? ' selected="selected"' : '' ?>>easeOutBack</option>
																<option<?php echo ($options['easingout'] == 'easeInOutBack' ) ? ' selected="selected"' : '' ?>>easeInOutBack</option>
																<option<?php echo ($options['easingout'] == 'easeInBounce' ) ? ' selected="selected"' : '' ?>>easeInBounce</option>
																<option<?php echo ($options['easingout'] == 'easeOutBounce' ) ? ' selected="selected"' : '' ?>>easeOutBounce</option>
																<option<?php echo ($options['easingout'] == 'easeInOutBounce' ) ? ' selected="selected"' : '' ?>>easeInOutBounce</option>
															</select>
														</td>
														<td><input type="text" name="durationout" value="<?php echo !empty($options['durationout']) ? $options['durationout'] : '250' ?>"> ms</td>
													</tr>
													<tr>
														<td><?php _e('Bubble animation', WPS_TEXTDOMAIN) ?></td>
														<td></td>
														<td>
															<?php $options['spot_bubble_easing'] = empty($options['spot_bubble_easing']) ? 'easeOutBack' : $options['spot_bubble_easing']; ?>
															<select name="spot_bubble_easing">
																<option<?php echo ($options['spot_bubble_easing'] == 'linear' ) ? ' selected="selected"' : '' ?>>linear</option>
																<option<?php echo ($options['spot_bubble_easing'] == 'swing' ) ? ' selected="selected"' : '' ?>>swing</option>
																<option<?php echo ($options['spot_bubble_easing'] == 'easeInQuad' ) ? ' selected="selected"' : '' ?>>easeInQuad</option>
																<option<?php echo ($options['spot_bubble_easing'] == 'easeOutQuad' ) ? ' selected="selected"' : '' ?>>easeOutQuad</option>
																<option<?php echo ($options['spot_bubble_easing'] == 'easeInOutQuad' ) ? ' selected="selected"' : '' ?>>easeInOutQuad</option>
																<option<?php echo ($options['spot_bubble_easing'] == 'easeInCubic' ) ? ' selected="selected"' : '' ?>>easeInCubic</option>
																<option<?php echo ($options['spot_bubble_easing'] == 'easeOutCubic' ) ? ' selected="selected"' : '' ?>>easeOutCubic</option>
																<option<?php echo ($options['spot_bubble_easing'] == 'easeInOutCubic' ) ? ' selected="selected"' : '' ?>>easeInOutCubic</option>
																<option<?php echo ($options['spot_bubble_easing'] == 'easeInQuart' ) ? ' selected="selected"' : '' ?>>easeInQuart</option>
																<option<?php echo ($options['spot_bubble_easing'] == 'easeOutQuart' ) ? ' selected="selected"' : '' ?>>easeOutQuart</option>
																<option<?php echo ($options['spot_bubble_easing'] == 'easeInOutQuart' ) ? ' selected="selected"' : '' ?>>easeInOutQuart</option>
																<option<?php echo ($options['spot_bubble_easing'] == 'easeInQuint' ) ? ' selected="selected"' : '' ?>>easeInQuint</option>
																<option<?php echo ($options['spot_bubble_easing'] == 'easeOutQuint' ) ? ' selected="selected"' : '' ?>>easeOutQuint</option>
																<option<?php echo ($options['spot_bubble_easing'] == 'easeInOutQuint' ) ? ' selected="selected"' : '' ?>>easeInOutQuint</option>
																<option<?php echo ($options['spot_bubble_easing'] == 'easeInSine' ) ? ' selected="selected"' : '' ?>>easeInSine</option>
																<option<?php echo ($options['spot_bubble_easing'] == 'easeOutSine' ) ? ' selected="selected"' : '' ?>>easeOutSine</option>
																<option<?php echo ($options['spot_bubble_easing'] == 'easeInOutSine' ) ? ' selected="selected"' : '' ?>>easeInOutSine</option>
																<option<?php echo ($options['spot_bubble_easing'] == 'easeInExpo' ) ? ' selected="selected"' : '' ?>>easeInExpo</option>
																<option<?php echo ($options['spot_bubble_easing'] == 'easeOutExpo' ) ? ' selected="selected"' : '' ?>>easeOutExpo</option>
																<option<?php echo ($options['spot_bubble_easing'] == 'easeInOutExpo' ) ? ' selected="selected"' : '' ?>>easeInOutExpo</option>
																<option<?php echo ($options['spot_bubble_easing'] == 'easeInCirc' ) ? ' selected="selected"' : '' ?>>easeInCirc</option>
																<option<?php echo ($options['spot_bubble_easing'] == 'easeOutCirc' ) ? ' selected="selected"' : '' ?>>easeOutCirc</option>
																<option<?php echo ($options['spot_bubble_easing'] == 'easeInOutCirc' ) ? ' selected="selected"' : '' ?>>easeInOutCirc</option>
																<option<?php echo ($options['spot_bubble_easing'] == 'easeInElastic' ) ? ' selected="selected"' : '' ?>>easeInElastic</option>
																<option<?php echo ($options['spot_bubble_easing'] == 'easeOutElastic' ) ? ' selected="selected"' : '' ?>>easeOutElastic</option>
																<option<?php echo ($options['spot_bubble_easing'] == 'easeInOutElastic' ) ? ' selected="selected"' : '' ?>>easeInOutElastic</option>
																<option<?php echo ($options['spot_bubble_easing'] == 'easeInBack' ) ? ' selected="selected"' : '' ?>>easeInBack</option>
																<option<?php echo ($options['spot_bubble_easing'] == 'easeOutBack' ) ? ' selected="selected"' : '' ?>>easeOutBack</option>
																<option<?php echo ($options['spot_bubble_easing'] == 'easeInOutBack' ) ? ' selected="selected"' : '' ?>>easeInOutBack</option>
																<option<?php echo ($options['spot_bubble_easing'] == 'easeInBounce' ) ? ' selected="selected"' : '' ?>>easeInBounce</option>
																<option<?php echo ($options['spot_bubble_easing'] == 'easeOutBounce' ) ? ' selected="selected"' : '' ?>>easeOutBounce</option>
																<option<?php echo ($options['spot_bubble_easing'] == 'easeInOutBounce' ) ? ' selected="selected"' : '' ?>>easeInOutBounce</option>
															</select>
														</td>
														<td><input type="text" name="spot_bubble_duration" value="<?php echo !empty($options['spot_bubble_duration']) ? $options['spot_bubble_duration'] : '200' ?>"> ms</td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
									<tr>
										<td><?php _e('Delay', WPS_TEXTDOMAIN) ?></td>
										<td><input type="text" name="delay" class="wpstickies_delay" value="<?php echo !empty($options['delay']) ? $options['delay'] : '30' ?>"></td>
										<td class="desc"><?php _e('Delay in milliseconds between the transition of tags to have a parallax-like effect.', WPS_TEXTDOMAIN) ?></td>
									</tr>
									<tr class="subheader">
										<th colspan="3">
											<?php _e('Spot bubble', WPS_TEXTDOMAIN) ?>
										</th>
									</tr>
									<tr>
										<td><?php _e('Spot bubble direction', WPS_TEXTDOMAIN) ?></td>
										<td>
											<select name="spot_bubble_direction">
												<option value="top"<?php echo ($options['spot_bubble_direction'] == 'top' ) ? ' selected="selected"' : '' ?>><?php _e('Top', WPS_TEXTDOMAIN) ?></option>
												<option value="right"<?php echo ($options['spot_bubble_direction'] == 'right' ) ? ' selected="selected"' : '' ?>><?php _e('Right', WPS_TEXTDOMAIN) ?></option>
												<option value="bottom"<?php echo ($options['spot_bubble_direction'] == 'bottom' ) ? ' selected="selected"' : '' ?>><?php _e('Bottom', WPS_TEXTDOMAIN) ?></option>
												<option value="left"<?php echo ($options['spot_bubble_direction'] == 'left' ) ? ' selected="selected"' : '' ?>><?php _e('Left', WPS_TEXTDOMAIN) ?></option>
											</select>
										</td>
										<td class="desc"><?php _e('The default position of the spot bubble.', WPS_TEXTDOMAIN) ?></td>
									</tr>
									<tr>
										<td><?php _e('Change bubble direction', WPS_TEXTDOMAIN) ?></td>
										<td><input type="checkbox" name="auto_change_direction"<?php echo ((empty($option) && $autoChangeDirectionDefault == true) || isset($options['auto_change_direction'])) ? ' checked="checked"' : '' ?>></td>
										<td class="desc"><?php _e('wpStickies can automatically reposition spot bubbles when there is no enough free space to display them with the default settings.', WPS_TEXTDOMAIN) ?></td>
									</tr>
									<tr>
										<td><?php _e('Spot bubble distance', WPS_TEXTDOMAIN) ?></td>
										<td><input type="text" name="spot_bubble_distance" value="<?php echo !empty($options['spot_bubble_distance']) ? $options['spot_bubble_distance'] : '2' ?>"></td>
										<td class="desc"><?php _e('The distance in pixels between the spot and the spot bubble.', WPS_TEXTDOMAIN) ?></td>
									</tr>
									<tr>
										<td><?php _e('Spot buttons position', WPS_TEXTDOMAIN) ?></td>
										<td>
											<?php $options['spot_bubble_spot_buttons_positiondirection'] = empty($options['spot_bubble_spot_buttons_positiondirection']) ? 'left' : $options['spot_bubble_spot_buttons_positiondirection']; ?>
											<select name="spot_bubble_spot_buttons_positiondirection">
												<option value="top"<?php echo ($options['spot_bubble_spot_buttons_positiondirection'] == 'top' ) ? ' selected="selected"' : '' ?>><?php _e('top', WPS_TEXTDOMAIN) ?></option>
												<option value="right"<?php echo ($options['spot_bubble_spot_buttons_positiondirection'] == 'right' ) ? ' selected="selected"' : '' ?>><?php _e('right', WPS_TEXTDOMAIN) ?></option>
												<option value="bottom"<?php echo ($options['spot_bubble_spot_buttons_positiondirection'] == 'bottom' ) ? ' selected="selected"' : '' ?>><?php _e('bottom', WPS_TEXTDOMAIN) ?></option>
												<option value="left"<?php echo ($options['spot_bubble_spot_buttons_positiondirection'] == 'left' ) ? ' selected="selected"' : '' ?>><?php _e('left', WPS_TEXTDOMAIN) ?></option>
											</select>
										</td>
										<td class="desc"><?php _e('The alignment of spot buttons withing the spot bubble.', WPS_TEXTDOMAIN) ?></td>
									</tr>
									<tr class="subheader">
										<th colspan="3">
											<?php _e('Other', WPS_TEXTDOMAIN) ?>
										</th>
									</tr>
									<tr>
										<td><?php _e('Show messages', WPS_TEXTDOMAIN) ?></td>
										<td><input type="checkbox" name="show_messages"<?php echo ((empty($option) && $showMessagesDefault == true) || isset($options['show_messages'])) ? ' checked="checked"' : '' ?>></td>
										<td class="desc"><?php _e('Show or hide the built-in notifications with explanatory messages to help new wpStickies users.', WPS_TEXTDOMAIN) ?></td>
									</tr>
									<tr>
										<td><?php _e('Always visible', WPS_TEXTDOMAIN) ?></td>
										<td><input type="checkbox" name="always_visible"<?php echo ((empty($option) && $alwaysVisibleDefault == true) || isset($options['always_visible'])) ? ' checked="checked"' : '' ?>></td>
										<td class="desc"><?php _e('Disable this option to only show tags when users are hovering over the images.', WPS_TEXTDOMAIN) ?></td>
									</tr>
								</tbody>
								<tbody>
									<tr>
										<td><?php _e('Use Google CDN version of jQuery', WPS_TEXTDOMAIN) ?></td>
										<td><input type="checkbox" name="wps_use_custom_jquery" class="exclude" <?php echo get_option('wps_use_custom_jquery', false) ? 'checked="checked"' : '' ?>></td>
										<td class="desc"><?php _e('This option will likely solve "Old jQuery" issues.', WPS_TEXTDOMAIN) ?></td>
									</tr>
									<tr>
										<td><?php _e("Include scripts in the footer", WPS_TEXTDOMAIN) ?></td>
										<td><input type="checkbox" name="wps_include_at_footer" class="exclude" <?php echo get_option('wps_include_at_footer', false) ? 'checked="checked"' : '' ?>></td>
										<td class="desc"><?php _e("Including resources in the footer could decrease load times, and solve other type of issues, but your theme might not support this method.", WPS_TEXTDOMAIN) ?></td>
									</tr>
									<tr>
										<td><?php _e('Concatenate output', WPS_TEXTDOMAIN) ?></td>
										<td><input type="checkbox" name="wps_concatenate_output" class="exclude" <?php echo get_option('wps_concatenate_output', true) ? 'checked="checked"' : '' ?>></td>
										<td class="desc"><?php _e("Concatenating the plugin's output could solve issues caused by custom filters your theme might use.", WPS_TEXTDOMAIN) ?></td>
									</tr>
									<tr>
										<td><?php _e('Put JS includes to body', WPS_TEXTDOMAIN) ?></td>
										<td><input type="checkbox" name="wps_put_js_to_body" class="exclude" <?php echo get_option('wps_put_js_to_body', false) ? 'checked="checked"' : '' ?>></td>
										<td class="desc"><?php _e('This is the most common workaround for jQuery related issues, and is recommended when you experience problems with jQuery.', WPS_TEXTDOMAIN) ?></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>


				<div class="wps-publish">
					<button class="button button-primary button-hero"><?php _e('Save changes', WPS_TEXTDOMAIN) ?></button>
				</div>
			</div>
			<?php endif ?>

			<!-- Stickies -->
			<div class="wps-page">

				<div id="wps-stickies-tabs">
					<a href="#" class="active"><?php _e('Pending', WPS_TEXTDOMAIN) ?></a>
					<a href="#"><?php _e('Latest', WPS_TEXTDOMAIN) ?></a>
					<a href="#"><?php _e('Restore', WPS_TEXTDOMAIN) ?></a>
					<div class="clear"></div>
				</div>
				<div id="wps-stickies">

					<!-- Stickies: Pending -->
					<div class="wps-box wps-stickies-box active">
						<!--<h2 class="header noborder"><?php _e('Pending stickies', WPS_TEXTDOMAIN) ?></h2>-->
						<table class="wps-stickies-table wpstickies-pending-table">
							<thead class="noborder">
								<tr>
									<th class="image"><?php _e('Preview', WPS_TEXTDOMAIN) ?></th>
									<th class="title"><?php _e('Title / Caption', WPS_TEXTDOMAIN) ?></th>
									<th class="content"><?php _e('Content', WPS_TEXTDOMAIN) ?></th>
									<th class="user"><?php _e('User', WPS_TEXTDOMAIN) ?></th>
									<th class="created"><?php _e('Created', WPS_TEXTDOMAIN) ?></th>
									<th class="actions" colspan="2"><?php _e('Actions', WPS_TEXTDOMAIN) ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($pending as $item) : ?>
								<?php
									$data = $item['data'];
									$titleRaw =  !empty($data['spot']['titleRaw']) ? stripslashes($data['spot']['titleRaw']) : stripslashes($data['spot']['title']);
									$contentRaw = !empty($data['spot']['contentRaw']) ? stripslashes($data['spot']['contentRaw']) : stripslashes($data['spot']['content']);
									$captionRaw = !empty($data['area']['captionRaw']) ? stripslashes($data['area']['captionRaw']) : stripslashes($data['area']['caption']);
								 ?>
								<tr>
									<td class="image">
										<a href="<?php echo $item['image']?>" class="wps-preview">
											<img src="<?php echo $item['image'] ?>">
										</a>
									</td>
									<td class="title">
										<?php if($data['sticky']['type'] == 'area') { ?>
										<?php echo !empty($captionRaw) ? $captionRaw : '<i>No caption</i>' ?>
										<?php } else { ?>
										<?php echo !empty($titleRaw) ? $titleRaw : '<i>No title</i>' ?>
										<?php } ?>
									</td>
									<td class="content">
										<?php if($data['sticky']['type'] == 'area') { ?>
										<?php _e('No content', WPS_TEXTDOMAIN) ?>
										<?php } else { ?>
										<?php echo !empty($contentRaw) ? htmlspecialchars($contentRaw) : '<i>No content</i>' ?>
										<?php } ?>
									</td>
									<td class="user">
										<?php if(empty($item['user_id'])) { ?>
										<?php _e('Unregistered', WPS_TEXTDOMAIN) ?>
										<?php } else { ?>
										<?php echo $item['user_name']?>
										<?php } ?>
									</td>
									<td class="created">
										<?php echo date(get_option('date_format'), $item['date_c']) ?><br>
										<?php echo date(get_option('time_format'), $item['date_c']) ?>
									</td>
									<td class="action wpstickies-actions">
										<a href="#" class="dashicons dashicons-yes accept" rel="wpstickies_accept,<?php echo $item['id']?>" title="Accept"></a>
										<a href="#" class="dashicons dashicons-dismiss reject" rel="wpstickies_reject,<?php echo $item['id']?>" title="Reject"></a>
									</td>
								</tr>
								<?php endforeach; ?>

								<?php if(empty($pending)) : ?>
								<tr class="empty">
									<td colspan="6"><?php _e('There are no pending stickies at the moment.', WPS_TEXTDOMAIN) ?></td>
								</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>

					<!-- Stickies: Latest -->
					<div class="wps-box wps-stickies-box">
						<!--<h2 class="header noborder"><?php _e('Latest stickies', WPS_TEXTDOMAIN) ?></h2>-->
						<table class="wps-stickies-table wpstickies-latest-table">
							<thead class="noborder">
								<tr>
									<th class="image"><?php _e('Preview', WPS_TEXTDOMAIN) ?></th>
									<th class="title"><?php _e('Title / Caption', WPS_TEXTDOMAIN) ?></th>
									<th class="content"><?php _e('Content', WPS_TEXTDOMAIN) ?></th>
									<th class="user"><?php _e('User', WPS_TEXTDOMAIN) ?></th>
									<th class="modified"><?php _e('Modified', WPS_TEXTDOMAIN) ?></th>
									<th class="actions"><?php _e('Actions', WPS_TEXTDOMAIN) ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($latest as $item) : ?>
								<?php
									$data = $item['data'];
									$titleRaw =  !empty($data['spot']['titleRaw']) ? stripslashes($data['spot']['titleRaw']) : stripslashes($data['spot']['title']);
									$contentRaw = !empty($data['spot']['contentRaw']) ? stripslashes($data['spot']['contentRaw']) : stripslashes($data['spot']['content']);
									$captionRaw = !empty($data['area']['captionRaw']) ? stripslashes($data['area']['captionRaw']) : stripslashes($data['area']['caption']);
								 ?>
								<tr>
									<td class="image">
										<a href="<?php echo $item['image'] ?>" class="wps-preview">
											<img src="<?php echo $item['image'] ?>">
										</a>
									</td>
									<td class="title">
										<?php if($data['sticky']['type'] == 'area') { ?>
										<?php echo !empty($captionRaw) ? $captionRaw : '<i>No caption</i>' ?>
										<?php } else { ?>
										<?php echo !empty($titleRaw) ? $titleRaw : '<i>No title</i>' ?>
										<?php } ?>
									</td>
									<td class="content">
										<?php if($data['sticky']['type'] == 'area') { ?>
										<?php _e('No content', WPS_TEXTDOMAIN) ?>
										<?php } else { ?>
										<?php echo !empty($contentRaw) ? htmlspecialchars($contentRaw) : '<i>No content</i>' ?>
										<?php } ?>
									</td>
									<td class="user"><?php echo $item['user_name'] ?></td>
									<td class="modified">
										<?php echo date(get_option('date_format'), $item['date_c']) ?><br>
										<?php echo date(get_option('time_format'), $item['date_c']) ?>
									</td>
									<td class="action wpstickies-actions">
										<a href="#" class="dashicons dashicons-dismiss remove" rel="wpstickies_remove,<?php echo $item['id'] ?>" title="Remove"></a>
										</td>
								</tr>
								<?php endforeach; ?>

								<?php if(empty($latest)) : ?>
								<tr class="empty">
									<td colspan="6"><?php _e('No stickes yet.', WPS_TEXTDOMAIN) ?></td>
								</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>

					<!-- Stickies: Restore -->
					<div class="wps-box wps-stickies-box">
						<!--<h2 class="header noborder"><?php _e('Restore removed stickies', WPS_TEXTDOMAIN) ?></h2>-->
						<table class="wps-stickies-table wpstickies-restore-table">
							<thead class="noborder">
								<tr>
									<th class="image"><?php _e('Preview', WPS_TEXTDOMAIN) ?></th>
									<th class="title"><?php _e('Title / Caption', WPS_TEXTDOMAIN) ?></th>
									<th class="content"><?php _e('Content', WPS_TEXTDOMAIN) ?></th>
									<th class="user"><?php _e('User', WPS_TEXTDOMAIN) ?></th>
									<th class="modified"><?php _e('Modified', WPS_TEXTDOMAIN) ?></th>
									<th class="actions"><?php _e('Actions', WPS_TEXTDOMAIN) ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($removed as $item) : ?>
								<?php
									$data = $item['data'];
									$titleRaw =  !empty($data['spot']['titleRaw']) ? stripslashes($data['spot']['titleRaw']) : stripslashes($data['spot']['title']);
									$contentRaw = !empty($data['spot']['contentRaw']) ? stripslashes($data['spot']['contentRaw']) : stripslashes($data['spot']['content']);
									$captionRaw = !empty($data['area']['captionRaw']) ? stripslashes($data['area']['captionRaw']) : stripslashes($data['area']['caption']);
								 ?>
								<tr>
									<td class="image">
										<a href="<?php echo $item['image'] ?>" class="wps-preview">
											<img src="<?php echo $item['image'] ?>">
										</a>
									</td>
									<td class="title">
										<?php if($data['sticky']['type'] == 'area') { ?>
										<?php echo !empty($captionRaw) ? $captionRaw : '<i>No caption</i>' ?>
										<?php } else { ?>
										<?php echo !empty($titleRaw) ? $titleRaw : '<i>No title</i>' ?>
										<?php } ?>
									</td>
									<td class="content">
										<?php if($data['sticky']['type'] == 'area') { ?>
										<?php _e('No content', WPS_TEXTDOMAIN) ?>
										<?php } else { ?>
										<?php echo !empty($contentRaw) ? htmlspecialchars($contentRaw) : '<i>No content</i>' ?>
										<?php } ?>
									</td>
									<td class="user"><?php echo $item['user_name'] ?></td>
									<td class="modified">
										<?php echo date(get_option('date_format'), $item['date_c']) ?><br>
										<?php echo date(get_option('time_format'), $item['date_c']) ?>
									</td>
									<td class="action wpstickies-actions">
										<a href="#" class="dashicons dashicons-backup restore" rel="wpstickies_restore,<?php echo $item['id']?>" title="Restore"></a>
										<a href="#" class="dashicons dashicons-trash delete" rel="wpstickies_delete,<?php echo $item['id']?>" title="Delete permanently"></a>
									</td>
								</tr>
								<?php endforeach; ?>

								<?php if(empty($removed)) : ?>
								<tr class="empty">
									<td colspan="6"><?php _e('There are no removed stickies yet.', WPS_TEXTDOMAIN) ?></td>
								</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<!-- Language -->
			<?php if($hasAdminPermissions) : ?>
			<div class="wps-page">
				<div class="wps-box wps-settings wps-language">
					<h3 class="header"><?php _e('Language', WPS_TEXTDOMAIN) ?></h3>
					<div class="inner">
						<?php _e('Note: You can translate wpStickies to multiple languages as well, these fields are shortcode aware, so you can use translation plugins like qTranslate. You can also use WPML\'s String Translation module. See the Help menu on the top right corner of the page for more details.', WPS_TEXTDOMAIN) ?>
					</div>
					<table>
						<tbody>
							<tr>
								<th colspan="3" style="border-top: 1px solid #DFDFDF;">Captions</th>
							</tr>
							<tr>
								<td><?php _e('Default caption text', WPS_TEXTDOMAIN) ?></td>
								<td><input type="text" name="lang_area_caption" value="<?php echo !empty($options['lang_area_caption']) ? htmlspecialchars(stripslashes($options['lang_area_caption'])) : __('add a name or caption', WPS_TEXTDOMAIN) ?>"></td>
							</tr>
							<tr>
								<td><?php _e('Default spot title', WPS_TEXTDOMAIN) ?></td>
								<td><input type="text" name="lang_spot_title" value="<?php echo !empty($options['lang_spot_title']) ? htmlspecialchars(stripslashes($options['lang_spot_title'])) : __('Sample Title', WPS_TEXTDOMAIN) ?>"></td>
							</tr>
							<tr>
								<td><?php _e('Default spot content', WPS_TEXTDOMAIN) ?></td>
								<td><input type="text" name="land_spot_content" value="<?php echo !empty($options['land_spot_content']) ? htmlspecialchars(stripslashes($options['land_spot_content'])) : __('You can write here text and you can also use HTML code. For example you can simply include an image or a link.', WPS_TEXTDOMAIN) ?>"></td>
							</tr>
							<tr>
								<th colspan="3">Social media buttons</th>
							</tr>
							<tr>
								<td><?php _e('Google button', WPS_TEXTDOMAIN) ?></td>
								<td><input type="text" name="lang_btn_google" value="<?php echo !empty($options['lang_btn_google']) ? htmlspecialchars(stripslashes($options['lang_btn_google'])) : __('Google', WPS_TEXTDOMAIN) ?>"></td>
							</tr>
							<tr>
								<td><?php _e('Youtube button', WPS_TEXTDOMAIN) ?></td>
								<td><input type="text" name="lang_btn_youtube" value="<?php echo !empty($options['lang_btn_youtube']) ? htmlspecialchars(stripslashes($options['lang_btn_youtube'])) : __('YouTube', WPS_TEXTDOMAIN) ?>"></td>
							</tr>
							<tr>
								<td><?php _e('Vimeo button', WPS_TEXTDOMAIN) ?></td>
								<td><input type="text" name="lang_btn_vimeo" value="<?php echo !empty($options['lang_btn_vimeo']) ? htmlspecialchars(stripslashes($options['lang_btn_vimeo'])) : __('Vimeo', WPS_TEXTDOMAIN) ?>"></td>
							</tr>
							<tr>
								<td><?php _e('Wikipedia button', WPS_TEXTDOMAIN) ?></td>
								<td><input type="text" name="lang_btn_wikipedia" value="<?php echo !empty($options['lang_btn_wikipedia']) ? htmlspecialchars(stripslashes($options['lang_btn_wikipedia'])) : __('Wikipedia', WPS_TEXTDOMAIN) ?>"></td>
							</tr>
							<tr>
								<td><?php _e('Facebook button', WPS_TEXTDOMAIN) ?></td>
								<td><input type="text" name="lang_btn_facebook" value="<?php echo !empty($options['lang_btn_facebook']) ? htmlspecialchars(stripslashes($options['lang_btn_facebook'])) : __('Facebook', WPS_TEXTDOMAIN) ?>"></td>
							</tr>
							<tr>
								<th colspan="3">Action messages</th>
							</tr>
							<tr>
								<td><?php _e('Hover message', WPS_TEXTDOMAIN) ?></td>
								<td><input type="text" name="lang_msg_over" value="<?php echo !empty($options['lang_msg_over']) ? htmlspecialchars(stripslashes($options['lang_msg_over'])) : __('wpStickies: Click on the image to create a new spot or draw an area to tag faces.', WPS_TEXTDOMAIN) ?>"></td>
							</tr>
							<tr>
								<td><?php _e('Spot drag message', WPS_TEXTDOMAIN) ?></td>
								<td><input type="text" name="lang_msg_drag_spot" value="<?php echo !empty($options['lang_msg_drag_spot']) ? htmlspecialchars(stripslashes($options['lang_msg_drag_spot'])) : __('wpStickies: You can drag this sticky anywhere over the image by taking and moving the spot.', WPS_TEXTDOMAIN) ?>"></td>
							</tr>
							<tr>
								<td><?php _e('Area drag message', WPS_TEXTDOMAIN) ?></td>
								<td><input type="text" name="lang_msg_drag_area" value="<?php echo !empty($options['lang_msg_drag_area']) ? htmlspecialchars(stripslashes($options['lang_msg_drag_area'])) : __('wpStickies: You can drag this sticky anywhere over the image by taking and moving the area.', WPS_TEXTDOMAIN) ?>"></td>
							</tr>
							<tr>
								<td><?php _e('Save button message', WPS_TEXTDOMAIN) ?></td>
								<td><input type="text" name="lang_msg_btn_save" value="<?php echo !empty($options['lang_msg_btn_save']) ? htmlspecialchars(stripslashes($options['lang_msg_btn_save'])) : __('wpStickies: SAVE CHANGES', WPS_TEXTDOMAIN) ?>"></td>
							</tr>
							<tr>
								<td><?php _e('Remove button message', WPS_TEXTDOMAIN) ?></td>
								<td><input type="text" name="lang_msg_btn_remove" value="<?php echo !empty($options['lang_msg_btn_remove']) ? htmlspecialchars(stripslashes($options['lang_msg_btn_remove'])) : __('wpStickies: REMOVE THIS STICKY', WPS_TEXTDOMAIN) ?>"></td>
							</tr>
							<tr>
								<td><?php _e('Reposition button message', WPS_TEXTDOMAIN) ?></td>
								<td><input type="text" name="lang_msg_btn_reposition" value="<?php echo !empty($options['lang_msg_btn_reposition']) ? htmlspecialchars(stripslashes($options['lang_msg_btn_reposition'])) : __('wpStickies: CHANGE THE DIRECTION OF THE BUBBLE', WPS_TEXTDOMAIN) ?>"></td>
							</tr>
							<tr>
								<td><?php _e('Color button message', WPS_TEXTDOMAIN) ?></td>
								<td><input type="text" name="lang_msg_btn_color" value="<?php echo !empty($options['lang_msg_btn_color']) ? htmlspecialchars(stripslashes($options['lang_msg_btn_color'])) : __('wpStickies: CHANGE THE COLOR OF THE BUBBLE', WPS_TEXTDOMAIN) ?>"></td>
							</tr>
							<tr>
								<td><?php _e('Size button message', WPS_TEXTDOMAIN) ?></td>
								<td><input type="text" name="lang_msg_btn_size" value="<?php echo !empty($options['lang_msg_btn_size']) ? htmlspecialchars(stripslashes($options['lang_msg_btn_size'])) : __('wpStickies: CHANGE THE WIDTH OF THE BUBBLE', WPS_TEXTDOMAIN) ?>"></td>
							</tr>
							<tr>
								<th colspan="3">Event messages</th>
							</tr>
							<tr>
								<td><?php _e('Save message', WPS_TEXTDOMAIN) ?></td>
								<td><input type="text" name="lang_msg_save" value="<?php echo !empty($options['lang_msg_save']) ? htmlspecialchars(stripslashes($options['lang_msg_save'])) : __('wpStickies: STICKY SAVED', WPS_TEXTDOMAIN) ?>"></td>
							</tr>
							<tr>
								<td><?php _e('Remove message', WPS_TEXTDOMAIN) ?></td>
								<td><input type="text" name="lang_msg_remove" value="<?php echo !empty($options['lang_msg_remove']) ? htmlspecialchars(stripslashes($options['lang_msg_remove'])) : __('wpStickies: STICKY REMOVED', WPS_TEXTDOMAIN) ?>"></td>
							</tr>
							<tr>
								<td><?php _e('Disable message', WPS_TEXTDOMAIN) ?></td>
								<td><input type="text" name="lang_msg_disabled" value="<?php echo !empty($options['lang_msg_disabled']) ? htmlspecialchars(stripslashes($options['lang_msg_disabled'])) : __('Disable wpStickies on this image', WPS_TEXTDOMAIN) ?>"></td>
							</tr>
							<tr>
								<td><?php _e('Remove confirmation message', WPS_TEXTDOMAIN) ?></td>
								<td><input type="text" name="lang_conf_remove" value="<?php echo !empty($options['lang_conf_remove']) ? htmlspecialchars(stripslashes($options['lang_conf_remove'])) :  __('wpStickies: You clicked to remove this sticky. If you confirm, it will be permanently removed from the database. Are you sure?', WPS_TEXTDOMAIN) ?>"></td>
							</tr>
							<tr>
								<td><?php _e('Remove error message', WPS_TEXTDOMAIN) ?></td>
								<td><input type="text" name="lang_err_remove" value="<?php echo !empty($options['lang_err_remove']) ? htmlspecialchars(stripslashes($options['lang_err_remove'])) : __("wpStickies: The following error occurred during remove: You don't have permission to remove this sticky!", WPS_TEXTDOMAIN) ?>"></td>
							</tr>
							<tr>
								<td><?php _e('Create error message', WPS_TEXTDOMAIN) ?></td>
								<td><input type="text" name="lang_err_create" value="<?php echo !empty($options['lang_err_create']) ? htmlspecialchars(stripslashes($options['lang_err_create'])) : __("wpStickies: The following error occurred during save: You don't have permission to create new stickies!", WPS_TEXTDOMAIN) ?>"></td>
							</tr>
							<tr>
								<td><?php _e('Modify error message', WPS_TEXTDOMAIN) ?></td>
								<td><input type="text" name="lang_err_modify" value="<?php echo !empty($options['lang_err_modify']) ? htmlspecialchars(stripslashes($options['lang_err_modify'])) : __("wpStickies: The following error occurred during save: You don't have permission to modify this sticky!", WPS_TEXTDOMAIN) ?>"></td>
							</tr>
						</tbody>
					</table>
				</div>

				<div class="wps-publish">
					<button class="button button-primary button-hero"><?php _e('Save changes', WPS_TEXTDOMAIN) ?></button>
				</div>
			</div>
			<?php endif ?>
		</form>

		<!-- Updates -->
		<?php if($hasAdminPermissions) : ?>
		<div class="wps-page">
			<?php if($GLOBALS['wpsAutoUpdateBox'] == true) : ?>
			<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post" class="wps-box wps-settings wps-auto-update">
				<input type="hidden" name="action" value="wpstickies_verify_purchase_code">
				<?php wp_nonce_field('wps-save-autoupdate-settings'); ?>
				<h3 class="header medium">
					<?php _e('Auto-updates', WPS_TEXTDOMAIN) ?>
					<figure><span>|</span>Update notifications and one-click installation</figure>
				</h3>
				<table>
					<tbody>
						<tr>
							<td class="right"><?php _e('Purchase code:', WPS_TEXTDOMAIN) ?></td>
							<td class="desc">
								<input type="text" class="license-key" name="purchase_code" value="<?php echo $code ?>" placeholder="e.g. bc8e2b24-3f8c-4b21-8b4b-90d57a38e3c7"><br>
							</td>
							<td class="right"><?php _e('Release channel:', WPS_TEXTDOMAIN) ?></td>
							<td>
								<label><input type="radio" name="channel" value="stable" <?php echo ($channel === 'stable') ? 'checked="checked"' : ''?>> <?php _e('Stable', WPS_TEXTDOMAIN) ?></label>
								<label data-help="<?php _e('Although pre-release versions should be fine, they might contain unknown issues, and are not recommended for sites in production.', WPS_TEXTDOMAIN) ?>">
									<input type="radio" name="channel" value="beta" <?php echo ($channel === 'beta') ? 'checked="checked"' : ''?>> <?php _e('Beta', WPS_TEXTDOMAIN) ?>
								</label>
							</td>
						</tr>
						<tr>
							<td colspan="4"><?php _e('To receive auto-updates, you need to enter your item purchase code. You can find it on your CodeCanyon downloads page, just click on the Download button and choose the "Licence Certificate" option. This will download a text file that contains your purchase code.', WPS_TEXTDOMAIN) ?></td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="4">
								<button class="button button-primary"><?php _e('Save changes', WPS_TEXTDOMAIN) ?></button>
								<span style="<?php echo ($validity == '0' && $code != '') ? 'color: #c33219;' : 'color: #4b982f'?>">
									<?php
										if($validity == '1') {
											_e('Thank you for purchasing wpStickies.', WPS_TEXTDOMAIN);
											echo '<a href="update-core.php">', __('Check for update', WPS_TEXTDOMAIN), '</a>';

										} else if($code != '') {
											_e("Your purchase code doesn't appear to be valid.", WPS_TEXTDOMAIN);
										}
									?>
								</span>
							</td>
						</tr>
					</tfoot>
				</table>
			</form>

			<div class="wps-box wps-news">
				<div class="header medium">
					<h2><?php _e('wpStickies News', WPS_TEXTDOMAIN) ?></h2>
					<div class="filters">
						<span><?php _e('Filter:', WPS_TEXTDOMAIN) ?></span>
						<ul>
							<li class="active" data-page="all"><?php _e('All', WPS_TEXTDOMAIN) ?></li>
							<li data-page="announcements"><?php _e('Announcements', WPS_TEXTDOMAIN) ?></li>
							<li data-page="changes"><?php _e('Release log', WPS_TEXTDOMAIN) ?></li>
							<li data-page="betas"><?php _e('Beta versions', WPS_TEXTDOMAIN) ?></li>
						</ul>
					</div>
					<div class="wps-version"><?php _e('You have version', WPS_TEXTDOMAIN) ?> <?php echo WPS_PLUGIN_VERSION ?> <?php _e('installed', WPS_TEXTDOMAIN) ?></div>
				</div>
				<div>
					<iframe src="https://news.kreaturamedia.com/wpstickies/"></iframe>
				</div>
			</div>
			<?php endif; ?>
		</div>
		<?php endif ?>
	</div>
</form>