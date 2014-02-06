<?php


function pw_post_exists ( $post_id ){
	// Check if a post exists
	global $wpdb;
	$post_exists = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE id = '" . $post_id . "'", 'ARRAY_A');
	if ( !empty($post_exists) )
	    return true;
	else
	    return false;
}


function pw_get_posts( $post_ids, $fields='all' ) {
	// • Run pw_post_data on each of the $post_ids, and return the given fields
	if($fields == null) $fields='all';
	// If $post_ids isn't an Array, return
	if (!is_array($post_ids))
		return false;

	// Cycle though each $post_id
	$posts = array();
	foreach ($post_ids as $post_id) {
		$post = pw_get_post($post_id, $fields );
		array_push($posts, $post);
	}

	// Return Array of post data
	return $posts;

}

////////// GET POST DATA //////////
function pw_get_post( $post_id, $fields='all', $viewer_user_id=null ){
	
	// Switch Modes (view/edit)
	// 'Edit' mode toggles content display filtering (oEmbed, shortcodes, etc)
	$mode = 'view';

	//• Gets data fields for the specified post
	if($fields == null) $fields='all';
	if(gettype($post_id) == "array") $post_id = $post_id['ID'];	
	else if(gettype($post_id) == "object")  {
		  $post_id =  $post_id->ID;
			//echo $post_id;
	}	
	// Check if the post exists
	global $wpdb;
	$post_exists = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE id = '" . $post_id . "'", 'ARRAY_A');
	if (!$post_exists)
		return false;

	///// SETUP VARIABLES /////
	global $template_paths;

	$edit_fields = array(
		'ID',
		'post_id',
		'post_type',
		'post_status',
		'post_title',
		'post_content',
		'post_excerpt',
		'post_name',
		'post_permalink',
		'post_date',
		'post_date_gmt',
		'post_class',
		'link_format',
		'link_url',
		'image(id)',
		'image(meta)',
		'taxonomy(all)',
		'taxonomy_obj(post_tag)',
		'comment_status',
		'author(ID,display_name,user_nicename,posts_url,user_profile_url)',
		'post_meta(all)',
		'post_parent',
		);

	////////// FIELDS MODEL //////////
	$preview_fields =	array(
		'ID',
		'post_title',
		'post_excerpt',
		'post_permalink',
		'post_type',
		'post_status',
		'post_date',
		'post_date_gmt',
		'comment_count',
		'link_url',
		'image(thumbnail)',
		//'image(medium)',
		'image(all)',
		'post_points',
		'edit_post_link',
		'post_categories_list',
		'post_tags_list',
		'taxonomy(all)',
		'author(ID,display_name,user_nicename,posts_url,user_profile_url)',
		'avatar(small,48)',
		'link_format',
		'time_ago',
		'post_meta(all)',
		'rank_score',
		);

	$detail_fields =	array(
		'post_path',
		'image(full)',
		'post_content',
		);
	
	$micro_fields =	array(
		'post_title',
		);

	$viewer_fields =array(
		'viewer(has_voted,is_favorite,is_view_later)',
		);

	// All Fields
	if ($fields == 'all'){
		$fields = array_merge($preview_fields, $detail_fields, $viewer_fields);
	}

	// Preview Fields
	else if ($fields == 'preview')
		$fields = $preview_fields;

	// Edit Fields
	else if ($fields == 'edit'){
		$fields = $edit_fields;
		$mode = 'edit';
	}

	// Micro Fields
	else if ($fields == 'micro')
		$fields = $micro_fields;

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
		//if( $viewer_user_data != false ){
		//	$fields = array_merge($fields, $viewer_fields);
		//}
	} else if ($viewer_user_id == 0){

	}
	//$fields = array_merge($fields, $viewer_fields);
	
	////////// WP GET_POST METHOD //////////
	// Get post data from Wordpress standard function
	$get_post = get_post($post_id, ARRAY_A);
	foreach ($get_post as $key => $value) {
		if( in_array($key, $fields) ){
			$post_data[$key] = $value;

			if ( $key == 'post_content' && $mode == 'view' ){
				///// CONTENT FILTERING /////
				// oEmbed URLs
				$post_data[$key] = pw_embed_content($post_data[$key]);
				// Apply Shortcodes
				$post_data[$key] = do_shortcode($post_data[$key]);
			}
			
		}
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
			$post_data["post_categories_list"] = get_the_category_list(' ','', $post_id );

		// Tags List
		if( in_array('post_tags_list', $fields) ){
			$post_data["post_tags_list"] = get_the_term_list( $post_id, 'post_tag', '', '', '' );
			if ( $post_data["post_tags_list"] == false ) $post_data["post_tags_list"] = '';
		}

		// Edit Post Link
		if( in_array('edit_post_link', $fields) ){
			$post_data["edit_post_link"] = get_edit_post_link($post_id);
			if ( $post_data["edit_post_link"] == false ) $post_data["edit_post_link"] = '#';
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
				$post_data['viewer']['has_voted'] = has_voted_on_post( $post_id, $viewer_user_id );

			// View Vote Power
			if( in_array('vote_power', $viewer_fields) )
				$post_data['viewer']['vote_power'] = get_user_vote_power( $viewer_user_id );
		
			// Is Favorite
			if( in_array('is_favorite', $viewer_fields) ){
				$is_favorite = is_favorite( $post_id );
				if ( !isset($is_favorite) )
					$is_favorite = "0";
				$post_data['viewer']['is_favorite'] = $is_favorite;
			}

			// Is View Later
			if( in_array('is_view_later', $viewer_fields) )
				$post_data['viewer']['is_view_later'] = is_view_later( $post_id );

		}

	////////// DATE & TIME //////////

		// Post Time Ago
		if ( in_array('time_ago', $fields) )
			$post_data['time_ago'] = time_ago( strtotime ( $post_data['post_date_gmt'] ) );


	////////// AVATAR IMAGES //////////

		// AVATAR FIELDS
		$avatars_object = get_avatar_sizes($author_id, $fields);
		if ( !empty($avatars_object) )
			$post_data["avatar"] = $avatars_object;


	////////// META DATA //////////
		
		// Extract meta fields
		$post_meta_fields = extract_linear_fields( $fields, 'post_meta', true );
		if ( !empty($post_meta_fields) ){
			// CYCLE THROUGH AND FIND EACH REQUESTED FIELD
			foreach ($post_meta_fields as $post_meta_field ) {
				// GET 'ALL' FIELDS
				if ( $post_meta_field == "all" ){
					// Return all meta data
					$post_data['post_meta'] = get_metadata('post', $post_id, '', true);
					// Convert to strings
					if ( !empty( $post_data['post_meta'] ) ){
						foreach( $post_data['post_meta'] as $meta_key => $meta_value ){
							$post_data['post_meta'][$meta_key] = $post_data['post_meta'][$meta_key][0];
						}
					}
				}
			}
		}



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
			if( in_array('user_profile_url', $author_fields) && function_exists('bp_core_get_userlink') )
				$post_data['author']['user_profile_url'] = bp_core_get_userlink( $author_id, false, true );

			///// POSTWORLD AUTHOR FIELDS /////
			/*
			if( in_array('posts_points', $author_fields) )
				$post_data['author']['posts_points'] = get_user_post_points( $post_id );
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
				$first_image_obj = first_image_obj($post_id);
				// If there is an image in the post
				if ($first_image_obj){
					$thumbnail_url = $first_image_obj['url'];
				}
				// If there is no image in the post, set fallbacks
				else {
					///// DEFAULT FALLBACK IMAGES /////

					// SETUP DEFAULT IMAGE FILE NAMES : ...jpg
					$link_format =  get_post_format( $post_id );
					$default_type_format_thumb_filename = 	'default-'.$post_data['post_type'].'-'.$link_format.'-thumb.jpg';
					$default_format_thumb_filename = 		'default-'.$link_format.'-thumb.jpg';
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

					// FULL : Get 'full' image
					if ( $image_handle == 'full' ) {
						$image_obj = image_obj($thumbnail_id, $image_handle);
						$post_data['image']['full']['url']	= $thumbnail_url;
						$post_data['image']['full']['width'] = (int)$image_obj['width'];
						$post_data['image']['full']['height'] = (int)$image_obj['height'];
					}

					// ALL : Get all registered images
					elseif( $image_handle == 'all' ) {
						$registered_images = registered_images_obj();

						foreach( $registered_images as $image_handle => $image_attributes ){
							$image_src = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), $image_handle );
							$registered_images[$image_handle]["url"] = $image_src[0];
							$registered_images[$image_handle]["width"] = $image_src[1];
							$registered_images[$image_handle]["height"] = $image_src[2];
							$registered_images[$image_handle]["hard_crop"] = $image_src[3];
						}

						$post_data['image'] = array_merge( $post_data['image'], $registered_images );
					}

					// HANDLE : Get registered image
					// If it is a registered image format
					elseif( array_key_exists($image_handle, $registered_images_obj) ) {
						$image_obj = image_obj($thumbnail_id, $image_handle);
						$post_data['image'][$image_handle]['url']	= $image_obj['url'];
						$post_data['image'][$image_handle]['width'] = (int)$image_obj['width'];
						$post_data['image'][$image_handle]['height'] = (int)$image_obj['width'];
					}

					// META : Get Image Meta Data
					elseif( $image_handle == 'meta' && is_numeric($thumbnail_id) ){
						$post_data['image']['meta'] = wp_get_attachment_metadata($thumbnail_id);

						// Get the actual file URLS and inject into the object
						if( isset($post_data['image']['meta']) && is_array($post_data['image']['meta']) ){
							
							foreach( $post_data['image']['meta']['sizes'] as $key => $value ){
								$image_size_meta = wp_get_attachment_image_src( $thumbnail_id, $key );
								$post_data['image']['meta']['sizes'][$key]['url'] = $image_size_meta[0];
							}
						}

					}
					// Get Image ID
					elseif( $image_handle == 'id' ){
						$post_data['thumbnail_id']= $thumbnail_id;

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
	$taxonomy_fields = extract_hierarchical_fields( $fields, 'taxonomy' );

	// ALL : If *all* taxonomies requested via taxonomy(all)
	/*
	if ( in_array('all', $taxonomy_fields) ){
		// QUERY TAXONOMIES
		$taxonomy_args = array('public'   => true ); 
        $the_taxonomies = get_taxonomies($taxonomy_args);
		
		// EXTRACT TAXONOMY NAMES
        $taxonomy_names = array();
        foreach( $the_taxonomies as $key => $value){
            array_push($taxonomy_names, $key);
        }
        $taxonomy_fields = $taxonomy_names;
	}*/
	
	if( !empty($taxonomy_fields) ){
		foreach( $taxonomy_fields as $taxonomy => $tax_fields ){

			
			// ALL : If *all* taxonomies requested via taxonomy(all)
			// Clear and populate all taxonomies with the sub-fields defined in 'all'
			if($taxonomy == 'all'){
				// Clear the Taxonomy field, start from scratch
				$taxonomy_fields = array();

				// QUERY TAXONOMIES
				$taxonomy_args = array('public'   => true ); 
		        $all_taxonomies = get_taxonomies($taxonomy_args, 'names');

		        // Over-write all taxonomies with sub-fields from 'all'
		        foreach ($all_taxonomies as $this_taxonomy) {
		        	$taxonomy_fields[$this_taxonomy] = $tax_fields;
		        }
		        break; // END FOREACH
			}
		}

		// Get each Taxonomy Field
		foreach ($taxonomy_fields as $taxonomy => $tax_fields) {
			/*
				INPUT MODEL :
				taxonomy_fields = {
					"category":["id","name"],
					"topic":["id","slug"],
					"section":["id","slug"],
					"post_tag":[""]
				}
			*/

			// Taxonomy Field Options
			$taxonomy_field_options = array(
				'term_id',
				'name',
				'slug',
				'description',
				'parent',
				'count',
				'taxonomy',
				'term_group',
				'url',
				);

			// If $fields is Empty or 'all', set to all field_options
			if ( empty($tax_fields[0]) || $tax_fields[0] == 'all' ){
				$tax_fields = $taxonomy_field_options;
			}

			// Get the post's terms for this taxonomy as an Array
			$post_terms =  wp_get_object_terms( $post_id, $taxonomy );
			$post_terms = (array) $post_terms;
			
			// TEMP :
			//$post_data['taxonomy'][$taxonomy] =  $post_terms;
			
			$post_data['taxonomy'][$taxonomy] = array();

			///// FOR EACH TERM /////
			foreach ($post_terms as $post_term) {
				$post_term = (array) $post_term;

				// If there is multiple taxonomy fields
				if( count($tax_fields) > 1 ){
					$term_obj = array();

					///// FOR EACH FIELD /////
					foreach ($tax_fields as $tax_field) {
						if ($tax_field != 'url')
							$term_obj[$tax_field] = $post_term[$tax_field];
					}
					// Get the URL field
					if( in_array('url', $tax_fields) ){
						$term_id = (int) $post_term['term_id'];
						$term_obj['url'] = get_term_link( $term_id , $taxonomy );
					}
					// Push the multi-dimensional Array of values
					array_push( $post_data['taxonomy'][$taxonomy], $term_obj );
				}
				// If there is only one taxonomy field
				else {
					// Just push the single term value in flat Array
					$term_field = $tax_fields[0];
					$term_value = $post_term[ $term_field ];
					array_push( $post_data['taxonomy'][$taxonomy], $term_value );
				}
				
			}

		} // END foreach
	} // END IF

	return $post_data;

}


function pw_insert_post ( $postarr, $wp_error = TRUE ){
	
	/*
	  
	 * Extends wp_insert_post : http://codex.wordpress.org/Function_Reference/wp_insert_post
		Include additional Postworld fields as inputs
		
	 * Parameters : $post Array
		All fields in wp_insert_post() Method
		post_class
		link_format
		link_url
		external_image
		 
	return :
	post_id - If added to the database, otherwise return WP_Error Object
	 
	* */
	//return json_encode($postarr);
	$post_ID = wp_insert_post( $postarr, $wp_error );
	
	if(gettype($post_ID) == 'integer'){ // successful

		///// ADD TERMS / TAXONOMIES //////
		if(isset($postarr["tax_input"])){
			foreach ( $postarr["tax_input"] as $taxonomy => $terms) {
				wp_set_object_terms( $post_ID, $terms, $taxonomy, false );
			}
		}
	
		///// ADD/UPDATE META FIELDS //////
		if( isset($postarr["post_meta"]) ){
			foreach ( $postarr["post_meta"] as $meta_key => $meta_value ) {

				// ENCODE ARRAYS AS JSON
				if( is_array($meta_value) )
					$meta_value = json_encode($meta_value);

				// CHECK FOR EXISTING VALUE
				$get_meta_value = get_post_meta( $post_ID, $meta_key, true );

				if ( !empty( $get_meta_value ) )
					// UPDATE META
					update_post_meta($post_ID, $meta_key, $meta_value);
				else
					// ADD META
					add_post_meta($post_ID, $meta_key, $meta_value, true);
			
			}
		}


		///// ADD POSTWORLD FIELDS //////
		if(isset($postarr["post_class"]) || isset($postarr["link_format"])|| isset($postarr["link_url"]))	{
			global $wpdb;
			$wpdb -> show_errors();
			
			add_record_to_post_meta($post_ID);

			$query = "update $wpdb->pw_prefix"."post_meta set ";
			 $insertComma = FALSE;
			if(isset($postarr["post_class"])){
				$query.="post_class='".$postarr["post_class"]."'";
				 $insertComma= TRUE;
			} 
			if(isset($postarr["link_format"])){
				if($insertComma === TRUE) $query.=" , ";
				$query.="link_format='".$postarr["link_format"]."'";
				 $insertComma= TRUE;
			} 
			if(isset($postarr["link_url"])){
				if($insertComma === TRUE) $query.=" , ";
				$query.="link_url='".$postarr["link_url"]."'";
				 $insertComma= TRUE;
			} 
			if(isset($postarr["post_author"])){
				if($insertComma === TRUE) $query.=" , ";
				$query.="author_id=".$postarr["post_author"];
				 $insertComma= TRUE;
			} 
		 	if($insertComma === FALSE ){}
			else{
				$query.=" where post_id=".$post_ID ;
				//echo $query;
 				$wpdb->query($query);
				
			}

		}


		///// AUTHOR NAME/SLUG FIELD /////
		// Adds support for an `author_name` parameter
		if( isset($postarr["post_author_name"]) ){
			$user = get_user_by( 'slug', $postarr["post_author_name"] );
			if( isset($user->data->ID) && current_user_can('edit_others_posts') ){
				wp_update_post( array( "ID" => $post_ID, "post_author" => $user->data->ID ) );
			}
		}
		


	}
	

	return $post_ID;

}

function pw_update_post ( $postarr ,$wp_error = TRUE){
	/*
	Extends wp_update_post() : http://codex.wordpress.org/Function_Reference/wp_update_post
	Include additional Postworld fields as inputs (see pw_insert_post() ) 
	 */
	 
	
	if ( is_object($postarr) ) {
		// non-escaped post was passed
		$postarr = get_object_vars($postarr);
		$postarr = wp_slash($postarr);
	}

	// First, get all of the original fields
	$post = get_post($postarr['ID'], ARRAY_A);
	
	//print_r($post);

	if ( is_null( $post ) ) {
		if ( $wp_error )
			return new WP_Error( 'invalid_post', __( 'Invalid post ID.' ) );
		return 0;
	}

	// Escape data pulled from DB.
	//$post = wp_slash($post);

	// Passed post category list overwrites existing category list if not empty.
	if ( isset($postarr['post_category']) && is_array($postarr['post_category'])
			 && 0 != count($postarr['post_category']) )
		$post_cats = $postarr['post_category'];
	else
		$post_cats = $post['post_category'];

	// Drafts shouldn't be assigned a date unless explicitly done so by the user
	if ( isset( $post['post_status'] ) && in_array($post['post_status'], array('draft', 'pending', 'auto-draft')) && empty($postarr['edit_date']) &&
			 ('0000-00-00 00:00:00' == $post['post_date_gmt']) )
		$clear_date = true;
	else
		$clear_date = false;

	// Merge old and new fields with new fields overwriting old ones.
	$postarr = array_merge($post, $postarr);
	$postarr['post_category'] = $post_cats;
	if ( $clear_date ) {
		$postarr['post_date'] = current_time('mysql');
		$postarr['post_date_gmt'] = '';
	}

	if ($postarr['post_type'] == 'attachment')
		return wp_insert_attachment($postarr);

	//print_r($postarr);
	return pw_insert_post( $postarr, $wp_error );

}


function pw_set_post_thumbnail( $post_id, $image ){

	///// ADD ID FROM MEDIA LIBRARY /////
	// If $image is an ID of an attachment in the Media Library
	if ( is_int($image) || is_numeric($image) ){
		$image_set = set_post_thumbnail( $post_id, $image );
		if ($image_set == true)
			return $image;
		else
			return array( 'error' => 'Not a valid Media Library ID.' );
	}
	// If it's empty, unset it
	elseif ( $image == 'delete' ){
		delete_post_thumbnail($post_id);
		return '0';
	}
	// Otherwise, assume it's a URL
	else{
		$attach_id = url_to_media_library($image,$post_id);
		if( is_numeric($attach_id) ){
			set_post_thumbnail( $post_id, $attach_id );
		}
		return $attach_id;
	}
}


function pw_trash_post( $post_id ){

	////////// SECURITY CHECKPOINT //////////
	// CHECK : ID
	$current_user_id = get_current_user_id();
	// GET : USERDATA
	$current_userdata = (array) get_userdata( $current_user_id );

	// Does post exist?
	if( pw_post_exists( $post_id ) ){ 

		///// GENERATE : ARRAY OF REQUIRED CAPABILITIES /////
		// The required capabilities of current action	
		$required_capabilities = array();
		// DETECT POST TYPE
		$post = get_post($post_id, "ARRAY_A");
		$post_type = $post["post_type"];
		// Does user own post?
		if( $current_user_id == $post["post_author"] ){
			// REQUIRE : DELETE
			array_push( $required_capabilities,"delete_".$post_type."s" );
		} else {
			// REQUIRE : DELETE OTHERS
			array_push( $required_capabilities,"delete_others_".$post_type."s" );
		}
		// Is the post published?
		if( $post["post_status"] == "published" ){
			// REQUIRE : DELETE PUBLISHED
			array_push( $required_capabilities,"delete_published_".$post_type."s" );

		} else if( $post["post_status"] == "private" ){
			// REQUIRE : DELETE PRIVATE
			array_push( $required_capabilities,"delete_private_".$post_type."s" );
		}

		///// VALIDATE CAPABILITIES /////
		// Compare required capabilities to array of current user's capabilities
		$pass = true;
		$no_capabilities = array();
		// Cycle through each required capability
		foreach( $required_capabilities as $cap ){
			// Does it not match value : true : in the current user's
			if ( $current_userdata['allcaps'][ $cap ] != true )
				// If any one is false, it sets false
				$pass = false;
			// Push failed capability to error message
			array_push( $no_capabilities, $cap );
		}
		if ( $pass == false ){
			return "User No Capabilities: " . json_encode( $no_capabilities );
		} else {
			$wp_trash_post = wp_trash_post( $post_id );
			if( $wp_trash_post == false )
				return array( "error" => "Unknown error.");
			else
				return $post_id;
		}

	} else {
		// If post does not exist
		return "Post does not exist with ID : ". $post_id;
	}

}


function detect_post_type( $post_data ){
	// $post_data = array( "ID"=>1, ["post_type"=>"post"] );

	///// DETECT : THE CURRENT POST TYPE //////
	// Check if there's a post_type set
	if( isset($post_data["post_type"]) ){
		// DEFINE : Post Type
		return $post_data["post_type"];
		// Check if the post exists
	} else if( pw_post_exists( $post_data["ID"] ) ) {
		// If it does, get the post_type
		$post = get_post( $post_data["ID"], "ARRAY_A" );
		return $post["post_type"];
		// Set Default
	} else {
		// DEFINE : Default Post Type
		return "post";
	}
}



function pw_save_post($post_data){

	extract($post_data);
	
	// Create forgeign keys
	if ( !empty($ID) ){
		$ID = (int) $ID;
		$post_id = $ID;
	}
	
	////////// SECURITY CHECKPOINT //////////
	// CHECK : ID
	$current_user_id = get_current_user_id();
	// GET : USERDATA
	$current_userdata = (array) get_userdata( $current_user_id );

	////////// CHECK : POST TYPE ACCESS //////////
	$post_type = detect_post_type( $post_data );

		
	///// GENERATE : ARRAY OF REQUIRED CAPABILITIES /////
	// The required capabilities of current action
	$required_capabilities = array();
	// Is a post ID not defined?
	if( !isset( $post_data["ID"] ) ){
		// REQUIRE : CREATION
		array_push( $required_capabilities,"edit_".$post_type."s" );
		// Does the post exist?
	} else if( pw_post_exists( $post_data["ID"] ) ){
		// GET : THE POST
		$post = get_post( $post_data["ID"], "ARRAY_A");
		// Does the current user own the post?
		if( $current_userdata["ID"] == $post["post_author"] ){
			// REQUIRE : EDITING
			array_push( $required_capabilities, "edit_".$post_type."s" );
		} else{
			// REQUIRE : EDITING OTHERS
			array_push( $required_capabilities, "edit_others_".$post_type."s" );
		}
		// Is the post published?
		if( $post["post_status"] == "publish" ){
			array_push( $required_capabilities, "edit_published_".$post_type."s" );
		// Is it private?
		} else if( $post["post_status"] == "private" ){
			array_push( $required_capabilities, "edit_private_".$post_type."s" );
		}
	}
	if( $post_data["post_status"] == "publish" ){
		array_push( $required_capabilities, "publish_".$post_type."s" );
	}


	///// VALIDATE CAPABILITIES /////
	// Compare required capabilities to array of current user's capabilities
	$pass = true;
	$no_capabilities = array();
	// Cycle through each required capability
	foreach( $required_capabilities as $cap ){
		// Does it not match value : true : in the current user's
		if ( $current_userdata['allcaps'][ $cap ] != true )
			// If any one is false, it sets false
			$pass = false;
		// Add failed capability to error message
		array_push( $no_capabilities, $cap );
	}

	if ( $pass == false ){
		return "User No Capabilities: " . json_encode( $no_capabilities );
	}

	///// SET METHOD /////
	// If there is a post_id and it exists
	if ( !empty( $ID ) && pw_post_exists( $ID ) ){
		$method = 'update';
	}
	else{
		$method = 'insert';
	}

	///// INSERT POST METHOD /////
	if( $method == 'insert' ){
		$post_id = pw_insert_post($post_data);
	}

	///// UPDATE POST METHOD /////
	else if ( $method == 'update' ) {
		$post_id = pw_update_post($post_data);
	}
	
	///// ADD / UPDATE POST META /////
	// IMAGE FIELDS
	// Handle Thumbnail ID
	if ( !empty($thumbnail_id) && !empty($post_id) )
		pw_set_post_thumbnail( $post_id, $thumbnail_id );

	// Handle Thumbnail URL 
	elseif ( !empty($thumbnail_url) && !empty($post_id) )
		pw_set_post_thumbnail( $post_id, $thumbnail_url );
	
	if ( !empty($post_class)  ){
		
	}

	return $post_id;

}


///// O EMBED HTML /////
// Takes an HTML / text string and replaces all oEmbed provider URLs with embed code

function pw_embed_url($input){
	$url = $input[0];
	$o_embed_providers = array(
		// GENERAL
		"twitter.com/",
		"api.embed.ly/",
		"wordpress.com/",
		"scribd.com/",
		"crowdranking.com/",
		"meetup.com/",
		"meetu.ps/",

		// AUDIO
		"soundcloud.com/",
		"mixcloud.com/",
		"official.fm/",
		"shoudio.com/",
		"rdio.com/",

		// VIDEO
		"youtube.com/",
		"youtu.be/",
		"vimeo.com/",
		"hulu.com/",
		"ted.com/",
		"sapo.pt/",
		"dailymotion.com",
		"blip.tv/",
		"ustream.tv/",
		"viddler.com/",
		"qik.com/",
		"revision3.com/",
		"jest.com/",

		// PRESENTATIONS
		"screenr.com/",
		"speakerdeck.com/",
		"kickstarter.com/projects/",

		// IMAGES
		//"flickr.com/", // Resolve after the @ in URL
		//"deviantart.com/", // Needs testing ??
		"slideshare.net/",
		"instagram.com/p/",
		"instagr.am/p/",

		// MOBILE
		"polleverywhere.com/",

	);

	foreach( $o_embed_providers as $provider ){
		if ( strpos( $url, $provider ) !== false ) {
		    $o_embed = true;
		    break;
		}
	}

	// OEMBED : check if it's an o-embed provider
	if( $o_embed == true )
		$embed = wp_oembed_get($url);
	// HOTLINK : if not, hotlink it
	else
		$embed = '<a target="_blank" href="' . $url . '" target="_blank">' . $url . '</a>';

	return $embed;
}


function pw_embed_content($content){
	return preg_replace_callback('$(https?://[a-z0-9_./?=&#-]+)(?![^<>]*>)$i', 'pw_embed_url', $content." ");
}

?>