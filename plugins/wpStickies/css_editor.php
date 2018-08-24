<?php

	if(!defined('WPS_ROOT_FILE')) { 
		header('HTTP/1.0 403 Forbidden');
		exit;
	}

	// Get uploads dir
	$upload_dir = wp_upload_dir();
	$file = $upload_dir['basedir'].'/wpstickies.custom.css';

	// Get contents
	$contents = file_exists($file) ? file_get_contents($file) : '';
?>

<div class="wrap">

	<!-- Page title -->
	<h2>
		<?php _e('wpStickies CSS Editor', WPS_TEXTDOMAIN) ?>
		<a href="?page=wpstickies" class="add-new-h2"><?php _e('Back to the list', WPS_TEXTDOMAIN) ?></a>
	</h2>

	<!-- Error messages -->
	<?php if(isset($_GET['edited'])) : ?>
	<div class="wps-notification updated">
		<div><?php _e('Your changes has been saved!', WPS_TEXTDOMAIN) ?></div>
	</div>
	<?php endif; ?>
	<!-- End of error messages -->

	<!-- Editor box -->
	<div class="wps-box wps-skin-editor-box">
		<h3 class="header medium">
			<?php _e('Contents of your custom CSS file', WPS_TEXTDOMAIN) ?>
			<figure><span>|</span><?php _e('Ctrl+Q to fold/unfold a block', WPS_TEXTDOMAIN) ?></figure>
		</h3>
		<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post" class="inner">
			<input type="hidden" name="wps-user-css" value="1">
			<?php wp_nonce_field('wps-save-user-css'); ?>
			<textarea rows="10" cols="50" name="contents" class="wps-codemirror"><?php if(!empty($contents)) {
					echo htmlentities($contents);
				} else {
					_e('/* You can type here any CSS code that will be loaded both on your admin and front-end pages.', WPS_TEXTDOMAIN);
					echo NL;
					_e('Let us help you by giving a few exmaple CSS classes: */', WPS_TEXTDOMAIN);
					echo NL . NL;
					echo '.wps-spotbubble-bg { /* Spot bubble background */' . NL . TAB . 'background: white;' . NL . '}' .NL.NL;
					echo '.wps-spot { /* Spot pointer */' . NL  . NL . '}' . NL.NL;
				}?></textarea>
			<p class="footer">
				<?php if(!is_writable($upload_dir['basedir'])) { ?>
				<?php _e('You need to make your uploads folder writable before you can save your changes. See the <a href="http://codex.wordpress.org/Changing_File_Permissions" target="_blank">Codex</a> for more information.', WPS_TEXTDOMAIN) ?>
				<?php } else { ?>
				<button class="button-primary"><?php _e('Save changes', WPS_TEXTDOMAIN) ?></button>
				<?php _e('Using bad CSS code could break the appearance of your site. Changes cannot be reverted after saving.', WPS_TEXTDOMAIN) ?>
				<?php } ?>
			</p>
		</form>
	</div>
</div>
