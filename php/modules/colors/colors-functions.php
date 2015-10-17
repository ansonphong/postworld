<?php
/**
 * Generate color metadata from an image.
 * Used for hooking into the 'wp_generate_attachment_metadata' filter
 * 
 * @see /wp-admin/includes/image.php :: wp_generate_attachment_metadata()
 */
add_filter( 'wp_generate_attachment_metadata', 'pw_colors_process_attachment' );
function pw_colors_process_attachment( $metadata, $attachment_id ){
	global $pwSiteGlobals;
	if( _get( $pwSiteGlobals, 'colors.process_images' ) ){
		pw_generate_attachment_colors( array( 'attachment_id' => $attachment_id ) );
	}
	return $metadata;
}

/**
 * Get the hexadecimal colors from an image.
 */
function pw_get_image_colors( $attachment_id ){
	return pw_generate_attachment_colors(array(
		'attachment_id' => $attachment_id,
		));
}

/**
 * If no colors are found, or the number of colors differs from global config
 * extract the colors from the image according to the Postworld configiration,
 * And save under 'pw_colors' meta key in the postmeta table.
 *
 * @param $vars
 *	@example array(
 *				'attachment_id' => $post_id
 *				)
 * @return array Colors in hexidecimal format.
 */
add_action( 'pw_generate_attachment_colors', 'pw_generate_attachment_colors', 10, 1 );
function pw_generate_attachment_colors( $vars ){
	global $pwSiteGlobals;

	$colors_settings = _get( $pwSiteGlobals, 'colors' );
	if( empty( $colors_settings ) )
		return false;

	if( !_get( $colors_settings, 'process_images' ) )
		return false;

	$attachment_id = _get( $vars, 'attachment_id' );
	if( empty( $attachment_id ) )
		return false;

	$number = _get( $colors_settings, 'number' );
	if( !is_int( $number ) )
		$number = 6;

	// Get existing colors entry
	$colors = pw_get_wp_postmeta( array(
		'post_id' => $post->ID,
		'meta_key' => PW_COLORS_KEY,
		));

	/**
	 * If there's no colors, or the color count is different
	 * Run the color extractor, and save the colors.
	 */
	if( empty($colors) || count($colors) != $number ){
		
		/**
		 * Get a smaller image than the original if it's bigger than 640x480
		 * Since processing larger images is time consuming.
		 */
		$image = pw_get_attachment_image_size(array(
			'attachment_id' => $attachment_id,
			'min_width' => 640,
			'min_height' => 480,
			'size' => 'smaller'
			));

		//Extract the most used colors from the image.
		$extract_vars = array(
			'number' => $number,
			'image_path' => $image['path'],
			);
		$pw_colors = new PW_Colors();
		$colors = $pw_colors->extract_image_colors( $extract_vars );

		// Save the colors to the postmeta table
		pw_set_wp_postmeta(array(
			'post_id' 		=> $attachment_id,
			'meta_key' 		=> PW_COLORS_KEY,
			'meta_value' 	=> $colors
			));

	}

	return $colors;

}


/**
 * Gets a pre-registered attachment image size
 * Within particular guidelines. If the right image isn't found
 * the original attachment image is returned.
 * @param array $vars
 * @return array
 *	@example array(
 *				'url' => 'http://...jpg'
 *				'path' => '/var/www/...jpg'
 *				'width' => 640,
 *				'height' => 480,
 *				'area' => 307200,
 *				//'mime-type' => 'image/jpeg'
 *				)
 */
function pw_get_attachment_image_size( $vars ){

	$default_vars = array(
		'attachment_id' => null,
		'size' => 'smaller', 	// [string] larger|smaller
		//'fields' => array( 'url', 'path', 'width', 'height', 'area', 'mime-type' ),
		'min_width' => 0, 		// [int] In pixels
		'min_height' => 0, 		// [int] In pixels
		);
	$vars = array_replace($default_vars, $vars);

	if( empty( $vars['attachment_id'] ) )
		return false;

	// Get the image attachment metadata
	$metadata = wp_get_attachment_metadata( $vars['attachment_id'] );

	// If no sizes, or no data, return false
	$sizes = _get( $metadata, 'sizes' );
	if( empty( $sizes ) )
		return false;

	/**
	 * Iterate through all the image sizes
	 * Checking for the first image which fits
	 * The specified criteria.
	 */
	$matching_sizes = array();
	foreach( $sizes as $size ){
		if( $size['width'] >= $vars['min_width'] &&
			$size['height'] >= $vars['min_height'] ){

			$size['area'] = (int)$size['width'] * (int)$vars['min_height'];
			$matching_sizes[] = $size;
		
		}
	}

	// If matching sizes were found
	if( !empty( $matching_sizes ) ){
		// Order them according to their image area
		if( $vars['size'] == 'smaller' )
			$matching_sizes = pw_array_order_by( $matching_sizes, 'area', SORT_ASC );
		elseif( $vars['size'] == 'larger' )
			$matching_sizes = pw_array_order_by( $matching_sizes, 'area', SORT_DESC );
	
		$matching_size = $matching_sizes[0];

	}
	// If no matching sizes, get the original image data
	else{
		$matching_size = array(
			'file' => basename($metadata['file']),
			'width' => $metadata['width'],
			'height' => $metadata['height'],
			);
	}

	// Get WP vars
	$upload_dir = wp_upload_dir();
	$attached_file = get_attached_file( $vars['attachment_id'] );

	// Construct output
	$output = $matching_size;
	$output['path'] = str_replace( basename( $attached_file ), $matching_size['file'], $attached_file );
	$output['url'] = str_replace( $upload_dir['path'], $upload_dir['url'], $output['path'] );

	return $output;

}


/**
 * Get processed colors.
 */
function pw_get_processed_color_profiles( $thumbnail_id, $profiles = array() ){
	global $pwSiteGlobals;

	if( empty( $profiles ) )
		$profiles = _get( $pwSiteGlobals, 'colors.color_profiles' );

	

	// Get the images in hex format
	$hex_value = pw_get_image_colors($thumbnail_id);

	/**
	 * For each color profile, run the post image's colors
	 * Through the image meta processing mechanism.
	 */
	$pw_colors = new PW_Colors();
	$processed_profiles = array();
	foreach( $profiles as $profile_key => $profile_vars ){
		$profile_vars['hex_values'] = $hex_value;
		pw_log( 'profile_vars', $profile_vars );
		$processed_profiles[$profile_key] = $pw_colors->process_color_profile( $profile_vars );
	}

	return $processed_profiles;

}







