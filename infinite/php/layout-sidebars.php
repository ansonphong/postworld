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
function i_insert_content($layout_args){

	extract($layout_args);

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
function i_insert_column_classes( $i_layout, $column ){
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
		switch($i_layout['layout']){

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
function i_insert_clearfix( $i_layout, $column ){
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


function i_print_layout( $layout_args ){

	global $iGlobals;
	extract($iGlobals);

	extract($layout_args);

	// Default Function Value
	$function = ( isset($function) ) ? $function : "" ;

	// Default Content Value
	$content = ( isset($content) ) ? $content : "" ;

	if( !isset($layout) )
		return array( "error"	=>	"No layout specified." );

	// Switch Layouts
	switch( $layout ){

		// TODO : Break each layout into individual template files
		// And organized under the heirarchical templates structure
		// So that templates can be added and removed

		////////// FULL LAYOUT ////////////
		case 'full-width'; ?>
			<div class="row page">
				<div class="<?php i_insert_column_classes($iGlobals['layout'], 'content') ?>">
					<?php i_insert_content($layout_args); ?>
				</div>
			</div>

			<?php
			break;

		////////// LEFT & RIGHT SIDEBAR LAYOUT ////////////
		case 'left-right-sidebar'; ?>
			<div class="row page">
				
				<div class="<?php i_insert_column_classes($iGlobals['layout'], 'left') ?> col-left sidebar">
					<?php i_insert_sidebar($iGlobals['layout'], 'left'); ?>
				</div>

				<div class="<?php i_insert_column_classes($iGlobals['layout'], 'content') ?> col-content">
						<?php i_insert_content($layout_args); ?>
				</div>

				<?php i_insert_clearfix($iGlobals['layout'], 'right'); ?>
				<div class="<?php i_insert_column_classes($iGlobals['layout'], 'right') ?> col-right sidebar">
					<?php i_insert_sidebar($iGlobals['layout'], 'right'); ?>
				</div>

			</div>

			<?php
			break;

		////////// LEFT SIDEBAR LAYOUT ////////////
		case 'left-sidebar'; ?>

			<div class="row page">
				
				<div class="<?php i_insert_column_classes($iGlobals['layout'], 'left') ?> col-left sidebar">
					<?php i_insert_sidebar($iGlobals['layout'], 'left'); ?>
				</div>

				<div class="<?php i_insert_column_classes($iGlobals['layout'], 'content') ?> col-content">
					<?php i_insert_content($layout_args); ?>
				</div>

			</div>

			<?php
			break;

		////////// RIGHT SIDEBAR LAYOUT ////////////
		case 'right-sidebar'; ?>

			<div class="row page">

				<div class="<?php i_insert_column_classes($iGlobals['layout'], 'content') ?> col-content">
					<?php i_insert_content($layout_args); ?>
				</div>

				<?php i_insert_clearfix($iGlobals['layout'], 'right'); ?>
				<div class="<?php i_insert_column_classes($iGlobals['layout'], 'right') ?> col-right sidebar">
					<?php i_insert_sidebar_template( $iGlobals['layout'], 'right'); ?>
				</div>

			</div>

			<?php
			break;
	}

}

// Infinite : Insert a Sidebar Template
function i_insert_sidebar_template( $i_layout, $sidebar ){

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

// Infinite : Insert a Sidebar
function i_insert_sidebar( $i_layout, $sidebar ){

	if( isset($i_layout['sidebars'][$sidebar]['id']) )
		dynamic_sidebar( $i_layout['sidebars'][$sidebar]['id'] );

}

?>