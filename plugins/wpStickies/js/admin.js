(function( $ ) {

	$.fn.customCheckbox = function() {

		return this.each(function() {

			// Get the original element
			var el = this;

			// Hide the checkbox
			$(this).hide();

			// Create replacement element
			var rep = $('<a href="#"><span></span></a>').addClass('wps-checkbox').insertAfter(this);

			// Set default state
			if($(this).is(':checked')) {
				$(rep).addClass('on');
			} else {
				$(rep).addClass('off');
			}

			// Click event
			$(rep).click(function(e) {

				e.preventDefault();

				if( $(el).is(':checked') ) {
					$(el).prop('checked', false);
					$(rep).removeClass('on').addClass('off');
				} else {
					$(el).prop('checked', true);
					$(rep).removeClass('off').addClass('on');
				}
			});
		});
	};

})( jQuery );


function lsSetCookie(c_name,value,exdays) {
	var exdate=new Date();
	exdate.setDate(exdate.getDate() + exdays);
	var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
	document.cookie=c_name + "=" + c_value;
}

function lsGetCookie(c_name) {
	var i,x,y,ARRcookies=document.cookie.split(";");
	for (i=0;i<ARRcookies.length;i++) {
		x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
		y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
		x=x.replace(/^\s+|\s+$/g,"");
		if (x==c_name) {
			return unescape(y);
		}
	}
}


var wpsPreview = {

	init: function() {

		// Open event
		jQuery(document).on('click', 'a.wps-preview', function(event) {
			event.preventDefault();
			wpsPreview.preload(this);
		});

		// Resize
		jQuery(window).resize(function() {
			if(jQuery('.wps-preview-image').length) {
				wpsPreview.position();
			}
		});
	},

	preload: function(img) {

		// Preload
		jQuery('<img>').load(function() {
			setTimeout(function() {
				wpsPreview.open(img);
			}, 100);
		}).attr('src', jQuery(img).attr('href'));
	},

	open: function(img) {

		// Prepend overlay and image
		jQuery('<div>', { 'class': 'wps-admin-overlay' }).prependTo('body');
		jQuery('<div>', { 'class': 'wps-preview-wrapper' }).prependTo('body');
		jQuery('<img>').load(function() {
			jQuery(this).css({
				marginLeft: - jQuery(this).width() / 2,
				marginTop: - jQuery(this).height() / 2,
				opacity: 1
			})

			jQuery('.wps-preview-image').wpStickies(wpsGlobals.json);

		}).attr({ 
			'src': jQuery(img).attr('href'), 
			'class': 'wps-preview-image'
		}).prependTo('.wps-preview-wrapper');

		// Close events
		jQuery(document).on('click', '.wps-preview-wrapper', function() {
			var source = event.target || event.srcElement;
			if(source == this) {
				wpsPreview.close();
			}
		});

		jQuery(document).one('keydown', function(event) {
			if(event.which == 27) {
				wpsPreview.close();
			}
		});
	},

	position: function() {

		jQuery('.wps-preview-image').css({
			marginLeft: - jQuery('.wps-preview-image').width() / 2,
			marginTop: - jQuery('.wps-preview-image').height() / 2
		});
	},

	close: function() {
		jQuery('.wps-preview-image').wpStickies('destroy');
		jQuery('.wps-admin-overlay, .wps-preview-wrapper').remove();
		
	}
};


var wpsMedia = {

	progress: false,
	currPage: 1,
	lastPage: false,

	init: function() {

		jQuery('#wps-main-nav-bar.wps-media-editor :checkbox').click(function() {
			jQuery(this).closest('form').submit();
		});

		jQuery(window).scroll(function() {

			var docHeight = jQuery(document).height();
			var scrollTop = jQuery(window).scrollTop() + jQuery(window).height();
			if(docHeight - scrollTop < 1000 && !wpsMedia.progress && !wpsMedia.lastPage) {
				wpsMedia.progress = true;
				wpsMedia.load();
			}
		})
	},


	load: function() {

		var search = jQuery('#wps-main-nav-bar input[name="terms"]').val();
		var own = jQuery('#wps-main-nav-bar select[name="own"]').val();
		var params = {
			paged: wpsMedia.currPage,
			action: 'wps_media_editor',
			terms: search,
			own: own,
		};

		jQuery.getJSON( ajaxurl, jQuery.param(params), function(data) {
			
			wpsMedia.lastPage = data.lastPage;
			wpsMedia.currPage = data.currPage;

			for(var c = 0; c < data.items.length; c++) {
				jQuery('#wps-media-editor').append(
					'<div class="item">\
						<a href="'+data['items'][c]['url']+'" class="wps-preview">\
							<span class="dashicons dashicons-edit"></span>\
							<img src="'+data['items'][c]['thumb']+'">\
						</a>\
						<ul>\
							<li>'+data['items'][c]['title']+'</li>\
							<li>'+data['items'][c]['date']+'</li>\
							<li>by '+data['items'][c]['author']+'</li>\
						</ul>\
					</div>'
				);
			}
		});
	}
};



var wpStickies = {

	selectMainTab : function(el) {

		// Remove highlight from the other tabs
		jQuery('#wps-main-nav-bar a').removeClass('active');

		// Highlight selected tab
		jQuery(el).addClass('active');

		// Hide other pages
		jQuery('#wps-pages .wps-page').removeClass('active');

		// Show selected page
		jQuery('#wps-pages .wps-page').eq( jQuery(el).index() ).addClass('active')

		// Set cookie
		lsSetCookie('wps-main-tab', jQuery(el).index(), 100);
	},

	selectSettingsTab : function(li) {
		var index = jQuery(li).index();
		jQuery(li).siblings().removeClass('active');
		jQuery(li).addClass('active');
		jQuery('div.wps-settings-contents tbody.active').removeClass('active');
		jQuery('div.wps-settings-contents > table > tbody').eq(index).addClass('active');
	},

	selectStickyTab : function(el) {

		// Close other layers
		jQuery('#wps-stickies-tabs a').removeClass('active');
		jQuery('.wps-stickies-box').removeClass('active');

		// Open new layer
		jQuery(el).addClass('active');
		jQuery('.wps-stickies-box').eq( jQuery(el).index() ).addClass('active');

		// Set cookie
		lsSetCookie('wps-sticky-tab', jQuery(el).index(), 100);
	},

	openPreview : function(el) {

	},

	acceptSticky : function(el) {

		// Get params
		var params = jQuery(el).attr('rel').split(',');

		// Post query
		jQuery.post( ajaxurl, { action : params[0], id : params[1] } );

		// Fade out
		jQuery(el).closest('tr').fadeOut(500, function() {

			// Create placeholder if needed
			if(jQuery(el).closest('table').find('tr').length < 3) {

				// Append success message
				var replacement = jQuery(el).closest('tr').after( jQuery('<tr>', { 'class' : 'empty' })
					.append( jQuery('<td>', { 'colspan' : '6', 'text' : 'There are no more pending stickies at the moment.' }))
				);

				// Animate changes
				jQuery(el).closest('table').find('tr.empty').hide().fadeIn(500);

				// Remove every table row
				jQuery(el).closest('tr').prependTo('.wpstickies-latest-table').show().find('.created').attr('class', 'modified');

				// Remove placeholder if any
				jQuery(el).closest('tbody').find('tr.empty').remove();

				// Get actions holder TD
				var parent = jQuery(el).parent();

				// Remove previous actions
				jQuery(el).closest('tr').find('.wpstickies-actions a').remove();

				// Add new actions
				parent.append( jQuery('<a>', { 'href' : '#', 'class' : 'dashicons dashicons-dismiss remove', 'title' : 'Remove', 'rel' : 'wpstickies_remove,'+params[1]+'' } ));
			}

			// Insert it into the removed stickies
			jQuery(el).closest('tr').prependTo('.wpstickies-latest-table tbody').show();

			// Get actions holder TD
			var parent = jQuery(el).parent();

			// Replace action buttons
			jQuery(el).closest('tr').find('.wpstickies-actions a').remove();

			// Append restore action
			parent.append( jQuery('<a>', { 'href' : '#', 'class' : 'dashicons dashicons-dismiss remove', 'title' : 'Remove', 'rel' : 'wpstickies_remove,'+params[1]+'' } ));
		});
	},

	removeSticky : function(el) {

		// Get params
		var params = jQuery(el).attr('rel').split(',');

		// Post query
		jQuery.post( ajaxurl, { action : params[0], id : params[1] } );

		// Fade out
		jQuery(el).closest('tr').fadeOut(500, function() {

			// Create placeholder if needed
			if(jQuery(el).closest('table').find('tr').length < 3) {

				// Info msg
				if(jQuery(el).hasClass('reject')) {
					var msg = 'There are no more pending stickies at the moment.';
				} else {
					var msg = 'No stickies yet.';
				}

				// Append success message
				var replacement = jQuery(el).closest('tr').after( jQuery('<tr>', { 'class' : 'empty' })
					.append( jQuery('<td>', { 'colspan' : '6', 'text' : msg }))
				);

				// Animate changes
				jQuery(el).closest('table').find('tr.empty').hide().fadeIn(500);
			}

			// Insert it into the removed stickies
			jQuery(el).closest('tr').prependTo('.wpstickies-restore-table tbody').show().find('.created').attr('class', 'modified');

			// Get actions holder TD
			var parent = jQuery(el).parent();

			// Get actions holder TD
			var parent = jQuery(el).parent();

			// Replace action buttons
			jQuery(el).closest('tr').find('.wpstickies-actions a').remove();

			// Append restore action
			parent.append( jQuery('<a>', { 'href' : '#', 'class' : 'dashicons dashicons-backup restore', 'rel' : 'wpstickies_restore,'+params[1]+'', 'title' : 'Restore' }));
			parent.append( jQuery('<a>', { 'href' : '#', 'class' : 'dashicons dashicons-trash delete', 'rel' : 'wpstickies_delete,'+params[1]+'', 'title' : 'Delete permanently' }));
		});
	},

	restoreSticky : function(el) {

		// Get params
		var params = jQuery(el).attr('rel').split(',');

		// Post query
		jQuery.post( ajaxurl, { action : params[0], id : params[1] } );

		// Fade out
		jQuery(el).closest('tr').fadeOut(500, function() {

			// Create placeholder if needed
			if(jQuery(el).closest('table').find('tr').length < 3) {

				// Append success message
				var replacement = jQuery(el).closest('tr').after( jQuery('<tr>', { 'class' : 'empty' })
					.append( jQuery('<td>', { 'colspan' : '6', 'text' : 'There are no more removed stickies.' }))
				);

				// Animate changes
				jQuery(el).closest('table').find('tr.empty').hide().fadeIn(500);
			}

			// Insert it into the removed stickies
			jQuery(el).closest('tr').prependTo('.wpstickies-latest-table tbody').show();

			// Remove notification if any
			jQuery(el).closest('table').find('tr.empty').remove();

			// Get actions holder TD
			var parent = jQuery(el).parent();

			// Replace action buttons
			jQuery(el).closest('tr').find('.wpstickies-actions a').remove();

			// Append restore action
			parent.append( jQuery('<a>', { 'href' : '#', 'class' : 'dashicons dashicons-dismiss remove', 'title' : 'Remove', 'rel' : 'wpstickies_remove,'+params[1]+'' } ));
		});
	},

	deleteSticky : function(el) {

		// Get params
		var params = jQuery(el).attr('rel').split(',');

		// Post query
		jQuery.post( ajaxurl, { action : params[0], id : params[1] } );

		// Fade out
		jQuery(el).closest('tr').fadeOut(500, function() {

			// Create placeholder if needed
			if(jQuery(el).closest('table').find('tr').length < 3) {

				// Append success message
				var replacement = jQuery(el).closest('tr').after( jQuery('<tr>', { 'class' : 'empty' })
					.append( jQuery('<td>', { 'colspan' : '6', 'text' : 'There are no more removed stickies.' }))
				);

				// Animate changes
				jQuery(el).closest('table').find('tr.empty').hide().fadeIn(500);
			}
		});
	},

	submit : function(el) {

		// Search and rewrite the name attribute of form elements
		jQuery(el).find('input, select').filter(':not(.exclude)').each(function() {
			if(jQuery(this).attr('type') == 'hidden') { return true; }
			jQuery(this).data('original-name', jQuery(this).attr('name'));
			jQuery(this).attr('name', 'wpstickies-options['+jQuery(this).attr('name')+']');
		});

		// Set 'saving' state on buttons
		jQuery('.wps-publish').addClass('saving').find('button').text('Saving ...').attr('disabled', true);

		// Send data
		jQuery.post( jQuery(el).attr('action'), jQuery(el).serialize(), function(data) {

			// Button feedback
			jQuery('.wps-publish').removeClass('saving').addClass('saved').find('button').text('Saved');
			setTimeout(function() {
				jQuery('.wps-publish').removeClass('saved').find('button').text('Save changes').attr('disabled', false);
			}, 2000);

			if(data != 'SUCCESS') {
				alert(data);
			}

			// Restore name fields
			jQuery(el).find('input, select').each(function() {
				jQuery(this).attr('name', jQuery(this).data('original-name'));
			});
		});
	}
};

jQuery(document).ready(function() {

	// Settings: checkboxes
	jQuery('.wps-settings :checkbox').customCheckbox();

	jQuery('ul.wps-settings-sidebar > li').click(function() {
		wpStickies.selectSettingsTab(this);
	});

	// wpStickies selectors
	jQuery('.wps-settings input.selector').tagsInput({
		width : '600px',
		height : 'auto',
		defaultText : 'add a rule'
	});

	jQuery('.wps-settings input.custom_roles').tagsInput({
		width : '600px',
		height : 'auto',
	});

	// Main tab bar page select
	jQuery('#wps-main-nav-bar a:not(.unselectable)').click(function(e) {
		e.preventDefault();
		wpStickies.selectMainTab( this );
	});

	// Restore last main tab
	if(typeof lsGetCookie('wps-main-tab') != "undefined") {
		jQuery('#wps-main-nav-bar a').eq( parseInt(lsGetCookie('wps-main-tab')) ).click();
	}

	// Documentation menu item
	jQuery('#wps-main-nav-bar a.support').click(function(e) {
		e.preventDefault();
		jQuery('#contextual-help-link').click();
	});

	// Select sticky tab
	jQuery('#wps-stickies-tabs a:not(.unsortable)').click(function(e) {
		e.preventDefault();
		wpStickies.selectStickyTab(this);
	});

	// Restore last main tab
	if(typeof lsGetCookie('wps-sticky-tab') != "undefined") {
		jQuery('#wps-stickies-tabs a').eq( parseInt(lsGetCookie('wps-sticky-tab')) ).click();
	}

	wpsPreview.init();

	// Accept
	jQuery('#wps-stickies').on('click', '.accept', function(e) {
		e.preventDefault();
		wpStickies.acceptSticky(this);
	});

	// Reject + Remove
	jQuery('#wps-stickies').on('click', '.reject, .remove', function(e) {
		e.preventDefault();
		wpStickies.removeSticky(this);
	});

	// Restore
	jQuery('#wps-stickies').on('click', '.restore', function(e) {
		e.preventDefault();
		wpStickies.restoreSticky(this);
	});

	// Delete
	jQuery('#wps-stickies').on('click', '.delete', function(e) {
		e.preventDefault();
		wpStickies.deleteSticky(this);
	});

	// Save
	jQuery('#wps-form').submit(function(e) {
		e.preventDefault();
		wpStickies.submit(this);
	});


	if(document.location.href.indexOf('wpstickies-media-editor') !== -1){
		wpsMedia.init();
	}

	// Auto-update
	jQuery('.wps-auto-update').submit(function(e) {

		// Prevent browser default submission
		e.preventDefault();

		// Set progress text
		jQuery('.wps-auto-update tfoot span').text('Validating ...').css('color', '#333');

		// Post it
		jQuery.post( ajaxurl, jQuery(this).serialize(), function(data) {

			// Parse data
			data = jQuery.parseJSON(data);

			// Check success
			jQuery('.wps-auto-update tfoot span').html(data['message']);

			// Check success
			if(data['success'] == true) {
				jQuery('.wps-auto-update tfoot span').css('color', '#4b982f');
			} else {
				jQuery('.wps-auto-update tfoot span').css('color', '#c33219');
			}
		});
	});


	// News filters
	jQuery('.wps-news .filters li').click(function() {

		// Highlight
		jQuery(this).siblings().attr('class', '');
		jQuery(this).attr('class', 'active');

		// Get stuff
		var page = jQuery(this).data('page');
		var frame = jQuery(this).closest('.wps-box').find('iframe');
		var baseUrl = frame.attr('src').split('#')[0];

		// Set filter
		frame.attr('src', baseUrl+'#'+page);

	});


	// Codemirror
	jQuery('.wps-codemirror').each(function() {
		var defaults = {
			mode: 'css',
			theme: 'solarized',
			lineNumbers: true,
			autofocus: true,
			indentUnit: 4,
			indentWithTabs: true,
			foldGutter: true,
			gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
			styleActiveLine: true,
			extraKeys: {
				"Ctrl-Q": function(cm) {
					cm.foldCode(cm.getCursor());
				}
			}
		}
		
		CodeMirror.fromTextArea(this, defaults);
	});
})