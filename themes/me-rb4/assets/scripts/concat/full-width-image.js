/**
 * Full width Image on 2-column layouts Script.
 */
window.WDS_fullWidth_Object = {};
( function( window, $, app ) {

	// Constructor.
	app.init = function() {
		app.cache();

		if ( app.meetsRequirements() ) {
			app.bindEvents();
		}
	};

	// Cache all the things.
	app.cache = function() {
		app.$c = {
			window: $(window),
			fullWidthSelector: $( '.primary .full-width' ),
			content: $( '.site-content .wrap' ),
		};
	};

	// Combine all events.
	app.bindEvents = function() {
		app.$c.window.on( 'load resize', app.doFullWidth );
	};

	// Do we meet the requirements?
	app.meetsRequirements = function() {
		return app.$c.fullWidthSelector.length;
	};

	// Some function.
	app.doFullWidth = function() {

		var windowWidth = $(window).width(), // get window width.
			contentWidth = app.$c.content.innerWidth(), // get contaienr width.
			difference = windowWidth - contentWidth, // subtract the two.
			containerDiffrence = difference / 2; // divide by two so to account for 1 side only.

		// Check to make sure we're still on desktop devices.
		if ( windowWidth > 1200 ) {

			// let's do the math.
			app.$c.fullWidthSelector.css( 'margin-left', -containerDiffrence );
		} else {
			app.$c.fullWidthSelector.css( 'margin-left', '-15px' ); // this is the .wrap padding on mobile.
		}
	};

	// Engage!
	$( app.init );

})( window, jQuery, window.WDS_fullWidth_Object );