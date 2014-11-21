<?php
include_once 'utilities.php';



// Depreciated
function i_get_option( $vars ){
	return pw_get_option( $vars );
}

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


function i_get_postmeta_key( $vars ){
	// Returns a value stored in the `i_meta` post meta
	//	From `wp_postmeta` table
	/*
		vars = array(
			'post_id'	=> 1,
			'meta_key'	=> PW_POSTMETA_KEY,
			'key' 		=> "images.logo",
			);
	*/
	// FOR DEV TESTING
	//echo "<pre style='margin-top:100px;'>". json_encode( $pwMeta ) ."</pre>";

	extract($vars);

	// Set Defaults
	if( !isset($post_id) ){
		global $post;
		$post_id = $post->ID;
	}
	if( !isset($meta_key) )
		$meta_key = PW_POSTMETA_KEY;
	if( !isset($key) )
		$key = '';

	// Retreive Option
	if( $meta_key == PW_POSTMETA_KEY ){
		$pwMeta = i_get_postmeta( $post_id );
	}
	else{
		$pwMeta = get_post_meta( $post_id, $meta_key, true );
		// If it doesn't exist
		if( empty($pwMeta) )
			return false;
		// Decode from JSON
		$pwMeta = json_decode( $pwMeta, true );
	}

	// Get Option Value
	$value = i_get_obj( $pwMeta, $key );

	return $value;

}


// DEPRECIATED
function i_site_logo( $size = 'full', $format = 'url' ){
	return pw_site_logo( $size, $format );
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

	return pw_get_image_option(
		array(
			'option_name' => PW_OPTIONS_SITE,
			'key' => 'images.favicon',
			'size' => $size
			)
		);

}


function i_site_logo_overlay( $size = 'full', $format = 'url' ){
	return i_get_option(
		array(
			"option_name" => PW_OPTIONS_SITE,
			"type" => "image",
			"key" => "images.logo_overlay",
			"format" => $format,
			"size"	=>	$size
			)
		);
}


function i_header_image( $size = 'full', $format = 'url' ){

	// Check the Postmeta Settings
	global $post;
	
	// Get Post Meta for Header Type
	$postmeta_header_type = i_get_postmeta_key(
		array(
			"post_id"	=>	$post->ID,
			"key"		=>	"header.type"
			)
		);

	// If Over-ride with Featured Image, get featured image
	if( $postmeta_header_type == 'featured_image' ){
		$post_thumbnail_id = get_post_thumbnail_id( $post->ID );
		$image_src = wp_get_attachment_image_src( $post_thumbnail_id, 'full' );
		return $image_src[0];
	}
	else	
		// Return default header image
		return i_get_option(
			array(
				"option_name" => PW_OPTIONS_SITE,
				"type" => "image",
				"key" => "images.header",
				"format" => $format,
				"size"	=>	$size
				)
			);

}



?>