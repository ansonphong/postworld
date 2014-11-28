<?php
/*_____                     _____             _ 
 |_   _|__ _ __ _ __ ___   |  ___|__  ___  __| |
   | |/ _ \ '__| '_ ` _ \  | |_ / _ \/ _ \/ _` |
   | |  __/ |  | | | | | | |  _|  __/  __/ (_| |
   |_|\___|_|  |_| |_| |_| |_|  \___|\___|\__,_|
                                                
/////////////// TERM FEED - VIEW ///////////////*/

class pw_term_feed_widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'pw_term_feed', 				// Base ID
			'(Postworld) Term Feed', 		// Name
			array( 'description' => __( 'A feed of taxonomy terms', 'text_domain' ), ) // Args
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
		$show_title = $OPTIONS['show_title'];

		$taxonomy = $OPTIONS['taxonomy'];
		$template_id = $OPTIONS['template_id'];

		////////// DRAW PAGES WIDGET //////////
		// SHOW TITLE (?)
			echo $before_widget;
			if ( ! empty( $title ) && $show_title == 1 )
				echo $before_title . $title . $after_title;

			////////// POST SHARE REPORT VIEW //////////
			///// RENDER PAGE WIDGET /////
			extract ($OPTIONS);
			include 'term-feed-view.php';	
			
		// CLOSE
		echo $after_widget;
		
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
		
		$OPTIONS['taxonomy'] = $NEW_OPTIONS['taxonomy'];
		$OPTIONS['template_id'] = $NEW_OPTIONS['template_id'];

		return $OPTIONS;
	}

	/**
	 * Back-end widget form.
	 * @see WP_Widget::form()
	 * @param array $OPTIONS Previously saved values from database.
	 */
	public function form( $OPTIONS ) {
		include 'term-feed-admin.php';
	}

} 

add_action( 'widgets_init', create_function( '', 'register_widget( "pw_term_feed_widget" );' ) );

?>