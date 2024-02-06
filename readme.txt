=== Podlove Subscribe button ===
Contributors: chemiker, cfoellmann
Donate link: https://flattr.com/thing/728463/Podlove-Podcasting-Plugin-for-WordPress
Tags: button, podlove, podcast, feed, subscribe, widget, network
Requires at least: 3.5.0
Tested up to: 6.1.1
Requires PHP: 5.3
Stable tag: 1.3.11
License: MIT
License URI: https://github.com/podlove/podlove-subscribe-button-wp-plugin/blob/master/LICENSE

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

* [***Podlove Podcast Publisher***](https://publisher.podlove.org) - a powerful plugin for WordPress for podcasters to publish metadata-rich podcasts
* ***Podlove Web Player*** - a podcast-optimized HTML5 web player with chapter support
* [***Podlove Subscribe Button***](https://subscribe-button.podlove.org) - a centrally hosted universal podcast subscribe button for the web

### Other Resources

* Podlove Project: https://podlove.org/
* Podlove Community: https://community.podlove.org/
* Documentation: https://docs.podlove.org/
* Donate: https://podlove.org/donations/

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

This is the easiest and recommended way of using this plugin especially if you ony want to display a button for a single podcast. For best visibility choose size "big" and format "cover" to make your Subscribe Button stand out and easily locatable.


### Include the Subscribe Button in WordPress pages using a Shortcode

If you want to include a Podlove Subscribe Button in a WordPress page (or a post) just put the shortcode `[podlove-subscribe-button]` at the desired position in your text. Use the following shortcode parameters to configure it properly:

* `button` - the ID of one of your preconfigured buttons
* `size` - one of 'small', 'medium', 'big'
* `width` - specify desired button width in CSS compatibles values or 'auto' automatic width depending on context.
* `color` - specify the color of the button in a CSS compatible format (keyword, rgb-hex, rgb, rgba, hsl, hsla)
* `style` - default is filled, options are 'outline' and 'frameless'
* `format` - default is a rectangle, options are 'square' and 'cover' (**Note**: 'cover' has a max size of 300px)
* `hide` - if set to `true` the button will not be shown (useful if you want to use your own element)
* `language` - specify the language the texts on the button and popup should be in (currently supports 'de', 'en', 'eo', 'fi', 'fr', 'nl', 'zh' and 'ja')

Note that if you do not provide one of the attributes the subscribe button will use the globally set default.

### Shortcode Examples

`[podlove-subscribe-button button="mybutton1" size="big"]`
Displays a large button with the podcast logo on top using data from button configuration with id "mybutton1". All other options will be set to the globally set default values.

`[podlove-subscribe-button button="mybutton2" size="medium" width="100pt" language="de"]`
Displays a small button with a width of 100pt using data from button configuration with id "mybutton2" in german. All other options will be set to the globally set default values.

`[podlove-subscribe-button button="mybutton3" size="big" width="auto" format="cover"]`
Displays a big button with a the podcast cover and automatically adjusted using data from button configuration with id "mybutton3". All other options will be set to the globally set default values.

`[podlove-subscribe-button button="mybutton4" format="square" style="frameless"]`
Displays a small, frameless square button with using data from button configuration with id "mybutton4". All other options will be set to the globally set default values.

== Frequently Asked Questions ==

### I'm running the Podlove Publisher. Do I need this plugin to display a Subscribe button?

Yes and No. If you simply want to display a Subscribe Button for your Podcast you publish with the Podlove Publisher you do not need this plugin as the Publisher itself provides this functionality. If you want to display multiple Subscribe Buttons you can use the Subscribe Button plugin.

### Where do I create those "Network-wide" Buttons?

You can find the Panel in the Networks Settings Section

### Does this plugin actually contain the code for the button?

No. This plugin just embeds the code that is needed to display the Podlove Subscribe Button. The button itself is served live from api.podlove.org

### Do I update the plugin to get support for more clients?

No. As the Podlove Subscribe Button is hosted centrally, it gets updated regularly independent from this pugin.

### I am a podcast app developer and would like my app be listed in the button. What do I need to do?

Consult the information provided on [the technical information page](https://podlove.org/podlove-subscribe-button) at podlove.org on how the button works and what app developers need to support and do in order to get listed in the button.


== Screenshots ==

1. The Subscribe buttons are listed with a preview to check their functionality.
2. The Subscribe button administration interface.
3. The Subscribe button widgets provides an easy way to include your button in your blog.
4. The Subscribe button widgets can be easily be adjusted to your needs.


== Upgrade Notice ==

### 1.2
* Due to internal changes the Subscribe Button widget might be removed from your sidebar. If this happened just readd it to the sidebar.

### 1.0.1
* Shortcodes: The `id` attribute was changed to `button`. Please adjust your shortcodes.


== Changelog ==

### 1.3.11
* FIXED SQL Injection vulnerabilities

### 1.3.8 / 1.3.9 / 1.3.10 (2023-02-10)
* FIXED XSS and CSRF vulnerabilities

### 1.3.7 (2019-07-20)
* FIXED an issue that prevented the subscribe button to appear
* FIXED a XSS vulnerability

### 1.3.6 (2018-10-02)
* FIXED autowidth value not saving to options correctly

### 1.3.5 (2018-09-09)
* Replaced the spectrum color selector with WP Color Picker
* FIXED multiple color pickers when adding a widget

### 1.3.4 (unreleased)
* FIXED detection of Publisher plugin (widget naming)
* FIXED deprecated WP core functions removed
* FIXED fix select button for widget

### 1.3.3
* Enhanced Support for translation
* Fixed an issue that prevented network buttons to be shown

### 1.3.2
* Fixed an error that caused notices on some installations

### 1.3.1
* Fixed an error that caused fatal errors on some installations

### 1.3
* Podcast Cover Image can be selected using the WordPress Media Library
* When using the Shortcode, the button language can now be set using the language attribute
* Enhanced support for button color
* Fixed a bug that caused an error message for non-network installations
* UI enhancements and further optimizations

### 1.2.1
* Various bugfixes

### 1.2
* Added various options that allow customization of the Subscribe button
* Various bugfixes

### 1.1.3
* Various bugfixes

### 1.1.2
* Remove duplicate `wp-admin/` from Network-Settings URL
* Check for old PHP versions before plugin is activated

### 1.1.1
* Smaller Bugfix: Build tables on plugin-activation only

### 1.1
* Network Support - If you run a WordPress Multisite Installation you can create network-wide available Buttons
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
