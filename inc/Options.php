<?php
/**
 * @author    Podlove <podlove@podlove.org>
 * @copyright Copyright (c) 2014-2018, Podlove
 * @license   https://github.com/podlove/podlove-subscribe-button-wp-plugin/blob/master/LICENSE MIT
 * @package   Podlove\PodloveSubscribeButton
 */

namespace PodloveSubscribeButton;

class Options {

	public static function register_settings() {

		$args = array(
			'description'       => '',
			'sanitize_callback' => array( __CLASS__, 'sanitize_settings' ),
		);

		register_setting(
			'podlove-psb',
			'podlove_psb_defaults',
			$args
		);

		add_settings_section(
			'podlove-psb-defaults',
			__( 'Default Settings', 'podlove-subscribe-button' ),
			'__return_empty_string',
			'podlove-psb'
		);

		add_settings_field(
			'size',
			__( 'Size', 'podlove-subscribe-button' ),
			array( __CLASS__, 'field' ),
			'podlove-psb',
			'podlove-psb-defaults',
			array(
				'label_for' => 'size',
				'type'      => 'select',
			)
		);

		add_settings_field(
			'autowidth',
			__( 'Autowidth', 'podlove-subscribe-button' ),
			array( __CLASS__, 'field' ),
			'podlove-psb',
			'podlove-psb-defaults',
			array(
				'label_for' => 'autowidth',
				'type'      => 'checkbox',
			)
		);

		add_settings_field(
			'color',
			__( 'Color', 'podlove-subscribe-button' ),
			array( __CLASS__, 'field' ),
			'podlove-psb',
			'podlove-psb-defaults',
			array(
				'label_for' => 'color',
				'type'      => 'color',
			)
		);

		add_settings_field(
			'style',
			__( 'Style', 'podlove-subscribe-button' ),
			array( __CLASS__, 'field' ),
			'podlove-psb',
			'podlove-psb-defaults',
			array(
				'label_for' => 'style',
				'type'      => 'select',
			)
		);

		add_settings_field(
			'format',
			__( 'Format', 'podlove-subscribe-button' ),
			array( __CLASS__, 'field' ),
			'podlove-psb',
			'podlove-psb-defaults',
			array(
				'label_for' => 'format',
				'type'      => 'select',
			)
		);

		add_settings_field(
			'language',
			__( 'Language', 'podlove-subscribe-button' ),
			array( __CLASS__, 'field' ),
			'podlove-psb',
			'podlove-psb-defaults',
			array(
				'label_for' => 'language',
				'type'      => 'language',
			)
		);

	}

	public static function field( $args ) {

		if ( is_network_admin() ) {
			$option = get_site_option( 'podlove_psb_defaults' );
		} else {
			$option = get_option( 'podlove_psb_defaults' );
		}

		switch ( $args['type'] ) {
			case 'checkbox':
				?>
                <input type="checkbox" name="<?php echo "podlove_psb_defaults[{$args['label_for']}]"; ?>"
                       id="<?php echo "podlove_psb_defaults[{$args['label_for']}]"; ?>" <?php checked( $option[ $args['label_for'] ], 'on' ); ?> />
				<?php
				break;

			case 'color':
				?>
                <input id="podlove_psb_defaults[color]" name="podlove_psb_defaults[color]" class="podlove_subscribe_button_color"
                       value="<?php echo $option['color'] ?>"/>
				<?php
				break;

			case 'language':
				?>
                <select name="podlove_psb_defaults[language]" id="podlove_psb_defaults[language]">
					<?php foreach ( Defaults::button( 'language' ) as $value ) : ?>
                        <option value="<?php echo $value; ?>" <?php selected( $option['language'], $value ); ?>><?php echo $value; ?></option>
					<?php endforeach; ?>
                </select>
				<?php
				break;


			case 'select':
				?>
                <select name="podlove_psb_defaults[<?php echo $args['label_for']; ?>]" id="podlove_psb_defaults[<?php echo $args['label_for']; ?>]">
					<?php foreach ( Defaults::button( $args['label_for'] ) as $value => $description ) { ?>
                        <option value="<?php echo $value; ?>" <?php selected( $option[ $args['label_for'] ], $value ); ?>><?php echo $description; ?></option>
					<?php } ?>
                </select>
				<?php break;

		}
	}

	public static function sanitize_settings( $input = null ) {

	    $output = $input;

	    if ( ! array_key_exists('autowidth', $input ) ) {
		    $output['autowidth'] = 'off';
        }

		return $output;
	}

} // END class
