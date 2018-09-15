<?php
/**
 * @author    Podlove <podlove@podlove.org>
 * @copyright Copyright (c) 2014-2018, Podlove
 * @license   https://github.com/podlove/podlove-subscribe-button-wp-plugin/blob/master/LICENSE MIT
 * @package   Podlove\PodloveSubscribeButton
 */

namespace PodloveSubscribeButton;

Class Helpers {

	public static function get_path( $folder = '' ) {

		$path = untrailingslashit( plugin_dir_path( \PodloveSubscribeButton::plugin_file() ) );

		return $path . $folder;

	} // End get_path()

	public static function get_url( $path = '' ) {

		$url = trailingslashit( plugins_url( $path, \PodloveSubscribeButton::plugin_file() ) );

		return $url;

	} // End get_url()

	/**
	 * Get plugin basename
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public static function get_basename() {
		return plugin_basename( \PodloveSubscribeButton::plugin_file() );
	} // get_basename()

	/**
	 * Check if `Podlove Publisher` is installed + activated
	 *
	 * @return bool
	 */
	public static function is_podlove_publisher_active() {
		if ( is_plugin_active( "podlove-podcasting-plugin-for-wordpress/podlove.php" ) ) {
			return true;
		}

		return false;

	}

	/**
	 * Get button compatible language string.
	 *
	 * Examples:
	 *
	 *  language('de');    // => 'de'
	 *  language('de_DE'); // => 'de'
	 *  language('en_GB'); // => 'en'
	 *
	 * @param  string $language language identifier
	 *
	 * @return string
	 */
	static function language( $language ) {
		if ( empty( $language ) ) {
			$language = get_option( 'WPLANG' );
		}

		$lang_code = strtolower(explode('_', $language)[0]);

		if ( in_array( $lang_code, \PodloveSubscribeButton\Defaults::button('language' ) ) ) {
			return $lang_code;
		} else {
			return 'en';
		}
	}

	public static function for_every_podcast_blog( $callback ) {
		global $wpdb;

		$plugin  = self::get_basename();
		$blogids = $wpdb->get_col( "SELECT blog_id FROM " . $wpdb->blogs );

		if ( ! is_array( $blogids ) )
			return;

		foreach ( $blogids as $blog_id ) {
			switch_to_blog( $blog_id );
			if ( is_plugin_active( $plugin ) ) {
				$callback();
			}
			restore_current_blog();
		}
	}

} // END Class
