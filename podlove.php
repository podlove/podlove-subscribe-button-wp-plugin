<?php
/**
 * @author    Podlove <podlove@podlove.org>
 * @copyright Copyright (c) 2014-2018, Podlove
 * @license   https://github.com/podlove/podlove-subscribe-button-wp-plugin/blob/master/LICENSE MIT
 * @package   Podlove\PodloveSubscribeButton
 * @version   1.4.0-beta
 */

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

/** Check if PHP version is sufficient */
if ( ! version_compare( phpversion(), '5.4', ">=" ) ) {

	function podlove_psb_php_notice() {
		?>
		<div id="message" class="error">
			<p>
				<strong>The Podlove Subscribe Button Plugin could not be activated</strong>
			</p>
			<p>
				The Podlove Subscribe Button Plugin requires <code>PHP 5.3</code> or higher.<br>
				You are running <code>PHP <?php echo phpversion(); ?></code>.<br>
				Please ask your hoster how to upgrade to an up-to-date PHP version.
			</p>
		</div>
		<?php
	}

	function podlove_psb_deactivate() {
		deactivate_plugins( plugin_basename( __FILE__ ) );
	}

	add_action( 'admin_notices', 'podlove_psb_php_notice' );
	add_action( 'admin_init', 'podlove_psb_deactivate' );

	return;

}

require_once __DIR__ . '/vendor/autoload.php';

register_activation_hook(   __FILE__, array( 'PodloveSubscribeButton\Setup', 'activation' ) );
register_deactivation_hook( __FILE__, array( 'PodloveSubscribeButton\Setup', 'deactivation' ) );
register_uninstall_hook(    __FILE__, array( 'PodloveSubscribeButton\Setup', 'uninstall' ) );

PodloveSubscribeButton\Migration::eval_db();
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
		wp_register_style( 'podlove-subscribe-button', \PodloveSubscribeButton\Helpers::get_url( '' ) . 'css/style.css' , false, self::$version );
		wp_enqueue_style( 'podlove-subscribe-button' );

		// Admin JS
		wp_enqueue_style( 'wp-color-picker' );
		wp_register_script( 'podlove-subscribe-button-admin-tools', \PodloveSubscribeButton\Helpers::get_url( '' ) . 'js/admin.js', array( 'jquery', 'wp-color-picker' ), self::$version );
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
			'language',
		);

		foreach ( $settings as $setting ) {
			register_setting( 'podlove-subscribe-button', 'podlove_subscribe_button_default_' . $setting );
		}

	}

	static function get_option( $key, $default = false ) {

		/** @todo after option reformat */
		$options = \get_option( 'podlove_psb_defaults' );
		// ! isset( $key ) -> $default;

        return $options[ $key ];
	}

	public static function register_shortcode() {
		add_shortcode( 'podlove-subscribe-button', array( 'PodloveSubscribeButton', 'shortcode' ) );
	}



	/**
	 * Add the shortcode
	 *
	 * @param $args
	 *
	 * @return string|void
	 */
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

		if ( isset( $args[ 'language' ] ) ) {
			$language = $args[ 'language' ];
		} else {
			$language = self::get_attribute( 'language', self::get_array_value_with_fallback( $args, 'language' ) );
		}

		if ( isset( $args[ 'color' ] ) ) {
			$color = $args[ 'color' ];
		} else {
			$color = self::get_attribute( 'color', self::get_array_value_with_fallback( $args, 'color' ) );
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
		    $default = get_option( 'podlove_psb_defaults', \PodloveSubscribeButton\Defaults::options() );
			return $default[ $attribute ];
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

		return self::get_option( 'autowidth', 'on' );

	}

	public static function plugin_file() {
		return __FILE__;
	}

} // END class
