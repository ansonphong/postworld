<?php
include_once 'utilities.php';

function i_get_option( $vars ){
	// Returns a sub key stored in the `i-options` option name
	//	From `wp_options` table
	/*
		vars = array(
			'option_name'	=>	[string], // "i-options",
			'key'			=> 	[string], // "images.logo",
			'filter'		=>	[boolean] // Gives option to disable filtering

			// TODO : Refactor into image sub-object
			'type'	=>	"image",
			'format' => "url",
			'size'	=>	"full",
			);
	*/

	extract($vars);

	// Set Defaults
	if( !isset($option_name) )
		$option_name = 'i-options';
	if( !isset($filter) )
		$filter = true;

	// Preformat Defaults
	if( !isset($format) )
		$format = 'url';
	if( !isset($type) )
		$type = '';
	if( !isset($size) )
		$size = 'full';
	if( !isset($key) )
		$key = '';
	

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
		// ie. iOptions-i-styles, to modify the default styles
		if( $filter )
			$value = apply_filters( 'iOptions-' . $option_name , $value );

		///// CACHING LAYER /////
		// Set the decoded data into the cache
		$i_options_cache[$option_name] = $value;

	}

	// If no key set, return the value directly
	if( empty( $key ) )
		return $value;

	// Get Option Value Object Key Value
	$value = i_get_obj( $value, $key );



	///// PREFORMAT RETURN DATA /////
	///// IMAGES /////
	// If it's an Image type
	if( $type == "image" && is_numeric($value) ){
		$url = wp_get_attachment_image_src( $value, $size );
		switch( $format ){
			case 'url':
				return $url[0];
				break;
			case 'array':
				return $url;
				break;
			case 'object':
				return array(
					"url" => $url[0],
					"width" => $url[1],
					"height" => $url[2]
					);
		}

	}

	return $value;

}



function i_get_postmeta_key( $vars ){
	// Returns a value stored in the `i_meta` post meta
	//	From `wp_postmeta` table
	/*
		vars = array(
			'post_id'	=> 1,
			'meta_key'	=> "i_meta",
			'key' 		=> "images.logo",
			);
	*/
	// FOR DEV TESTING
	//echo "<pre style='margin-top:100px;'>". json_encode( $iMeta ) ."</pre>";

	extract($vars);

	// Set Defaults
	if( !isset($post_id) ){
		global $post;
		$post_id = $post->ID;
	}
	if( !isset($meta_key) )
		$meta_key = 'i_meta';
	if( !isset($key) )
		$key = '';

	// Retreive Option
	if( $meta_key == 'i_meta' ){
		$iMeta = i_get_postmeta( $post_id );
	}
	else{
		$iMeta = get_post_meta( $post_id, $meta_key, true );
		// If it doesn't exist
		if( empty($iMeta) )
			return false;
		// Decode from JSON
		$iMeta = json_decode( $iMeta, true );
	}

	// Get Option Value
	$value = i_get_obj( $iMeta, $key );

	return $value;

}



function i_site_logo( $size = 'full', $format = 'url' ){
	return i_get_option(
		array(
			"type" => "image",
			"key" => "images.logo",
			"format" => $format,
			"size"	=>	$size
			)
		);
}

function i_site_logo_overlay( $size = 'full', $format = 'url' ){
	return i_get_option(
		array(
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
				"type" => "image",
				"key" => "images.header",
				"format" => $format,
				"size"	=>	$size
				)
			);

}


function i_site_favicon( $size = 'thumbnail', $format = 'url' ){
	return i_get_option(
		array(
			"type" => "image",
			"key" => "images.favicon",
			"format" => $format,
			"size"	=>	$size
			)
		);
}

?>