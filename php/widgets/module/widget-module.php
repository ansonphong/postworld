<?php
/*__  __           _       _       __        ___     _            _   
 |  \/  | ___   __| |_   _| | ___  \ \      / (_) __| | __ _  ___| |_ 
 | |\/| |/ _ \ / _` | | | | |/ _ \  \ \ /\ / /| |/ _` |/ _` |/ _ \ __|
 | |  | | (_) | (_| | |_| | |  __/   \ V  V / | | (_| | (_| |  __/ |_ 
 |_|  |_|\___/ \__,_|\__,_|_|\___|    \_/\_/  |_|\__,_|\__, |\___|\__|
                                                       |___/                    
/////////////////////// MODULE WIDGET - CLASS ///////////////////////*/

class pw_module_widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'pw_module_widget', 	// Base ID
			'(Postworld) Module', 	// Name
			array( 'description' => __( 'Adds a module to a widget from /templates/modules', 'text_domain' ), ) // Args
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
		$module_id = $OPTIONS['module_id'];
		$show_title = $OPTIONS['show_title'];
		
		////////// DRAW PAGES WIDGET //////////
		// SHOW TITLE (?)
			echo $before_widget;
			if ( ! empty( $title ) && $show_title == 1 )
				echo $before_title . $title . $after_title;

			////////// POST SHARE REPORT VIEW //////////
			///// RENDER PAGE WIDGET /////
			extract ($OPTIONS);
			echo '<div class="pw-module '.$module_id.'">';
			include pw_get_template( 'modules', $module_id, 'php', 'dir' );
			echo "</div>";
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
		$OPTIONS['module_id'] = $NEW_OPTIONS['module_id'];
		return $OPTIONS;
	}

	/**
	 * Back-end widget form.
	 * @see WP_Widget::form()
	 * @param array $OPTIONS Previously saved values from database.
	 */
	public function form( $OPTIONS ) {
		include 'module-admin.php';
	}

} 

add_action( 'widgets_init', create_function( '', 'register_widget( "pw_module_widget" );' ) );

?>