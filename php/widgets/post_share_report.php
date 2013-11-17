<?php
/*
  ____           _     ____  _                      ____                       _   
 |  _ \ ___  ___| |_  / ___|| |__   __ _ _ __ ___  |  _ \ ___ _ __   ___  _ __| |_ 
 | |_) / _ \/ __| __| \___ \| '_ \ / _` | '__/ _ \ | |_) / _ \ '_ \ / _ \| '__| __|
 |  __/ (_) \__ \ |_   ___) | | | | (_| | | |  __/ |  _ <  __/ |_) | (_) | |  | |_ 
 |_|   \___/|___/\__| |____/|_| |_|\__,_|_|  \___| |_| \_\___| .__/ \___/|_|   \__|
                                                             |_|                   
/////////////////////////////// POST SHARE REPORT ///////////////////////////////*/

class post_share_report extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'pw_post_share_report', // Base ID
			'(Postworld) Post Share Report', // Name
			array( 'description' => __( 'Post Share Report', 'text_domain' ), ) // Args
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
		$title = apply_filters( 'widget_menu-title', $OPTIONS['title'] );
		$menu_type = apply_filters( 'widget_menu-type', $OPTIONS['menu_type'] );
		
		////////// DRAW PAGES WIDGET //////////
		$OPTIONS['show_title'] = apply_filters( 'widget_menu-show_title', $OPTIONS['show_title'] );
		// SHOW TITLE (?)
			echo $before_widget;
			if ( ! empty( $title ) && $OPTIONS['show_title'] == 1 )
				echo $before_title . $title . $after_title;
			////////// POST SHARE REPORT VIEW //////////
			extract ($OPTIONS);
			$post_id = $GLOBALS['post']->ID; //get_the_ID(); //$post->ID;
			include 'post_share_report-view.php';
			
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

		return $OPTIONS;
	}

	/**
	 * Back-end widget form.
	 * @see WP_Widget::form()
	 * @param array $OPTIONS Previously saved values from database.
	 */
	public function form( $OPTIONS ) {
		include 'post_share_report-admin.php';
	}

} 

add_action( 'widgets_init', create_function( '', 'register_widget( "post_share_report" );' ) );

?>