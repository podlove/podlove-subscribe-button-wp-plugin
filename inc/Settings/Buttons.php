<?php
/**
 * @author    Podlove <podlove@podlove.org>
 * @copyright Copyright (c) 2014-2018, Podlove
 * @license   https://github.com/podlove/podlove-subscribe-button-wp-plugin/blob/master/LICENSE MIT
 * @package   Podlove\PodloveSubscribeButton
 */

namespace PodloveSubscribeButton\Settings;

use PodloveSubscribeButton\Helpers;

class Buttons {

	public static function page() {

		$action = null !== filter_input( INPUT_GET, 'action' ) ? filter_input( INPUT_GET, 'action' ) : null;
		$is_network = is_network_admin();

		if ( $action == 'confirm_delete' && null !== filter_input( INPUT_GET, 'button' ) ) {
			$button = ( $is_network === true ? \PodloveSubscribeButton\Model\NetworkButton::find_by_id( (int) filter_input( INPUT_GET, 'button' ) ) : \PodloveSubscribeButton\Model\Button::find_by_id( (int) filter_input( INPUT_GET, 'button' ) ) );
			?>
			<div class="updated">
				<p>
					<strong>
						<?php printf( __( 'You selected to delete the button "%s". Please confirm this action.', 'podlove-subscribe-button' ), $button->title ) ?>
					</strong>
				</p>
				<p>
					<?php echo self::get_action_link( $button, __( 'Delete button permanently', 'podlove-subscribe-button' ), 'delete', 'button' ) ?>
					<?php echo self::get_action_link( $button, __( "Don't change anything", 'podlove-subscribe-button' ), 'keep', 'button-primary' ) ?>
				</p>
			</div>
			<?php
		}
		?>
		<div class="wrap">
			<h1><?php echo __( 'Podlove Subscribe Button', 'podlove-subscribe-button' ); ?> <a href="?page=<?php echo filter_input( INPUT_GET, 'page' ); ?>&amp;action=new&amp;network=<?php echo $is_network; ?>" class="add-new-h2"><?php _e( 'Add New', 'podlove-subscribe-button' ); ?></a></h1>
            <div id="poststuff">
                <div id="post-body" class="columns-2">
                    <div id="post-body-content">
	                    <?php
	                    switch ( $action ) {
		                    case 'new':   self::new_template(); break;
		                    case 'edit':  self::edit_template(); break;
		                    case 'index': self::view_template(); break;
		                    default:      self::view_template(); break;
	                    }
	                    ?>
                    </div>
                    <div id="postbox-container-1" class="postbox-container">
                        <?php self::sidebar(); ?>
                    </div>
                </div>
            </div>
		</div><!-- .wrap -->
		<?php
	}

	/**
	 * Render the sidebar HTML
	 */
	public static function sidebar() { ?>
        <div id="submitdiv" class="postbox">
            <h2 class="ui-sortable-handle"><span>Podlove Subscribe Button <code><?php echo \PodloveSubscribeButton::$version; ?></code></span></h2>
            <div class="inside">
                <div id="minor-publishing" style="padding:0 10px;">
                    <p><?php _e( 'This plugin allows easy inclusion of the Podlove Subscribe Button. Put it in your sidebar with a simple widget or include the button in pages and/or posts with a simple shortcode.', 'podlove-subscribe-button' ); ?></p>
                    <p><?php _e( 'Start by adding a button for each of your podcasts here. You can then add the button to your sidebar by adding the <a href="widgets.php">Podlove Subscribe Button widget</a>.', 'podlove-subscribe-button' ); ?></p>
                    <p><?php _e( 'If you want to display the button inside a page or article, you can also use the <code>[podlove-subscribe-button]</code> shortcode anywhere.', 'podlove-subscribe-button' ); ?></p>
                </div>
                <div id="major-publishing-actions">
                    <ul>
                        <li>
                            <a href="https://subscribe-button.podlove.org/" target="_blank">Podlove Subscribe Button</a>
                        </li>
                        <li>
                            <a href="https://podlove.org" target="_blank">Podlove Initiative</a>
                        </li>
                        <li>
                            <a href="https://community.podlove.org/" target="_blank">Podlove Community</a>
                        </li>
                        <li>
                            <a href="https://docs.podlove.org" target="_blank">Documentation &amp; Guides</a>
                        </li>
                        <li>
                            <a href="https://podlove.org/donations/" target="_blank">Donate</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <?php

    }

	/**
	 * Process form: save/update a format
	 */
	public static function save() {
		if ( null == filter_input( INPUT_GET, 'button' ) )
			return;

		$post = filter_input_array( INPUT_POST );

		$button = ( filter_input( INPUT_GET, 'network' ) === '1' ? \PodloveSubscribeButton\Model\NetworkButton::find_by_id( filter_input( INPUT_GET, 'button' ) ) : \PodloveSubscribeButton\Model\Button::find_by_id( filter_input( INPUT_GET, 'button' ) ) );
		$button->update_attributes( $post[ 'podlove_button' ] );

		if ( isset( $post[ 'submit_and_stay' ] ) ) {
			self::redirect( 'edit', $button->id, array( 'network' => filter_input( INPUT_GET, 'network' ) ), ( filter_input( INPUT_GET, 'network' ) === '1' ? true : false ) );
		} else {
			self::redirect( 'index', $button->id, array(), ( filter_input( INPUT_GET, 'network' ) === '1' ? true : false ) );
		}
	}
	/**
	 * Process form: create a format
	 */
	public static function create() {
		global $wpdb;

		$post = filter_input_array( INPUT_POST );

		$button = ( filter_input( INPUT_GET, 'network' ) === '1' ? new \PodloveSubscribeButton\Model\NetworkButton : new \PodloveSubscribeButton\Model\Button );
		$button->update_attributes( $post[ 'podlove_button' ] );

		if ( isset( $post[ 'submit_and_stay' ] ) ) {
			self::redirect( 'edit', $button->id, array( 'network' => filter_input( INPUT_GET, 'network' ) ), ( filter_input( INPUT_GET, 'network' ) === '1' ? true : false ) );
		} else {
			self::redirect( 'index', $button->id, array(), ( filter_input( INPUT_GET, 'network' ) === '1' ? true : false ) );
		}
	}

	/**
	 * Process form: delete a format
	 */
	public static function delete() {
		if ( null == filter_input( INPUT_GET, 'button' ) )
			return;

		$button = ( filter_input( INPUT_GET, 'network' ) === '1' ? \PodloveSubscribeButton\Model\NetworkButton::find_by_id( filter_input( INPUT_GET, 'button' ) ) : \PodloveSubscribeButton\Model\Button::find_by_id( filter_input( INPUT_GET, 'button' ) ) );
		$button->delete();

		self::redirect( 'index', null, array(), ( filter_input( INPUT_GET, 'network' ) === '1' ? true : false ) );
	}

	/**
	 * Helper method: redirect to a certain page.
	 */
	public static function redirect( $action, $button_id = null, $params = array(), $network = false ) {
		$page    = ( $network ? '/network/settings' : 'options-general' ) . '.php?page=' . filter_input( INPUT_GET, 'page' );
		$show    = ( $button_id ) ? '&button=' . $button_id : '';
		$action  = '&action=' . $action;

		array_walk( $params, function( &$value, $key ) { $value = "&$key=$value"; } );

		wp_redirect( admin_url( $page . $show . $action . implode( '', $params ) ) );
	}

	public static function process_form() {
		if ( null === filter_input( INPUT_GET, 'button' ) )
			return;

		$action = ( null !== filter_input( INPUT_GET, 'action' ) ? filter_input( INPUT_GET, 'action' ) : null );

		if ( $action === 'save' ) {
			self::save();
		} elseif ( $action === 'create' ) {
			self::create();
		} elseif ( $action === 'delete' ) {
			self::delete();
		}
	}

	public static function new_template() {
		if ( filter_input( INPUT_GET, 'network' ) == '1' ) {
			$button = new \PodloveSubscribeButton\Model\NetworkButton;
		} else {
			$button = new \PodloveSubscribeButton\Model\Button;
		}

		echo '<h2>' . __( 'New Subscribe button', 'podlove-subscribe-button' ) . '</h2>' .
				__( 'Please fill in your Podcast metadata to create a Podlove Subscription button', 'podlove-subscribe-button' );
		self::form_template( $button, 'create' );
	}

	public static function edit_template() {
		if ( filter_input( INPUT_GET, 'network' ) == '1' ) {
			$button = \PodloveSubscribeButton\Model\NetworkButton::find_by_id( filter_input( INPUT_GET, 'button' ) );
		} else {
			$button = \PodloveSubscribeButton\Model\Button::find_by_id( filter_input( INPUT_GET, 'button' ) );
		}

		echo '<h2>' . sprintf( __( 'Edit Subscribe button: %s', 'podlove-subscribe-button' ), $button->title ) . '</h2>';
		self::form_template( $button, 'save' );
	}

	public static function view_template() {

		$is_network = is_network_admin();
		$table = new \PodloveSubscribeButton\Button_List_Table;
		$table->prepare_items();
		$table->display();

		if ( $is_network ) {
		    // https://vedovini.net/2015/10/using-the-wordpress-settings-api-with-network-admin-pages/
			?>
            <form method="post" action="edit.php?action=podlove_psb_update_network_options">
				<?php
				settings_fields( 'podlove-psb' );
				do_settings_sections( 'podlove-psb' );
				submit_button();
				?>
            </form>
			<?php
		} else {
			?>
            <form method="post" action="options.php">
				<?php
				settings_fields( 'podlove-psb' );
				do_settings_sections( 'podlove-psb' );
				submit_button();
				?>
            </form>
		<?php }
	}

	private static function form_template( $button, $action ) {
		// Enqueue Scripts for Media Manager
		wp_enqueue_media();
		// Adjust if is_network
		$is_network = is_network_admin();
		?>
		<form method="post" action="<?php echo ( $is_network === true ? '/wp-admin/network/settings' : 'options-general' ) ?>.php?page=podlove-subscribe-button&button=<?php echo $button->id; ?>&action=<?php echo $action; ?>&network=<?php echo $is_network; ?>">
			<input type="hidden" value="<?php echo $button->id; ?>" name="podlove_button[id]" />
			<table class="form-table" border="0" cellspacing="0">
					<tbody>
					<tr>
						<th scope="row">
							<label for="podlove_button_name"><?php _e( 'Button ID', 'podlove-subscribe-button' ); ?></label>
						</th>
						<td>
							<input type="text" class="regular-text" id="podlove_button_name" name="podlove_button[name]" value="<?php echo $button->name; ?>" />
                            <p class="description"><?php _e( 'The ID will be used as in internal identifier for shortcodes.', 'podlove-subscribe-button' ); ?></p>
                        </td>
					</tr>
					<tr>
						<th scope="row">
							<label for="podlove_button_title"><?php _e( 'Podcast Title', 'podlove-subscribe-button' ); ?></label>
						</th>
						<td>
							<input type="text" class="regular-text" id="podlove_button_title" name="podlove_button[title]" value="<?php echo $button->title; ?>" />
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="podlove_button_subtitle"><?php _e( 'Podcast Subtitle', 'podlove-subscribe-button' ); ?></label>
						</th>
						<td>
							<input type="text" class="regular-text" id="podlove_button_subtitle" name="podlove_button[subtitle]" value="<?php echo $button->subtitle; ?>" />
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="podlove_button_description"><?php _e( 'Podcast Description', 'podlove-subscribe-button' ); ?></label>
						</th>
						<td>
							<textarea class="autogrow" cols="40" rows="3" id="podlove_button_description" name="podlove_button[description]"><?php echo $button->description; ?></textarea>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="podlove-button-cover"><?php _e( 'Podcast Image URL', 'podlove-subscribe-button' ); ?></label>
						</th>
						<td>
							<input type="text" class="regular-text" id="podlove-button-cover" name="podlove_button[cover]" value="<?php echo $button->cover; ?>" />
							<a id="Podlove_cover_image_select" class="button" href="#">Select</a>
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
						<th scope="row">
							<label for="feeds_table"><?php _e( 'Podcast Feeds', 'podlove-subscribe-button' ); ?></label>
						</th>
						<td>
							<table id="feeds_table" class="podlove_alternating widefat striped">
								<thead>
									<tr>
										<th scope="col" id="url" class="manage-column column-primary"><?php _e( 'URL', 'podlove-subscribe-button' ); ?></th>
										<th scope="col" id="itunes_id" class="manage-column"><?php _e( 'iTunes feed ID', 'podlove-subscribe-button' ); ?></th>
										<th scope="col" id="format" class="manage-column"><?php _e( 'Media format', 'podlove-subscribe-button' ); ?></th>
										<th scope="col" id="action" class="manage-column"><?php _e( 'Actions', 'podlove-subscribe-button' ); ?></th>
									</tr>
								</thead>
								<tbody id="feeds_table_body">
								</tbody>
							</table>
							<input type="button" class="button add_feed" value="+" />
							<p><span class="description"><?php _e( 'Provide all Feeds with their corresponding Media File Type. The Subscribe Button will then automatically provide the most suitable feed to the subscriber with respect to their Podcast Client.', 'podlove-subscribe-button' ); ?></span></p>
						</td>
					</tr>
					</tbody>
				</table>
				<input name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes', 'podlove-subscribe-button' ); ?>" type="submit" />
				<input type="submit" name="submit_and_stay" id="submit_and_stay" class="button" value="<?php _e( 'Save Changes and Continue Editing', 'podlove-subscribe-button' ); ?>"  />

				<script type="text/template" id="feed_line_template">
					<tr>
						<td>
							<input type="text" class="regular-text" name="podlove_button[feeds][{{id}}][url]" value="{{url}}" />
						</td>
						<td>
						<input type="text" class="regular-text" name="podlove_button[feeds][{{id}}][itunesfeedid]" value="{{itunesfeedid}}" />
						</td>
						<td>
							<select class="regular-text podlove-media-format" name="podlove_button[feeds][{{id}}][format]">
								<?php
									foreach ( \PodloveSubscribeButton\Defaults::media_types() as $id => $audio ) {
										echo "<option value='" . $id . "'>" . $audio[ 'title' ] . "</option>\n";
									}
								?>
							</select>
						</td>
						<td><span class="dashicons dashicons-trash clickable podlove-icon-remove"></span></td>
					</tr>
				</script>
				<script type="text/javascript">
					var feeds = <?php echo json_encode( $button->feeds ); ?>;
				</script>
		</form>
		<?php
	}

	public static function get_action_link( $button, $title, $action = 'edit', $type = 'link' ) {
		return sprintf(
			'<a href="?page=%s&action=%s&button=%s&network=' . is_network_admin() . '"%s>' . $title . '</a>',
			filter_input( INPUT_GET, 'page' ),
			$action,
			$button->id,
			$type == 'button' ? ' class="button"' : ''
		);
	}

	public static function podlove_psb_update_network_options() {

		//Verify Post Referring Page
		check_admin_referer( 'podlove-psb-options' );
		update_site_option( 'podlove_psb_defaults', $_POST['podlove_psb_defaults'] );

		//Redirect to Network Settings Page
		wp_redirect( add_query_arg( array( 'page' => 'podlove-subscribe-button', 'updated' => 'true' ), network_admin_url( 'settings.php' ) ) );

		exit;
	}

} // END class
