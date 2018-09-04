<?php
/**
 * @author    Podlove <podlove@podlove.org>
 * @copyright Copyright (c) 2014-2018, Podlove
 * @license   https://github.com/podlove/podlove-subscribe-button-wp-plugin/blob/master/LICENSE MIT
 * @package   Podlove\PodloveSubscribeButton
 */

namespace PodloveSubscribeButton;

function for_every_podcast_blog( $callback ) {

	global $wpdb;

	$plugin  = basename( \PodloveSubscribeButton\PLUGIN_DIR ) . '/' . \PodloveSubscribeButton\PLUGIN_FILE_NAME;
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

/**
 * Check if `Podlove Publisher` is installed + activated
 *
 * @return bool
 */
function is_podlove_publisher_active() {
	if ( is_plugin_active( "podlove-podcasting-plugin-for-wordpress/podlove.php" ) ) {
		return true;
	}

	return false;

}
