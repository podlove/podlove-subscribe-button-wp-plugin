<?php
/**
 * Plugin Name: Podlove Subscribe Button
 * Plugin URI:  http://wordpress.org/extend/plugins/podlove-subscribe-button/
 * Description: Brings the Podlove Subscribe Button to your WordPress installation.
 * Version:     1.1
 * Author:      Podlove
 * Author URI:  http://podlove.org
 * License:     MIT
 * License URI: license.txt
 * Text Domain: podlove
 */

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
} );

// Register Settings
add_action( 'admin_init', function () {
	register_setting( 'podlove-subscribe-button', 'podlove_subscribe_button_default_style' );
	register_setting( 'podlove-subscribe-button', 'podlove_subscribe_button_default_autowidth' );
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
		} else {
			$autowidth = get_option('podlove_subscribe_button_default_autowidth', 'on');
		}

		if ( isset($args['size']) && in_array($args['size'], array('small', 'medium', 'big', 'big-logo')) ) {
			$size = $args['size'];
		} else {
			$size = get_option('podlove_subscribe_button_default_style', 'big-logo');
		}

		return $button->render( $size, $autowidth );
	}

}