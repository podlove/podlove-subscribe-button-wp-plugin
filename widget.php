<?php

namespace PodloveSubscribeButton;

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

class Podlove_Subscribe_Button_Widget extends \WP_Widget {

	public function __construct() {
		parent::__construct(
					'podlove_subscribe_button_wp_plugin_widget',
					( self::is_podlove_publisher_active() ? 'Podlove Subscribe Button (WordPress plugin)' : 'Podlove Subscribe Button' ),
					array( 'description' => __( 'Adds a Podlove Subscribe Button to your Sidebar', 'podlove-subscribe-button' ), )
				);
	}

	public static $widget_settings = array('infotext', 'title', 'size', 'style', 'format', 'autowidth', 'button', 'color');

	public static function is_podlove_publisher_active() {
		if ( is_plugin_active("podlove-podcasting-plugin-for-wordpress/podlove.php") ) {
			return true;
		}

		return false;
	}

	public function widget( $args, $instance ) {
		// Fetch the (network)button by it's name
		if ( ! $button = \PodloveSubscribeButton\Model\Button::get_button_by_name($instance['button']) )
			return sprintf( __('Oops. There is no button with the ID "%s".', 'podlove-subscribe-button'), $args['button'] );

		echo $args['before_widget'];
		echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];

		echo $button->render(
				\PodloveSubscribeButton::get_array_value_with_fallback($instance, 'size'),
				\PodloveSubscribeButton::get_array_value_with_fallback($instance, 'autowidth'),
				\PodloveSubscribeButton::get_array_value_with_fallback($instance, 'style'),
				\PodloveSubscribeButton::get_array_value_with_fallback($instance, 'format'), 
				\PodloveSubscribeButton::get_array_value_with_fallback($instance, 'color')
			);
		
		if ( strlen($instance['infotext']) )
			echo wpautop($instance['infotext']);

		echo $args['after_widget'];
	}	

	public function form( $instance ) {
		foreach (self::$widget_settings as $setting) {
			$$setting = isset( $instance[$setting] ) ? $instance[$setting] : '';
		}

		$buttons = \PodloveSubscribeButton\Model\Button::all();
		if ( is_multisite() )
			$network_buttons = \PodloveSubscribeButton\Model\NetworkButton::all();

		$buttons_as_options = function ($buttons) {
			foreach ($buttons as $subscribebutton) {
				echo "<option value='".$subscribebutton->name."' ".( $subscribebutton->name == $button ? 'selected=\"selected\"' : '' )." >".$subscribebutton->title." (".$subscribebutton->name.")</option>";
			}
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'podlove-subscribe-button' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>" />

			<label for="<?php echo $this->get_field_id( 'color' ); ?>"><?php _e( 'Color', 'podlove-subscribe-button' ); ?></label>
			<input class="podlove_subscribe_button_color" id="<?php echo $this->get_field_id( 'color' ); ?>" name="<?php echo $this->get_field_name( 'color' ); ?>" value="<?php echo $color; ?>" />
			<style type="text/css">
				.sp-replacer {
					display: flex;
				}
				.sp-preview {
					flex-grow: 10;
				}
			</style>

			<label for="<?php echo $this->get_field_id( 'button' ); ?>"><?php _e( 'Button', 'podlove-subscribe-button' ); ?></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'button' ); ?>"
				      name="<?php echo $this->get_field_name( 'button' ); ?>">
				<?php if ( isset($network_buttons) && count($network_buttons) > 0 ) : ?>
					<optgroup label="<?php _e('Local', 'podlove'); ?>">
						<?php $buttons_as_options($buttons); ?>
					</optgroup>
					<optgroup label="<?php _e('Network', 'podlove'); ?>">
						<?php $buttons_as_options($network_buttons); ?>
					</optgroup>
				<?php else : 
					$buttons_as_options($buttons);
				 endif; ?>
			</select>

			<?php
			$customize_options = array(
					'size' => array(
							'name' => 'Size',
							'options' => \PodloveSubscribeButton\Model\Button::$size
						),
					'style' => array(
							'name' => 'Style',
							'options' => \PodloveSubscribeButton\Model\Button::$style
						),
					'format' => array(
							'name' => 'Format',
							'options' => \PodloveSubscribeButton\Model\Button::$format
						),
					'autowidth' => array(
							'name' => 'Autowidth',
							'options' => \PodloveSubscribeButton\Model\Button::$width
						)
				);

			foreach ($customize_options as $slug => $properties) : ?>
				<label for="<?php echo $this->get_field_id( $slug ); ?>"><?php _e( $properties['name'], 'podlove-subscribe-button' ); ?></label> 
				<select class="widefat" id="<?php echo $this->get_field_id( $slug ); ?>" name="<?php echo $this->get_field_name( $slug ); ?>">
					<option value="default" <?php echo ( $$slug == 'default' ? 'selected="selected"' : '' ); ?>><?php _e( 'Default ' . $properties['name'], 'podlove-subscribe-button' ) ?></option>
					<optgroup>
						<?php foreach ( $properties['options'] as $property => $name ) : ?>
						<option value="<?php echo $property; ?>" <?php echo ( $$slug == $property ? 'selected="selected"' : '' ); ?>><?php _e( $name, 'podlove-subscribe-button' ) ?></option>
						<?php endforeach; ?>
					</optgroup>
				</select>
			<?php endforeach; ?>
		
			<label for="<?php echo $this->get_field_id( 'infotext' ); ?>"><?php _e( 'Description', 'podlove-subscribe-button' ); ?></label> 
			<textarea class="widefat" rows="10" id="<?php echo $this->get_field_id( 'infotext' ); ?>" name="<?php echo $this->get_field_name( 'infotext' ); ?>"><?php echo $infotext; ?></textarea>
		</p>
		<?php 
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();

		foreach (self::$widget_settings as $setting) {
			$instance[$setting]  = ( ! empty( $new_instance[$setting] ) ) ? strip_tags( $new_instance[$setting] ) : '';
		}

		return $instance;
	}
}
add_action( 'widgets_init', function(){
     register_widget( '\PodloveSubscribeButton\Podlove_Subscribe_Button_Widget' );
});