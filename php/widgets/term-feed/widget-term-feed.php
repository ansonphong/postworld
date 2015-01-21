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
		
		$OPTIONS = apply_filters( 'pw_term_feed_widget', $OPTIONS );

		////////// DRAW PAGES WIDGET //////////
		// SHOW TITLE (?)
			echo $before_widget;
			if ( !empty( $OPTIONS['title'] ) && $OPTIONS['show_title'] == 1 )
				echo $before_title . $OPTIONS['title'] . $after_title;

			////////// POST SHARE REPORT VIEW //////////
			///// RENDER PAGE WIDGET /////
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
	public function update( $OPTIONS, $OLD_OPTIONS ) {
		$OPTIONS = apply_filters( 'pw_term_feed_widget', $OPTIONS );

		// SANITIZE FIELDS
		$OPTIONS['title'] = strip_tags( $OPTIONS['title'] );
		$OPTIONS['show_title'] = strip_tags( $OPTIONS['show_title'] );
		return $OPTIONS;
	}

	/**
	 * Back-end widget form.
	 * @see WP_Widget::form()
	 * @param array $OPTIONS Previously saved values from database.
	 */
	public function form( $OPTIONS ) {
		$OPTIONS = apply_filters( 'pw_term_feed_widget', $OPTIONS );
		include 'term-feed-admin.php';
	}

} 

add_action( 'widgets_init', create_function( '', 'register_widget( "pw_term_feed_widget" );' ) );

function pw_term_feed_widget_filter( $OPTIONS ){
	///// SET DEFAULTS /////
	if( empty( $OPTIONS['terms_number'] ) )
		$OPTIONS['terms_number'] = 20;
	if( empty( $OPTIONS['terms_order'] ) )
		$OPTIONS['terms_order'] = 'DESC';
	if( empty( $OPTIONS['terms_orderby'] ) )
		$OPTIONS['terms_orderby'] = 'count';
	if( empty( $OPTIONS['template_id'] ) )
		$OPTIONS['template_id'] = 'term-feed-default';

	return $OPTIONS;

}
add_filter( 'pw_term_feed_widget', 'pw_term_feed_widget_filter' );


?>