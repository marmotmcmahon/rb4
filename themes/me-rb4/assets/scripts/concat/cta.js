/**
 * CTA on all screens.
 */
window.WDS_ctaBlock_Object = {};
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
			ctaBlockSelector: $( '.site-content .wrap' ),
			content: $( '.content-area' ),
			cta: $( '.flexible-call-to-action' ),
		};
	};

	// Combine all events.
	app.bindEvents = function() {
		app.$c.window.on( 'load resize', app.doCtaBlock );
	};

	// Do we meet the requirements?
	app.meetsRequirements = function() {
		return app.$c.ctaBlockSelector.length;
	};

	// Some function.
	app.doCtaBlock = function() {

		var windowWidth = $(window).width(), // get window width.
			contentWidth = app.$c.ctaBlockSelector.innerWidth(), // get contaienr width.
			content = app.$c.content.innerWidth(),
			difference = windowWidth - contentWidth, // subtract the two.
			containerDiffrence = difference / 2, // divide by two so to account for 1 side only.
			containerMargin = content + containerDiffrence;

		// Check to make sure we're still on desktop devices.
		if ( windowWidth > 1024 ) {

			// let's do the math.
			app.$c.cta.css( 'width', containerMargin ).css( 'margin-left', -containerDiffrence );
		} else {
			app.$c.cta.css( 'width', windowWidth ).css( 'margin-left', '-15px' ); // this is the .wrap padding on mobile.
		}
	};

	// Engage!
	$( app.init );

})( window, jQuery, window.WDS_ctaBlock_Object );