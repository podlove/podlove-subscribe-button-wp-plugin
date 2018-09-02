(function($) {

	function podlove_init_color_buttons() {
        $('.podlove_subscribe_button_color').wpColorPicker();
	}

	$( document ).ready( function() {

		podlove_init_color_buttons();

		$("#Podlove_cover_image_select").on( 'click', function(event) {
			podlove_cover_image_selector = wp.media.frames.customHeader = wp.media( {
					title: i18n.media_library,
					library: {
						type: 'image'
					},
					button: {
						text: i18n.use_for
			   		},
			   		multiple: false
			    } );
			podlove_cover_image_selector.open();

			podlove_cover_image_selector.on('select', function() {
				var podcast_image_url = podlove_cover_image_selector.state().get('selection').first().toJSON().url;
				$("#podlove-button-cover").val(podcast_image_url);
				$("#podlove-button-cover").trigger('change');
			});
		} );

		$(document).ready(function () {
		    podlove_init_color_buttons();

		    jQuery(document).on('widget-updated', podlove_init_color_buttons);
		    jQuery(document).on('widget-added', podlove_init_color_buttons);

		    // re-init after saving configs
		    jQuery(document).on('ajaxComplete', function(e){
		        podlove_init_color_buttons();
		    });
		})

		var feed_counter = 0;
		var source = $("#feed_line_template").html();

		$(".add_feed").on( 'click', function () {
			add_new_feed();
		} );

		if ( window.feeds !== undefined ) {
			$.each( feeds, function (index, feed) {
				add_existing_feed(feed);
			} );
		}

		function add_new_feed() {
			row = source.replace( /\{\{url\}\}/g, '' );
			row = row.replace( /\{\{itunesfeedid\}\}/g, '' );
			row = row.replace( /\{\{id\}\}/g, feed_counter );

			$("#feeds_table_body").append(row);

			new_row = $("#feeds_table_body tr:last");
			new_row.find("input:first").focus();

			$(".podlove-icon-remove").on( 'click', function () {
				$(this).closest("tr").remove();
			} );

			feed_counter++;
		}

		function add_existing_feed( feed ) {
			row = source.replace( /\{\{url\}\}/g, feed.url );
			row = row.replace( /\{\{id\}\}/g, feed_counter );
			if ( feed.itunesfeedid == null ) {
				row = row.replace( /\{\{itunesfeedid\}\}/g, '' );
			} else {
				row = row.replace( /\{\{itunesfeedid\}\}/g, feed.itunesfeedid );
			}

			$("#feeds_table_body").append(row);

			new_row = $("#feeds_table_body tr:last");
			new_row.find('select.podlove-media-format option[value="' + feed.format + '"]').attr('selected',true);

			$(".podlove-icon-remove").on( 'click', function () {
				$(this).closest("tr").remove();
			} );

			feed_counter++;
		}

	} );
}(jQuery));