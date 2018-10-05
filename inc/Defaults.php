<?php
/**
 * @author    Podlove <podlove@podlove.org>
 * @copyright Copyright (c) 2014-2018, Podlove
 * @license   https://github.com/podlove/podlove-subscribe-button-wp-plugin/blob/master/LICENSE MIT
 * @package   Podlove\PodloveSubscribeButton
 */

namespace PodloveSubscribeButton;

class Defaults {

	static function button( $property ) {

		$style = array(
			'filled'    => __( 'Filled', 'podlove-subscribe-button' ),
			'outline'   => __( 'Outline', 'podlove-subscribe-button' ),
			'frameless' => __( 'Frameless', 'podlove-subscribe-button' ),
		);

		$format = array(
			'rectangle' => __( 'Rectangle', 'podlove-subscribe-button' ),
			'square'    => __( 'Square', 'podlove-subscribe-button' ),
			'cover'     => __( 'Cover', 'podlove-subscribe-button' ),
		);

		$autowidth = array(
			'on'  => __( 'Yes', 'podlove-subscribe-button' ),
			'off' => __( 'No', 'podlove-subscribe-button' ),
		);

		$size = array(
			'small'  => __( 'Small', 'podlove-subscribe-button' ),
			'medium' => __( 'Medium', 'podlove-subscribe-button' ),
			'big'    => __( 'Big', 'podlove-subscribe-button' ),
		);

		$language = array( 'de', 'en', 'eo', 'fi', 'fr', 'nl', 'zh', 'ja', );

		return $$property;

	}

	/**
	 * @return array
	 */
	public static function media_types() {

		$media_types = array(
			0 => array(
				'title'     => 'MP3 audio',
				'mime_type' => 'audio/mpeg',
				'extension' => 'mp3',
			),
			1 => array(
				'title'     => 'MPEG-4 AAC Audio',
				'mime_type' => 'audio/mp4',
				'extension' => 'aac',
			),
			2 => array(
				'title'     => 'MPEG-4 ALAC Audio',
				'mime_type' => 'audio/mp4',
				'extension' => 'aac',
			),
			3 => array(
				'title'     => 'Ogg Vorbis Audio',
				'mime_type' => 'audio/ogg',
				'extension' => 'ogg',
			),
			4 => array(
				'title'     => 'WebM Audio',
				'mime_type' => 'audio/webm',
				'extension' => 'webm',
			),
			5 => array(
				'title'     => 'FLAC Audio',
				'mime_type' => 'audio/flac',
				'extension' => 'flac',
			),
			6 => array(
				'title'     => 'Matroska Audio',
				'mime_type' => 'audio/x-matroska',
				'extension' => 'mka',
			),
			7 => array(
				'title'     => 'Opus Audio',
				'mime_type' => 'audio/opus',
				'extension' => 'opus',
			)
		);

		return apply_filters( 'podlove_psb_defaults_media_types', $media_types );

	}

	static function options_install() {

		$options = array(
			'size'      => 'big',
			'autowidth' => 'on',
			'color'     => '#599677',
			'style'     => 'filled',
			'format'    => 'rectangle',
			'language'  => \PodloveSubscribeButton\Helpers::language( get_site_option( 'WPLANG' ) ),
		);

		return $options;

	}

	static function options() {

		$options = array(
			'size'      => 'big',
			'autowidth' => 'on',
			'color'     => '#599677',
			'style'     => 'filled',
			'format'    => 'rectangle',
			'language'  => \PodloveSubscribeButton\Helpers::language( '' ),
		);

		return apply_filters( 'podlove_psb_defaults_options', $options );

	}

} // END class
