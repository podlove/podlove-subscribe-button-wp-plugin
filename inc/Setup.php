<?php
/**
 * @author    Podlove <podlove@podlove.org>
 * @copyright Copyright (c) 2014-2018, Podlove
 * @license   https://github.com/podlove/podlove-subscribe-button-wp-plugin/blob/master/LICENSE MIT
 * @package   Podlove\PodloveSubscribeButton
 */

namespace PodloveSubscribeButton;

class Setup {

	public static function activation( $network_wide ) {

		global $wpdb;

		if ( $network_wide ) {
			Model\NetworkButton::build();
			set_time_limit(0); // may take a while, depending on network size
			$blogids = $wpdb->get_col( "SELECT blog_id FROM " . $wpdb->blogs );
			foreach ( $blogids as $blog_id ) {
				switch_to_blog( $blog_id );
				self::activate_for_current_blog();
			}
		} else {
			self::activate_for_current_blog();
		}

	}

	public static function activate_for_current_blog() {

		// Build Databases
		Model\Button::build();

		$default_values = Defaults::options();

//		add_option( 'podlove_psb_defaults', $default_values );

		foreach ( $default_values as $option => $default_value ) {
			if ( ! get_option( 'podlove_subscribe_button_default_' . $option ) ) {
				update_option( 'podlove_subscribe_button_default_' . $option, $default_value );
			}
		}

	}

	public static function deactivation( $network_wide ) {

	}

	public static function uninstall() {
		if ( is_multisite() ) {
			self::uninstall_for_network();
		} else {
			self::uninstall_for_current_blog();
		}
	}

	public static function uninstall_for_network() {

		global $wpdb;

		Model\NetworkButton::destroy();

		$current_blog = $wpdb->blogid;
		$blogids      = $wpdb->get_col( "SELECT blog_id FROM " . $wpdb->blogs );

		foreach ( $blogids as $blog_id ) {
			switch_to_blog( $blog_id );
			self::uninstall_for_current_blog();
		}

		switch_to_blog( $current_blog );

//		$options = array(
//			/** 1.4+ */
//			'podlove_subscribe_button_defaults',
//		);
//
//		foreach ( $options as $option ) {
//			delete_site_option( $option );
//		}

	}

	public static function uninstall_for_current_blog() {

		// remove DB tables
		Model\Button::destroy();

		$options = array(
			/** 1.4+ */
			'podlove_subscribe_button_defaults',
			'widget_podlove_subscribe_button_wp_plugin_widget',
			/** 1.3.x */
			'podlove_subscribe_button_default_size',
			'podlove_subscribe_button_default_autowidth',
			'podlove_subscribe_button_default_color',
			'podlove_subscribe_button_default_style',
			'podlove_subscribe_button_default_format',
			'podlove_subscribe_button_default_language',
			'podlove_subscribe_button_plugin_database_version',
		);

		foreach ( $options as $option ) {
			delete_option( $option );
		}

	}

} // END class
