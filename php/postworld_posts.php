<?php


function pw_get_posts( $post_ids, $fields='all', $viewer_user_id=null ) {
	// • Run pw_post_data on each of the $post_ids, and return the given fields

	// If $post_ids isn't an Array, return
	if (!is_array($post_ids))
		return false;

	// Cycle though each $post_id
	$posts = array();
	foreach ($post_ids as $post_id) {
		$post = pw_get_post($post_id, $fields, $viewer_user_id );
		array_push($posts, $post);
	}

	// Return Array of post data
	return $posts;

}

////////// GET POST DATA //////////
function pw_get_post( $post_id, $fields='all', $viewer_user_id=null ){
	//• Gets data fields for the specified post

	// Check if the post exists
	global $wpdb;
	$post_exists = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE id = '" . $post_id . "'", 'ARRAY_A');
	if (!$post_exists)
		return false;


	///// SETUP VARIABLES /////
	global $template_paths;


	////////// FIELDS MODEL //////////
	$preview_fields =	array(
		'ID',
		'post_title',
		'post_content',
		'post_excerpt',
		'post_permalink',
		'post_type',
		'post_date',
		'post_date_gmt',
		'comment_count',
		'link_url',
		'image(thumbnail)',
		'post_points',
		'edit_post_link',
		'post_categories_list',
		'post_tags_list',

		'taxonomy(post_tag)',
		'taxonomy(category)',

		'author(ID,display_name,user_nicename,posts_url,profile_url)',
		'avatar(small,48)',

		'post_format',
		'time_ago',

		);

	$detail_fields =	array(
		'post_path',
		'image(medium)',
		'image(large)',
		'image(full)',
		);
	
	$viewer_fields =		array(
		'viewer(has_voted,vote_power)'
		);

	// Add Preview Fields
	if ($fields == 'preview')
		$fields = $preview_fields;

	// Add Detail Fields
	if ($fields == 'all'){
		$fields = array_merge($preview_fields, $detail_fields);
	}

	///// ADD VIEWER USER /////
	// Check if the $viewer_user_id is supplied - if not, get it
	if ( !$viewer_user_id ){
		$viewer_user_id = get_current_user_id();
	}

	// Add User Fields of Current Logged in User who is viewing
	if ( is_int($viewer_user_id) && $viewer_user_id != 0 ){
		// Get User Data
		$viewer_user_data = get_userdata( $viewer_user_id );
		// If user exists, add user fields
		if( $viewer_user_data != false ){
			$fields = array_merge($fields, $viewer_fields);
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

		// Get post row from Postworld Meta table, as an Array
		$pw_post_meta = pw_get_post_meta($post_id);
		if ( !empty($pw_post_meta) ){
			// Cycle though each PW $pw_post_meta value
			// If it's in $fields, transfer it to $post_data
			foreach($pw_post_meta as $key => $value ){
				if( in_array($key, $fields) ){
					$post_data[$key] = $pw_post_meta[$key];
				}
			}
		}

		// Points
		if( in_array('post_points', $fields) ){
			$post_data['post_points'] = get_post_points( $post_id );
		}


	////////// VIEWER FIELDS //////////

		// Extract viewer() fields
		$viewer_fields = extract_linear_fields( $fields, 'viewer', true );

		if ( !empty($viewer_fields) ){
			///// GET VIEWER DATA /////
			// Has Viewer Voted?
			if( in_array('has_voted', $viewer_fields) )
				$post_data['viewer']['has_voted'] = has_voted_on_post( $post_id, $viewer_user_data->ID );

			// View Vote Power
			if( in_array('vote_power', $viewer_fields) )
				$post_data['viewer']['vote_power'] = get_user_vote_power( $viewer_user_data->ID );
		
		}



	////////// DATE & TIME //////////

		// Post Time Ago
		if ( in_array('time_ago', $fields) )
			$post_data['time_ago'] = time_ago( strtotime ( $post_data['post_date_gmt'] ) );




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
		$author_fields = extract_linear_fields( $fields, 'author', true );

		if ( !empty($author_fields) ){

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

			///// WORDPRESS AUTHOR FIELDS /////
			
			// Author Posts URL
			if( in_array('posts_url', $author_fields) )
				$post_data['author']['posts_url'] = get_author_posts_url( $author_id );


			///// BUDDYPRESS AUTHOR FIELDS : requires Buddypress /////

			// Author Profile URL
			if( in_array('profile_url', $author_fields) && function_exists('bp_core_get_userlink') )
				$post_data['author']['profile_url'] = bp_core_get_userlink( $author_id, false, true );


			///// POSTWORLD AUTHOR FIELDS /////
			/*
			if( in_array('posts_points', $author_fields) )
				$post_data['author']['posts_points'] = get_user_posts_points( $post_id );
			if( in_array('comments_points', $author_fields) )
				$post_data['author']['comments_points'] = get_user_comments_points( $post_id );
			*/

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



	////////// TAXONOMIES //////////

	// Extract taxonomy() fields
	$taxonomy_fields = extract_linear_fields( $fields, 'taxonomy' );

	// Get each Taxonomy 
	foreach ($taxonomy_fields as $taxonomy_field) {

		$taxonomy_terms = wp_get_object_terms( $post_id, $taxonomy_field );
		if ( !empty($taxonomy_terms) && !is_wp_error( $taxonomy_terms ) ){

			// Get each Term
			foreach($taxonomy_terms as $term){
				$term_obj['term'] = $term->name;
				$term_obj['slug'] = $term->slug;
				$term_obj['url'] = get_term_link($term->slug, $taxonomy_field);

				$post_data['taxonomy'][$taxonomy_field][$term->slug] = $term_obj;
			}

		} // END if

	} // END foreach



	return $post_data;

}

?>