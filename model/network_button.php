<?php
/**
 * @author    Podlove <podlove@podlove.org>
 * @copyright Copyright (c) 2014-2018, Podlove
 * @license   https://github.com/podlove/podlove-subscribe-button-wp-plugin/blob/master/LICENSE MIT
 * @package   Podlove\PodloveSubscribeButton
 */

namespace PodloveSubscribeButton\Model;

class NetworkButton extends Button {

	public static function table_name() {
		global $wpdb;

		// prefix with $wpdb prefix
		return $wpdb->base_prefix . self::name();
	}

}

NetworkButton::property( 'id', 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY' );
NetworkButton::property( 'name', 'VARCHAR(255)' );
NetworkButton::property( 'title', 'VARCHAR(255)' );
NetworkButton::property( 'subtitle', 'VARCHAR(255)' );
NetworkButton::property( 'description', 'TEXT' );
NetworkButton::property( 'cover', 'VARCHAR(255)' );
NetworkButton::property( 'feeds', 'TEXT' );
