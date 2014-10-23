<?php

////////// LAYOUT OPTIONS //////////
function i_layout_options(){

	///// DEFINE OPTIONS /////
	$iLayoutOptions = array(
		"contexts"	=>	pw_get_contexts(),

		"templates"	=>	array(
			"default"	=>	array(
				array(
					'label' =>	'Default',
					'slug'	=>	'default',
					'image'	=>	get_infinite_directory_uri().'/images/layouts/default.png',
					)
				),
			"options"	=>	array(
				array(
					'label' =>	'Full Width',
					'slug'	=>	'full-width',
					'image'	=>	get_infinite_directory_uri().'/images/layouts/full.png',
					'supports' =>	array(),
					),
				array(
					'label' =>	'Left Sidebar',
					'slug'	=>	'left-sidebar',
					'image'	=>	get_infinite_directory_uri().'/images/layouts/left.png',
					'supports' =>	array( 'sidebar-left' ),  // TODO : IMPLIMENT SUPPORTS
					),
				array(
					'label' =>	'Right Sidebar',
					'slug'	=>	'right-sidebar',
					'image'	=>	get_infinite_directory_uri().'/images/layouts/right.png',
					'supports' =>	array( 'sidebar-right' ),
					),
				array(
					'label' =>	'Left & Right Sidebars',
					'slug'	=>	'left-right-sidebar',
					'image'	=>	get_infinite_directory_uri().'/images/layouts/left-right.png',
					'supports' =>	array( 'sidebar-left', 'sidebar-right' ),
					),
				),
			),

		"widget_areas"	=>	array(
			array(
				'name'	=>	'Left Sidebar',
				'slug'	=>	'left',
				'type'	=>	'sidebar',
				),
			array(
				'name'	=>	'Right Sidebar',
				'slug'	=>	'right',
				'type'	=>	'sidebar',
				),
			),

		"column_widths"	=>	array(
			array(
				"name"	=>	"00/12 : hidden",
				"slug"	=>	"0",
				),
			array(
				"name"	=>	"01/12 : thinner",
				"slug"	=>	"1",
				),
			array(
				"name"	=>	"02/12 : thin",
				"slug"	=>	"2",
				),
			array(
				"name"	=>	"03/12 : quarter",
				"slug"	=>	"3",
				),
			array(
				"name"	=>	"04/12 : third",
				"slug"	=>	"4",
				),
			array(
				"name"	=>	"05/12 : wide",
				"slug"	=>	"5",
				),
			array(
				"name"	=>	"06/12 : half",
				"slug"	=>	"6",
				),
			array(
				"name"	=>	"12/12 : full",
				"slug"	=>	"12",
				),
			),

		"screen_sizes"	=>	array(
			array(
				'name'	=>	'Extra small devices',
				'slug'	=>	'xs',
				'icon'	=>	'icon-mobile',
				'default_sidebar_width' => '12',
				),
			array(
				'name'	=>	'Small devices',
				'slug'	=>	'sm',
				'icon'	=>	'icon-mobile-wide',
				'default_sidebar_width' => '12',
				),
			array(
				'name'	=>	'Medium devices',
				'slug'	=>	'md',
				'icon'	=>	'icon-tablet',
				'default_sidebar_width' => '4',
				),
			array(
				'name'	=>	'Large devices',
				'slug'	=>	'lg',
				'icon'	=>	'icon-laptop',
				'default_sidebar_width' => '3',
				),
			),

		);

	///// FILTER /////
	// Filter results so that themes can over-ride settings
	$iLayoutOptions = apply_filters( 'i_layout_options', $iLayoutOptions );	

	return $iLayoutOptions;

}



function pw_get_contexts(){
	global $pw;

	///// GET FROM CACHE /////
	// If the contexts have already been generated
	// Get them directly from the global object
	if( isset( $pw['contexts'] ) && is_array( $pw['contexts'] ) ){
		$pw['contexts'] = apply_filters( 'pw_contexts', $pw['contexts'] );
		return $pw['contexts'];
	}

	///// ADD STANDARD CONTEXTS /////
	// TODO : Add Multi-language support, draw values from Language array
	$contexts = array(
		array(
			"label"	=>	"Default",
			"name"	=>	"default",
			"icon"	=>	"icon-circle-medium",
			),
		array(
			"label"	=>	"Home",
			"name"	=>	"home",
			"icon"	=>	"icon-home",
			),
		array(
			"label"	=>	"Blog",
			"name"	=>	"blog",
			"icon"	=>	"icon-pushpin",
			),
		array(
			"label"	=>	"Page",
			"name"	=>	"page",
			"icon"	=>	"icon-file",
			),
		array(
			"label"	=>	"Post",
			"name"	=>	"single",
			"icon"	=>	"icon-pushpin",
			),
		array(
			"label"	=>	"Archive",
			"name"	=>	"archive",
			"icon"	=>	"icon-th-list",
			),
		array(
			"label"	=>	"Search",
			"name"	=>	"search",
			"icon"	=>	"icon-search",
			),
		);


	///// ADD CUSTOM POST TYPES /////
	// Get registered custom post types
	$custom_post_types = get_post_types( array( '_builtin' => false, ), 'objects' );

	// Iterate through each post type and add it to contexts
	foreach( $custom_post_types as $post_type ){

		/// SINGLES ///
		array_push( $contexts,
			array(
				"label"	=>	$post_type->labels->singular_name . " : Single",
				"name"	=>	"single-" . $post_type->name,
				"icon"	=>	"icon-cube",
				)
		 );

		/// ARCHIVES ///
		if( $post_type->has_archive )
			array_push( $contexts,
				array(
					"label"	=>	$post_type->labels->singular_name . " : Archive",
					"name"	=>	"archive-cpt-" . $post_type->name,
					"icon"	=>	"icon-cubes",
					)
			 );

	}

	///// ADD BUILTIN TAXONOMIES /////
	/// CATEGORIES ///
	if( taxonomy_exists('category') )
		array_push( $contexts,
			array(
				"label"	=>	"Category : Archive",
				"name"	=>	"category",
				"icon"	=>	"icon-folder",
				)
		 );
	/// TAGS ///
	if( taxonomy_exists('post_tag') )
		array_push( $contexts,
			array(
				"label"	=>	"Tag : Archive",
				"name"	=>	"tag",
				"icon"	=>	"icon-tags",
				)
		 );
	
	///// ADD CUSTOM TAXONOMIES /////
	// Get registered custom taxonomies
	$custom_taxonomies = get_taxonomies( array( '_builtin' => false, ), 'objects' );

	foreach( $custom_taxonomies as $taxonomy ){

		/// TAXONOMIES ///
		// Only custom taxonomies
		if( !$taxonomy->_builtin )
			array_push( $contexts,
				array(
					"label"	=>	$taxonomy->labels->singular_name . " : Archive",
					"name"	=>	"archive-taxonomy-" . $taxonomy->name,
					"icon"	=>	"icon-cube-o",
					)
			 );
	}


	///// ADD BUDDYPRESS /////
	if( pw_is_buddypress_active() ){

		$contexts[] = array(
			"label"	=>	"BuddyPress",
			"name"	=>	"buddypress",
			"icon"	=>	"icon-plugin",
			);

		$contexts[] = array(
			"label"	=>	"BuddyPress User",
			"name"	=>	"buddypress-user",
			"icon"	=>	"icon-user",
			);

	}

	// Apply contexts filter
	$contexts = apply_filters( 'pw_contexts', $contexts );
	
	// Set into globals
	$pw['contexts'] = $contexts;

	return $contexts;
}



function pw_get_current_layout(){

	// An array with the current context(s)
	$contexts = pw_current_context();
	// Set Layout Variable
	$layout = false;

	$i_layouts = i_get_option( array( 'option_name' => 'i-layouts' ) );

	/// GET LAYOUT : FROM POSTMETA : OVERRIDE ///
	// Check for layout override in : post_meta.pw_meta.layout
	$override_layout = pw_get_wp_postmeta( array( 'sub_key' => 'layout' ) );
	
	// If override layout exists
	if( $override_layout != false && !empty( $override_layout ) ){
		$layout = $override_layout;
		$layout['source'] = 'post_meta';
	}

	/// GET LAYOUT : FROM CONTEXT ///
	if( !$layout ){
		// Iterate through all the current contexts
		// And find a match for it
		foreach( $contexts as $context ){
			$test_layout = pw_get_obj( $i_layouts, $context );
			// If there is a match
			if( (bool) $test_layout ){
				$layout = $test_layout;
				$layout['source'] = $context;
			}
		}
	}

	/// GET LAYOUT : DEFAULT LAYOUT : FALLBACK ///
	if( !$layout || $layout['template'] == 'default' || $layout['layout'] == 'default' ){
		$layout = pw_get_obj( $i_layouts, 'default' );
		$layout['source'] = 'default';
	}

	// FILL IN DEFAULT VALUES
	// In case of incomplete layout values
	if( pw_get_obj( $layout, 'source' ) != 'default' ){
		// Get the default layout
		$default_layout = pw_get_obj( $i_layouts, 'default' );
		// Merge it with the default layout, in case values are missing
		$layout = array_replace_recursive( $default_layout, $layout );

		// TODO : THIS BETTER TECHNIQUE
		// Fill in default header and footer
		if( empty( $layout['header']['id'] ) )
			$layout['header']['id'] = $default_layout['header']['id'];
		if( empty( $layout['footer']['id'] ) )
			$layout['footer']['id'] = $default_layout['footer']['id'];

	}

	//echo json_encode( $default_layout );

	// Autocorrect layout in case of migrations
	$layout = pw_autocorrect_layout( $layout );

	// Apply filter so that $layout can be over-ridden
	$layout = apply_filters( 'pw_layout', $layout );

	return $layout;

}


?>