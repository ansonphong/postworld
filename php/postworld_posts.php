<?php

////////// GET POST DATA //////////
function get_post_data( $post_id, $fields='all', $user_id ){
	//• Gets data fields for the specified post
	global $template_paths;

	///// SETUP VARIABLES /////
	//

	////////// FIELDS MODEL //////////
	$preview_fields =	array(
		'ID',
		'post_title',
		'post_excerpt',
		'post_permalink',
		'post_path',
		'post_type',
		'post_date',
		'post_time_ago',
		'comment_count',
		'link_url',
		'points',
		'image(thumbnail)',
		);

	$detail_fields =	array(
		'image(medium)',
		'image(large)',
		'image(full)',
		
		'image(topview,300,200,1)',
		'has_voted',
		//'image'	=> 'original',
		);
	
	$user_fields =		array(
		'user_vote',
		'user_data'
		);

	// Add Preview Fields
	if ($fields == 'preview')
		$fields = $preview_fields;

	// Add Detail Fields
	if ($fields == 'all'){
		$fields = array_merge($preview_fields, $detail_fields);
	}

	// Add User Fields
	if (is_int($user_id)){
		// Get User Data
		$user_data = get_userdata( $user_id );
		// If user exists, add user fields
		if( $user_data != false ){
			$fields = array_merge($fields, $user_fields);
		}
	}

	////////// WP GET_POST METHOD //////////
	// Get post data from Wordpress standard function
	$get_post = get_post($post_id, ARRAY_A);
	foreach ($get_post as $key => $value) {
		if( in_array($key, $fields) )
			$post_data[$key] = $value;
	}

	////////// WP GET_POST_CUSTOM METHOD //////////
	// Get post data from Wordpress standard function
	$get_post_custom = get_post_custom($post_id);
	foreach ($get_post_custom as $key => $value) {
		if( in_array($key, $fields) )
			$post_data[$key] = $value;
	} 

	////////// POSTWORLD //////////
	// Points
	if( in_array('points', $fields) )
		$post_data['points'] = get_points( $post_id );

	// User Has Voted
	if( in_array('has_voted', $fields) )
		$post_data['has_voted'] = has_voted( $post_id, $user_data->ID );


	////////// IMAGE FIELDS //////////
		///// EXTRACT IMAGE FIELDS /////
		// Into $images Array
		$images = array();
		foreach ($fields as $field) {
			if (strpos($field, 'image') !== FALSE)
				// Push $field into $images Array
			    array_push($images, $field);
		}
		
		///// PROCESS IMAGE FIELDS /////
		// Check if there are images to process
		if ( !empty($images) ){

			///// GET IMAGE TO USE /////
			// Setup Thumbnail Image Variables
			$thumbnail_id = get_post_thumbnail_id( $post_id );
			
			// If there is a set 'featured image' set the $thumbnail_url
			if ( $thumbnail_id ){
				$thumbnail_url = wp_get_attachment_url( $thumbnail_id ,'full');
			}
			// If there is no set 'featured image', get fallback - first image in post
			else {
				$first_image_obj = first_image_obj($id);
				// If there is an image in the post
				if ($first_image_obj){
					$thumbnail_url = $first_image_obj['URL'];
				}
				// If there is no image in the post, set fallbacks
				else {
					///// DEFAULT FALLBACK IMAGES /////

					// SETUP DEFAULT IMAGE FILE NAMES : ...jpg
					$default_type_format_thumb_filename = 	'default-'.$post_data['post_type'].'-'.$post_data['post_format'].'-thumb.jpg';
					$default_format_thumb_filename = 		'default-'.$post_data['post_format'].'-thumb.jpg';
					$default_thumb_filename = 				'default-thumb.jpg';

					// SETUP DEFAULT IMAGE PATHS : /home/user/...
					$theme_images_dir = 				$template_paths['THEME_PATH'].$template_paths['IMAGES_PATH'];
					$default_type_format_thumb_path = 	$theme_images_dir . $default_type_format_thumb_filename;
					$default_format_thumb_path = 		$theme_images_dir . $default_format_thumb_filename;
					$default_thumb_path = 				$theme_images_dir . $default_thumb_filename;
					
					// SETUP DEFAULT IMAGE URLS : http://...
					$theme_images_url = 				$template_paths['THEME_URL'].$template_paths['IMAGES_PATH'];
					$default_type_format_thumb_url = 	$theme_images_url . $default_type_format_thumb_filename;
					$default_format_thumb_url = 		$theme_images_url . $default_format_thumb_filename;
					$default_thumb_url = 				$theme_images_url . $default_thumb_filename;
					
					// SET DEFAULT POST *TYPE + FORMAT* IMAGE PATH
					if ( file_exists( $default_type_format_thumb_path ) ) {
						$thumbnail_url = $default_type_format_thumb_url;
					}
					// SET DEFAULT POST *FORMAT* IMAGE PATH
					elseif ( file_exists( $default_format_thumb_path ) ) {
						$thumbnail_url = $default_format_thumb_url;
					}
					// SET DEFAULT POST IMAGE PATH
					elseif ( file_exists( $default_thumb_path ) ) {
						$thumbnail_url = $default_thumb_url;
					}
					// SET DEFAULT POST IMAGE PATH TO PLUGIN DEFAULT
					else{
						$thumbnail_url = $template_paths['PLUGINS_URL'].$template_paths['IMAGES_PATH'].$default_thumb_filename;
					}

				} // END else

			}// END else


			///// PROCESS IMAGES /////
			// Load in registered images attributes
			$registered_images_obj = registered_images_obj();

			// Process each $image one at a time >> image(name,300,200,1) 
			foreach ($images as $image) {
				// Extract image attributes from (parenthesis) >> name,300,200,1
				preg_match('#\((.*?)\)#', $image, $match);
				// Split into an Array of $image_attributes >> array('name','300','200','1' )
				$image_attributes = explode(',', $match[1]);

				// Set $image_handle to name of requested image
				$image_handle = $image_attributes[0];

				///// REGISTERED IMAGE SIZES /////
				// If image attributes contains only a handle for 'full' or registered image sizes
				if ( count($image_attributes) == 1 ){

					// Get 'full' image
					if ( $image_handle == 'full' ) {
						$image_obj = image_obj($thumbnail_id, $image_handle);
						$post_data['images']['full']['URL']	= $thumbnail_url;
						$post_data['images']['full']['width'] = $image_obj['width'];
						$post_data['images']['full']['height'] = $image_obj['height'];
					}

					// Get registered image
					// If it is a registered image format
					elseif( array_key_exists($image_handle, $registered_images_obj) ) {
						$image_obj = image_obj($thumbnail_id, $image_handle);
						$post_data['images'][$image_handle]['URL']	= $image_obj['URL'];
						$post_data['images'][$image_handle]['width'] = $image_obj['width'];
						$post_data['images'][$image_handle]['height'] = $image_obj['width'];
					}

				} 
				///// CUSTOM IMAGE SIZES /////
				// If image attributes contains custom height and width parameters
				else {
					// Set image attributes
					$thumb_width = $image_attributes[1];
					$thumb_height = $image_attributes[2];
					$hard_crop = $image_attributes[3];

					// Process custom image size, return URL
					$post_data['images'][$image_handle]['URL'] = aq_resize( $thumbnail_url, $thumb_width, $thumb_height, $hard_crop );
					$post_data['images'][$image_handle]['width'] = $thumb_width;
					$post_data['images'][$image_handle]['height'] = $thumb_height;
				}

			} // END foreeach

		} // END if



	return json_encode($post_data);

}


?>