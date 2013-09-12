<?php



function extract_parenthesis_values ( $input, $force_array = false ){
	// Extracts comma deliniated values which are contained in parenthesis
	// Returns an Array of values that were previously comma deliniated,
	// unless $force_array is set TRUE.

	// Extract contents of (parenthesis)
	preg_match('#\((.*?)\)#', $input, $match);

	// Split into an Array
	$value_array = explode(',', $match[1]);

	// Remove extra white spaces from Array values
	foreach($value_array as $index => $value) 
			$value_array[$index] = trim($value);

	// If $value_array has only 1 item
	if ( count($value_array) == 1 && $force_array == false )
		return $value_array[0];
	// Otherwise, return array
	else
		return $value_array;
}


function extract_fields( $fields_array, $query_string ){
	// Extracts values starting with $query_string from $fields_array
	// and returns them in a new Array.

	$values_array = array();
	foreach ($fields_array as $field) {
		if ( strpos( $field, $query_string ) !== FALSE )
			// Push $field into $values_array
		    array_push($values_array, $field);
	}
	return $values_array;
}

function get_avatar_url( $user_id, $avatar_size ){

	// Get Buddypress Avatar Image
	if ( function_exists(bp_core_fetch_avatar) ) {

		// Set Buddypress Avatar 'Type' Attribute
		if ( $avatar_size > $bp_avatar_thumb_size )
			$bp_avatar_size = 'thumb';
		else
			$bp_avatar_size = 'full';

		// Set Buddypress Avatar Settings
		$bp_avatar_args = array(
			'item_id' => $user_id,
			'type' => $bp_avatar_size,
			'html' => false
			);

		return bp_core_fetch_avatar( $bp_avatar_args );
	}

	// Get Avatar Image with Wordpress Method (embedded in an image tag)
	else {
		$avatar_img = get_avatar( $user_id, $avatar_size );
		// Remove the image tag
		preg_match("/src='(.*?)'/i", $avatar_img, $matches);
	    return $matches[1];
	}

}


////////// GET POST DATA //////////
function get_post_data( $post_id, $fields='all', $viewer_user_id ){
	//• Gets data fields for the specified post
	global $template_paths;

	///// SETUP VARIABLES /////
	//

	////////// FIELDS MODEL //////////
	$preview_fields =	array(
		'ID',
		'post_title',
		'post_content',
		'post_excerpt',
		'post_permalink',
		'post_type',
		'post_date',
		'post_time_ago',
		'comment_count',
		'link_url',
		'image(thumbnail)',
		'points',
		'edit_post_link',
		'post_categories_list',
		'post_tags_list',
		'taxonomy_list(topic)',
		'author(ID,display_name,user_nicename,posts_url,profile_url)',
		'avatar(small,48)'
		);

	$detail_fields =	array(
		'post_path',
		'image(medium)',
		'image(large)',
		'image(full)',
		);
	
	$user_fields =		array(
		'user_vote',
		'user_data',
		'has_voted',
		);

	// Add Preview Fields
	if ($fields == 'preview')
		$fields = $preview_fields;

	// Add Detail Fields
	if ($fields == 'all'){
		$fields = array_merge($preview_fields, $detail_fields);
	}

	
	// Add User Fields of Current Logged in User who is viewing
	if (is_int($viewer_user_id)){
		// Get User Data
		$viewer_user_data = get_userdata( $viewer_user_id );
		// If user exists, add user fields
		if( $viewer_user_data != false ){
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

	///// SET LOCAL VALUES /////
	// Set Author ID
	$author_id = $get_post['post_author']; 

	////////// WORDPRESS //////////
	// Permalink
	if( in_array('post_permalink', $fields) )
		$post_data['post_permalink'] = get_permalink( $post_id );

	// Post Path (Permalink without Home url)
	if( in_array('post_path', $fields) )
		$post_data['post_path']	= str_replace( home_url(), '', get_permalink( $post_id ) );

	// Category List
	if( in_array('post_categories_list', $fields) )
		$post_data["post_categories_list"] = get_the_category_list(' ','', $id );

	// Tags List
	if( in_array('post_tags_list', $fields) ){
		$post_data["post_tags_list"] = get_the_term_list( $id, 'post_tag', '', '', '' );
		if ( $post_data["post_tags_list"] == false ) $post_data["post_tags_list"] = '';
	}


	////////// POSTWORLD //////////
	// Points
	if( in_array('points', $fields) )
		$post_data['points'] = get_points( $post_id );

	// User Has Voted
	if( in_array('has_voted', $fields) )
		$post_data['has_voted'] = has_voted( $post_id, $viewer_user_data->ID );


	////////// DATE & TIME //////////

	// Post Time Ago
	if ( in_array('post_time_ago', $fields) )
		$post_data['time_ago'] = '';


	////////// AVATAR IMAGES //////////
	// Extract avatar() fields
	$avatars = extract_fields( $fields, 'avatar' );
	
	// Get each avatar() image
	foreach ($avatars as $avatar) {
		// Extract image attributes from parenthesis
   		$avatar_attributes = extract_parenthesis_values($avatar, true);

   		// Check format
   		if ( count($avatar_attributes) == 2 && !is_numeric( $avatar_attributes[0]) && is_numeric( $avatar_attributes[1]) ){
   			// Setup Values
   			$avatar_handle = $avatar_attributes[0];
   			$avatar_size = $avatar_attributes[1];

   			// Set Avatar Size
   			$post_data['avatar'][$avatar_handle]['width'] = $avatar_size;
   			$post_data['avatar'][$avatar_handle]['height'] = $avatar_size;
			$post_data['avatar'][$avatar_handle]['url'] = get_avatar_url( $author_id, $avatar_size );

   		}

	} // END foreach



	////////// AUTHOR DATA //////////

		// Extract author() fields
		$author_fields_request = extract_fields( $fields, 'author' );

		///// PROCESS REQUESTED AUTHOR FIELDS /////
		// Check if there are any author fields requested
		if ( !empty($author_fields_request) ){

			// Create empty Array
			$author_fields = array();
			
			// Process each request one at a time >> author(display_name,user_name,posts_url) 
			foreach ($author_fields_request as $author_field_request) 
				$author_fields = array_merge( $author_fields, extract_parenthesis_values($author_field_request, true) );
   			

   			////////// GET AUTHOR FIELDS DATA //////////

   			// Standard Wordpress get_the_author_meta() fields
			$get_the_author_meta_fields = array('user_login','user_nicename','user_email','user_url','user_registered','user_activation_key',
				'user_status','display_name','nickname','first_name','last_name','description','jabber','aim','yim','user_level',
				'user_firstname','user_lastname','user_description','rich_editing','comment_shortcuts','admin_color','ID');

			///// USE GET_THE_AUTHOR_META() FUNCTION /////
			// Check if $author_fields are accessible by get_the_author_meta() Wordpress function
			// If so, pull in that data with that function
			foreach ($author_fields as $author_field) {
				if( in_array( $author_field, $get_the_author_meta_fields ) )
					$post_data['author'][$author_field] = get_the_author_meta( $author_field, $author_id );
			}

			///// POSTWORLD AUTHOR FIELDS /////
			/*
			if( in_array('posts_points', $author_fields) )
				$post_data['author']['posts_points'] = get_user_posts_points( $post_id );
			if( in_array('comments_points', $author_fields) )
				$post_data['author']['comments_points'] = get_user_comments_points( $post_id );
			*/

			///// WORDPRESS AUTHOR FIELDS /////
			
			// Author Posts URL
			if( in_array('posts_url', $author_fields) )
				$post_data['author']['posts_url'] = get_author_posts_url( $author_id );

			// Author Profile URL : requires Buddypress
			if( in_array('profile_url', $author_fields) && function_exists('bp_core_get_userlink') )
				$post_data['author']['profile_url'] = bp_core_get_userlink( $author_id, false, true );

			// ++ ADD : twitter, facebook_url, 
			

		} // END if


	////////// IMAGE FIELDS //////////

		// Extract image() fields
		$images = extract_fields( $fields, 'image' );
		
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
					$thumbnail_url = $first_image_obj['url'];
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
					
					// SETUP DEFAULT IMAGE urlS : http://...
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

				// Extract image attributes from parenthesis
   				$image_attributes = extract_parenthesis_values($image, true);

				// Set $image_handle to name of requested image
				$image_handle = $image_attributes[0];

				///// REGISTERED IMAGE SIZES /////
				// If image attributes contains only a handle
				if ( count($image_attributes) == 1 ){

					// Get 'full' image
					if ( $image_handle == 'full' ) {
						$image_obj = image_obj($thumbnail_id, $image_handle);
						$post_data['image']['full']['url']	= $thumbnail_url;
						$post_data['image']['full']['width'] = (int)$image_obj['width'];
						$post_data['image']['full']['height'] = (int)$image_obj['height'];
					}

					// Get registered image
					// If it is a registered image format
					elseif( array_key_exists($image_handle, $registered_images_obj) ) {
						$image_obj = image_obj($thumbnail_id, $image_handle);
						$post_data['image'][$image_handle]['url']	= $image_obj['url'];
						$post_data['image'][$image_handle]['width'] = (int)$image_obj['width'];
						$post_data['image'][$image_handle]['height'] = (int)$image_obj['width'];
					}

				} 
				///// CUSTOM IMAGE SIZES /////
				// If image attributes contains custom height and width parameters
				else {
					// Set image attributes
					$thumb_width = $image_attributes[1];
					$thumb_height = $image_attributes[2];
					$hard_crop = $image_attributes[3];
					if ( !$hard_crop )
						$hard_crop = 1;

					// Process custom image size, return url
					$post_data['images'][$image_handle]['url'] = aq_resize( $thumbnail_url, $thumb_width, $thumb_height, $hard_crop );
					$post_data['images'][$image_handle]['width'] = (int)$thumb_width;
					$post_data['images'][$image_handle]['height'] = (int)$thumb_height;
				}

			} // END foreeach

		} // END if



	return json_encode($post_data);

}


?>