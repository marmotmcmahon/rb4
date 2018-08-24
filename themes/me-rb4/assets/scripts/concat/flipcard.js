/**
 * Flip Card Script.
 */
window.WDS_flipCard_Object = {};
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
			flipCardSelector: $( '.flip-container' ),
		};
	};

	// Combine all events.
	app.bindEvents = function() {
		app.$c.flipCardSelector.on( 'hover', app.doFlipCard );
	};

	// Do we meet the requirements?
	app.meetsRequirements = function() {
		return app.$c.flipCardSelector.length;
	};

	// Some function.
	app.doFlipCard = function() {
		$(this).toggleClass('flipped');
	};

	// Engage!
	$( app.init );

})( window, jQuery, window.WDS_flipCard_Object );