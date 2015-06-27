<?php

function pw_post_exists ( $post_id ){
	// Check if a post exists
	global $wpdb;
	$post_exists = $wpdb->get_row("
		SELECT *
		FROM $wpdb->posts
		WHERE id = '" . $post_id . "'",
		'ARRAY_A');
	if ( !empty($post_exists) )
	    return true;
	else
	    return false;
}

function pw_get_posts( $post_ids, $fields = 'preview', $options = array() ) {
	// Returns an array of specified posts
	/*
		$post_ids 	= [ array ] 				// A numerical array of posts to retrieve
		$fields 	= [ string / array ]		// pw_get_post() Field Model
		$options 	= array(
			'galleries'	=> array(
				'include_galleries' =>  [ boolean ]	// Whether to include the gallery images in the posts
				// All available options from include pw_merge_galleries() and pw_gallery_feed() functions
				),
			);

	*/

	///// SET DEFAULTS /////
	// Set default fields value
	if( $fields == null ) $fields = 'preview';

	// If $post_ids isn't an Array, return
	if (!is_array($post_ids))
		return false;


	///// CACHING LAYER /////
	if( in_array( 'post_cache', pw_enabled_modules() ) ){
		$cache_hash = hash( 'sha256',
			json_encode( $post_ids ) .
			json_encode( $fields ) .
			json_encode( $options )
			);
		$get_cache = pw_get_cache( array( 'cache_hash' => $cache_hash ) );
		if( !empty( $get_cache ) ){
			return json_decode( $get_cache['cache_content'], true);
		}
	}
	
	///// OPTIONS : GALLERIES /////
	// Condition field Model
	$include_galleries = _get( $options, 'galleries.include_galleries' );
	if( (bool) $include_galleries ){
		$fields = pw_add_gallery_field( $fields );
	}

	///// GET POSTS /////
	// Cycle though each $post_id
	$posts = array();
	$i = 0; // Iterator
	foreach ($post_ids as $post_id) {
		$post = pw_get_post($post_id, $fields );

		///// ADD META DATA //////
		// FEED ORDER
		if( is_array( $fields ) && in_array( 'feed_order', $fields ) || $fields == 'all' ){
			$i++;
			if( !isset( $post['feed'] ) )
				$post['feed'] = array();
			$post['feed']['order'] = $i;
		}
		array_push($posts, $post);
	}

	///// OPTIONS : GALLERIES /////
	// Condition field Model
	if( (bool) $include_galleries ){
		// Merge the galleries with the gallery options passed directly in 
		$posts = pw_merge_galleries( $posts, $options['galleries'] );
	}

	///// CACHING LAYER /////
	if( in_array( 'post_cache', pw_enabled_modules() ) )
		pw_set_cache( array(
			'cache_type'	=>	'feed-posts',
			'cache_hash' 	=> 	$cache_hash,
			'cache_content'	=>	json_encode($posts),
			));


	// Return Array of post data
	return $posts;

}

////////// GET POST DATA //////////
function pw_get_post( $post_id, $fields = 'preview', $viewer_user_id = null ){
	
	//pw_log( "pw_get_post : " . $post_id, $fields );

	// Switch Modes (view/edit)
	// 'Edit' mode toggles content display filtering (oEmbed, shortcodes, etc)
	$mode = ( $fields == 'edit' ) ? 'edit' : 'view';

	// If no post ID, return here
	if( $post_id == null ) return false;

	// Get data fields for the specified post
	if( $fields == null ) $fields = 'preview';

	// If a post array is passed in
	if( is_array( $post_id ) )
		$post_id = $post_id['ID'];	

	// If a post object is passed in
	else if( is_object( $post_id ) )  
		  $post_id =  $post_id->ID;

	///// CHECK POST EXISTS /////
	// Get the post and check if the post exists
	$get_post = get_post( $post_id, ARRAY_A );
	if( $get_post == null )
		return false;

	// Preserve the $fields value
	$fields_value = $fields;

	///// PRESET FIELD MODELS /////
	if( is_string( $fields) ){
		$fields = pw_get_field_model('post',$fields);
		// If the specified field model does not exist
		if( empty( $fields ) )
			// Set the default field model
			$fields = pw_get_field_model('post','preview');
	}
	//pw_log( 'pw_get_post : FIELDS : ', $fields );

	///// ADD ACTION HOOK : PW GET POST INIT /////
	do_action( 'pw_get_post_init',
		array(
			'post_id' => $post_id,
			'fields' => $fields,
			'mode' => $mode,
			)
		);

	// Add ID to the post
	$post = array(
		'ID' => $post_id,
		);

	///// ADD VIEWER USER /////
	// Check if the $viewer_user_id is supplied - if not, get it
	if ( !$viewer_user_id )
		$viewer_user_id = get_current_user_id();
	
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
	foreach ($get_post as $key => $value) {
		if( in_array($key, $fields) ){
			$post[$key] = $value;	
		}
	}

	////////// WP GET_POST_CUSTOM METHOD //////////
	// Get post data from Wordpress standard function
	$get_post_custom = get_post_custom($post_id);
	foreach ($get_post_custom as $key => $value) {
		if( in_array($key, $fields) )
			$post[$key] = $value;
	}

	///// SET LOCAL VALUES /////
	// Set Author ID
	$author_id = $get_post['post_author']; 

	////////// WORDPRESS //////////
		// Permalink
		if( in_array('post_permalink', $fields) ){
			if( $post['post_type'] == 'attachment' )
				$post['post_permalink'] = get_attachment_link( $post_id );
			else 
				$post['post_permalink'] = get_permalink( $post_id );
		}

		// Post Path (Permalink without Home url)
		if( in_array('post_path', $fields) )
			$post['post_path']	= str_replace( home_url(), '', get_permalink( $post_id ) );

		// Category List
		if( in_array('post_categories_list', $fields) )
			$post["post_categories_list"] = get_the_category_list(' ','', $post_id );

		// Tags List
		if( in_array('post_tags_list', $fields) ){
			$post["post_tags_list"] = get_the_term_list( $post_id, 'post_tag', '', '', '' );
			if ( $post["post_tags_list"] == false ) $post["post_tags_list"] = '';
		}

		// Edit Post Link
		if( in_array('edit_post_link', $fields) ){
			$post["edit_post_link"] = htmlspecialchars_decode( get_edit_post_link($post_id) );
			if ( $post["edit_post_link"] == false ) $post["edit_post_link"] = '#';
		}

		// Post Format
		if( in_array('post_format', $fields) ){
			$post['post_format'] = get_post_format( $post_id );
			if( $post['post_format'] == false )
				$post['post_format'] = 'standard';
		}

		// Post Type Object
		if( in_array('post_type_labels', $fields) ){
			$post['post_type_labels'] = get_post_type_object( $get_post['post_type'] )->labels;
		}
		
	////////// POSTWORLD //////////
		// Get post row from Postworld Meta table, as an Array
		$pw_post_meta = pw_get_post_meta($post_id);
		if ( !empty($pw_post_meta) ){
			// Cycle though each PW $pw_post_meta value
			// If it's in $fields, transfer it to $post
			foreach($pw_post_meta as $key => $value ){
				if( in_array($key, $fields) ){
					$post[$key] = $pw_post_meta[$key];
				}
			}
		}

		// Points
		if( in_array('post_points', $fields) ){
			$post['post_points'] = pw_get_post_points( $post_id );
		}


	////////// VIEWER FIELDS //////////
		// Extract viewer() fields
		$viewer_fields = extract_linear_fields( $fields, 'viewer', true );

		if ( !empty($viewer_fields) ){
			///// GET VIEWER DATA /////
			// Has Viewer Voted?
			if( in_array('has_voted', $viewer_fields) )
				$post['viewer']['has_voted'] = pw_has_voted_on_post( $post_id, $viewer_user_id );

			// View Vote Power
			if( in_array('vote_power', $viewer_fields) )
				$post['viewer']['vote_power'] = pw_get_user_vote_power( $viewer_user_id );
		
			// Is Favorite
			if( in_array('is_favorite', $viewer_fields) ){
				$is_favorite = pw_is_favorite( $post_id );
				if ( !isset($is_favorite) )
					$is_favorite = "0";
				$post['viewer']['is_favorite'] = $is_favorite;
			}

			// Is View Later
			if( in_array('is_view_later', $viewer_fields) )
				$post['viewer']['is_view_later'] = pw_is_view_later( $post_id );

		}

	
	////////// AUTHOR DATA //////////
		// Extract author() fields
		$relationships = extract_linear_fields( $fields, 'is_relationship', true );
		if ( !empty($relationships) ){
			if( !isset($post['viewer']) )
				$post['viewer'] = array();
			foreach ($relationships as $relationship ) {
				$post['viewer'][$relationship] = pw_is_post_relationship( $relationship, $post_id, $user_id);
			}
		}

	////////// DATE & TIME //////////
		// Post Time Ago
		if ( in_array('time_ago', $fields) )
			$post['time_ago'] = time_ago( strtotime ( $get_post['post_date_gmt'] ) );
		// Post Timestamp
		if ( in_array('post_timestamp', $fields) )
			$post['post_timestamp'] = (int) strtotime( $get_post['post_date_gmt'] ) ;


	////////// AVATAR IMAGES //////////
		// AVATAR FIELDS
		//$avatars = pw_get_avatar_sizes( $author_id, $fields );
		$avatars = pw_get_avatars( array(
			'user_id' 	=> 	$author_id,
			'fields'	=>	$fields,
			));
		
		if ( !empty($avatars) )
			$post["avatar"] = $avatars;


	////////// META DATA //////////
		
		// Extract meta fields
		$post_meta_fields = extract_linear_fields( $fields, 'post_meta', true );
		if ( !empty($post_meta_fields) ){
			// CYCLE THROUGH AND FIND EACH REQUESTED FIELD
			foreach ($post_meta_fields as $post_meta_field ) {

				/**
				 * GET ALL META FIELDS
				 */
				if( in_array("all", $post_meta_fields) ||
					in_array("_all", $post_meta_fields) ||
					$post_meta_field == "all" ){
					// Return all meta data
					$post['post_meta'] = get_metadata('post', $post_id, '', true);
					// Convert to strings
					if ( !empty( $post['post_meta'] ) ){
						foreach( $post['post_meta'] as $meta_key => $meta_value ){
							
							/**
							 * Delete metadata keys starting with '_'
							 * Which are reserved for system keys
							 * When in viewing mode
							 * Unless the '_all' sub-field is present
							 */
							if( $mode == 'view' &&
								substr( $meta_key, 0, 1 ) === '_' &&
								!in_array( '_all', $post_meta_fields ) ){
								unset( $post['post_meta'][$meta_key] );
								continue;
							}
							
							// Convert values from arrays
							$post['post_meta'][$meta_key] = $post['post_meta'][$meta_key][0];
						
						}
					}
					// Break from the foreach
					break;
				}

				// GET SPECIFIC FIELDS
				else {
					$post_meta_data = get_post_meta( $post_id, $post_meta_field, true );
					if( !empty($post_meta_data) )
						$post['post_meta'][$post_meta_field] = $post_meta_data;
				}

			}

			///// JSON META KEYS /////
			if( is_array( $post['post_meta'] ) ){

				// Parse known JSON keys from JSON strings into objects
				global $pwSiteGlobals;
				// Get known metakeys from the theme configuration
				$json_meta_keys = pw_get_obj( $pwSiteGlobals, 'db.wp_postmeta.json_meta_keys' );
				// If there are no set fields, define empty array
				if( !$json_meta_keys ) $json_meta_keys = array();
				// Add the globally defined postmeta key
				$json_meta_keys[] = pw_postmeta_key;

				// Iterate through each post_meta value
				foreach( $post['post_meta'] as $meta_key => $meta_value ){
					// If the key is known to be a JSON field
					if(
						in_array($meta_key, $json_meta_keys) &&
						is_string($meta_value) ){
						// Decode it from JSON into a PHP array
						$post['post_meta'][$meta_key] = json_decode($post['post_meta'][$meta_key], true);
					}
				}
			}

			///// SERIALIZED ARRAY META KEYS /////
			$serialized_meta_keys = array( "_wp_attachment_metadata" );
			if( is_array( $post['post_meta'] ) ){
				foreach( $post['post_meta'] as $meta_key => $meta_value ){
					if(
						in_array($meta_key, $serialized_meta_keys) &&
						is_string($meta_value) ){
						$post['post_meta'][$meta_key] = unserialize( $post['post_meta'][$meta_key] );
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
			$get_the_author_meta_fields = array(
				'user_login',
				'user_nicename',
				'user_email',
				'user_url',
				'user_registered',
				'user_activation_key',
				'user_status',
				'display_name',
				'nickname',
				'first_name',
				'last_name',
				'description',
				'user_level',
				'user_firstname',
				'user_lastname',
				'user_description',
				'comment_shortcuts',
				'ID'
				);

			///// USE GET_THE_AUTHOR_META() FUNCTION /////
			// Check if $author_fields are accessible by get_the_author_meta() Wordpress function
			// If so, pull in that data with that function
			foreach ($author_fields as $author_field) {
				if( in_array( $author_field, $get_the_author_meta_fields ) )
					$post['author'][$author_field] = get_the_author_meta( $author_field, $author_id );
			}

			///// WORDPRESS AUTHOR FIELDS /////
			
			// Author Posts URL
			if( in_array('posts_url', $author_fields) )
				$post['author']['posts_url'] = get_author_posts_url( $author_id );


			///// BUDDYPRESS AUTHOR FIELDS : requires Buddypress /////

			// Author Profile URL
			if( in_array('user_profile_url', $author_fields) && function_exists('bp_core_get_userlink') )
				$post['author']['user_profile_url'] = bp_core_get_userlink( $author_id, false, true );

			///// POSTWORLD AUTHOR FIELDS /////
			/*
			if( in_array('posts_points', $author_fields) )
				$post['author']['posts_points'] = pw_get_user_post_points( $post_id );
			if( in_array('comments_points', $author_fields) )
				$post['author']['comments_points'] = pw_get_user_comments_points( $post_id );
			*/

		} // END if


	////////// IMAGE FIELDS //////////
		$post_image = pw_get_post_image( $post, $fields );
		if( !empty( $post_image ) )
			$post['image'] = $post_image;

	////////// GALLERY //////////
		// Extract meta fields
		$gallery_fields = extract_linear_fields( $fields, 'gallery', true );
		if ( !empty($gallery_fields) ){
			$post['gallery'] = array();
			// TODO : If already has post_content from get_post, feed post_content directly
			// 		  To bypass recursive query of the post_content
			$gallery_post_ids = pw_get_post_galleries_attachment_ids( $post_id );

			// Gallery Attachment IDs
			if( in_array( 'ids', $gallery_fields ) ){
				$post['gallery']['ids'] = $gallery_post_ids;
			}
			
			// Get the gallery field model
			$gallery_field_model = pw_get_field_model('post','gallery');

			// Gallery Attachment Posts
			if( in_array( 'posts', $gallery_fields ) && is_array( $gallery_field_model ) ){
				// For performance, prevent from recusive checking every image for a gallery
				$new_fields = array_diff( $gallery_field_model, array( 'gallery(ids,posts)', 'gallery(ids)', 'gallery(posts)' ) );
				$post['gallery']['posts'] = pw_get_posts( $gallery_post_ids, $new_fields );
			}

		}

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
			//$post['taxonomy'][$taxonomy] =  $post_terms;
			
			$post['taxonomy'][$taxonomy] = array();

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
					array_push( $post['taxonomy'][$taxonomy], $term_obj );
				}
				// If there is only one taxonomy field
				else {
					// Just push the single term value in flat Array
					$term_field = $tax_fields[0];
					$term_value = $post_term[ $term_field ];
					array_push( $post['taxonomy'][$taxonomy], $term_value );
				}
				
			}

		} // END foreach
	} // END IF


	////////// PARENT POST //////////
		$parent_post_fields = extract_linear_fields( $fields, 'parent_post', true );
		if ( !empty($parent_post_fields) ){
			$post['parent_post'] = array();
			if( $get_post['post_parent'] != 0 )
				$post['parent_post'] = pw_get_post( $get_post['post_parent'], $parent_post_fields[0] );
			else
				$post['parent_post'] = array();
		}
	
	////////// CHILD POST COUNT //////////
		// Gets the number of child posts
		if( in_array( 'child_post_count', $fields ) ){
			global $wpdb;
			$post['child_post_count'] = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_parent = $post_id AND post_status = 'publish'"); 
		}

	////////// CHILD POSTS COMMENT COUNT //////////
		// Gets the sum of all comment counts on all child posts
		if( in_array( 'child_posts_comment_count', $fields ) ){
			global $wpdb;
			$post['child_posts_comment_count'] = $wpdb->get_var("SELECT SUM(comment_count) FROM $wpdb->posts WHERE post_parent = $post_id AND post_status = 'publish'"); 
		}

	////////// CHILD POSTS KARMA COUNT //////////
		// Gets a sum of all the karma on all child posts
		if( in_array( 'child_posts_karma_count', $fields ) ){
			global $wpdb;
			$pw_post_meta_table = $wpdb->pw_prefix . "post_meta";
			$post['child_posts_karma_count'] = $wpdb->get_var(
				"SELECT SUM(post_points)
				FROM $pw_post_meta_table
				INNER JOIN $wpdb->posts ON post_id = $wpdb->posts.ID
				WHERE $wpdb->posts.post_parent = $post_id
				AND $wpdb->posts.post_status = 'publish'"
				);
		}

	///// FIELDS /////
		if( in_array( 'fields', $fields ) ){
			$post['fields'] = $fields;
		}

	///// ADD MODE WHEN EDITING /////
		if( $mode == 'edit' )
			$post['mode']= 'edit';
		else
			$post['mode']= 'view';

	///// ADD ACTION HOOK : PW GET POST CONTENT /////
		do_action( 'pw_get_post_content',
			array(
				'post_id' => $post_id,
				'fields' => $fields,
				'mode' => $mode,
				'post'	=>	$post,
				)
			);

	///// POST CONTENT /////
	// Condition Post Content
		if( in_array( 'post_content', $fields ) &&
			$mode == 'view' &&
			$post['post_type'] !== 'nav_menu_item' ){
			
			///// CONTENT FILTERING /////

			// oEmbed URLs
			$post['post_content'] = pw_embed_content($post['post_content']);
			
			// Apply Shortcodes
			//$post[$key] = do_shortcode($post[$key]);

			// Apply AutoP
			//$post[$key] = wpautop($post[$key]);

			// Apply all content filters
			$post['post_content'] = apply_filters('the_content', $post['post_content']);

			// Trim off whitespace at beginning and end of post content
			$post['post_content'] = trim( $post['post_content'] );

		}

	///// POST EXCERPT /////
		// Returns the post excerpt cropped to a certain number of characters
		// Post_content is optionally used
		$post_excerpt_fields = extract_linear_fields( $fields, 'post_excerpt', true );
		if ( !empty($post_excerpt_fields) ){
			// If the first field is a number
			if( is_numeric( $post_excerpt_fields[0] ) ){
				$max_chars = intval($post_excerpt_fields[0]);
				// If the second value is 'post_content'
				if( $post_excerpt_fields[1] == 'post_content' ){
					// Set the excerpt as the post content
					$post_excerpt = $get_post['post_content'];
					// Strip all shortcodes
					$post_excerpt = strip_shortcodes( $post_excerpt );
					// Strip all HTML tags
					$post_excerpt = wp_strip_all_tags( $post_excerpt, true );
					// Set it into the post object
					$post['post_excerpt'] = $post_excerpt;
				}
				// Crop the post excerpt to the word
				$post['post_excerpt'] = pw_crop_string_to_word( $post['post_excerpt'], $max_chars, "..." );
			}
		}


	///// COMMENTS /////
		// 	Gets comments associated with the post
		//	comments(3,all,comment_date_gmt)
		$comment_linear_fields = extract_linear_fields( $fields, 'comments', true );
		if ( !empty( $comment_linear_fields ) ){

			$comments_query = array(
				'post_id' => $post_id,
				);

			$comments_query['number'] = ( is_numeric( $comment_linear_fields[0] ) ) ?
				$comment_linear_fields[0] : 3;

			$comment_fields = ( is_string( $comment_linear_fields[1] ) ) ?
				$comment_linear_fields[1] : 'all';

			$comments_query['orderby'] = ( is_string( $comment_linear_fields[2] ) ) ?
				$comment_linear_fields[2] : 'comment_date_gmt';

			$comments = pw_get_comments( $comments_query, $comment_fields, false );

			$post['comments'] = $comments;

		}

		//$post['comments'] = "test";

		/*
		// pw_get_comments ( $query, [$fields], [$tree] )
		post_id
		fields
		orderby
		number
		*/

	///// ADD ACTION HOOK : PW GET POST COMPLETE /////
		do_action( 'pw_get_post_complete',
			array(
				'post_id' => 	$post_id,
				'fields' => 	$fields,
				'mode' => 		$mode,
				'post'	=>		$post,
				)
			);

	///// FILTERS /////
	$post = apply_filters( 'pw_get_post_complete_filter', $post );

	return $post;

}


function pw_set_wp_postmeta_array( $post_id, $post_meta = array() ){
	/*
		$post_id = [integer]
		$post_meta = array(
			'key'	=>	[ string / array / object ]	
		)
	*/
	// If the post array contains a populated 'post_meta' variable
	// Iterate through each of the subkeys and encode the arrays as JSON
	// Writing them to meta keys which match their array keys in the wp_postmeta table

	///// ADD/UPDATE META FIELDS //////
	if( !empty( $post_meta ) ){
		
		///// JSON SETUP /////
		// Parse known object to JSON strings
		global $pwSiteGlobals;
		// Get known metakeys from the theme configuration
		$json_meta_keys = pw_get_obj( $pwSiteGlobals, 'db.wp_postmeta.json_meta_keys' );
		// If there are no set fields, define empty array
		if( !$json_meta_keys ) $json_meta_keys = array();
		// Add the globally defined postmeta key
		$json_meta_keys[] = pw_postmeta_key;

		///// ITERATE : POST META ///// 
		foreach ( $post_meta as $meta_key => $meta_value ) {
			// If the key is a known JSON meta key
			if( in_array( $meta_key, $json_meta_keys ) ){
				// And the value is an object or array
				if( is_array($meta_value) || is_object($meta_value) ){
					// Encode the array as JSON
					$meta_value = json_encode($meta_value);
				}
			}
			// UPDATE META
			update_post_meta($post_id, $meta_key, $meta_value);
		}
	}

	return true;

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
	
	if ($postarr['post_type'] == 'attachment')
		$post_id = wp_insert_attachment( $postarr );
	else
		$post_id = wp_insert_post( $postarr, $wp_error );

	// If WP insert post was successful
	if( gettype($post_id) == 'integer' ){

		///// ADD TERMS / TAXONOMIES //////
		if(isset($postarr["tax_input"])){
			foreach ( $postarr["tax_input"] as $taxonomy => $terms) {
				wp_set_object_terms( $post_id, $terms, $taxonomy, false );
			}
		}

		/*
		///// POST FORMAT //////
		if(isset($postarr["post_format"])){
			set_post_format( $post_id , $postarr["post_format"] );
		}
		*/
	
		///// WORDPRESS POSTMETA /////
		if( isset( $postarr['post_meta']) ){
			pw_set_wp_postmeta_array( $post_id, $postarr['post_meta'] );
		}

		///// POSTWORLD META FIELDS //////
		// Set the Post Author to Current User ID if not found
		if( !isset($postarr['post_author']) )
			$postarr['post_author'] = get_current_user_id();

		// Define which fields are Postworld Post Meta
		$pw_post_meta_field_model = pw_get_field_model( 'post', 'pw_post_meta' );
		// Check to see if the post array has any Postworld Post Meta Field Values
		$has_pw_post_meta_fields = false;
		foreach( $postarr as $key => $value ){
			if( in_array( $key, $pw_post_meta_field_model ) && !empty( $value ) ){
				$has_pw_post_meta_fields = true;
				break;
			}
		}
		// If it has Postworld Post Meta, Set it
		if( $has_pw_post_meta_fields ){
			pw_set_post_meta( $post_id, $postarr );
		} 

		///// AUTHOR NAME/SLUG FIELD /////
		// Adds support for an `author_name` parameter
		if( isset($postarr["post_author_name"]) ){
			$user = get_user_by( 'slug', $postarr["post_author_name"] );

			// If the author name is valid and the user can edit others posts
			if( isset($user->data->ID) && current_user_can('edit_others_posts') ){
				// Set the post author to the ID of the specified username
				wp_update_post( array( "ID" => $post_id, "post_author" => $user->data->ID ) );
			}
			// Otherwise, set the post to the user ID of the current user
			else{
				$current_user_id = get_current_user_id();
				wp_update_post( array( "ID" => $post_id, "post_author" => $current_user_id ) );
			}
		}

		
	}
	
	return $post_id;

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
	$post = get_post( $postarr['ID'], ARRAY_A );
	
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
		$attach_id = pw_url_to_media_library($image,$post_id);
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
		// DETECT POST TYPE
		$post = get_post($post_id, "ARRAY_A");
		$post_type = $post["post_type"];
		
		///// GENERATE : ARRAY OF REQUIRED CAPABILITIES /////
		// The required capabilities of current action	
		$required_capabilities = array();
		// The fallback capabilities which will also pass
		// If the specific capabilities aren't configured
		$post_type_object = get_post_type_object( $post_type );
		if( $post_type_object->capability_type == 'post' )
			$fallback_capabilities = array();
		
		// Does user own post?
		if( $current_user_id == $post["post_author"] ){
			// REQUIRE : DELETE {{CPT}}
			array_push( $required_capabilities,"delete_".$post_type."s" );
			// FALLBACK : DELETE POSTS
			if( isset( $fallback_capabilities ) )
				array_push( $fallback_capabilities,"delete_posts" );
		} else {
			// REQUIRE : DELETE OTHERS {{CPT}}
			array_push( $required_capabilities,"delete_others_".$post_type."s" );
			// FALLBACK : DELETE OTHERS POSTS
			if( isset( $fallback_capabilities ) )
				array_push( $fallback_capabilities,"delete_others_posts" );
		}
		// Is the post published?
		if( $post["post_status"] == "published" ){
			// REQUIRE : DELETE PUBLISHED {{CPT}}
			array_push( $required_capabilities,"delete_published_".$post_type."s" );
			// FALLBACK : DELETE PUBLISHED POSTS
			if( isset( $fallback_capabilities ) )
				array_push( $fallback_capabilities,"delete_published_posts" );
		} else if( $post["post_status"] == "private" ){
			// REQUIRE : DELETE PRIVATE {{CPT}}
			array_push( $required_capabilities,"delete_private_".$post_type."s" );
			// FALLBACK : DELETE PRIVATE POSTS
			if( isset( $fallback_capabilities ) )
				array_push( $fallback_capabilities,"delete_private_posts" );
		}

		///// VALIDATE CAPABILITIES /////
		// Compare required capabilities to array of current user's capabilities
		$pass = true;
		$no_capabilities = array();
		// Cycle through each required capability
		foreach( $required_capabilities as $cap ){
			// If the capability isn't true for the current user
			if ( $current_userdata['allcaps'][ $cap ] != true ){
				// If any one is false, it sets false
				$pass = false;
				// Push failed capability to error message
				array_push( $no_capabilities, $cap );
			}
		}
		//// VALIDATE FALLBACK CAPABILITIES /////
		// If the post type has the capabilities of 'post'
		// Then check against the post capabilities
		if( $pass == false && isset( $fallback_capabilities ) ){
			// Reset to pass
			$pass = true;
			// Iterate through fallback capabilities
			foreach( $fallback_capabilities as $cap ){
				// If the capability isn't true for the current user
				if ( $current_userdata['allcaps'][ $cap ] != true ){
					// If any one is false, it sets false
					$pass = false;
					// Push failed capability to error message
					array_push( $no_capabilities, $cap );
				}
			}
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
	// A catch-all post saving mechanism

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
	
	// Get the post type object
	$post_type_object = get_post_type_object( $post_type );
	// If the post type has "capability_type" of "post"
	if( $post_type_object->capability_type == 'post' )
		// Set the post type to "post"
		$post_type = 'post';
		
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
	

	///// RANK SCORE /////
	// Cache the post's rank score
	global $pwSiteGlobals;
	$rank_post_types = pw_get_obj( $pwSiteGlobals, 'rank.post_types' );
	if( in_array( $post_data['post_type'], $rank_post_types ) )
		pw_cache_rank_score ( $post_id );

	if ( !empty($post_class) ){}

	do_action( 'pw_save_post' );

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


function pw_embed_link_url( $post_id ){
	// Returns the embed code if a given post ID contains a link_url and it's media

	$fields = array( 'ID', 'link_url', 'link_format' );
	$post = pw_get_post( $post_id, $fields );

	$embed_formats = array( 'video', 'audio' );

	if( in_array( $post['link_format'], $embed_formats )  ){
		return wp_oembed_get( $post['link_url'] );
	}
	else
		return false;
}

function pw_print_post( $vars ){
	extract($vars);

	global $pw;

	$pw_post = array();

	//$pw_post['post'] = pw_get_post( $post_id );
	$pw_post['post'] = pw_get_post( $post_id, $fields );

	// Add custom input variables
	if( isset($vars) && !empty($vars) ){
		foreach( $vars as $key => $value ){
			$pw_post[$key] = $value;
		}
	}

	// Use $view to over-ride $template
	if( isset($view) )
		$template = pw_get_post_template ( $post_id, $view, 'dir', true );

	// H2O
	require_once POSTWORLD_PATH.'/lib/h2o/h2o.php';
	$h2o = new h2o($template);
	$post_html = $h2o->render($pw_post);

	// Add Javascript Variables
	if( isset( $js_vars ) && !empty( $js_vars ) ){

		// Add Post
		if( in_array( 'post', $js_vars ) ){
			// Prepare to print to $window for AngularJS / Javascript
			$pw_post_window = $pw_post['post'];

			// Remove post_content
			if( !empty($pw_post_window['post_content']) )
				$pw_post_window['post_content'] = '';

			// Prepare the Javascript
			$js_var_post =	'<script type=\'text/javascript\'>';
			$js_var_post .=	'var post = ';
			$js_var_post .=	json_encode( $pw_post_window );
			$js_var_post .=	';</script>';

			// Prepend the Javascript
			$post_html = $js_var_post . $post_html;

		}
		
	}
	return $post_html;

}

function pw_editor( $content, $editor_id, $settings = array() ){
	ob_start();
	wp_editor( $content, $editor_id, $settings );
	$editor = ob_get_contents();
	ob_end_clean();
	return $editor;
}

?>