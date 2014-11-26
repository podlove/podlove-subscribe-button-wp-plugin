<?php

namespace PodloveSubscribeButton\Settings;

class Podcasts {

	public static function page() {

		$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : NULL;

		if ( $action == 'confirm_delete' && isset( $_REQUEST['podcast'] ) ) {
			$podcast = \PodloveSubscribeButton\Model\Podcast::find_by_id( (int) $_REQUEST['podcast'] );
			?>
			<div class="updated">
				<p>
					<strong>
						<?php echo sprintf( __( 'You selected to delete the podcast "%s". Please confirm this action.', 'podlove' ), $podcast->title ) ?>
					</strong>
				</p>
				<p>
					<?php echo __( 'Clients subscribing to this podcast will no longer receive updates. If you are moving your podcast, you must inform your subscribers.', 'podlove' ) ?>
				</p>
				<p>
					<?php echo self::get_action_link( $podcast, __( 'Delete podcast permanently', 'podlove' ), 'delete', 'button' ) ?>
					<?php echo self::get_action_link( $podcast, __( 'Don\'t change anything', 'podlove' ), 'keep', 'button-primary' ) ?>
				</p>
			</div>
			<?php
		}
		?>
		<div class="wrap">
			<?php screen_icon( 'podlove-podcast' ); ?>
			<h2><?php echo __( 'Podcast Subscribe Button', 'podlove' ); ?> <a href="?page=<?php echo $_REQUEST['page']; ?>&amp;action=new" class="add-new-h2"><?php echo __( 'Add New', 'podlove' ); ?></a></h2>
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

		if ( ! isset( $_REQUEST['podcast'] ) )
			return;

		$podcast = \PodloveSubscribeButton\Model\Podcast::find_by_id( $_REQUEST['podcast'] );
		$podcast->update_attributes( $_POST['podlove_podcast'] );
		
		self::redirect( 'index' );
	}
	/**
	 * Process form: create a format
	 */
	public static function create() {
		global $wpdb;

		$podcast = new \PodloveSubscribeButton\Model\Podcast;
		$podcast->update_attributes( $_POST['podlove_podcast'] );

		self::redirect( 'index' );
	}
	
	/**
	 * Process form: delete a format
	 */
	public static function delete() {
		if ( ! isset( $_REQUEST['podcast'] ) )
			return;

		\PodloveSubscribeButton\Model\Podcast::find_by_id( $_REQUEST['podcast'] )->delete();

		self::redirect( 'index' );		
	}

	/**
	 * Helper method: redirect to a certain page.
	 */
	public function redirect( $action, $podcast_id = NULL, $params = array() ) {
		$page    = 'options-general.php?page=' . $_REQUEST['page'];
		$show    = ( $podcast_id ) ? '&podcast=' . $podcast_id : '';
		$action  = '&action=' . $action;

		array_walk( $params, function(&$value, $key) { $value = "&$key=$value"; } );
		
		wp_redirect( admin_url( $page . $show . $action . implode( '', $params ) ) );
		exit;
	}
	
	public static function process_form() {

		if ( ! isset( $_REQUEST['podcast'] ) )
			return;

		$action = ( isset( $_REQUEST['action'] ) ) ? $_REQUEST['action'] : NULL;
		
		if ( $action === 'save' ) {
			self::save();
		} elseif ( $action === 'create' ) {
			self::create();
		} elseif ( $action === 'delete' ) {
			self::delete();
		}
	}

	public static function new_template() {
		$podcast = new \PodloveSubscribeButton\Model\Podcast;
		echo '<h3>' . __( 'New Podcast', 'podlove' ) . '</h3>';
		self::form_template( $podcast, 'create' );
	}

	public static function edit_template() {
		$podcast = \PodloveSubscribeButton\Model\Podcast::find_by_id( $_REQUEST['podcast'] );
		echo '<h3>' . sprintf( __( 'Edit Podcast: %s', 'podlove' ), $podcast->title ) . '</h3>';
		self::form_template( $podcast, 'save' );
	}

	public static function view_template() {
		$table = new \PodloveSubscribeButton\Podcast_List_Table;
		$table->prepare_items();
		$table->display();
	}

	private static function form_template( $podcast, $action ) {
		?>
		<form method="post" action="options-general.php?page=podlove-subscribe-button&podcast=<?php echo $podcast->id; ?>&action=<?php echo $action; ?>">
			<input type="hidden" value="<?php echo $podcast->id; ?>" name="podlove_podcast[id]" />
			<table class="form-table" border="0" cellspacing="0">
					<tbody>
					<tr>
						<td scope="row">
							<label for="">ID</label>
						</td>
						<td>
							<input type="text" class="regular-text" name="podlove_podcast[name]" value="<?php echo $podcast->name; ?>" />
						</td>
					</tr>
					<tr>
						<td scope="row">
							<label for="">Title</label>
						</td>
						<td>
							<input type="text" class="regular-text" name="podlove_podcast[title]" value="<?php echo $podcast->title; ?>" />
						</td>
					</tr>
					<tr>
						<td scope="row">
							<label for="">Subtitle</label>
						</td>
						<td>
							<input type="text" class="regular-text" name="podlove_podcast[subtitle]" value="<?php echo $podcast->subtitle; ?>" />
						</td>
					</tr>
					<tr>
						<td scope="row">
							<label for="">Description</label>
						</td>
						<td>
							<textarea class="autogrow" cols="40" rows="3" name="podlove_podcast[description]"><?php echo $podcast->description; ?></textarea>
						</td>
					</tr>
					<tr>
						<td scope="row">
							<label for="">Cover</label>
						</td>
						<td>
							<input type="text" class="regular-text" id="podlove-podcast-cover" name="podlove_podcast[cover]" value="<?php echo $podcast->cover; ?>" />
							<br /><img src="<?php echo $podcast->cover; ?>" alt="" style="width: 200px" /> 
							<script type="text/javascript">
								(function($) {
									$("#podlove-podcast-cover").on( 'change', function() {
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
							<input type="text" class="regular-text" name="podlove_podcast[feeds][{{id}}][url]" value="{{url}}" />
						</td>
						<td>
							<select class="regular-text podlove-media-format" name="podlove_podcast[feeds][{{id}}][format]">
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
					var feeds = <?php echo json_encode($podcast->feeds); ?>;

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

	public static function get_action_link( $podcast, $title, $action = 'edit', $type = 'link' ) {
		return sprintf(
			'<a href="?page=%s&action=%s&podcast=%s"%s>' . $title . '</a>',
			$_REQUEST['page'],
			$action,
			$podcast->id,
			$type == 'button' ? ' class="button"' : ''
		);
	}

}
?>