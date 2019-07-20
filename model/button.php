<?php
namespace PodloveSubscribeButton\Model;

class Button extends Base {

	public static $properties = array(
		// $property => $default value
		'size' => 'big',
		'color' => '#599677',
		'autowidth' => 'on',
		'style' => 'filled',
		'format' => 'rectangle',
		'hide' => 'false'
		// Note: the fields 'language' and 'json-data' cannot be set here (No function call allowed within class variables)
	);

	public static $style = array(
		'filled' => 'Filled',
		'outline' => 'Outline',
		'frameless' => 'Frameless'
	);

	public static $format = array(
		'rectangle' => 'Rectangle',
		'square' => 'Square',
		'cover' => 'Cover'
	);

	public static $width = array(
		'on' => 'Yes',
		'off' => 'No'
	);

	public static $size = array(
		'small' => 'Small',
		'medium' => 'Medium',
		'big' => 'Big'
	);


	/**
	 * Fetches a Button or Network Button with a specific name
	 * @param  string $name
	 * @return object||FALSE
	 */
	public static function get_button_by_name($name) {
		if ( $button = \PodloveSubscribeButton\Model\Button::find_one_by_property('name', $name) ) {
			return $button;
		}

		if ( $network_button = \PodloveSubscribeButton\Model\NetworkButton::find_one_by_property('name', $name) ) {
			$network_button->id = $network_button->id . 'N';
			return $network_button;
		}

		return false;
	}

	/**
	 * Returns either global buttons settings or the default settings
	 * @param  array
	 * @return array
	 */
	public static function get_global_setting_with_fallback( $settings=array() ) {
		foreach (self::$properties as $property => $default) {
			$settings[$property] = ( get_option('podlove_subscribe_button_default_' . $property) ? get_option('podlove_subscribe_button_default_' . $property) : $default );
		}

		return $settings;
	}

	/**
	 * Gathers all information and renders the Subscribe button.
	 * @param  string  $size
	 * @param  string  $autowidth
	 * @param  string  $style
	 * @param  string  $format
	 * @param  string  $color
	 * @param  boolean $hide
	 * @return string
	 */
	public function render( $size='big', $autowidth='on', $style='filled', $format='rectangle', $color='#599677', $hide = false, $buttonid = false, $language='en' ) {
		$button_styling = array_merge(
				$this->get_button_styling($size, $autowidth, $style, $format, $color),
				array(
						'hide' => $hide,
						'language' => $language
					)
			);

		return $this->provide_button_html(
			array(
				'title' => sanitize_text_field($this->title),
				'subtitle' => sanitize_text_field($this->subtitle),
				'description' => sanitize_textarea_field($this->description),
				'cover' => sanitize_text_field($this->cover),
				'feeds' => $this->get_feeds_as_array($this->feeds)
			), $button_styling );
	}

	/**
	 * Provides the feed as an array in the required format
	 * @return array
	 */
	private function get_feeds_as_array( $feeds=array() ) {
		$returnedFeeds = array();

		if (! $feeds)
			return $returnedFeeds;

		foreach ($feeds as $feed) {
			if ( isset(\PodloveSubscribeButton\MediaTypes::$audio[$feed['format']]['extension']) ) {
				$new_feed = array(
						'type' => 'audio',
						'format' => \PodloveSubscribeButton\MediaTypes::$audio[$feed['format']]['extension'],
						'url' => $feed['url'],
						'variant' => 'high'
					);

				if ( isset($feed['itunesfeedid']) && $feed['itunesfeedid'] > 0 )
					$new_feed['directory-url-itunes'] = "https://itunes.apple.com/podcast/id" . $feed['itunesfeedid'];

				$returnedFeeds[] = $new_feed;
			}
		}

		return $returnedFeeds;
	}

	/**
	 * Provides the HTML source of the Subscribe Button
	 * @param  array $podcast_data
	 * @param  array $button_styling
	 * @param  string $data_attributes
	 * @return string
	 */
	private function provide_button_html($podcast_data, $button_styling, $data_attributes="") {
		// Create data attributes for Button
		foreach ($button_styling as $attribute => $value) {
			$data_attributes .= 'data-' . $attribute . '="' . $value . '" ';
		}

		return"
			<script>
				podcastData".$this->id . " = ".json_encode($podcast_data)."
			</script>
			<script
				class=\"podlove-subscribe-button\"
				src=\"https://cdn.podlove.org/subscribe-button/javascripts/app.js\" " . $data_attributes . ">
			</script>
		";
	}

	/**
	 * Returns an array with either the set or default values
	 * @param  string $size
	 * @param  string $autowidth
	 * @param  string $style
	 * @param  string $format
	 * @param  string $color
	 * @return array
	 */
	public function get_button_styling($size, $autowidth, $style, $format, $color) {

		return array(
				// $attribute => $value
				'size' => ( $size == 'default' ? get_option('podlove_subscribe_button_default_size', $size) : $size )
			 	. self::interpret_autowidth_attribute($autowidth),
				'style' => ( $style == 'default' ? get_option('podlove_subscribe_button_default_style', $style) : $style ),
				'format' => ( $format == 'default' ? get_option('podlove_subscribe_button_default_format', $format) : $format ),
				'color' => ( isset($color) ? $color : get_option('podlove_subscribe_button_default_color', $color) ),
				'json-data' => 'podcastData' . $this->id
			);
	}

	/**
	 * Helper function to interpret the given $autowidth value correctly
	 * @param  string $autowidth
	 * @return string
	 */
	private static function interpret_autowidth_attribute($autowidth) {
		if ( $autowidth == 'default' && get_option('podlove_subscribe_button_default_autowidth') !== 'on' )
			return '';

		if ( $autowidth !== 'default' && $autowidth !== 'on' )
			return '';

		return ' auto';
	}
}

Button::property( 'id', 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY' );
Button::property( 'name', 'VARCHAR(255)' );
Button::property( 'title', 'VARCHAR(255)' );
Button::property( 'subtitle', 'VARCHAR(255)' );
Button::property( 'description', 'TEXT' );
Button::property( 'cover', 'VARCHAR(255)' );
Button::property( 'feeds', 'TEXT' );
