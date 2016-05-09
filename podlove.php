<?php
/**
 * Plugin Name: Podlove Subscribe Button
 * Plugin URI:  http://wordpress.org/extend/plugins/podlove-subscribe-button/
 * Description: Brings the Podlove Subscribe Button to your WordPress installation.
 * Version:     2.0
 * Author:      Podlove
 * Author URI:  http://podlove.org
 * License:     MIT
 * License URI: license.txt
 * Text Domain: podlove
 */

$correct_php_version = version_compare( phpversion(), "5.3", ">=" );

if ( ! $correct_php_version ) {
	echo "Podlove Subscribe Button Plugin requires <strong>PHP 5.3</strong> or higher.<br>";
	echo "You are running PHP " . phpversion();
	exit;
}

require('settings/buttons.php');
// Models
require('model/base.php');
require('model/button.php');
require('model/network_button.php');
// Table
require('settings/buttons_list_table.php');
// Media Types
require('media_types.php');
// Widget
require('widget.php');

add_action( 'admin_menu', array( 'PodloveSubscribeButton', 'admin_menu') );
if ( is_multisite() )
	add_action( 'network_admin_menu', array( 'PodloveSubscribeButton', 'admin_network_menu') );

add_action( 'admin_init', array( 'PodloveSubscribeButton\Settings\Buttons', 'process_form' ) );
register_activation_hook( __FILE__, array( 'PodloveSubscribeButton', 'build_models' ) );

add_action( 'admin_enqueue_scripts', function () {
	wp_register_style( 'podlove-subscribe-button', plugin_dir_url(__FILE__).'style.css' );
	wp_enqueue_style( 'podlove-subscribe-button' );

	wp_enqueue_style('podlove-subscribe-button-spectrum', plugin_dir_url(__FILE__). 'js/spectrum/spectrum.css');
	wp_enqueue_script('podlove-subscribe-button-spectrum', plugin_dir_url(__FILE__). 'js/spectrum/spectrum.js', array('jquery'));
	wp_enqueue_script('podlove-subscribe-button-admin-tools', plugin_dir_url(__FILE__). 'js/admin.js', array('jquery'));
} );

// Register Settings
add_action( 'admin_init', function () {
	$settings = array('size', 'autowidth', 'style', 'format', 'color');

	foreach ($settings as $setting) {
		register_setting( 'podlove-subscribe-button', 'podlove_subscribe_button_default_' . $setting );
	}
} );

add_shortcode( 'podlove-subscribe-button', array( 'PodloveSubscribeButton', 'shortcode' ) );

class PodloveSubscribeButton {

	public static function admin_menu() {
		add_options_page(
				'Podlove Subscribe Button Options',
				'Podlove Subscribe Button',
				'manage_options',
				'podlove-subscribe-button',
				array( 'PodloveSubscribeButton\Settings\Buttons', 'page')
			);
	}

	public static function admin_network_menu() {
		add_submenu_page(
				'settings.php',
				'Podlove Subscribe Button Options',
				'Podlove Subscribe Button',
				'manage_options',
				'podlove-subscribe-button',
				array( 'PodloveSubscribeButton\Settings\Buttons', 'page')
			);
	}

	public static function build_models() {
		\PodloveSubscribeButton\Model\Button::build();
		if ( is_multisite() )
			\PodloveSubscribeButton\Model\NetworkButton::build();
	}

	public static function shortcode( $args ) {
		if ( ! $args || ! isset($args['button']) )
			return __('You need to create a Button first and provide its ID.', 'podlove');

		if ( ! $button = ( \PodloveSubscribeButton\Model\Button::find_one_by_property('name', $args['button']) ? \PodloveSubscribeButton\Model\Button::find_one_by_property('name', $args['button']) : \PodloveSubscribeButton\Model\NetworkButton::find_one_by_property('name', $args['button']) ) )
			return sprintf( __('Oops. There is no button with the ID "%s".', 'podlove'), $args['button'] );

		if ( isset($args['width']) && $args['width'] == 'auto' ) {
			$autowidth = 'on';
		} elseif ( isset($args['width']) && $args['width'] !== 'auto' ) {
			$autowidth = 'off';
		} else {
			$autowidth = get_option('podlove_subscribe_button_default_autowidth', 'on');
		}

		if ( isset($args['size']) && in_array($args['size'], array('small', 'medium', 'big')) ) {
			$size = $args['size'];
		} else {
			$size = get_option('podlove_subscribe_button_default_size', 'big');
		}

		if ( isset($args['style']) && in_array($args['style'], array('filled', 'outline', 'frameless')) ) {
			$style = $args['style'];
		} else {
			$style = get_option('podlove_subscribe_button_default_style', 'filled');
		}

		if ( isset($args['format']) && in_array($args['format'], array('rectangle', 'square', 'cover')) ) {
			$format = $args['format'];
		} else {
			$format = get_option('podlove_subscribe_button_default_format', 'rectangle');
		}

		if ( isset($args['color']) ) {
			$color = $args['color'];
		} else {
			$color = get_option('podlove_subscribe_button_default_color', '#599677');
		}

		if ( isset($args['hide']) && $args['hide'] == 'true' ) {
			$hide = TRUE;
		}

		if ( isset($args['buttonid']) ) {
			$buttonid = $args['buttonid'];
		}

		return $button->render($size, $autowidth, $style, $format, $color, $hide, $buttonid);
	}

}