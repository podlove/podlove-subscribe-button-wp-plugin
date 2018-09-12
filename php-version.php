<?php
/**
 * @author    Podlove <podlove@podlove.org>
 * @copyright Copyright (c) 2014-2018, Podlove
 * @license   https://github.com/podlove/podlove-subscribe-button-wp-plugin/blob/master/LICENSE MIT
 * @package   Podlove\PodloveSubscribeButton
 */

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
