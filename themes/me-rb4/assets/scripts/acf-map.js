(function($) {
	/*
	 *  new_map
	 *
	 *  This function will render a Google Map onto the selected jQuery element
	 *
	 *  @type	function
	 *  @date	8/11/2013
	 *  @since	4.3.0
	 *
	 *  @param	$el (jQuery element)
	 *  @return	n/a
	 */
	function new_map($el) {
		// var
		var $markers = $el.find('.marker');
		// vars
		var args = {
			zoom: 16,
			center: new google.maps.LatLng(0, 0),
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			scrollwheel: false,
			styles: [{
				"featureType": "administrative",
				"elementType": "labels.text.fill",
				"stylers": [
					{
						"color": "#62469c"
					},
					{
						"lightness": "-1"
					}
				]
			},
			{
				"featureType": "landscape",
				"elementType": "all",
				"stylers": [
					{
						"color": "#f2f2f2"
					}
				]
			},
			{
				"featureType": "landscape.man_made",
				"elementType": "geometry.fill",
				"stylers": [
					{
						"lightness": "0"
					},
					{
						"visibility": "on"
					},
					{
						"color": "#dfe0dd"
					}
				]
			},
			{
				"featureType": "landscape.man_made",
				"elementType": "labels.text.fill",
				"stylers": [
					{
						"lightness": "0"
					},
					{
						"visibility": "on"
					}
				]
			},
			{
				"featureType": "landscape.natural",
				"elementType": "geometry.fill",
				"stylers": [
					{
						"lightness": "-4"
					}
				]
			},
			{
				"featureType": "landscape.natural.landcover",
				"elementType": "geometry.fill",
				"stylers": [
					{
						"saturation": "18"
					},
					{
						"lightness": "-35"
					},
					{
						"gamma": "1.28"
					}
				]
			},
			{
				"featureType": "landscape.natural.terrain",
				"elementType": "geometry.fill",
				"stylers": [
					{
						"lightness": "-32"
					}
				]
			},
			{
				"featureType": "poi",
				"elementType": "all",
				"stylers": [
					{
						"visibility": "off"
					}
				]
			},
			{
				"featureType": "road",
				"elementType": "all",
				"stylers": [
					{
						"saturation": -100
					},
					{
						"lightness": 45
					}
				]
			},
			{
				"featureType": "road",
				"elementType": "labels.text.fill",
				"stylers": [
					{
						"color": "#59c2cc"
					}
				]
			},
			{
				"featureType": "road.highway",
				"elementType": "all",
				"stylers": [
					{
						"visibility": "simplified"
					}
				]
			},
			{
				"featureType": "road.arterial",
				"elementType": "labels.icon",
				"stylers": [
					{
						"visibility": "off"
					}
				]
			},
			{
				"featureType": "transit",
				"elementType": "all",
				"stylers": [
					{
						"visibility": "off"
					}
				]
			},
			{
				"featureType": "water",
				"elementType": "all",
				"stylers": [
					{
						"color": "#62469c"
					},
					{
						"visibility": "on"
					}
				]
			},
			{
				"featureType": "water",
				"elementType": "geometry.fill",
				"stylers": [
					{
						"lightness": "1"
					},
					{
						"color": "#392a52"
					}
				]
			}]
		};
		// create map
		var map = new google.maps.Map($el[0], args);
		// this is our gem
		google.maps.event.addDomListener(window, "resize", function() {
			var center = map.getCenter();
			google.maps.event.trigger(map, "resize");
			map.setCenter(center);
		});
		// add a markers reference
		map.markers = [];
		// add markers
		$markers.each(function() {
			add_marker($(this), map);
		});
		// center map
		center_map(map);
		// return
		return map;
	}
	/*
	 *  add_marker
	 *
	 *  This function will add a marker to the selected Google Map
	 *
	 *  @type	function
	 *  @date	8/11/2013
	 *  @since	4.3.0
	 *
	 *  @param	$marker (jQuery element)
	 *  @param	map (Google Map object)
	 *  @return	n/a
	 */
	function add_marker($marker, map) {
		// var
		var latlng = new google.maps.LatLng($marker.attr('data-lat'), $marker.attr('data-lng'));
		var icon = $marker.attr('data-icon');
		// create marker
		var marker = new google.maps.Marker({
			position: latlng,
			map: map,
			icon: icon,
		});
		// add to array
		map.markers.push(marker);
		// if marker contains HTML, add it to an infoWindow
		if ($marker.html()) {

			// create info window
			var infowindow = new google.maps.InfoWindow({
				content: $marker.html(),
				maxWidth: 200,
			});

			// show info window when marker is clicked
			google.maps.event.addListener(marker, 'click', function() {
				infowindow.open(map, marker);
				infowindow.setContent($marker.html());
			});

			// close info window when map is clicked
			google.maps.event.addListener(map, 'click', function(event) {
				if ( infowindow ) {
					infowindow.close();
				}
			});
		}
	}
	/*
	 *  center_map
	 *
	 *  This function will center the map, showing all markers attached to this map
	 *
	 *  @type	function
	 *  @date	8/11/2013
	 *  @since	4.3.0
	 *
	 *  @param	map (Google Map object)
	 *  @return	n/a
	 */
	function center_map(map) {
		// vars
		var bounds = new google.maps.LatLngBounds();
		// loop through all markers and create bounds
		$.each(map.markers, function(i, marker) {
			var latlng = new google.maps.LatLng(marker.position.lat(), marker.position.lng());
			bounds.extend(latlng);
		});
		// only 1 marker?
		if (map.markers.length == 1) {
			// set center of map
			map.setCenter(bounds.getCenter());
			map.setZoom(16);
		} else {
			// fit to bounds
			map.fitBounds(bounds);
			var listener = google.maps.event.addListener(map, "idle", function() {
			  map.setZoom(12);
			  google.maps.event.removeListener(listener);
			});
		}
	}
	/*
	 *  document ready
	 *
	 *  This function will render each map when the document is ready (page has loaded)
	 *
	 *  @type	function
	 *  @date	8/11/2013
	 *  @since	5.0.0
	 *
	 *  @param	n/a
	 *  @return	n/a
	 */
	// global var
	var map = null;
	$(document).ready(function() {
		$('.acf-map').each(function() {
			// create map
			map = new_map($(this));
		});
	});
})(jQuery);