<?php
global $i_paths;
$i_paths = array(
	'child_theme'	=>	array(
		'dir'	=>	get_stylesheet_directory(),
		'url'	=>	get_stylesheet_directory_uri(),
		),
	
	'infinite'	=>	array(
		'dir'	=>	get_infinite_directory(),
		'url'	=>	get_infinite_directory_uri(),
		),

	'templates'	=> array(
		'dir'	=>	array(
			'default'	=>	get_infinite_directory().'/views/',
			'override'	=>	get_stylesheet_directory().'/views/',
			),
		'url'	=>	array(
			'default'	=>	get_infinite_directory_uri().'/views/',
			'override'	=>	get_stylesheet_directory_uri().'/views/',
			),
		),

	);


function i_image_url( $path ){
	global $i_paths;

	$image_path = '/images/'.$path;

	// Check if the image exists in the child theme
	if( file_exists( $i_paths['child_theme']['dir'].$image_path ) )
		return $i_paths['child_theme']['url'].$image_path;

	// Otherwise return the image from the infinite theme
	else
		return $i_paths['infinite']['url'].'/images/'.$path;
}


?>