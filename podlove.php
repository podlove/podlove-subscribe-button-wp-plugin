<?php
/**
 * Plugin Name: Podlove Subscribe Button
 * Plugin URI:  http://wordpress.org/extend/plugins/podlove-subscribe-button/
 * Description: Brings the Podlove Subscribe Button to your WordPress installation.
 * Version:     1.0
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
// Table
require('settings/buttons_list_table.php');
// Media Types
require('media_types.php');
// Widget
require('widget.php');

add_action( 'admin_menu', array( 'PodloveSubscribeButton', 'admin_menu') );
add_action( 'admin_init', array( 'PodloveSubscribeButton', 'register_settings') );
add_action( 'admin_init', array( 'PodloveSubscribeButton\Settings\Buttons', 'process_form' ) );
add_action( 'admin_enqueue_scripts', function () {
	wp_register_style( 'podlove-subscribe-button', plugin_dir_url(__FILE__).'style.css' );
	wp_enqueue_style( 'podlove-subscribe-button' );
} );

if ( is_plugin_active('podlove-publisher/podlove.php') )
	add_action( 'admin_notices', function () {
		echo '<div class="error"><p>'.__('Warning: If you are using Podlove Publisher please use the <em>Subscribe button</em> module instead of the WordPress plugin!', 'podlove').'</p></div>';
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

	public static function register_settings() {
		\PodloveSubscribeButton\Model\Button::build();		
	}

	public static function shortcode( $args ) {
		if ( ! $args || ! isset($args['id']) )
			return __('You need create a Button first and provide its ID.', 'podlove');

		if ( ! $button = \PodloveSubscribeButton\Model\Button::find_one_by_property('name', $args['id']) )
			return __('Oops. There is no button with the provided ID.', 'podlove');

		$autowidth = ( isset($args['width']) && $args['width'] == 'auto' ? 'on' : '' ); // "on" because this value originates from a checkbox
		$size = ( isset($args['size']) && in_array($args['size'], array('small', 'medium', 'big', 'big-logo')) ? $args['size'] : 'medium' );

		return $button->render( $size, $autowidth );
	}

}