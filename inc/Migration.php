<?php
/**
 * @author    Podlove <podlove@podlove.org>
 * @copyright Copyright (c) 2014-2018, Podlove
 * @license   https://github.com/podlove/podlove-subscribe-button-wp-plugin/blob/master/LICENSE MIT
 * @package   Podlove\PodloveSubscribeButton
 */

/**
 * Version management for database migrations.
 *
 * Database changes require special care:
 * - the model has to be adjusted for users installing the plugin
 * - the current setup has to be migrated for current users
 *
 * These migrations are a way to handle current users. They do *not*
 * run on plugin activation.
 *
 * Pattern:
 *
 * - increment \PodloveSubscribeButton\DATABASE_VERSION constant by 1, e.g.
 * 		```php
 * 		define( __NAMESPACE__ . '\DATABASE_VERSION', 2 );
 * 		```
 *
 * - add a case in `\PodloveSubscribeButton\run_migrations_for_version`, e.g.
 * 		```php
 * 		function run_migrations_for_version( $version ) {
 *			global $wpdb;
 *			switch ( $version ) {
 *				case 2:
 *					$wbdb-> // run sql or whatever
 *					break;
 *			}
 *		}
 *		```
 *
 *		Feel free to move the migration code into a separate function if it's
 *		rather complex.
 *
 * - adjust the main model / setup process so new users installing the plugin
 *   will have these changes too
 *
 * - Test the migrations! :)
 */

namespace PodloveSubscribeButton;
use \PodloveSubscribeButton\Model;

define( __NAMESPACE__ . '\DATABASE_VERSION', 3 );

class Migration {

	public static function eval_db() {
		add_action( 'admin_init', array( 'PodloveSubscribeButton\Migration', 'maybe_run_database_migrations' ) );
		add_action( 'admin_init', array( 'PodloveSubscribeButton\Migration', 'run_database_migrations' ), 5 );
	}

	public static function maybe_run_database_migrations() {
		$database_version = get_option( 'podlove_subscribe_button_plugin_database_version' );

		if ( $database_version === false ) {
			// plugin has just been installed or Plugin Version < 1.3
			update_option( 'podlove_subscribe_button_plugin_database_version', DATABASE_VERSION );
		}

	}

	public static function run_database_migrations() {
		if ( get_option( 'podlove_subscribe_button_plugin_database_version' ) >= DATABASE_VERSION ) {
			return;
		}

		if ( is_multisite() ) {
			set_time_limit( 0 ); // may take a while, depending on network size
			Helpers::for_every_podcast_blog( function() { self::migrate_for_current_blog(); });
		} else {
			self::migrate_for_current_blog();
		}

		if ( isset( $_REQUEST[ '_wp_http_referer' ] ) && $_REQUEST[ '_wp_http_referer' ] ) {
			wp_redirect( $_REQUEST[ '_wp_http_referer' ] );
			exit;
		}
	}

	public static function migrate_for_current_blog() {
		$database_version = get_option( 'podlove_subscribe_button_plugin_database_version' );

		for ( $i = $database_version + 1; $i <= DATABASE_VERSION; $i++ ) {
			self::run_migrations_for_version( $i );
			update_option( 'podlove_subscribe_button_plugin_database_version', $i );
		}

	}

	/**
	 * Find and run migration for given version number.
	 *
	 * @todo  move migrations into separate files
	 *
	 * @param  int $version
	 */
	private static function run_migrations_for_version( $version ) {
		global $wpdb;

		switch ( $version ) {
			case 3:
				self::migration_case_3();
				break;
		}

	}

	private static function migration_case_3() {

		/**
		 * @todo
		 *
		 * network: for each blog with plugin active (or all ?)
		 */

		//
		/**
		 * 1. multiple options -> 1 option as associative array
		 * - consolidate `_default_*` into `_podlove_psb_defaults[*]`
		 * - remove old options
		 */
		if ( ! get_option( 'podlove_subscribe_button_default_size' ) ) {
			$old_options = array(
				'podlove_subscribe_button_default_size',
				'podlove_subscribe_button_default_autowidth',
				'podlove_subscribe_button_default_color',
				'podlove_subscribe_button_default_style',
				'podlove_subscribe_button_default_format',
				'podlove_subscribe_button_default_language',
			);
			$options     = array();

			foreach ( $old_options as $option ) {

				$key   = end( explode( '_', $option ) );
				$value = get_option( $option );

				$options[ $key ] = $value;
			}

			// if old ones exist
			add_option( 'podlove_psb_defaults', $options );

			foreach ( $old_options as $option ) {
				delete_option( $option );
			}
		}

		// 2. DB !?

	}

}
