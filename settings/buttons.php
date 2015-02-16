<?php

namespace PodloveSubscribeButton\Settings;

class Buttons {

	public static function page() {

		$action = null !== filter_input(INPUT_GET, 'action') ? filter_input(INPUT_GET, 'action') : NULL;

		if ( $action == 'confirm_delete' && null !== filter_input(INPUT_GET, 'button') ) {
			$button = \PodloveSubscribeButton\Model\Button::find_by_id( (int) filter_input(INPUT_GET, 'button') );
			?>
			<div class="updated">
				<p>
					<strong>
						<?php echo sprintf( __( 'You selected to delete the button "%s". Please confirm this action.', 'podlove' ), $button->title ) ?>
					</strong>
				</p>
				<p>
					<?php echo self::get_action_link( $button, __( 'Delete button permanently', 'podlove' ), 'delete', 'button' ) ?>
					<?php echo self::get_action_link( $button, __( 'Don\'t change anything', 'podlove' ), 'keep', 'button-primary' ) ?>
				</p>
			</div>
			<?php
		}
		?>
		<div class="wrap">
			<?php screen_icon( 'podlove-button' ); ?>
			<h2><?php echo __( 'Podcast Subscribe Button', 'podlove' ); ?> <a href="?page=<?php echo filter_input(INPUT_GET, 'page'); ?>&amp;action=new" class="add-new-h2"><?php echo __( 'Add New', 'podlove' ); ?></a></h2>
			<?php
			
			switch ( $action ) {
				case 'new':   self::new_template();  break;
				case 'edit':  self::edit_template(); break;
				case 'index': self::view_template(); break;
				default:      self::view_template(); break;
			}
			?>
		</div>	
		<?php
	}

	/**
	 * Process form: save/update a format
	 */
	public static function save() {
		if ( null == filter_input(INPUT_GET, 'button') )
			return;

		$post = filter_input_array(INPUT_POST);

		$button = \PodloveSubscribeButton\Model\Button::find_by_id( filter_input(INPUT_GET, 'button') );
		$button->update_attributes( $post['podlove_button'] );
		
		self::redirect( 'index' );
	}
	/**
	 * Process form: create a format
	 */
	public static function create() {
		global $wpdb;
		
		$post = filter_input_array(INPUT_POST);

		$button = new \PodloveSubscribeButton\Model\Button;
		$button->update_attributes( $post['podlove_button'] );

		self::redirect( 'index' );
	}
	
	/**
	 * Process form: delete a format
	 */
	public static function delete() {
		if ( null ==  filter_input(INPUT_GET, 'button') )
			return;

		\PodloveSubscribeButton\Model\Button::find_by_id( filter_input(INPUT_GET, 'button') )->delete();

		self::redirect( 'index' );		
	}

	/**
	 * Helper method: redirect to a certain page.
	 */
	public function redirect( $action, $button_id = NULL, $params = array() ) {
		$page    = 'options-general.php?page=' . filter_input(INPUT_GET, 'page');
		$show    = ( $button_id ) ? '&button=' . $button_id : '';
		$action  = '&action=' . $action;

		array_walk( $params, function(&$value, $key) { $value = "&$key=$value"; } );
		
		wp_redirect( admin_url( $page . $show . $action . implode( '', $params ) ) );
	}
	
	public static function process_form() {
		if ( null === filter_input(INPUT_GET, 'button') )
			return;

		$action = ( null !== filter_input(INPUT_GET, 'action') ? filter_input(INPUT_GET, 'action') : NULL );
		
		if ( $action === 'save' ) {
			self::save();
		} elseif ( $action === 'create' ) {
			self::create();
		} elseif ( $action === 'delete' ) {
			self::delete();
		}
	}

	public static function new_template() {
		$button = new \PodloveSubscribeButton\Model\Button;
		echo '<h3>' . __( 'New Subscribe button', 'podlove' ) . '</h3>'.
				__( 'Please fill in your Podcast metadata to create a Podlove Subscription button', 'podlove' );
		self::form_template( $button, 'create' );
	}

	public static function edit_template() {
		$button = \PodloveSubscribeButton\Model\Button::find_by_id( filter_input(INPUT_GET, 'button') );
		echo '<h3>' . sprintf( __( 'Edit Subscribe button: %s', 'podlove' ), $button->title ) . '</h3>';
		self::form_template( $button, 'save' );
	}

	public static function view_template() {
		$table = new \PodloveSubscribeButton\Button_List_Table;
		$table->prepare_items();
		$table->display();
	}

	private static function form_template( $button, $action ) {
		?>
		<form method="post" action="options-general.php?page=podlove-subscribe-button&button=<?php echo $button->id; ?>&action=<?php echo $action; ?>">
			<input type="hidden" value="<?php echo $button->id; ?>" name="podlove_button[id]" />
			<table class="form-table" border="0" cellspacing="0">
					<tbody>
					<tr>
						<td scope="row">
							<label for="podlove_button_name"><?php _e('ID', 'podlove'); ?></label>
						</td>
						<td>
							<input type="text" class="regular-text" id="podlove_button_name" name="podlove_button[name]" value="<?php echo $button->name; ?>" />
							<br /><span class="description"><?php _e('The ID will be used as in internal identifier for shortcodes.', 'podlove'); ?></span>
						</td>
					</tr>
					<tr>
						<td scope="row">
							<label for="podlove_button_title"><?php _e('Title', 'podlove'); ?></label>
						</td>
						<td>
							<input type="text" class="regular-text" id="podlove_button_title" name="podlove_button[title]" value="<?php echo $button->title; ?>" />
						</td>
					</tr>
					<tr>
						<td scope="row">
							<label for="podlove_button_subtitle"><?php _e('Subtitle', 'podlove'); ?></label>
						</td>
						<td>
							<input type="text" class="regular-text" id="podlove_button_subtitle" name="podlove_button[subtitle]" value="<?php echo $button->subtitle; ?>" />
						</td>
					</tr>
					<tr>
						<td scope="row">
							<label for="podlove_button_description"><?php _e('Description', 'podlove'); ?></label>
						</td>
						<td>
							<textarea class="autogrow" cols="40" rows="3" id="podlove_button_description" name="podlove_button[description]"><?php echo $button->description; ?></textarea>
						</td>
					</tr>
					<tr>
						<td scope="row">
							<label for="podlove-button-cover"><?php _e('Image URL', 'podlove'); ?></label>
						</td>
						<td>
							<input type="text" class="regular-text" id="podlove-button-cover" name="podlove_button[cover]" value="<?php echo $button->cover; ?>" />
							<br /><img src="<?php echo $button->cover; ?>" alt="" style="width: 200px" /> 
							<script type="text/javascript">
								(function($) {
									$("#podlove-button-cover").on( 'change', function() {
										url = $(this).val();
										$(this).parent().find("img").attr("src", url);
									} );
								})(jQuery);								
							</script>
						</td>
					</tr>
					<tr>
						<td scope="row">
							<label for="">Feeds</label>
						</td>
						<td>
							<table class="podlove_alternating" border="0" cellspacing="0">
								<thead>
									<tr>
										<th>URL</th>
										<th>Media format</th>
										<th>Actions</th>
									</tr>
								</thead>
								<tbody id="feeds_table_body">
								</tbody>
							</table>
							<input type="button" class="button add_feed" value="+" />
						</td>
					</tr>
					</tbody>
				</table>
				<input name="submit" id="submit" class="button button-primary" value="Save Changes" type="submit" />
				<script type="text/template" id="feed_line_template">
					<tr>
						<td>
							<input type="text" class="regular-text" name="podlove_button[feeds][{{id}}][url]" value="{{url}}" />
						</td>
						<td>
							<select class="regular-text podlove-media-format" name="podlove_button[feeds][{{id}}][format]">
								<?php 
									foreach (\PodloveSubscribeButton\MediaTypes::$audio as $id => $audio) {
										echo "<option value='".$id."'>".$audio['title']."</option>\n";
									}
								?>
							</select>
						</td>
						<td><i class="clickable podlove-icon-remove"></i></td>
					</tr>
				</script>
				<script type="text/javascript">
					var feed_counter = 0;
					var feeds = <?php echo json_encode($button->feeds); ?>;

					(function($) {
						$( document ).ready( function() {
							var source = $("#feed_line_template").html();

							$(".add_feed").on( 'click', function () {
								add_new_feed();
							} );

							$.each( feeds, function (index, feed) {
								add_existing_feed(feed);
							} );



							function add_new_feed() {
								row = source.replace( /\{\{url\}\}/g, '' );
								row = row.replace( /\{\{id\}\}/g, feed_counter );

								$("#feeds_table_body").append(row);

								$(".podlove-icon-remove").on( 'click', function () {
									$(this).closest("tr").remove();
								} );

								feed_counter++;
							}

							function add_existing_feed( feed ) {
								row = source.replace( /\{\{url\}\}/g, feed.url );
								row = row.replace( /\{\{id\}\}/g, feed_counter );

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
				</script>
		</form>
		<?php
	}

	public static function get_action_link( $button, $title, $action = 'edit', $type = 'link' ) {
		return sprintf(
			'<a href="?page=%s&action=%s&button=%s"%s>' . $title . '</a>',
			filter_input(INPUT_GET, 'page'),
			$action,
			$button->id,
			$type == 'button' ? ' class="button"' : ''
		);
	}

}