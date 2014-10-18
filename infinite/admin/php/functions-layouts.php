<?php

////////// LAYOUT OPTIONS //////////
function i_layout_options(){

	///// DEFINE OPTIONS /////
	$iLayoutOptions = array(
		"contexts"	=>	array(
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
			),

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


	///// ADD CUSTOM POST TYPES /////
	// Get registered custom post types
	$custom_post_types = get_post_types( array( '_builtin' => false, ), 'objects' );

	// Iterate through each post type and add it to contexts
	foreach( $custom_post_types as $post_type ){

		/// SINGLES ///
		array_push( $iLayoutOptions['contexts'],
			array(
				"label"	=>	$post_type->labels->singular_name . " : Single",
				"name"	=>	"cpt_" . $post_type->name . "_single",
				"icon"	=>	"icon-cube",
				)
		 );

		/// ARCHIVES ///
		if( $post_type->has_archive )
			array_push( $iLayoutOptions['contexts'],
				array(
					"label"	=>	$post_type->labels->singular_name . " : Archive",
					"name"	=>	"cpt_" . $post_type->name . "_archive",
					"icon"	=>	"icon-cubes",
					)
			 );

	}

	///// ADD BUILTIN TAXONOMIES /////
	/// CATEGORIES ///
	if( taxonomy_exists('category') )
		array_push( $iLayoutOptions['contexts'],
			array(
				"label"	=>	"Category : Archive",
				"name"	=>	"category_archive",
				"icon"	=>	"icon-folder",
				)
		 );
	/// TAGS ///
	if( taxonomy_exists('post_tag') )
		array_push( $iLayoutOptions['contexts'],
			array(
				"label"	=>	"Tag : Archive",
				"name"	=>	"tag_archive",
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
			array_push( $iLayoutOptions['contexts'],
				array(
					"label"	=>	$taxonomy->labels->singular_name . " : Archive",
					"name"	=>	"term_" . $taxonomy->name . "_archive",
					"icon"	=>	"icon-cube-o",
					)
			 );

	}


	///// FILTER /////
	// Filter results so that themes can over-ride settings
	$iLayoutOptions = apply_filters( 'i_layout_options', $iLayoutOptions );	

	return $iLayoutOptions;

}

?>