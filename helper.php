<?php

namespace PodloveSubscribeButton;

function for_every_podcast_blog($callback) {
	global $wpdb;

	$plugin  = basename(\PodloveSubscribeButton\PLUGIN_DIR) . '/' . \PodloveSubscribeButton\PLUGIN_FILE_NAME;
	$blogids = $wpdb->get_col("SELECT blog_id FROM " . $wpdb->blogs);

	if (!is_array($blogids))
		return;

	foreach ($blogids as $blog_id) {
		switch_to_blog($blog_id);
		if (is_plugin_active($plugin)) {
			$callback();
		}
		restore_current_blog();
	}
}