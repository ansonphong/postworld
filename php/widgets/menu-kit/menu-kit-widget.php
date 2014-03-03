<?php


// ADD MENU KIT WIDGET

class menu_kit_widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'menu_kit', // Base ID
			'(Postworld) Menu Kit', // Name
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
		
		
		////////// DRAW PAGES WIDGET //////////
		
		$OPTIONS['show_title'] = apply_filters( 'widget_menu-show_title', $OPTIONS['show_title'] );
		
		// SHOW TITLE (?)
			echo $before_widget;
			if ( ! empty( $title ) && $OPTIONS['show_title'] == 1 )
				echo $before_title . $title . $after_title;
				
		if ($menu_type == 'pages') :
			
			// EXTRACT SETTINGS FROM ADMIN & COMPRESS SETTINGS INTO ARRAY
			$OPTIONS['show_parent_pages'] = apply_filters( 'widget_menu-show_parent_pages', $OPTIONS['show_parent_pages'] );
			$OPTIONS['show_sibling_pages'] = apply_filters( 'widget_menu-show_sibling_pages', $OPTIONS['show_sibling_pages'] );
			$OPTIONS['show_child_pages'] = apply_filters( 'widget_menu-show_child_pages', $OPTIONS['show_child_pages'] );
			

			// RENDER MENU KIT PAGES
			menu_kit_pages($OPTIONS);
			
		endif;
		
		
		////////// DRAW CATEGORIES WIDGET //////////
		
		if ($menu_type == 'categories') :
			
			// EXTRACT SETTINGS FROM ADMIN & COMPRESS SETTINGS INTO ARRAY
			
			$TAXONOMY = apply_filters( 'widget_menu-taxonomy', $OPTIONS['taxonomy'] );
			$POST_TYPE = apply_filters( 'widget_menu-post_type', $OPTIONS['post_type'] );
		
			$TAXONOMY_LAYOUT = apply_filters( 'widget_menu-taxonomy_layout', $OPTIONS['taxonomy_layout'] );
			
			$TAXONOMY_HIERARCHICAL = apply_filters( 'widget_menu-taxonomy_hierarchical', $OPTIONS['taxonomy_hierarchical'] );
			$TAXONOMY_HIDE_EMPTY = apply_filters( 'widget_menu-taxonomy_hide_empty', $OPTIONS['taxonomy_hide_empty'] );
			
			$CONTAINER = "menu-taxonomy-".$TAXONOMY_LAYOUT;
			
			$OPTIONS = array(
				'POST_TYPE'		=> $POST_TYPE,
				'TAXONOMY'  	=> $TAXONOMY,
				'CONTAINER' 	=> '#'.$CONTAINER,
				'ITEM_CLASS'	=> 'menu-item',
				'POST_STATUS'	=> 'publish',
				'HIDE_EMPTY'	=> $TAXONOMY_HIDE_EMPTY,
				'HIERARCHICAL' 	=> $TAXONOMY_HIERARCHICAL,
				'DEPTH' 		=> 5,
				'SHOW_COUNT'	=> 0,
				'EXCLUDE_'		=> null,
				'NUMBER'		=> null,
				'TITLE'			=> '',
				'STYLE'			=> 'list',
				'LAYOUT'		=> $TAXONOMY_LAYOUT
			);
			
			
			// RENDER MENU KIT PAGES
			echo "<div id='".$CONTAINER."'>";
			menu_kit_categories($OPTIONS);
			echo "</div>";
		
		endif;
		
		////////// DRAW CATEGORIES WIDGET //////////
		
		if ($menu_type == 'authors') :
		
			$AUTHORS_HIDE_EMPTY =	apply_filters( 'widget_menu-authors_hide_empty', $OPTIONS['authors_hide_empty'] );
			$AUTHORS_SHOW_ADMINS = 	apply_filters( 'widget_menu-authors_show_admins', $OPTIONS['authors_show_admins'] );
			$AUTHORS_AVATAR_SIZE = 	apply_filters( 'widget_menu-authors_avatar_size', $OPTIONS['authors_avatar_size'] );
			$AUTHORS_ROLE = 		apply_filters( 'widget_menu-authors_role', $OPTIONS['authors_role'] );
			$AUTHORS_ORDER_BY = 	apply_filters( 'widget_menu-authors_order_by', $OPTIONS['authors_order_by'] );
			$AUTHORS_ORDER = 	apply_filters( 'widget_menu-authors_order', $OPTIONS['authors_order'] );
		
			$OPTIONS = array(
				'AUTHORS_HIDE_EMPTY' => $AUTHORS_HIDE_EMPTY,
				'AUTHORS_SHOW_ADMINS' => $AUTHORS_SHOW_ADMINS,
				'AUTHORS_AVATAR_SIZE' => $AUTHORS_AVATAR_SIZE,
				'AUTHORS_ROLE' => $AUTHORS_ROLE,
				'AUTHORS_ORDER_BY' => $AUTHORS_ORDER_BY,
				'AUTHORS_ORDER' => $AUTHORS_ORDER,
				);
		
		
			menu_kit_authors($OPTIONS);
			
		endif;
		
		
		
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
		
		$OPTIONS['menu_type'] = $NEW_OPTIONS['menu_type'];
		
		// PAGES SETTINGS : SAVE
		$OPTIONS['show_parent_pages'] = $NEW_OPTIONS['show_parent_pages'];
		$OPTIONS['show_sibling_pages'] = $NEW_OPTIONS['show_sibling_pages'];
		$OPTIONS['show_child_pages'] = $NEW_OPTIONS['show_child_pages'];
		
		// TAXONOMY SETTINGS : SAVE
		$OPTIONS['taxonomy'] = $NEW_OPTIONS['taxonomy'];
		$OPTIONS['post_type'] = $NEW_OPTIONS['post_type'];
		$OPTIONS['taxonomy_layout'] = $NEW_OPTIONS['taxonomy_layout'];
		$OPTIONS['taxonomy_hierarchical'] = $NEW_OPTIONS['taxonomy_hierarchical'];
		$OPTIONS['taxonomy_hide_empty'] = $NEW_OPTIONS['taxonomy_hide_empty'];
		
		// AUTHOR SETTINGS : SAVE
		$OPTIONS['authors_hide_empty'] = $NEW_OPTIONS['authors_hide_empty'];
		$OPTIONS['authors_show_admins'] = $NEW_OPTIONS['authors_show_admins'];
		$OPTIONS['authors_avatar_size'] = $NEW_OPTIONS['authors_avatar_size'];
		$OPTIONS['authors_role'] = $NEW_OPTIONS['authors_role'];
		$OPTIONS['authors_order_by'] = $NEW_OPTIONS['authors_order_by'];
		$OPTIONS['authors_order'] = $NEW_OPTIONS['authors_order'];

		// MENUS SETTINGS : SAVE
		$OPTIONS['menu_slug'] = $NEW_OPTIONS['menu_slug'];
		
		
		return $OPTIONS;
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