<?php
/**
 * Plugin Name: Podlove Subscribe Button
 * Plugin URI:  https://wordpress.org/plugins/podlove-subscribe-button/
 * Description: Brings the Podlove Subscribe Button to your WordPress installation.
 * Version:     1.4.0-beta
 * Author:      Podlove
 * Author URI:  https://podlove.org/
 * License:     MIT
 * License URI: license.txt
 * Text Domain: podlove-subscribe-button
 */

$correct_php_version = version_compare( phpversion(), "5.3", ">=" );

if ( ! $correct_php_version ) {
	printf( __( 'Podlove Subscribe Button Plugin requires %s or higher.<br>', 'podlove-subscribe-button' ), '<code>PHP 5.3</code>' );
	echo '<br />';
	printf( __( 'You are running %s', 'podlove-subscribe-button' ), '<code>PHP ' . phpversion() . '</code>' );
	exit;
}

// Constants
require( 'constants.php' );
require( 'settings/buttons.php' );
// Models
require( 'model/base.php' );
require( 'model/button.php' );
require( 'model/network_button.php' );
// Table
require( 'settings/buttons_list_table.php' );
// Media Types
require( 'media_types.php' );
// Widget
require( 'widget.php' );
// Version control
require( 'version.php' );
// Helper functions
require( 'helper.php' );

register_activation_hook( __FILE__, array( 'PodloveSubscribeButton', 'build_models' ) );

PodloveSubscribeButton::run();

/**
 * Class PodloveSubscribeButton
 */
class PodloveSubscribeButton {

	/**
	 * @var string current plugin version
	 */
	public static $version = '1.4.0-beta';

	public static function run() {
		add_action( 'plugins_loaded', array( __CLASS__, 'load_translations' ) );
		add_action( 'init', array( __CLASS__, 'register_shortcode' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
		add_action( 'admin_init', array( 'PodloveSubscribeButton\Settings\Buttons', 'process_form' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_action( 'widgets_init', array( __CLASS__, 'widgets' ) );
		self::menu();

	}

	public static function widgets() {
		register_widget( '\PodloveSubscribeButton\Widget' );

	}

	public static function menu() {
		add_action( 'admin_menu', array( 'PodloveSubscribeButton', 'admin_menu' ) );

		if ( is_network_admin() ) {
			add_action( 'network_admin_menu', array( 'PodloveSubscribeButton', 'admin_network_menu' ) );
		}

	}

	public static function enqueue_assets( $hook ) {

		$pages = array( 'settings_page_podlove-subscribe-button', 'widgets.php' );

		if ( ! in_array( $hook, $pages ) ) {
			return;
		}

		// CSS Stylesheet
		wp_register_style( 'podlove-subscribe-button', plugin_dir_url( __FILE__ ) . 'style.css', false, self::$version );
		wp_enqueue_style( 'podlove-subscribe-button' );

		// Admin JS
		wp_enqueue_style( 'wp-color-picker' );
		wp_register_script( 'podlove-subscribe-button-admin-tools', plugin_dir_url( __FILE__ ) . 'js/admin.js', array( 'jquery', 'wp-color-picker' ), self::$version );
		$js_translations = array(
			'media_library' => __( 'Media Library', 'podlove-subscribe-button' ),
			'use_for'       => __( 'Use for Podcast Cover Art', 'podlove-subscribe-button' ),
		);
		wp_localize_script( 'podlove-subscribe-button-admin-tools', 'i18n', $js_translations );
		wp_enqueue_script( 'podlove-subscribe-button-admin-tools' );

	}

	public static function admin_menu() {
		add_options_page(
			__( 'Podlove Subscribe Button Options', 'podlove-subscribe-button' ),
			__( 'Podlove Subscribe Button', 'podlove-subscribe-button' ),
			'manage_options',
			'podlove-subscribe-button',
			array( 'PodloveSubscribeButton\Settings\Buttons', 'page' )
		);

	}

	public static function admin_network_menu() {
		add_submenu_page(
			'settings.php',
			__( 'Podlove Subscribe Button Options', 'podlove-subscribe-button' ),
			__( 'Podlove Subscribe Button', 'podlove-subscribe-button' ),
			'manage_options',
			'podlove-subscribe-button',
			array( 'PodloveSubscribeButton\Settings\Buttons', 'page' )
		);

	}

	public static function load_translations() {
		load_plugin_textdomain( 'podlove-subscribe-button' );

	}

	public static function register_settings() {
		$settings = array(
			'size',
			'autowidth',
			'style',
			'format',
			'color',
		);

		foreach ( $settings as $setting ) {
			register_setting( 'podlove-subscribe-button', 'podlove_subscribe_button_default_' . $setting );
		}

	}

	public static function register_shortcode() {
		add_shortcode( 'podlove-subscribe-button', array( 'PodloveSubscribeButton', 'shortcode' ) );

	}

	public static function build_models() {
		// Build Databases
		\PodloveSubscribeButton\Model\Button::build();

		if ( is_multisite() ) {
			\PodloveSubscribeButton\Model\NetworkButton::build();
		}

		// Set Button "default" values
		$default_values = array(
			'size'      => 'big',
			'autowidth' => 'on',
			'color'     => '#599677',
			'style'     => 'filled',
			'format'    => 'rectangle'
		);

		foreach ( $default_values as $option => $default_value ) {
			if ( ! get_option( 'podlove_subscribe_button_default_' . $option ) ) {
				update_option( 'podlove_subscribe_button_default_' . $option, $default_value );
			}
		}

	}

	public static function shortcode( $args ) {
		if ( ! $args || ! isset( $args[ 'button' ] ) ) {
			return __( 'You need to create a Button first and provide its ID.', 'podlove-subscribe-button' );
		} else {
			$buttonid = $args[ 'button' ];
		}

		// Fetch the (network)button by it's name
		if ( ! $button = \PodloveSubscribeButton\Model\Button::get_button_by_name( $args[ 'button' ] ) )
			return sprintf( __( 'Oops. There is no button with the ID "%s".', 'podlove-subscribe-button' ), $args[ 'button' ] );

		// Get button styling and options
		$autowidth = self::interpret_width_attribute( self::get_array_value_with_fallback( $args, 'width' ) );
		$size      = self::get_attribute( 'size', self::get_array_value_with_fallback( $args, 'size' ) );
		$style     = self::get_attribute( 'style', self::get_array_value_with_fallback( $args, 'style' ) );
		$format    = self::get_attribute( 'format', self::get_array_value_with_fallback( $args, 'format' ) );
		$color     = self::get_attribute( 'color', self::get_array_value_with_fallback( $args, 'color' ) );

		if ( isset( $args[ 'language' ] ) ) {
			$language = $args[ 'language' ];
		} else {
			$language = 'en';
		}

		if ( isset( $args[ 'color' ] ) ) {
			$color = $args[ 'color' ];
		} else {
			$color = get_option( 'podlove_subscribe_button_default_color', '#599677' );
		}

		if ( isset( $args[ 'hide' ] ) && $args[ 'hide' ] == 'true' ) {
			$hide = true;
		} else {
			$hide = false;
		}

		// Render button
		return $button->render( $size, $autowidth, $style, $format, $color, $hide, $buttonid, $language );

	}

	public static function get_array_value_with_fallback( $args, $key ) {
		if ( isset( $args[ $key ] ) ) {
			return $args[ $key ];
		}

		return false;

	}

	/**
	 * @param  string $attribute
	 * @param  string $attribute_value
	 * @return string
	 */
	private static function get_attribute( $attribute = null, $attribute_value = null ) {
		if ( isset( $attribute_value ) && ctype_alnum( $attribute_value ) && key_exists( $attribute_value, \PodloveSubscribeButton\Model\Button::$$attribute ) ) {
			return $attribute_value;
		} else {
			return get_option( 'podlove_subscribe_button_default_' . $attribute, \PodloveSubscribeButton\Model\Button::$properties[ $attribute ] );
		}

	}

	/**
	 * Interprets the provided width attribute and return either auto- or a specific width
	 *
	 * @param  string $width_attribute
	 * @return string
	 */
	private static function interpret_width_attribute( $width_attribute = null ) {
		if ( $width_attribute == 'auto' ) {
			return 'on';
		}

		if ( $width_attribute && $width_attribute !== 'auto' ) {
			return 'off';
		}

		return get_option( 'podlove_subscribe_button_default_autowidth', 'on' );

	}

} // END class
