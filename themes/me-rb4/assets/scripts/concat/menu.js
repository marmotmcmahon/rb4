/**
 * File menu-file.js
 *
 * menu comment which helps describe the following object.
 */
window.wdsmenuObject = {};
( function( window, $, app ) {

	var menuButton = document.getElementById( 'menuButton' );

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
			menuSelector: $( '.menu-button' ),
			menuNav: $( '.main-navigation' ),
			menuLi: $( '#primary-menu li.menu-item-has-children' ),
			menuSubNav: $( '.sub-menu' ),
		};
	};

	// Combine all events.
	app.bindEvents = function() {
		app.$c.window.on( 'load', app.doMenu );
		app.$c.window.on( 'resize', app.removeToggled );
	};

	// Do we meet the requirements?
	app.meetsRequirements = function() {
		return app.$c.menuSelector.length;
	};

	// Some function.
	app.doMenu = function() {
		menuButton.addEventListener( 'click', function(e) {
			menuButton.classList.toggle( 'is-active' );
			e.preventDefault();

			app.$c.menuNav.toggleClass( 'is-open' );
		});
	};

	app.removeToggled = function() {
		var width = $(window).width();

		if ( width >= '1023' ) {
			app.$c.menuNav.removeClass( 'is-open' );
			app.$c.menuSelector.removeClass( 'is-active' );
		}
	}
	// Engage!
	$( app.init );

})( window, jQuery, window.wdsmenuObject );
