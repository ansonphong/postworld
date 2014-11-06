<?php
include_once 'utilities.php';


function pw_get_option( $vars ){
	// Returns a sub key stored in the `i-options` option name
	//	From `wp_options` table
	/*
		vars = array(
			'option_name'	=>	[string], // "i-options",
			'key'			=> 	[string], // "images.logo",
			'filter'		=>	[boolean] // Gives option to disable filtering
	*/

	$default_vars = array(
		'option_name'	=>	'i-options',
		'key'			=>	false,
		'filter'		=>	true,
		);
	$vars = array_replace_recursive( $default_vars, $vars );

	extract($vars);

	///// CACHING LAYER /////
	// Make a global to cache data at runtime
	// To prevent making multiple queries and json_decodes on the same option
	global $i_options_cache;

	// If cached data is already found, return it instantly
	if( isset( $i_options_cache[$option_name] ) ){
		$value = $i_options_cache[$option_name];
	}
	else{

		///// GET OPTION /////
		// Retreive Option
		$value = get_option( $option_name, array() );

		// If it doesn't exist
		//if( empty($value) )
		//	return false;
		
		// Decode from JSON, assuming it's a JSON string
		// TODO : Handle non-JSON string values
		if( !empty( $value ) )
			$value = json_decode( $value, true );

		///// APPLY FILTERS /////
		// This allows themes to over-ride default settings for options
		// ie. pwGetOption-postworld-styles-theme, to modify the default values
		if( $filter ){
			// Apply Filters
			$value = apply_filters( $option_name , $value );
			// Depreciated
			$value = apply_filters( 'iOptions-' . $option_name , $value );

		}

		///// CACHING LAYER /////
		// Set the decoded data into the cache
		$i_options_cache[$option_name] = $value;

	}

	// If no key set, return the value directly
	if( empty( $key ) )
		return $value;

	// Get Option Value Object Key Value
	$value = _get( $value, $key );

	return $value;

}

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