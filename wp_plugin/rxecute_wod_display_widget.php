<?php

/*
Plugin Name: RXecute WOD Display
Plugin URI: http://rxecute.com/
Description: WOD Display Widget provided by RXecute.
Author: Ryan Beggs
Author URI: http://rxecute.com/
Text Domain: wpcf7
Domain Path: /languages/
Version: 1.0.0
*/

//Register the display widget
function register_rxecute_display_widget() {
    register_widget( 'rxecute_display_widget' );
}
add_action( 'widgets_init', 'register_rxecute_display_widget' );


class rxecute_display_widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'rxecute_wod_display', // Base ID
			__('RXecute WOD Display', 'text_domain'), // Name
			array( 'description' => __( 'Display todays WOD', 'text_domain' ), ) // Args
		);
	}

	//Front end display for Widget

	public function widget( $args, $instance ) {

		$uniqueBoxID = apply_filters( 'widget_title', $instance['uniqueBoxID'] );
		$title = apply_filters( 'widget_title', $instance['title'] );

		//Go out and get the data from webservice

		$url = 'http://tailored-medical.com/v1/wodDisplayWidget.php';
		$fields = array('userBoxCode' => urlencode($uniqueBoxID));
		foreach($fields as $key=>$value)
		{
			$fields_string .= $key.'='.$value.'&';
		}
		rtrim($fields_string, '&');

		$ch = curl_init();

		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POST, count($fields));
		curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);


		echo $args['before_widget'];
		if ( ! empty( $title ) && ! empty( $uniqueBoxID ))
			echo $args['before_title'] . $title . $args['after_title'];
		echo __( curl_exec($ch), 'text_domain' );
		echo $args['after_widget'];

		curl_close($ch);

	}

	//Backend widget form. Enter in Unique Box ID and Title
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) && isset( $instance[ 'uniqueBoxID' ] ))
		{
			$title = $instance[ 'title' ];
			$uniqueBoxID = $instance[ 'uniqueBoxID' ];
		}
		else {
			$title = __( 'Enter Title', 'text_domain' );
			$uniqueBoxID = __( 'Enter Box ID', 'text_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />

		<label for="<?php echo $this->get_field_id( 'uniqueBoxID' ); ?>"><?php _e( 'uniqueBoxID:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'uniqueBoxID' ); ?>" name="<?php echo $this->get_field_name( 'uniqueBoxID' ); ?>" type="text" value="<?php echo esc_attr( $uniqueBoxID ); ?>" />

		</p>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['uniqueBoxID'] = ( ! empty( $new_instance['uniqueBoxID'] ) ) ? strip_tags( $new_instance['uniqueBoxID'] ) : '';

		return $instance;
	}

} // class rxecute_display_widget

?>