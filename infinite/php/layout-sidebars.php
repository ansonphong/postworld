<?php
/////////////// LAYOUT & SIDEBAR FUNCTIONS ///////////////

// Infinite : Insert the Header
function i_header(){
	iGlobals();
	global $iGlobals;
	
	// Get the set header ID
	$header_id = i_get_obj( $iGlobals, 'layout.header.id' );
	// If the header ID is empty, or not set, or default
	if( empty( $header_id ) )
		// Set it to the default header ID
		$header_id = i_get_obj( $iGlobals, 'layouts.default.header.id' );

	// Get Templates
	$templates = i_get_templates();

	if( !empty( $templates['header'][$header_id] ) )
		include $templates['header'][$header_id];
}

// Infinite : Insert the Footer
function i_footer(){
	iGlobals();
	global $iGlobals;

	// Get the set footer ID
	$footer_id = i_get_obj( $iGlobals, 'layout.footer.id' );
	// If the footer ID is empty, or not set, or default
	if( empty( $footer_id ) )
		// Set it to the default footer ID
		$footer_id = i_get_obj( $iGlobals, 'layouts.default.footer.id' );

	// Get Templates
	$templates = i_get_templates();
	
	if( !empty( $templates['footer'][$footer_id] ) )
		include $templates['footer'][$footer_id];
}


// Infinite : Insert Content
function i_insert_content($vars){

	extract($vars);

	if( !empty($function) )
		call_user_func( $function );
	
	if( !empty($content) || !empty($before_content) ){
		// Before Content
		if( !empty( $before_content ) )
			echo $before_content;
		// Content
		echo $content;
		// After Content
		if( !empty( $after_content ) )
			echo $after_content;
	}

}

// Infinite : Insert Column Classes
function i_insert_column_classes( $column, $i_layout = array() ){
	global $pw;
	if( empty( $i_layout ) )
		$i_layout = $pw['layout'];

	$classes = "";

	$full_width = (int) 12;

	// Cycle through each screen size
	foreach( i_layout_options()['screen_sizes'] as $screen_size ){
		//$screen_size_columns = array();
		$screen_size_slug = $screen_size['slug'];

		$left_sidebar_width = ( isset($i_layout['sidebars']['left']['width'][$screen_size_slug]) ) ?
			(int) $i_layout['sidebars']['left']['width'][$screen_size_slug] :
			(int) $screen_size['default_sidebar_width'];

		$right_sidebar_width = ( isset($i_layout['sidebars']['right']['width'][$screen_size_slug]) ) ?
			(int) $i_layout['sidebars']['right']['width'][$screen_size_slug] :
			(int) $screen_size['default_sidebar_width'];

		// Switch Layouts
		switch($i_layout['template']){

			// Full Width
			case 'full-width';
				$screen_size_columns = array(
					"left"		=>	0,
					"content"	=>	$full_width,
					"right"		=>	0
					);
				break;

			// Left & Right Sidebar
			case 'left-right-sidebar';
				//$content_width = 
				$screen_size_columns = array(
					"left"		=>	$left_sidebar_width,
					"content"	=>	($full_width - $left_sidebar_width - $right_sidebar_width),
					"right"		=>	$right_sidebar_width
					);
				break;

			// Left Sidebar
			case 'left-sidebar';
				$screen_size_columns = array(
					"left"		=>	$left_sidebar_width,
					"content"	=>	($full_width - $left_sidebar_width),
					"right"		=>	0
					);
				break;

			// Right Sidebar
			case 'right-sidebar';
				$screen_size_columns = array(
					"left"		=>	0,
					"content"	=>	($full_width - $right_sidebar_width),
					"right"		=>	$right_sidebar_width
					);
				break;

		}

		// Handle a 0 width (content) column
		if( $screen_size_columns[$column] < 1 ){
			if( $column == 'content' ){
				// Make Content full width
				$screen_size_columns[$column] = $full_width;
				//$classes .= " clearfix ";
			}
			else{
				// Hide Sidebars which have 0 width
				$classes .= "hidden-".$screen_size['slug']." ";
				$screen_size_columns[$column] = 0;
			}
			
		}

		$classes .= "col-".$screen_size['slug']."-".$screen_size_columns[$column]." ";

	} // foreach

	echo $classes;

}

// Infinite : Insert Responsive Clearfix
function i_insert_clearfix( $column, $i_layout = array() ){
	global $pw;
	// Get current layout
	if( empty( $i_layout ) )
		$i_layout = $pw['layout'];

	$full_width = (int) 12;
	$classes = "";

	foreach( i_layout_options()['screen_sizes'] as $screen_size ){
		$screen_size_slug = $screen_size['slug'];
		$column_width = $i_layout['sidebars'][$column]['width'][$screen_size_slug];

		if($column_width >= $full_width)
			$classes .= " visible-".$screen_size_slug;
	}

	echo "<div class=\"clearfix hidden ". $classes ."\"></div>";
}


function i_print_layout( $vars ){
	/*
	$vars = array(
		'template'			=>	$pw['layout']['template'],
		'function'			=>	'page_content_function',
		'content'			=>	apply_filters( 'the_content', $post->post_content ),
		'before_content' 	=>	'<div>',
		'after_content' 	=>	'</div>',
		'echo'				=>	true,
		);
	*/

	global $pw;

	///// SETUP VARIABLES /////
	// Apply filter so that layout can be over-ridden
	$vars = apply_filters( 'pw_print_layout', $vars );



	// Set the default variables
	$vars_defaults = array(
		'function'	=>	'',
		'content'	=>	'',
		'template'	=>	$pw['layout']['template'],
		'echo'		=>	true,
		);
	$vars = pw_set_defaults( $vars, $vars_defaults );

	//echo json_encode($pw['layout']);

	///// TEMPLATES ////
	$subdir = 'layouts';
	$layout_templates = pw_get_templates(
		array(
			'subdirs' => array( $subdir ),
			'path_type' => 'dir',
			'ext'=>'php',
			)
		)[$subdir];


	///// INCLUDE TEMPLATE /////
	$template_path = $layout_templates[ $vars['template'] ];

	$html = pw_ob_include( $template_path, $vars );

	///// ECHO /////
	if( $vars['echo'] )
		echo $html;
	///// RETURN /////
	else
		return $html;

}

// Infinite : Insert a Sidebar
function i_insert_sidebar( $sidebar, $i_layout = array() ){
	global $pw;
	// Get current layout
	if( empty( $i_layout ) )
		$i_layout = $pw['layout'];

	$sidebar_id = pw_get_obj( $i_layout, 'sidebars.'. $sidebar .'.id' );

	if( isset( $sidebar_id ) )
		dynamic_sidebar( $sidebar_id );
}

// Infinite : Insert a Sidebar Template
function i_insert_sidebar_template( $sidebar, $i_layout = array() ){
	global $pw;
	// Get current layout
	if( empty( $i_layout ) )
		$i_layout = $pw['layout'];

	$sidebar_id = $i_layout['sidebars'][$sidebar]['id'];
	
	$template_by_id = locate_template( 'views/sidebars/sidebar-'.$sidebar_id.'.php' );
	$template_default = locate_template( 'views/sidebars/sidebar.php' );

	// Get a template specifically with the sidebar ID
	if( !empty($template_by_id) ){
		include $template_by_id;

	// Get the default sidebar template
	} else if( !empty($template_default) ){
		include $template_default;

	// Insert the sidebar directly, with no template
	} else {
		i_insert_sidebar( $i_layout, $sidebar );
	}

}


?>