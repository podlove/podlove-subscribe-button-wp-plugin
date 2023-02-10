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

        $title     = isset( $instance[ 'title' ] )     ? $instance[ 'title' ]     : '';
        $button = isset( $instance[ 'button' ] )    ? $instance[ 'button' ]    : '';
        $size      = isset( $instance[ 'size' ] )      ? $instance[ 'size' ]      : 'big';
        $style     = isset( $instance[ 'style' ] )     ? $instance[ 'style' ]     : 'filled';
        $format    = isset( $instance[ 'format' ] )    ? $instance[ 'format' ]    : 'cover';
        $autowidth = isset( $instance[ 'autowidth' ] ) ? $instance[ 'autowidth' ] : true;
        $infotext  = isset( $instance[ 'infotext' ] )  ? $instance[ 'infotext' ]  : '';
        $color     = isset( $instance[ 'color' ] )     ? $instance[ 'color' ]     : '#75ad91';

		$buttons = \PodloveSubscribeButton\Model\Button::all();
		if ( is_multisite() )
			$network_buttons = \PodloveSubscribeButton\Model\NetworkButton::all();
		?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'podlove-subscribe-button' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr($title); ?>" />

            <label for="<?php echo $this->get_field_id( 'color' ); ?>"><?php _e( 'Color', 'podlove-subscribe-button' ); ?></label>
            <input class="podlove_subscribe_button_color" id="<?php echo $this->get_field_id( 'color' ); ?>" name="<?php echo $this->get_field_name( 'color' ); ?>" value="<?php echo esc_attr($color); ?>" />
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
						<?php
						foreach ($buttons as $subscribebutton) {
							echo "<option value='" . sanitize_title($subscribebutton->name) . "' " . selected( sanitize_title($subscribebutton->name), $button ) . " >" . sanitize_title($subscribebutton->title) . " (" . sanitize_title($subscribebutton->name) . ")</option>";
						}
                        ?>
                    </optgroup>
                    <optgroup label="<?php _e('Network', 'podlove'); ?>">
						<?php
						foreach ($network_buttons as $subscribebutton) {
							echo "<option value='" . sanitize_title($subscribebutton->name) . "' " . selected( sanitize_title($subscribebutton->name), $button ) . " >" . sanitize_title($subscribebutton->title) . " (" . sanitize_title($subscribebutton->name) . ")</option>";
						}
                        ?>
                    </optgroup>
				<?php else :
					foreach ($buttons as $subscribebutton) {
						echo "<option value='" . sanitize_title($subscribebutton->name) . "' " . selected( sanitize_title($subscribebutton->name), $button ) . " >" . sanitize_title($subscribebutton->title) . " (" . sanitize_title($subscribebutton->name) . ")</option>";
					}
				endif; ?>
            </select>

			<?php
			$customize_options = array(
				'size'      => array(
					'name'    => __( 'Size', 'podlove-subscribe-button' ),
					'options' => \PodloveSubscribeButton\Model\Button::$size
				),
				'style'     => array(
					'name'    => __( 'Style', 'podlove-subscribe-button' ),
					'options' => \PodloveSubscribeButton\Model\Button::$style
				),
				'format'    => array(
					'name'    => __( 'Format', 'podlove-subscribe-button' ),
					'options' => \PodloveSubscribeButton\Model\Button::$format
				),
				'autowidth' => array(
					'name'    => __( 'Autowidth', 'podlove-subscribe-button' ),
					'options' => \PodloveSubscribeButton\Model\Button::$width
				)
			);

			foreach ($customize_options as $slug => $properties) : ?>
				<label for="<?php echo $this->get_field_id( $slug ); ?>"><?php echo $properties['name']; ?></label>
				<select class="widefat" id="<?php echo $this->get_field_id( $slug ); ?>" name="<?php echo $this->get_field_name( $slug ); ?>">
					<option value="default" <?php echo ( $$slug == 'default' ? 'selected="selected"' : '' ); ?>><?php printf( __( 'Default %s', 'podlove-subscribe-button' ), $properties['name'] ) ?></option>
					<optgroup>
						<?php foreach ( $properties['options'] as $property => $name ) : ?>
						<option value="<?php echo $property; ?>" <?php echo ( $$slug == $property ? 'selected="selected"' : '' ); ?>><?php echo $name; ?></option>
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
