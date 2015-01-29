<?php
 /*   _               
 | | | |___  ___ _ __ 
 | | | / __|/ _ \ '__|
 | |_| \__ \  __/ |   
  \___/|___/\___|_|   
                      
/////// USER ///////*/

class pw_user_widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'pw_user', 					// Base ID
			'(Postworld) User', 		// Name
			array( 'description' => __( 'Display a user\'s data.', 'text_domain' ), ) // Args
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
		
		$OPTIONS = apply_filters( 'pw_user_widget', $OPTIONS );

		////////// DRAW PAGES WIDGET //////////
		// SHOW TITLE (?)
			echo $before_widget;
			if ( !empty( $OPTIONS['title'] ) && $OPTIONS['show_title'] == 1 )
				echo $before_title . $OPTIONS['title'] . $after_title;

			////////// POST SHARE REPORT VIEW //////////
			///// RENDER PAGE WIDGET /////
			include 'user-view.php';	
			
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
		return $OPTIONS;
	}

	/**
	 * Back-end widget form.
	 * @see WP_Widget::form()
	 * @param array $OPTIONS Previously saved values from database.
	 */
	public function form( $options ) {
		$options = apply_filters( 'pw_user_widget', $options );

		$defaultOptions = array(
			'title'			=>	'',
			'show_title'	=>	false,
			'user_select'	=>	'current_author',
			'user_id'		=>	0,
			);
		$options = array_replace_recursive( $defaultOptions, $options);

		$viewOptions = pw_get_templates(
			array(
				'subdirs'			=>	'user-widget',
				'path_type'			=>	'dir',
				'ext'				=>	'php',
				'output'			=>	'ids',
				)
			)['user-widget'];

		if( _get( $options, 'user_id' ) )
			$user = get_user_by( 'id', _get( $options, 'user_id' ) )->data;
		else
			$user = array();

		include 'user-admin.php';
	}

} 

add_action( 'widgets_init', create_function( '', 'register_widget( "pw_user_widget" );' ) );

function pw_user_widget_filter( $OPTIONS ){
	///// SET DEFAULT VALUES /////
	

	return $OPTIONS;

}
add_filter( 'pw_user_widget', 'pw_user_widget_filter' );


?>