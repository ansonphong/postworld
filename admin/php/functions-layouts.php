<?php
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
				'name'	=>	_x('Left Sidebar','widget area','postworld'),
				'slug'	=>	'left',
				'type'	=>	'sidebar',
				),
			array(
				'name'	=>	_x('Right Sidebar','widget area','postworld'),
				'slug'	=>	'right',
				'type'	=>	'sidebar',
				),
			),

		"column_widths"	=>	array(
			array(
				"name"	=>	_x('00/12 : hidden','column width','postworld'),
				"slug"	=>	"0",
				),
			array(
				"name"	=>	_x('01/12 : thinner','column width','postworld'),
				"slug"	=>	"1",
				),
			array(
				"name"	=>	_x('02/12 : thin','column width','postworld'),
				"slug"	=>	"2",
				),
			array(
				"name"	=>	_x('03/12 : quarter','column width','postworld'),
				"slug"	=>	"3",
				),
			array(
				"name"	=>	_x('04/12 : third','column width','postworld'),
				"slug"	=>	"4",
				),
			array(
				"name"	=>	_x('05/12 : wide','column width','postworld'),
				"slug"	=>	"5",
				),
			array(
				"name"	=>	_x('06/12 : half','column width','postworld'),
				"slug"	=>	"6",
				),
			array(
				"name"	=>	_x('12/12 : full','column width','postworld'),
				"slug"	=>	"12",
				),
			),

		"screen_sizes"	=>	array(
			array(
				'name'	=>	_x('Extra small devices','screen size','postworld'),
				'slug'	=>	'xs',
				'icon'	=>	'pwi-mobile',
				'default_sidebar_width' => '12',
				),
			array(
				'name'	=>	_x('Small devices','screen size','postworld'),
				'slug'	=>	'sm',
				'icon'	=>	'pwi-mobile-wide',
				'default_sidebar_width' => '12',
				),
			array(
				'name'	=>	_x('Medium devices','screen size','postworld'),
				'slug'	=>	'md',
				'icon'	=>	'pwi-tablet',
				'default_sidebar_width' => '4',
				),
			array(
				'name'	=>	_x('Large devices','screen size','postworld'),
				'slug'	=>	'lg',
				'icon'	=>	'pwi-laptop',
				'default_sidebar_width' => '3',
				),
			),

		);

	// Filter results so that themes can over-ride settings
	$pwLayoutOptions = apply_filters( 'pw_layout_options', $pwLayoutOptions );	

	return $pwLayoutOptions;

}

