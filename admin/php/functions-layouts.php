<?php
function i_layout_options(){
	// DEPRECIATED : use pw_layout_options()
	return pw_layout_options();
}

/**
 * Defines the options when selecting a layout.
 */
function pw_layout_options(){
	$pwLayoutOptions = array(
		"contexts"	=>	pw_get_contexts(),

		"templates"	=>	array(
			"default"	=>	array(
				array(
					'label' =>	_x('Default','default layout','postworld'),
					'slug'	=>	'default',
					'image'	=>	postworld_directory_uri().'/images/layouts/default.png',
					)
				),
			"options"	=>	array(
				array(
					'label' =>	_x('Full Width','layout option','postworld'),
					'slug'	=>	'full-width',
					'image'	=>	postworld_directory_uri().'/images/layouts/full.png',
					'supports' =>	array(),
					),
				array(
					'label' =>	_x('Left Sidebar','layout option','postworld'),
					'slug'	=>	'left-sidebar',
					'image'	=>	postworld_directory_uri().'/images/layouts/left.png',
					'supports' =>	array( 'sidebar-left' ),  // TODO : IMPLIMENT SUPPORTS
					),
				array(
					'label' =>	_x('Right Sidebar','layout option','postworld'),
					'slug'	=>	'right-sidebar',
					'image'	=>	postworld_directory_uri().'/images/layouts/right.png',
					'supports' =>	array( 'sidebar-right' ),
					),
				array(
					'label' =>	_x('Left & Right Sidebars','layout option','postworld'),
					'slug'	=>	'left-right-sidebar',
					'image'	=>	postworld_directory_uri().'/images/layouts/left-right.png',
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
				'icon'	=>	'pwi-mobile',
				'default_sidebar_width' => '12',
				),
			array(
				'name'	=>	'Small devices',
				'slug'	=>	'sm',
				'icon'	=>	'pwi-mobile-wide',
				'default_sidebar_width' => '12',
				),
			array(
				'name'	=>	'Medium devices',
				'slug'	=>	'md',
				'icon'	=>	'pwi-tablet',
				'default_sidebar_width' => '4',
				),
			array(
				'name'	=>	'Large devices',
				'slug'	=>	'lg',
				'icon'	=>	'pwi-laptop',
				'default_sidebar_width' => '3',
				),
			),

		);

	///// FILTER /////
	// Filter results so that themes can over-ride settings
	// TODO : New Filter Name
	$pwLayoutOptions = apply_filters( 'i_layout_options', $pwLayoutOptions );	

	return $pwLayoutOptions;

}







?>