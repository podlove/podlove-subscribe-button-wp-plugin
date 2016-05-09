(function($) {
	$( document ).ready( function() {

		$("#podlove_subscribe_button_default_color").spectrum({
			preferredFormat: 'hex',
			showInput: true,
			palette: [ '#599677' ],
			showPalette: true,
			showSelectionPalette: false,
			chooseText: "Select Color",
			cancelText: "Cancel",
		});

		var feed_counter = 0;
		var source = $("#feed_line_template").html();

		$(".add_feed").on( 'click', function () {
			add_new_feed();
		} );

		if ( window.feeds != undefined ) {
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