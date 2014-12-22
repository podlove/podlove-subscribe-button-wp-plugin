<?php
namespace PodloveSubscribeButton\Model;

class Button extends Base {

	public function render( $style='medium', $autowidth='on' ) {
		$feeds = array();
		foreach ($this->feeds as $feed) {
			$feeds[] = array(
					'type' => 'audio',
					'format' => \PodloveSubscribeButton\MediaTypes::$audio[$feed['format']]['extension'],
					'url' => $feed['url'],
					'variant' => 'high'
				);
		}

		$podcast_data = array(
				'title' => $this->title,
				'subtitle' => $this->subtitle,
				'description' => $this->description,
				'cover' => $this->cover,
				'feeds' => $feeds
			);

		return"
			<script>
				podcastData".$this->id." =".json_encode($podcast_data)."
			</script>
			<script class=\"podlove-subscribe-button\" src=\"http://docs.podlove.org/podlove-subscribe-button/dist/javascripts/app.js\"
			 data-language=\"".get_bloginfo('language')."\" data-size=\"".$style.( $autowidth === 'on' ? ' auto' : '' )."\" data-json-data=\"podcastData".$this->id."\"></script>
		";
	}

}

Button::property( 'id', 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY' );
Button::property( 'name', 'VARCHAR(255)' );
Button::property( 'title', 'VARCHAR(255)' );
Button::property( 'subtitle', 'VARCHAR(255)' );
Button::property( 'description', 'TEXT' );
Button::property( 'cover', 'VARCHAR(255)' );
Button::property( 'feeds', 'TEXT' );