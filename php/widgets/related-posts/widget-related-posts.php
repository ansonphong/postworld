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
		$prefix = pw_get_widget_prefix();
		parent::__construct(
	 		'pw_related_posts', 			// Base ID
			$prefix.' Related Posts', 	// Name
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
	public function widget( $args, $options ) {
		extract( $args );
		
		$options = apply_filters( 'pw_related_posts_widget', $options );

		////////// DRAW PAGES WIDGET //////////
		// SHOW TITLE (?)
			echo $before_widget;
			if ( !empty( $options['title'] ) && $options['show_title'] == 1 )
				echo $before_title . $options['title'] . $after_title;

			////////// POST SHARE REPORT VIEW //////////
			///// RENDER PAGE WIDGET /////
			include 'related-posts-view.php';	
			
		// CLOSE
		echo $after_widget;
		
	}

	/**
	 * Sanitize widget form values as they are saved.
	 * @see WP_Widget::update()
	 * @param array $options Values just sent to be saved.
	 * @param array $old_options Previously saved values from database.
	 * @return array Updated safe values to be saved.
	 */
	public function update( $options, $old_options ) {
		$options = apply_filters( 'pw_user_widget', $options );

		// SANITIZE FIELDS
		$options['title'] = strip_tags( $options['title'] );
		$options['show_title'] = strip_tags( $options['show_title'] );

		// DECODE SETTINGS FROM JSON TO ARRAY
		$options['settings'] = json_decode( $options['settings'], true); 

		return $options;
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
				'depth'		=>	0,
				'view'		=>	'list',
				'query'		=>	array(
					'post_type' 	=> 	array('post'),
					'post_status'	=>	'publish',
					'date_from' => array(
						'after_ago' => array(
							'multiplier' => 1,
							'period' => 'year'
							),
						),
					),
				'related_by' =>	array(
					)
				),
			);
	$options = array_replace_recursive( $defaultOptions, $options);
	
	$options = apply_filters( 'pw_related_posts_widget_options', $options );

	return $options;

}
add_filter( 'pw_related_posts_widget', 'pw_related_posts_widget_filter' );

?>