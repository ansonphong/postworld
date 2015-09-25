<?php

////////// LAYOUT OPTIONS //////////
function i_layout_options(){
	// DEPRECIATED : use pw_layout_options()
	return pw_layout_options();
}
function pw_layout_options(){

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
	$iLayoutOptions = apply_filters( 'i_layout_options', $iLayoutOptions );	

	return $iLayoutOptions;

}







?>