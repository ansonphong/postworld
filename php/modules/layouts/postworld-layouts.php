<?php

function pw_default_layouts_filter( $layouts ){
	if( !empty( $layouts ) )
		return $layouts;

	return array(
		'default' => array(
			'template' 	=> 'full-width',
			'header'	=> array(
				'id' => 'theme-header'
				),
			'footer'	=> array(
				'id' => 'theme-footer'
				),
			)
		);
}
add_filter( 'pw_default_layouts', 'pw_default_layouts_filter', 9 );

function pw_get_current_layout( $vars = array() ){

	global $pw;

	// If layouts module is not activated, return false
	if( !in_array( 'layouts', $pw['info']['modules'] ) )
		return false;

	// An array with the current context(s)
	$contexts = pw_current_context();

	// Set Layout Variable
	$layout = false;

	// Get layouts
	$pwLayouts = pw_get_option( array( 'option_name' => PW_OPTIONS_LAYOUTS ) );

	// If no layouts have been saved yet
	if( empty( $pwLayouts ) ){
		// Apply filter to get default layouts configuration
		$pwLayouts = apply_filters( 'pw_default_layouts', array() );
	}

	/// GET LAYOUT : FROM POSTMETA : OVERRIDE ///
	if( in_array( 'single', $contexts ) || in_array( 'page', $contexts ) || isset($vars['post_id']) ){
		/// DEFINE POST ID ///
		global $post;
		// Get use provided vars.post_id to override current post
		$get_post_id = _get( $vars, 'post_id' );
		$post_id = ( empty( $get_post_id ) ) ?
			$post->ID : $vars['post_id'];

		// Check for layout override in : post_meta.pw_meta.layout
		$postmeta_layout = pw_get_wp_postmeta( array(
			'post_id' => $post_id,
			'sub_key' => 'layout'
			));
		
		// If override layout exists
		if( $postmeta_layout != false && !empty( $postmeta_layout ) ){
			$layout = $postmeta_layout;
			$layout['source'] = 'post_meta';
		}
	}

	/// GET LAYOUT : FROM CONTEXT ///
	if( !$layout || _get( $layout, 'template' ) == 'default' ){
		// Iterate through all the current contexts
		// And find a match for it
		foreach( $contexts as $context ){
			$test_layout = _get( $pwLayouts, $context );
			// If there is a match
			if( (bool) $test_layout ){
				$layout = $test_layout;
				$layout['source'] = 'context:'.$context;
			}
		}
	}

	/// GET LAYOUT : FROM POST PARENT ///
	// Check if it's a single context request
	$is_single = (
		in_array( 'single', $contexts ) ||
		isset($vars['post_id'])
		);
	// Check if the template value is default
	$is_default = ( _get( $layout, 'template' ) === 'default' );
	// If it's eligible for a post parent layout
	if( ( !$layout && $is_single ) || ( $is_default && $is_single ) ){
		// Get default layout from post parent's layout
		$get_post = get_post( $post_id );
		if( $get_post->post_parent !== 0 )
			$layout = pw_get_current_layout( array(
				'post_id' => $get_post->post_parent
				));
	}

	/// GET LAYOUT : DEFAULT LAYOUT : FALLBACK ///
	if( !$layout || $layout['template'] == 'default' ){ //  || $layout['layout'] == 'default'
		// Get from saved default layout
		if( !empty( $pwLayouts ) )
			$layout = _get( $pwLayouts, 'default' );
		// Get from theme filter
		else
			$layout = apply_filters( 'pw_default_layout', array() );

		$layout['source'] = 'default';

	}

	// FILL IN DEFAULT VALUES
	// In case of incomplete layout values
	if( _get( $layout, 'source' ) != 'default' ){
		// Get the default layout
		$default_layout = _get( $pwLayouts, 'default' );

		// Merge it with the default layout, in case values are missing
		$layout = array_replace_recursive( $default_layout, $layout );

		// @todo : Set this with a better technique.
		// Fill in default header and footer
		if( empty( $layout['header']['id'] ) )
			$layout['header']['id'] = _get( $default_layout, 'header.id' );
		if( empty( $layout['footer']['id'] ) )
			$layout['footer']['id'] = _get( $default_layout, 'footer.id' );
	}

	// Autocorrect layout in case of migrations
	$layout = pw_autocorrect_layout( $layout );

	// Apply filter so that $layout can be over-ridden
	$layout = apply_filters( 'pw_layout', $layout );

	return $layout;

}

function pw_autocorrect_layout( $layout ){
	// In the case of an old data model, auto-correct layout settings
	if( isset( $layout['layout'] ) && !isset( $layout['template'] ) )
		$layout['template'] = $layout['layout'];

	//if( !isset($layout['template']) )
	//	$layout['template'] = 'full-width';

	return $layout;
}


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

	// If there is no value, return false
	if( empty( $template_path ) )
		return false;

	///// CACHING LAYER /////
	if( in_array( 'layout_cache', pw_enabled_modules() ) ){
		$hash_array = array(
			'template_path' => $template_path,
			'device' 		=> pw_device_meta(),
			'view' 			=> $pw['view'],
			'_get' 			=> $_GET,
			//'user_id'		=> get_current_user_id()
			);
		//pw_log( 'hash_array', $hash_array );
		$cache_hash = hash( 'sha256', json_encode( $hash_array ) );
		//pw_log( 'cache_hash', $cache_hash );
		$get_cache = pw_get_cache( array( 'cache_hash' => $cache_hash ) );

		// If cached content, echo it here and return
		if( !empty( $get_cache ) ){
			$cache_content = $get_cache['cache_content'];
			echo $cache_content;
			return;
		}
	}

	// If no cached content, include here
	$template_content = pw_ob_include( $template_path );

	///// CACHING LAYER /////
	if( in_array( 'layout_cache', pw_enabled_modules() ) )
		pw_set_cache( array(
			'cache_type'	=>	'layout',
			'cache_name'	=>  $template . ':' . $template_path,
			'cache_hash' 	=> 	$cache_hash,
			'cache_content'	=>	$template_content,
			));

	echo $template_content;

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

	if( !empty($function) && function_exists( $function ) )
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
function i_insert_column_classes( $column, $pw_layout = array() ){
	// DEPRECIATED : use pw_insert_column_classes()
	return pw_insert_column_classes( $column, $pw_layout );
}
function pw_insert_column_classes( $column, $pw_layout = array() ){
	global $pw;
	if( empty( $pw_layout ) )
		$pw_layout = $pw['layout'];

	$classes = "";

	$full_width = (int) 12;

	// Cycle through each screen size
	$layout_options = pw_layout_options();
	foreach( $layout_options['screen_sizes'] as $screen_size ){
		//$screen_size_columns = array();
		$screen_size_slug = $screen_size['slug'];

		$left_sidebar_width = ( isset($pw_layout['sidebars']['left']['width'][$screen_size_slug]) ) ?
			(int) $pw_layout['sidebars']['left']['width'][$screen_size_slug] :
			(int) $screen_size['default_sidebar_width'];

		$right_sidebar_width = ( isset($pw_layout['sidebars']['right']['width'][$screen_size_slug]) ) ?
			(int) $pw_layout['sidebars']['right']['width'][$screen_size_slug] :
			(int) $screen_size['default_sidebar_width'];

		// Switch Layouts
		switch($pw_layout['template']){

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
function i_insert_clearfix( $column, $pw_layout = array() ){
	// DEPRECIATED : use pw_insert_clearfix()
	pw_insert_clearfix( $column, $pw_layout = array() );
}
function pw_insert_clearfix( $column, $pw_layout = array() ){
	global $pw;
	// Get current layout
	if( empty( $pw_layout ) )
		$pw_layout = $pw['layout'];

	$full_width = (int) 12;
	$classes = "";

	$layout_options = pw_layout_options();
	foreach( $layout_options['screen_sizes'] as $screen_size ){
		$screen_size_slug = $screen_size['slug'];
		$column_width = $pw_layout['sidebars'][$column]['width'][$screen_size_slug];

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
		);
	$layout_templates = $layout_templates[$subdir];

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
function i_insert_sidebar( $sidebar, $pw_layout = array() ){
	// DEPRECIATED : use pw_insert_sidebar()
	return pw_insert_sidebar( $sidebar, $pw_layout );
}
function pw_insert_sidebar( $sidebar, $pw_layout = array() ){
	global $pw;
	// Get current layout
	if( empty( $pw_layout ) )
		$pw_layout = $pw['layout'];

	$sidebar_id = pw_get_obj( $pw_layout, 'sidebars.'. $sidebar .'.id' );

	if( isset( $sidebar_id ) )
		dynamic_sidebar( $sidebar_id );
}

// Infinite : Insert a Sidebar Template
function i_insert_sidebar_template( $sidebar, $pw_layout = array() ){
	// DEPRECIATED : use pw_insert_sidebar_template()
	return pw_insert_sidebar_template( $sidebar, $pw_layout );
}
function pw_insert_sidebar_template( $sidebar, $pw_layout = array() ){
	global $pw;
	// Get current layout
	if( empty( $pw_layout ) )
		$pw_layout = $pw['layout'];

	$sidebar_id = $pw_layout['sidebars'][$sidebar]['id'];
	
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
		pw_insert_sidebar( $pw_layout, $sidebar );
	}

}


?>