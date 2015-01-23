<?php
/////////////// LAYOUT & SIDEBAR FUNCTIONS ///////////////
function pw_header_footer( $template = 'header' ){
	// TODO : See why this is being instantiated 3 times, by index.php, home.php and single.php
	global $pw;
	// Get the set template ID
	$template_id = _get( $pw, 'layout.'.$template.'.id' );
	// If the template ID is empty, or not set, or default
	if( empty( $template_id ) )
		// Set it to the default template ID
		$template_id = _get( $pw, 'layouts.default.'.$template.'.id' );
	// If no default template is set
	if( empty( $template_id ) ){
		// Get the default layout
		$default_layout = apply_filters( 'pw_default_layout', array() );
		$template_id = _get( $default_layout, $template.'.id' );
	}
	// Get Templates
	$templates = pw_get_templates( array(
		'subdirs'	=>	array('header','footer'),
		'path_type'	=>	'dir',
		'ext'		=>	'php',
		));
	// Get the template path
	$template_path = _get( $templates, $template.'.'.$template_id );
	// If a template path exists
	if( !empty( $template_path ) )
		// Include the template
		include $template_path;
}



// Insert the Header
function i_header(){
	// DEPRECIATED : use pw_header()
	pw_header();
}
function pw_header( $debug = 'default' ){
	pw_header_footer('header');
}

// Insert the Footer
function i_footer(){
	// DEPRECIATED : use pw_footer()
	pw_footer();
}
function pw_footer(){
	pw_header_footer('footer');
}


// Insert Content
function i_insert_content($vars){
	// DEPRECIATED : use pw_insert_content()
	return pw_insert_content($vars);
}
function pw_insert_content($vars){
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

// Insert Column Classes
function i_insert_column_classes( $column, $i_layout = array() ){
	// DEPRECIATED : use pw_insert_column_classes()
	return pw_insert_column_classes( $column, $i_layout );
}
function pw_insert_column_classes( $column, $i_layout = array() ){
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

// Insert Responsive Clearfix
function i_insert_clearfix( $column, $i_layout = array() ){
	// DEPRECIATED : use pw_insert_clearfix()
	pw_insert_clearfix( $column, $i_layout = array() );
}
function pw_insert_clearfix( $column, $i_layout = array() ){
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


function i_print_layout($vars){
	// DEPRECIATED : use pw_print_layout()
	return pw_print_layout( $vars );
}

function pw_print_layout( $vars ){
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
	$vars = array_replace_recursive( $vars_defaults, $vars );

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
	// DEPRECIATED : use pw_insert_sidebar()
	return pw_insert_sidebar( $sidebar, $i_layout );
}
function pw_insert_sidebar( $sidebar, $i_layout = array() ){
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
	// DEPRECIATED : use pw_insert_sidebar_template()
	return pw_insert_sidebar_template( $sidebar, $i_layout );
}
function pw_insert_sidebar_template( $sidebar, $i_layout = array() ){
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