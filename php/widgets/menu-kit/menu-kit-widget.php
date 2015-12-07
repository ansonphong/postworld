<?php
// ADD MENU KIT WIDGET

class menu_kit_widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		$prefix = pw_get_widget_prefix();
		parent::__construct(
	 		'pw_menu_kit', // Base ID
			$prefix.' Menu Kit', // Name
			array( 'description' => __( 'Menu Kit Widget', 'text_domain' ), ) // Args
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
		
		$OPTIONS['show_title'] = apply_filters( 'widget_menu-show_title', $OPTIONS['show_title'] );
		
		echo $before_widget;
		if ( ! empty( $title ) && $OPTIONS['show_title'] == 1 )
			echo $before_title . $title . $after_title;

		////////// INCLUDE WIDGET TEMPLATE //////////
		switch( $menu_type ){
			case "pages":
				include "templates/menu-pages.php";
				break;
			case "categories":
				include "templates/menu-terms.php";
				break;
			case "authors":
				include "templates/menu-authors.php";
				break;
			case "custom_menu":
				include "templates/menu-custom.php";
				break;
			case "menu_feed":
				include "templates/menu-feed.php";
				break;
		}
		
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
		return $NEW_OPTIONS;
	}

	/**
	 * Back-end widget form.
	 * @see WP_Widget::form()
	 * @param array $OPTIONS Previously saved values from database.
	 */
	public function form( $OPTIONS ) {
		include 'menu-kit-widget-admin.php';
	}

} // class menu_kit_widget


// register menu_kit_widget widget
add_action( 'widgets_init', create_function( '', 'register_widget( "menu_kit_widget" );' ) );



?>