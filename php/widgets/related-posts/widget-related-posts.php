<?php
/*____      _       _           _   ____           _       
 |  _ \ ___| | __ _| |_ ___  __| | |  _ \ ___  ___| |_ ___ 
 | |_) / _ \ |/ _` | __/ _ \/ _` | | |_) / _ \/ __| __/ __|
 |  _ <  __/ | (_| | ||  __/ (_| | |  __/ (_) \__ \ |_\__ \
 |_| \_\___|_|\__,_|\__\___|\__,_| |_|   \___/|___/\__|___/
                                                           
////////////////////// RELATED POSTS //////////////////////*/

class pw_related_posts_widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'pw_related_posts', 			// Base ID
			'(Postworld) Related Posts', 	// Name
			array( 'description' => __( 'Display related posts.', 'text_domain' ), ) // Args
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
		
		$OPTIONS = apply_filters( 'pw_related_posts_widget', $OPTIONS );

		////////// DRAW PAGES WIDGET //////////
		// SHOW TITLE (?)
			echo $before_widget;
			if ( !empty( $OPTIONS['title'] ) && $OPTIONS['show_title'] == 1 )
				echo $before_title . $OPTIONS['title'] . $after_title;

			////////// POST SHARE REPORT VIEW //////////
			///// RENDER PAGE WIDGET /////
			include 'related-posts-view.php';	
			
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
		$OPTIONS = apply_filters( 'pw_user_widget', $OPTIONS );

		// SANITIZE FIELDS
		$OPTIONS['title'] = strip_tags( $OPTIONS['title'] );
		$OPTIONS['show_title'] = strip_tags( $OPTIONS['show_title'] );

		// DECODE SETTINGS FROM JSON TO ARRAY
		$OPTIONS['settings'] = json_decode( $OPTIONS['settings'], true); 

		return $OPTIONS;
	}

	/**
	 * Back-end widget form.
	 * @see WP_Widget::form()
	 * @param array $OPTIONS Previously saved values from database.
	 */
	public function form( $options ) {
		$options = apply_filters( 'pw_related_posts_widget', $options );

		include 'related-posts-admin.php';

	}

} 

add_action( 'widgets_init', create_function( '', 'register_widget( "pw_related_posts_widget" );' ) );

function pw_related_posts_widget_filter( $options ){
	///// SET DEFAULT VALUES /////
	$defaultOptions = array(
			'title'			=>	'',
			'show_title'	=>	false,
			'settings'	=>	array(
				'number' 	=>	10,
				'depth'		=>	1000,
				'query'		=>	array(
					'post_type' => array('post'),
					),
				'related_by' =>	array(
					)
				),
			);

	$options = array_replace_recursive( $defaultOptions, $options);

	return $options;

}
add_filter( 'pw_related_posts_widget', 'pw_related_posts_widget_filter' );

?>