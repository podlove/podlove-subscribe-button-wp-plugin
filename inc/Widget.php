<?php
/**
 * @author    Podlove <podlove@podlove.org>
 * @copyright Copyright (c) 2014-2018, Podlove
 * @license   https://github.com/podlove/podlove-subscribe-button-wp-plugin/blob/master/LICENSE MIT
 * @package   Podlove\PodloveSubscribeButton
 */

namespace PodloveSubscribeButton;

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

class Widget extends \WP_Widget {

	public static $widget_settings = array(
		'infotext',
		'title',
		'size',
		'style',
		'format',
		'autowidth',
		'button',
		'color',
        'language'
	);

	public function __construct() {
		parent::__construct(
			'podlove_subscribe_button_wp_plugin_widget',
			( Helpers::is_podlove_publisher_active() ? 'Podlove Subscribe Button (WordPress plugin)' : 'Podlove Subscribe Button' ),
			array( 'description' => __( 'Adds a Podlove Subscribe Button to your Sidebar', 'podlove-subscribe-button' ), )
		);

	}

	public function widget( $args, $instance ) {
		// Fetch the (network)button by it's name
		if ( ! $button = Model\Button::get_button_by_name( $instance[ 'button' ] ) ) {
			return sprintf( __( 'Oops. There is no button with the ID "%s".', 'podlove-subscribe-button' ), $args['button'] );
		}

		echo $args[ 'before_widget' ];
		echo $args[ 'before_title' ] . apply_filters( 'widget_title', $instance[ 'title' ] ) . $args[ 'after_title' ];

		echo $button->render(
			\PodloveSubscribeButton::get_array_value_with_fallback( $instance, 'size' ),
			\PodloveSubscribeButton::get_array_value_with_fallback( $instance, 'autowidth' ),
			\PodloveSubscribeButton::get_array_value_with_fallback( $instance, 'style' ),
			\PodloveSubscribeButton::get_array_value_with_fallback( $instance, 'format' ),
			\PodloveSubscribeButton::get_array_value_with_fallback( $instance, 'color' ),
			false,
            false,
			\PodloveSubscribeButton::get_array_value_with_fallback( $instance, 'language' )
		);

		if ( strlen( $instance[ 'infotext' ] ) ) {
			echo wpautop( $instance[ 'infotext' ] );
		}

		echo $args[ 'after_widget' ];

	}

	public function form( $instance ) {
		$options = get_option( 'podlove_psb_defaults' );

		$title     = isset( $instance[ 'title' ] )     ? $instance[ 'title' ]     : '';
		$button    = isset( $instance[ 'button' ] )    ? $instance[ 'button' ]    : '';
		$size      = isset( $instance[ 'size' ] )      ? $instance[ 'size' ]      : $options['size'];
		$style     = isset( $instance[ 'style' ] )     ? $instance[ 'style' ]     : $options['style'];
		$format    = isset( $instance[ 'format' ] )    ? $instance[ 'format' ]    : $options['format'];
		$autowidth = isset( $instance[ 'autowidth' ] ) ? $instance[ 'autowidth' ] : true;
		$infotext  = isset( $instance[ 'infotext' ] )  ? $instance[ 'infotext' ]  : '';
		$color     = isset( $instance[ 'color' ] )     ? $instance[ 'color' ]     : $options['color'];
		$language  = isset( $instance[ 'language' ] )  ? $instance[ 'language' ]  : $options['language'];

		$buttons = Model\Button::all();
		if ( is_multisite() ) {
			$network_buttons = Model\NetworkButton::all();
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
				<?php if ( isset( $network_buttons ) && count( $network_buttons ) > 0 ) : ?>
                    <optgroup label="<?php _e( 'Local', 'podlove-subscribe-button' ); ?>">
						<?php
						foreach ( $buttons as $subscribebutton ) {
							echo "<option value='" . $subscribebutton->name . "' " . selected( $subscribebutton->name, $button ) . " >" . $subscribebutton->title . " (" . $subscribebutton->name . ")</option>";
						} ?>
                    </optgroup>
                    <optgroup label="<?php _e( 'Network', 'podlove-subscribe-button' ); ?>">
						<?php
						foreach ( $network_buttons as $subscribebutton ) {
							echo "<option value='" . $subscribebutton->name . "' " . selected( $subscribebutton->name, $button ) . " >" . $subscribebutton->title . " (" . $subscribebutton->name . ")</option>";
						} ?>
                    </optgroup>
				<?php else :
					foreach ( $buttons as $subscribebutton ) {
						echo "<option value='" . $subscribebutton->name . "' " . selected( $subscribebutton->name, $button ) . " >" . $subscribebutton->title . " (" . $subscribebutton->name . ")</option>";
					}
				endif; ?>
            </select>
			<?php
			$customize_options = array(
				'size'      => array(
					'name'    => __( 'Size', 'podlove-subscribe-button' ),
					'options' => Defaults::button( 'size' ),
				),
				'style'     => array(
					'name'    => __( 'Style', 'podlove-subscribe-button' ),
					'options' => Defaults::button( 'style' ),
				),
				'format'    => array(
					'name'    => __( 'Format', 'podlove-subscribe-button' ),
					'options' => Defaults::button( 'format' ),
				),
				'autowidth' => array(
					'name'    => __( 'Autowidth', 'podlove-subscribe-button' ),
					'options' => Defaults::button( 'autowidth' ),
				),
				'language'    => array(
					'name'    => __( 'Language', 'podlove-subscribe-button' ),
					'options' => array_combine( Defaults::button( 'language' ), Defaults::button( 'language' ) ),
				),
			);

			foreach ( $customize_options as $slug => $properties ) : ?>
				<label for="<?php echo $this->get_field_id( $slug ); ?>"><?php echo $properties[ 'name' ]; ?></label>
				<select class="widefat" id="<?php echo $this->get_field_id( $slug ); ?>" name="<?php echo $this->get_field_name( $slug ); ?>">
					<option value="default" <?php echo ( $$slug == 'default' ? 'selected="selected"' : '' ); ?>><?php printf( __( 'Default %s', 'podlove-subscribe-button' ), $properties[ 'name' ] ) ?></option>
					<optgroup>
						<?php foreach ( $properties[ 'options' ] as $property => $name ) : ?>
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

		foreach ( self::$widget_settings as $setting ) {
			$instance[ $setting ] = ( ! empty( $new_instance[ $setting ] ) ) ? strip_tags( $new_instance[ $setting ] ) : '';
		}

		return $instance;

	}

} // END class
