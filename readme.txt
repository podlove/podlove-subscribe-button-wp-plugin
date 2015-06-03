=== Podlove Subscribe button ===
Contributors: chemiker
Donate link: http://flattr.com/thing/728463/Podlove-Podcasting-Plugin-for-WordPress
Tags: button, podlove, podcast, feed, subscribe, widget, network
Requires at least: 3.0.1
Tested up to: 4.2
Stable tag: 1.1.2
License: MIT
License URI: http://opensource.org/licenses/MIT

Podlove Subscribe button allows your users to easily select a podcast feed and pass it along to their favorite podcast app.

== Description ==

This plugin allows easy inclusion of the Podlove Subscribe Button. Put it in your sidebar with a simple widget or include the button in pages and/or posts with a simple shortcode.


### About the Podlove Subscribe Button

The Podlove Subscribe Button allows for simple subscription of podcasts in anybody's favorite podcast app right from the browser without having to deal with pesky feed URLs and go on a hunt for feed subscription dialogues.

The button knows how to activate the subscription functionality in all popular podcast apps supporting various operating systems including but not limited to iOS, Android, WindowsPhone, OSX, Windows and Linux.

The button is centrally hosted and gets permanently updated when new podcast apps emerge or when new features are added. Using the button ensures your podcast can easily be subscribed to and presents an ever more popular user interface that your audience gets more and more used to.


### About Podlove

Podlove is an open source initiative to improve the overall podcasting infrastructure and to come up with simple but helpful standards and tools to make publishing and listening to podcast as easy as possible.

Podlove currently provides:

* ***Podlove Podcast Publisher*** - a powerful plugin for WordPress for podcasters to publish metadata-rich podcasts 
* ***Podlove Web Player*** - a podcast-optimized HTML5 web player with chapter support
* ***Podlove Subscribe Button*** - a centrally hosted universal podcast subscribe button for the web

### Other Resources

* Podlove Project: http://podlove.org/
* Podlove Community: https://community.podlove.org/
* Documentation: http://docs.podlove.org/
* Donate: http://podlove.org/donations/




== Installation ==

###  Install the plugin

1. Upload the plugin folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

### Configure buttons

1. Navigate to `Settings > Podlove Subscribe Button`
2. Fill out all fields, save and grab the shortcode
3. Add the button to your blog using the shortcode `[podlove-subscribe-button id="{{yourButtonID}}"]` with your button ID, or use the provided Podlove Subscribe Button Widget

### Display the Subscribe Button in your Sidebar using a Widget

Go to `Appearance -> Widgets` and drag the "Podlove Subscribe Button" widget to your sidebar configuration. Select the pre-configured button from the list and determine which style you want. That's it. The button should show up in your sidebar. Optionally add a title and description for your widget.

This is the easiest and recommended way of using this plugin especially if you ony want to display a button for a single podcast. For best visibility choose size "big-logo" to make your Subscribe Button stand out and easily locatable.


### Include the Subscribe Button in WordPress pages using a Shortcode

If you want to include a Podlove Subscribe Button in a WordPress page (or a post) just put the shortcode `[podlove-subscribe-button]` at the desired position in your text. Use the following shortcode parameters to configure it properly:

* `button` - the ID of one of your preconfigured buttons
* `size` - one of 'small', 'medium', 'big', 'big-logo'
* `width` - specify desired button width in CSS compatibles values or 'auto' automatic width depending on context.


### Shortcode Examples

`[podlove-subscribe-button button="mybutton1" size="big-logo"]`
Displays a large button with the podcast logo on top using data from button configuration with id "mybutton1"

`[podlove-subscribe-button button="mybutton2" size="medium" width="100pt"]`
Displays a small button with a width of 100pt using data from button configuration with id "mybutton2"





== Frequently Asked Questions ==

### I'm running the ***Podlove Publisher***. Do I need this plugin to display a Subscribe button?

Yes and No. If you simply want to display a Subscribe Button for your Podcast you publish with the Podlove Publisher you do not need this plugin as the Publisher itself provides this functionality. If you want to display multiple Subscribe Buttons you can use the Subscribe Button plugin.

### Where do I create those "Network-wide" Buttons?

You can find the Panel in the Networks Settings Section

### Does this plugin actually contain the code for the button?

No. This plugin just embeds the code that is needed to display the Podlove Subscribe Button. The button itself is served live from api.podlove.org

### Do I update the plugin to get support for more clients?

No. As the Podlove Subscribe Button is hosted centrally, it gets updated regularly independent from this pugin.

### I am a podcast app developer and would like my app be listed in the button. What do I need to do?

Consult the information provided on [the technical information page](http://podlove.org/podlove-subscribe-button) at podlove.org on how the button works and what app developers need to support and do in order to get listed in the button.


== Screenshots ==

1. The Subscribe buttons are listed with a preview to check their functionality.
2. The Subscribe button administration interface.
3. The Subscribe button widgets provides an easy way to include your button in your blog.
4. The Subscribe button widgets can be easily be adjusted to your needs.


== Upgrade Notice ==

### 1.0.1
* Shortcodes: The `id` attribute was changed to `button`. Please adjust your shortcodes.


== Changelog ==

### 1.1.2
* Remove duplicate `wp-admin/` from Network-Settings URL
* Check for old PHP versions before plugin is activated

### 1.1.1
* Smaller Bugfix: Build tables on plugin-activation only

### 1.1
* Network Support - If you run a WordPress Multisite Installation you can create Buttons that are available in the whole Network
* Default Settings for Size and Autowidth
* UI enhancements

### 1.0.1
* Enhancements in documentation
* Smaller Bugfixes
* Shortcodes: Change `id` attribute to `button`

### 1.0

* Initial Release
* Settings page to allow definition of preconfigured buttons
* WordPress Widget to show preconfigured buttons in WordPress Sidebar
* WordPress Shortcode [podlove-subscribe-button] to insert preconfigured buttons in pages and articles

