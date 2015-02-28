<?php
/*_____             _  __        ___     _            _   
 |  ___|__  ___  __| | \ \      / (_) __| | __ _  ___| |_ 
 | |_ / _ \/ _ \/ _` |  \ \ /\ / /| |/ _` |/ _` |/ _ \ __|
 |  _|  __/  __/ (_| |   \ V  V / | | (_| | (_| |  __/ |_ 
 |_|  \___|\___|\__,_|    \_/\_/  |_|\__,_|\__, |\___|\__|
                                           |___/          
////////////////////// FEED WIDGET //////////////////////*/

class pw_feed_widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'pw_feed', 				// Base ID
			'(Postworld) Feed', 	// Name
			array( 'description' => __( 'Adds a feed from the Postworld feeds', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $OPTIONS Saved values from database.
	 */
	public function widget( $args, $OPTIONS ) {
		extract( $args );
		
		// PULL IN DATA
		$title = $OPTIONS['title'];
		$feed_id = $OPTIONS['feed_id'];
		$show_title = $OPTIONS['show_title'];
		
		$output = '';

		////////// DRAW WIDGET //////////
		// SHOW TITLE (?)
		$output .= $before_widget;
		if ( ! empty( $title ) && $show_title == 1 )
			$output .= $before_title . $title . $after_title;

			///// RENDER FEED WIDGET /////
			ob_start();

			extract ($OPTIONS);
			include 'feed-view.php';
			$feed = ob_get_contents();
			ob_end_clean();
			
			$output .= $feed;

		// CLOSE
		$output .= $after_widget;

		if( !empty( $feed ) )
			echo $output;
		
	}

	/**
	 * Sanitize widget form values as they are saved.
	 * @see WP_Widget::update()
	 * @param array $NEW_OPTIONS Values just sent to be saved.
	 * @param array $OLD_OPTIONS Previously saved values from database.
	 * @return array Updated safe values to be saved.
	 */
	public function update( $NEW_OPTIONS, $OLD_OPTIONS ) {
		$OPTIONS = array();
		
		// GLOBAL SETTINGS : SAVE
		$OPTIONS['title'] = strip_tags( $NEW_OPTIONS['title'] );
		$OPTIONS['show_title'] = strip_tags( $NEW_OPTIONS['show_title'] );
		$OPTIONS['feed_id'] = $NEW_OPTIONS['feed_id'];
		return $OPTIONS;
	}

	/**
	 * Back-end widget form.
	 * @see WP_Widget::form()
	 * @param array $OPTIONS Previously saved values from database.
	 */
	public function form( $OPTIONS ) {
		include 'feed-admin.php';
	}

} 

add_action( 'widgets_init', create_function( '', 'register_widget( "pw_feed_widget" );' ) );

?>