<?php

function pw_get_image_option( $vars ){
	// Gets an image ID with pw_get_option
	// Then gets the image object with pw_get_image_obj
	/*
		$vars = array(
			'option_name'	=>	...
			'key'			=>	...
			'size'			=>  ...
		)
	*/
	$defaultVars = array(
		'size'	=>	'full',
		);

	$vars = array_replace_recursive( $defaultVars, $vars );
	$image_id = pw_get_option( $vars );

	if( (bool) $image_id )
		return	pw_get_image_obj( $image_id, $vars['size'] );
	else
		return false;
}


function pw_site_logo( $size = 'full', $format = 'url' ){
	return pw_get_option(
		array(
			"option_name" => PW_OPTIONS_THEME,
			"type" => "image",
			"key" => "images.logo",
			"format" => $format,
			"size"	=>	$size
			)
		);
}


function pw_site_favicon( $size = 'thumbnail' ){
	// Depreciated -- Use native WordPress customizer

	return pw_get_image_option(
		array(
			'option_name' => PW_OPTIONS_SITE,
			'key' => 'images.favicon',
			'size' => $size
			)
		);

}


?>