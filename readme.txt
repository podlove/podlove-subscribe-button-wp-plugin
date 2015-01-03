=== Plugin Name ===
Contributors: chemiker
Donate link: http://flattr.com/thing/728463/Podlove-Podcasting-Plugin-for-WordPress
Tags: button, podlove, podcast, feed, subscribe
Requires at least: 3.0.1
Tested up to: 4.1
Stable tag: 1.0.0
License: MIT
License URI: http://opensource.org/licenses/MIT

Podlove Subscribe button allows your users to easily select a podcast feed and pass it along to their favourite podcast app.

== Description ==

odcasts are becoming more and more popular for many reasons – the subscription model being one of the more important ones. Automatic downloads make podcast content easy to access and flexible to use and create an important bond between creators and the audience.

However, to actually subscribe to a podcast has proven to be a confusing and error prone task. No more: because today, we are going to change all this with the introduction of the Podlove Subscribe Button.

The idea is simple: present one simple button that makes selecting a podcast feed and passing it along to your favourite podcast app (either on your computer, mobile phone or in the cloud) a no-brainer. To subscribe, your audience just clicks the Podlove Subscribe Button, selects an app and there is (usually) no step three.

Development of the plugin is an open process. The current version is available on github:

https://github.com/podlove/podlove-subscribe-button-wp-plugin

Feel free to contribute and to fix errors or send improvements via github.

== Installation ==
1. Upload the plugin folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Usage ==
1. Navigate to `Settings > Podcast Subscribe Button`
2.  Fill out all fields and remember your Button ID
3. Add the button to your blog using the shortcode `[podlove-subscribe-button id="{{yourButtonID}}"]` and your button ID

== Options ==
* `size` One of 'small', 'medium', 'big', 'big-logo'
* `width` Empty or 'auto'

See also http://docs.podlove.org/podlove-subscribe-button/

== Examples ==
`[podlove-subscribe-button id="mybutton" size="big-logo"]`
Brings a large button with the podcast logo on top.

`[podlove-subscribe-button id="mybutton" size="small" width="auto"]`
Brings a medium-sized button with full width (100% of the parent container element).