<?php
/*____  _     _      _                    
 / ___|(_) __| | ___| |__   __ _ _ __ ___ 
 \___ \| |/ _` |/ _ \ '_ \ / _` | '__/ __|
  ___) | | (_| |  __/ |_) | (_| | |  \__ \
 |____/|_|\__,_|\___|_.__/ \__,_|_|  |___/
                                          
//////////////// SIDEBARS ////////////////*/

include 'widgets/module/widget-module.php';
include 'widgets/menu-kit/widget-menu-kit.php';
include 'widgets/feed/widget-feed.php';
include 'widgets/term-feed/widget-term-feed.php';

///// POSTWORLD GET SIDEBAR /////
// This is a slight remix of the WP code dynamic_sidebar function
// The main difference is near the end, rather than simply calling the sidebar functions
// Output Buffering is used to capture the HTML output by the sidebar function
// And the HTML for the sidebars is returned in an array.
// This allows sidebars to be

function pw_get_sidebar($index = 1) {
	global $wp_registered_sidebars, $wp_registered_widgets;

	if ( is_int($index) ) {
		$index = "sidebar-$index";
	} else {
		$index = sanitize_title($index);
		foreach ( (array) $wp_registered_sidebars as $key => $value ) {
			if ( sanitize_title($value['name']) == $index ) {
				$index = $key;
				break;
			}
		}
	}

	$sidebars_widgets = wp_get_sidebars_widgets();
	if ( empty( $wp_registered_sidebars[ $index ] ) || empty( $sidebars_widgets[ $index ] ) || ! is_array( $sidebars_widgets[ $index ] ) ) {
		/** This action is documented in wp-includes/widgets.php */
		do_action( 'dynamic_sidebar_before', $index, false );
		/** This action is documented in wp-includes/widgets.php */
		do_action( 'dynamic_sidebar_after',  $index, false );
		/** This filter is documented in wp-includes/widgets.php */
		return apply_filters( 'dynamic_sidebar_has_widgets', false, $index );
	}

	/**
	 * Fires before widgets are rendered in a dynamic sidebar.
	 *
	 * Note: The action also fires for empty sidebars, and on both the front-end
	 * and back-end, including the Inactive Widgets sidebar on the Widgets screen.
	 *
	 * @since 3.9.0
	 *
	 * @param int|string $index       Index, name, or ID of the dynamic sidebar.
	 * @param bool       $has_widgets Whether the sidebar is populated with widgets.
	 *                                Default true.
	 */
	do_action( 'dynamic_sidebar_before', $index, true );
	$sidebar = $wp_registered_sidebars[$index];



	$did_one = false;
	foreach ( (array) $sidebars_widgets[$index] as $id ) {

		if ( !isset($wp_registered_widgets[$id]) ) continue;

		$params = array_merge(
			array( array_merge( $sidebar, array('widget_id' => $id, 'widget_name' => $wp_registered_widgets[$id]['name']) ) ),
			(array) $wp_registered_widgets[$id]['params']
		);

		// Substitute HTML id and class attributes into before_widget
		$classname_ = '';
		foreach ( (array) $wp_registered_widgets[$id]['classname'] as $cn ) {
			if ( is_string($cn) )
				$classname_ .= '_' . $cn;
			elseif ( is_object($cn) )
				$classname_ .= '_' . get_class($cn);
		}
		$classname_ = ltrim($classname_, '_');
		$params[0]['before_widget'] = sprintf($params[0]['before_widget'], $id, $classname_);

		/**
		 * Filter the parameters passed to a widget's display callback.
		 *
		 * Note: The filter is evaluated on both the front-end and back-end,
		 * including for the Inactive Widgets sidebar on the Widgets screen.
		 *
		 * @since 2.5.0
		 *
		 * @see register_sidebar()
		 *
		 * @param array $params {
		 *     @type array $args  {
		 *         An array of widget display arguments.
		 *
		 *         @type string $name          Name of the sidebar the widget is assigned to.
		 *         @type string $id            ID of the sidebar the widget is assigned to.
		 *         @type string $description   The sidebar description.
		 *         @type string $class         CSS class applied to the sidebar container.
		 *         @type string $before_widget HTML markup to prepend to each widget in the sidebar.
		 *         @type string $after_widget  HTML markup to append to each widget in the sidebar.
		 *         @type string $before_title  HTML markup to prepend to the widget title when displayed.
		 *         @type string $after_title   HTML markup to append to the widget title when displayed.
		 *         @type string $widget_id     ID of the widget.
		 *         @type string $widget_name   Name of the widget.
		 *     }
		 *     @type array $widget_args {
		 *         An array of multi-widget arguments.
		 *
		 *         @type int $number Number increment used for multiples of the same widget.
		 *     }
		 * }
		 */
		$params = apply_filters( 'dynamic_sidebar_params', $params );

		$callback = $wp_registered_widgets[$id]['callback'];

		/**
		 * Fires before a widget's display callback is called.
		 *
		 * Note: The action fires on both the front-end and back-end, including
		 * for widgets in the Inactive Widgets sidebar on the Widgets screen.
		 *
		 * The action is not fired for empty sidebars.
		 *
		 * @since 3.0.0
		 *
		 * @param array $widget_id {
		 *     An associative array of widget arguments.
		 *
		 *     @type string $name                Name of the widget.
		 *     @type string $id                  Widget ID.
		 *     @type array|callback $callback    When the hook is fired on the front-end, $callback is an array
		 *                                       containing the widget object. Fired on the back-end, $callback
		 *                                       is 'wp_widget_control', see $_callback.
		 *     @type array          $params      An associative array of multi-widget arguments.
		 *     @type string         $classname   CSS class applied to the widget container.
		 *     @type string         $description The widget description.
		 *     @type array          $_callback   When the hook is fired on the back-end, $_callback is populated
		 *                                       with an array containing the widget object, see $callback.
		 * }
		 */
		do_action( 'dynamic_sidebar', $wp_registered_widgets[ $id ] );


		if( !isset( $widgets_html ) ){
			$widgets_html = array();
		}
		

		if ( is_callable($callback) ) {
			ob_start();

			call_user_func_array($callback, $params);
			$did_one = true;

			$widgets_html[] = ob_get_contents();
			ob_end_clean();

		}
		


	}

	/**
	 * Fires after widgets are rendered in a dynamic sidebar.
	 *
	 * Note: The action also fires for empty sidebars, and on both the front-end
	 * and back-end, including the Inactive Widgets sidebar on the Widgets screen.
	 *
	 * @since 3.9.0
	 *
	 * @param int|string $index       Index, name, or ID of the dynamic sidebar.
	 * @param bool       $has_widgets Whether the sidebar is populated with widgets.
	 *                                Default true.
	 */
	do_action( 'dynamic_sidebar_after', $index, true );

	/**
	 * Filter whether a sidebar has widgets.
	 *
	 * Note: The filter is also evaluated for empty sidebars, and on both the front-end
	 * and back-end, including the Inactive Widgets sidebar on the Widgets screen.
	 *
	 * @since 3.9.0
	 *
	 * @param bool       $did_one Whether at least one widget was rendered in the sidebar.
	 *                            Default false.
	 * @param int|string $index   Index, name, or ID of the dynamic sidebar.
	 */

	$did_one = apply_filters( 'dynamic_sidebar_has_widgets', $did_one, $index );



	return $widgets_html;


	//return $did_one;
}



///// INCOMPLETE : IN DEVELOPMENT /////
function pw_get_widget_objs( $sidebar_id ){
	// Returns an array of the captured HTML from each widget in the given sidebar

	global $wp_registered_widgets;

	// Gets a multi-dimentional array of all the sidebar areas, and the widgets therein
	$sidebars_widgets = wp_get_sidebars_widgets();
	/*	Example Output :
		$sidebars_widgets = array(
			'sidebar-1' => array(
				"search-2",
		        "recent-posts-2",
		        "recent-comments-2",
		        "archives-2",
		        "categories-2",
		        "meta-2"
			),
		);
	*/

	$widgets = _get( $sidebars_widgets, $sidebar_id );
	if( !$widgets )
		return false;

	$widget_objs = array();

	foreach( $widgets as $widget ){
		$widget_objs[ $widget ] = $wp_registered_widgets[ $widget ];
	}

	return $widget_objs;

}






?>