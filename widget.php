<?php

namespace PodloveSubscribeButton;

class Podlove_Subscribe_Button_Widget extends \WP_Widget {

	public function __construct() {
		parent::__construct(
					'podlove_subscribe_button_widget',
					'Podlove Subscribe Button',
					array( 'description' => __( 'Adds a Podlove Subscribe Button to your Sidebar', 'podlove' ), )
				);
	}

	public function widget( $args, $instance ) {
		$button = \PodloveSubscribeButton\Model\Button::find_by_id($instance['button']);

		echo $args['before_widget'];
		echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];

		echo $button->render($instance['style'], $instance['autowidth']);
		
		if ( strlen($instance['infotext']) )
			echo wpautop($instance['infotext']);

		echo $args['after_widget'];
	}	

	public function form( $instance ) {
		$title     = isset( $instance[ 'title' ] )     ? $instance[ 'title' ]      : '';
		$button    = isset( $instance[ 'button' ] )    ? $instance[ 'button' ]     : '';
		$style     = isset( $instance[ 'style' ] )     ? $instance[ 'style' ]      : '';
		$autowidth = isset( $instance[ 'autowidth' ] ) ? $instance[ 'autowidth' ]  : 0;
		$infotext  = isset( $instance[ 'infotext' ] )  ? $instance[ 'infotext' ]   : '';
		$buttons = \PodloveSubscribeButton\Model\Button::all();
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'podlove' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>" />

			<label for="<?php echo $this->get_field_id( 'button' ); ?>"><?php _e( 'Button', 'podlove' ); ?></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'button' ); ?>"
				      name="<?php echo $this->get_field_name( 'button' ); ?>">
				<?php
					foreach ($buttons as $subscribebutton) {
						echo "<option value='".$subscribebutton->id."' ".( $subscribebutton->id == $button ? 'selected=\"selected\"' : '' )." >".$subscribebutton->title." (".$subscribebutton->name.")</option>";
					}
				?>
			</select>

			<label for="<?php echo $this->get_field_id( 'style' ); ?>"><?php _e( 'Style', 'podlove' ); ?></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'style' ); ?>" name="<?php echo $this->get_field_name( 'style' ); ?>">
				<option value="default"      <?php echo ( $style == 'default'    ? 'selected=\"selected\"' : '' ); ?>><?php _e( 'Default Style', 'podlove' ) ?></option>
				<optgroup>
					<option value="small"    <?php echo ( $style == 'small'    ? 'selected=\"selected\"' : '' ); ?>><?php _e( 'Small', 'podlove' ) ?></option>
					<option value="medium"   <?php echo ( $style == 'medium'   ? 'selected=\"selected\"' : '' ); ?>><?php _e( 'medium', 'podlove' ) ?></option>
					<option value="big"      <?php echo ( $style == 'big'      ? 'selected=\"selected\"' : '' ); ?>><?php _e( 'Big', 'podlove' ) ?></option>
					<option value="big-logo" <?php echo ( $style == 'big-logo' ? 'selected=\"selected\"' : '' ); ?>><?php _e( 'Big with logo', 'podlove' ) ?></option>
				</optgroup>
			</select>

			<label for="<?php echo $this->get_field_id( 'autowidth' ); ?>"><?php _e( 'Autowidth', 'podlove' ); ?></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'autowidth' ); ?>" name="<?php echo $this->get_field_name( 'autowidth' ); ?>">
				<option value="default"      <?php echo ( $autowidth == 'default'    ? 'selected=\"selected\"' : '' ); ?>><?php _e( 'Default Autowidth', 'podlove' ) ?></option>
				<optgroup>
					<option value="on"    <?php echo ( $autowidth == 'on'    ? 'selected=\"selected\"' : '' ); ?>><?php _e( 'Yes', 'podlove' ) ?></option>
					<option value=""   <?php echo ( $autowidth == ''   ? 'selected=\"selected\"' : '' ); ?>><?php _e( 'No', 'podlove' ) ?></option>
				</optgroup>
			</select>
		
			<label for="<?php echo $this->get_field_id( 'infotext' ); ?>"><?php _e( 'Description', 'podlove' ); ?></label> 
			<textarea class="widefat" rows="10" id="<?php echo $this->get_field_id( 'infotext' ); ?>" name="<?php echo $this->get_field_name( 'infotext' ); ?>"><?php echo $infotext; ?></textarea>
		</p>
		<?php 
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['infotext']  = ( ! empty( $new_instance['infotext'] ) )  ? $new_instance['infotext']                : '';
		$instance['title']     = ( ! empty( $new_instance['title'] ) )     ? strip_tags( $new_instance['title'] )     : '';
		$instance['style']     = ( ! empty( $new_instance['style'] ) )     ? strip_tags( $new_instance['style'] )     : '';
		$instance['autowidth'] = ( ! empty( $new_instance['autowidth'] ) ) ? strip_tags( $new_instance['autowidth'] ) : 0;
		$instance['button']    = ( ! empty( $new_instance['button'] ) )    ? strip_tags( $new_instance['button'] )    : '';
		return $instance;
	}
}
add_action( 'widgets_init', function(){
     register_widget( '\PodloveSubscribeButton\Podlove_Subscribe_Button_Widget' );
});