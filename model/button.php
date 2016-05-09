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
		'hide' => 'false',
		'buttonid' => ''
		// Note: the fields 'language' and 'json-data' cannot be set here (No function call allowed within class variables)
	);

	public static $styles = array(
		'filled' => 'Filled',
		'outline' => 'Outline',
		'frameless' => 'Frameless'
	);

	public static $formats = array(
		'rectangle' => 'Rectangle',
		'square' => 'Square',
		'cover' => 'Cover'
	);

	public static $autowidth = array(
		'on' => 'Yes',
		'off' => 'No'
	);

	public static $sizes = array(
		'small' => 'Small',
		'medium' => 'Medium',
		'big' => 'Big'
	);

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
	 * Provides the feed as an array in the required format
	 * @return array
	 */
	private function get_feeds_as_array( $feeds=array() ) {
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

				$feeds[] = $new_feed;
			}
		}

		return $feeds;
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
				podcastData".$this->id." =".json_encode($podcast_data)."
			</script>
			<script 
				class=\"podlove-subscribe-button\" 
				src=\"https://cdn.podlove.org/subscribe-button/javascripts/app.js\"" . $data_attributes . ">
			</script>
		";
	}

	/**
	 * Gathers all information and renders the Subscribe button.
	 * @param  string  $size      
	 * @param  string  $autowidth
	 * @param  string  $style     
	 * @param  string  $format    
	 * @param  string  $color     
	 * @param  boolean $hide      
	 * @param  boolean $buttonid  
	 * @return string
	 */
	public function render( $size='big', $autowidth='on', $style='filled', $format='rectangle', $color='#599677', $hide = FALSE, $buttonid = FALSE ) {

		// Helper function to interprete the given $autowidth value correctly
		$apply_autowidth = function ($autowidth) {
			if ( $autowidth == 'default' && get_option('podlove_subscribe_button_default_autowidth') !== 'on' ) {
				return '';
			} 
			if ( $autowidth !== 'default' && $autowidth !== 'on' ) {
				return '';
			}

			return ' auto';
		};

		$feeds = $this->get_feeds_as_array($this->feeds);		

		$podcast_data = array(
				'title' => $this->title,
				'subtitle' => $this->subtitle,
				'description' => $this->description,
				'cover' => $this->cover,
				'feeds' => $feeds
			);

		$button_styling = array(
				// $attribute => $value
				'language' => get_bloginfo('language'),
				'size' => ( $size == 'default' ? get_option('podlove_subscribe_button_default_size', $size) : $size )
			 	. $apply_autowidth($autowidth),
				'style' => ( $style == 'default' ? get_option('podlove_subscribe_button_default_style', $style) : $style ),
				'format' => ( $format == 'default' ? get_option('podlove_subscribe_button_default_format', $format) : $format ),
				'color' => ( $color == '#599677' ? get_option('podlove_subscribe_button_default_color', $color) : $color ),
				'json-data' => 'podcastData' . $this->id
			);

		return $this->provide_button_html($podcast_data, $button_styling);
	}
}

Button::property( 'id', 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY' );
Button::property( 'name', 'VARCHAR(255)' );
Button::property( 'title', 'VARCHAR(255)' );
Button::property( 'subtitle', 'VARCHAR(255)' );
Button::property( 'description', 'TEXT' );
Button::property( 'cover', 'VARCHAR(255)' );
Button::property( 'feeds', 'TEXT' );