<?php
/*
 * PHP / USER FUNCTIONS
 * */
/*class user_fields_names {
	public static $USER_ID = 'user_id';
	public static $USER_ROLE = 'user_role';
	public static $VIEWED = 'viewed';
	public static $FAVORITES = 'favorites';
	public static $LOCATION_CITY = 'location_city';
	public static $LOCATION_COUNTRY = 'location_country';
	public static $LOCATION_REGION = 'location_region';
	public static $VIEW_KARMA = 'view_karma';
	public static $SHARE_KARMA = 'share_karma';
	public static $POST_RELATIONSHIP = 'post_relationships';

}*/

class user_fields_names {
	public static $USER_ID = 'user_id';
	public static $POST_POINTS='post_points';
	public static $POST_POINTS_META='post_points_meta';
	public static $COMMENT_POINTS = 'comment_points';
	//public static $COMMENT_POINTS_META='comment_points_meta';
	public static $SHARE_POINTS = 'share_points';
	public static $SHARE_POINTS_META = 'share_points_meta';
	public static $POST_RELATIONSHIPS='post_relationships';
	public static $POST_VOTES='post_votes';
	public static $COMMENT_VOTES='comment_votes';
	public static $LOCATION_CITY = 'location_city';
	public static $LOCATION_COUNTRY = 'location_country';
	public static $LOCATION_REGION = 'location_region';
}

class get_user_location_output {
	public $city = '';
	public $country = '';
	public $region = '';
}

function pw_current_userdata($field) {
	$user_data = pw_get_userdata(get_current_user_id(), 'all');
	echo $user_data[$field];
}

function pw_current_userdata_obj($fields) {
	$user_data = pw_get_userdata(get_current_user_id(), $fields);
	echo $user_data;
}

function pw_can_edit_profile( $user_id ){
	if( $user_id == get_current_user_id() ||
		current_user_can('edit_users') )
		return true;
	else
		return false;
}


function pw_get_userdatas( $user_ids, $fields = false ){
	// DEPRECIATED as of Version 1.7.2
	return pw_get_users( $user_ids, $fields );
}


function pw_get_users( $user_ids, $fields = 'all' ){
	$users_array = array();
	foreach( $user_ids as $user_id ){
		array_push(
			$users_array,
			pw_get_user($user_id, $fields)
			);
	}
	return $users_array;
}

function pw_get_userdata($user_id, $fields = false) {
	// DEPRECIATED as of Version 1.6
	return pw_get_user( $user_id, $fields );
}

function pw_get_current_user($fields){
	$user_id = get_current_user_id();
	return pw_get_user( $user_id, $fields );
}

function pw_get_user( $user_id, $fields = 'preview' ) {
	// Gets user data from Wordpress, Postworld and Buddypress field APIs

	// Set defaults
	$single_field = false;

	$user_data = array();


	///// PRESET FIELD MODELS /////
	if( is_string( $fields) ){
		$fields = pw_get_field_model('user',$fields);
		// If the specified field model does not exist
		if( empty( $fields ) )
			// Set the default field model
			$fields = pw_get_field_model('user','preview');
	}

	/*
	// If Fields is empty or 'all', add all fields
	if ( $fields == false || $fields == 'all')
		$fields = array_merge($wordpress_user_fields, $postworld_user_fields, $buddypress_user_fields, $wordpress_usermeta_fields, $postworld_avatar_fields);
	else if( is_string( $fields ) ){
		$fields = array( $fields );
		$single_field = true;
	}
	*/

	///// TRANSFER ONLY REQUESTED FIELDS!! /////

	// WORDPRESS USER FIELDS
	// Check to see if any requested fields are standard Wordpress User Fields
	foreach ($fields as $field) {
		// If a requested field is provided by WP get_userdata() Method, collect all the data
		if (in_array($field, pw_get_field_model('user','wordpress') )) {
			$wordpress_user_data = get_userdata($user_id);
			if ((isset($wordpress_user_data))&&($wordpress_user_data)) {
				// Transfer the requested user data into $user_data
				foreach ($wordpress_user_data->data as $key => $field){
					if ( in_array($key, $fields) )
						$user_data[$key] = $field;
				}
				// Get user Roles
				if (in_array('roles', $fields))
					$user_data['roles'] = $wordpress_user_data->roles;
				// Get user Capabilities
				if (in_array('capabilities', $fields))
					$user_data['capabilities'] = $wordpress_user_data->allcaps;
			}
			// Break out of foreach
			break;
		}
	}

	// Author Posts URL
	if( in_array('posts_url', $fields) )
		$user_data['posts_url'] = get_author_posts_url( $user_id );

	// POSTWORLD USER FIELDS
	// Check to see if requested fields are custom Postworld User Fields
	foreach ($fields as $value) {
		// If a requested field is custom Postworld, get the user's row in *user_meta* table
		if ( in_array($value, pw_get_field_model('user','postworld') )) {
			global $wpdb;
			if( pw_dev_mode() )
				$wpdb -> show_errors();
			$query = "select * from " . $wpdb -> pw_prefix . 'user_meta' . " where user_id=" . $user_id;
			// Result will be output as an numerically indexed array of associative arrays, using column names as keys
			$postworld_user_data = $wpdb -> get_results($query, ARRAY_A);
			// Transfer the user data into $user_data
			if ( is_array($postworld_user_data[0]) && isset($postworld_user_data[0]["user_id"]) ){
				foreach ( $postworld_user_data[0] as $meta_key => $meta_value ){
					if ( in_array( $meta_key, $fields ) ){
						$pw_json_fields = array( "post_points_meta", "share_points_meta", "post_relationships" );
						// Decode the JSON encoded DB fields
						if ( in_array( $meta_key, $pw_json_fields ) )
							$meta_value = json_decode( $meta_value );
						$user_data[$meta_key] = $meta_value;
					}
				}
			}
			break;
		}
	}


	///// WORDPRESS USER META /////

		$usermeta_fields = extract_linear_fields( $fields, 'usermeta', true );
		if ( !empty($usermeta_fields) ){

			/**
			 * When handling the 'all' value
			 * Go ahead and get the pre-set all fields.
			 * For security reasons, the fields are pre-defined
			 * as actually returning all the usermeta fields
			 * would cause security issues.
			 */
			if( in_array("all", $usermeta_fields) ){

				$usermeta_all_fields = array(
					PW_USERMETA_KEY,
					PW_AVATAR_KEY,
					'first_name',
					'nickname',
					'last_name',
					'description',
					);

				$usermeta_all_fields = apply_filters(
					'pw_get_usermeta_all_fields',
					$usermeta_all_fields );

				if( count( $usermeta_fields ) > 1 )
					$usermeta_fields = array_unique(array_merge($usermeta_fields, $usermeta_all_fields));
				else
					$usermeta_fields = $usermeta_all_fields;

			}

			// CYCLE THROUGH AND FIND EACH REQUESTED FIELD
			foreach( $usermeta_fields as $usermeta_field ) {
				
				$usermeta_data = get_user_meta( $user_id, $usermeta_field, true );

				if( !empty($usermeta_data) )
					$user_data['usermeta'][$usermeta_field] = $usermeta_data;
			
			}


			///// JSON META KEYS /////
			if( is_array( $user_data['usermeta'] ) ){

				// Parse known JSON keys from JSON strings into objects
				global $pwSiteGlobals;
				// Get known metakeys from the theme configuration
				$json_meta_keys = pw_get_obj( $pwSiteGlobals, 'db.wp_usermeta.json_meta_keys' );
				// If there are no set fields, define empty array
				if( !$json_meta_keys ) $json_meta_keys = array();
				// Add the globally defined postmeta key
				$json_meta_keys[] = pw_usermeta_key;

				// Iterate through each usermeta value
				foreach( $user_data['usermeta'] as $meta_key => $meta_value ){
					// If the key is known to be a JSON field
					if(
						in_array($meta_key, $json_meta_keys) &&
						is_string($meta_value) ){
						// Decode it from JSON into a PHP array
						$user_data['usermeta'][$meta_key] = json_decode($user_data['usermeta'][$meta_key], true);
					}
				}
			}

			///// SERIALIZED ARRAY META KEYS /////
			$serialized_meta_keys = array( "_wp_attachment_metadata" );
			if( is_array( $user_data['usermeta'] ) ){
				foreach( $user_data['usermeta'] as $meta_key => $meta_value ){
					if(
						in_array($meta_key, $serialized_meta_keys) &&
						is_string($meta_value) ){
						$user_data['usermeta'][$meta_key] = unserialize( $user_data['usermeta'][$meta_key] );
					}
				}
			}

		}

	///// BUDDYPRESS PROFILE LINK /////
	// Check to see if requested fields are Buddypress User Fields
	foreach ($fields as $value) {
		// If a requested field is Buddypress
		if( is_array( $buddypress_user_fields ) &&
			in_array( $value, $buddypress_user_fields ) ){
			
			// Author Profile URL
			if ($value == 'user_profile_url' && function_exists('bp_core_get_userlink')){
				$user_data['user_profile_url'] = bp_core_get_userlink($user_id, false, true);
				//pw_log( $user_data['user_profile_url'] );
			}

		}
	}


	/*
	// DEPRECIATED as of Version 1.7.2 - use field : 'xprofile(all)'
	////////// BUDDYPRESS CUSTOM PROFILE FIELDS //////////
	// Extract meta fields
	$buddypress_fields = extract_linear_fields( $fields, 'buddypress', true );
	if ( !empty($buddypress_fields) && function_exists('bp_get_profile_field_data') ){

		$user_data["buddypress"] = array();

		// CYCLE THROUGH AND FIND EACH REQUESTED FIELD
		foreach ($buddypress_fields as $buddypress_field ) {
			$args = array(
		        'field'   => $buddypress_field, // Field name or ID.
		        'user_id' => $user_id // Default
		        );
			$user_data["buddypress"][$buddypress_field] = bp_get_profile_field_data( $args );
		}
	}
	*/


	///// BUDDYPRESS XPROFILE FIELDS /////
	$xProfile_fields = extract_linear_fields( $fields, 'xprofile', true );
	if ( !empty( $xProfile_fields ) )
		$user_data['xprofile'] = pw_get_xprofile( $user_id, $xProfile_fields );
	

	// AVATAR FIELDS
	$avatar = pw_get_avatars( array( 'user_id' => $user_id, 'fields' => $fields ) );
	if ( !empty($avatar) )
		$user_data["avatar"] = $avatar;

	// REMOVE PASSWORD
	unset($user_data["user_pass"]);

	// SINGLE FIELD
	// If it's a single field
	if( $single_field )
		// Return just the first field
		return $user_data[ $fields[0] ];

	return $user_data;

}


function pw_get_avatar_sizes( $user_id, $fields ){
	// DEPRECIATED
	return pw_get_avatars( array(
			'user_id' 	=> 	$user_id,
			'fields'	=>	$fields,
			)
		);
}

function pw_get_avatars( $vars ){
	// Takes input $fields in the following format "avatar(handle,size)"
	// $fields = array( 'avatar(small,48)', 'avatar(medium, 150)', ... );
	// Produces an object like : array( 'small'=>array( "width"=>48, "height"=>48, "url"=>"http://...jpg" ) )

	$user_id = get_current_user_id();

	$default_vars = array(
		'user_id' 	=> 	$user_id,
		'fields'	=>	array(), // 'avatar(small,64)', 'avatar(medium,256)' 
		);
	$vars = array_replace_recursive( $default_vars, $vars );

	extract( $vars );

	// Extract avatar() fields
	$avatars = extract_fields( $fields, 'avatar' );
	$avatars_object = array();
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
   			$avatars_object[$avatar_handle]['width'] = (int) $avatar_size;
   			$avatars_object[$avatar_handle]['height'] = (int) $avatar_size;
			$avatars_object[$avatar_handle]['url'] = pw_get_avatar( array( "user_id"=> $user_id, "size" => $avatar_size ) ); //get_avatar_url( $author_id, $avatar_size );
   		}

	} // END foreach
	return $avatars_object;
}




//TODO: change to new schema
function pw_update_user($userdata) {
	/*
	 *
	 * Extends wp_update_user() to add data to the Postworld user_meta table
	 See wp_update_user() : http://codex.wordpress.org/Function_Reference/wp_update_user
	 Usage

	 $userdata = array(
	 'ID' => 1,
	 'user_url' => 'http://...com',
	 'user_description' => 'Description here.',
	 'favorites' => '23,24,27',
	 'location_country' => 'Egypt',
	 );
	 return : integer

	 user_id - If successful
	 *
	 * */
	$set = '';
	$insertComma = FALSE;
	$user_id = wp_update_user($userdata);
	global $wpdb;
	if( pw_dev_mode() )
		$wpdb -> show_errors();
	if (gettype($user_id) == 'integer') {// successful
		pw_insert_user_meta($user_id);
		/*if ($userdata[user_fields_names::$FAVORITES]) {
			$set .= " " . user_fields_names::$FAVORITES . "='" . $userdata[user_fields_names::$FAVORITES] . "'";
			$insertComma = TRUE;
		}*/
		if (isset($userdata[user_fields_names::$LOCATION_CITY])) {
			$set .= " " . user_fields_names::$LOCATION_CITY . "='" . $userdata[user_fields_names::$LOCATION_CITY] . "'";
			$insertComma = TRUE;
		}
		if (isset($userdata[user_fields_names::$LOCATION_COUNTRY])) {
			if ($insertComma === TRUE)
				$set .= " , ";
			$set .= " " . user_fields_names::$LOCATION_COUNTRY . "='" . $userdata[user_fields_names::$LOCATION_COUNTRY] . "'";
			$insertComma = TRUE;
		}
		if (isset($userdata[user_fields_names::$LOCATION_REGION])) {
			if ($insertComma === TRUE)
				$set .= " , ";
			$set .= " " . user_fields_names::$LOCATION_REGION . "='" . $userdata[user_fields_names::$LOCATION_REGION] . "'";
			$insertComma = TRUE;
		}
		if (isset($userdata[user_fields_names::$POST_RELATIONSHIPS])) {
			if ($insertComma === TRUE)
				$set .= " , ";
			$set .= " " . user_fields_names::$POST_RELATIONSHIPS . "='" . $userdata[user_fields_names::$POST_RELATIONSHIPS] . "'";
			$insertComma = TRUE;
		}
		if (isset($userdata[user_fields_names::$SHARE_POINTS_META])) {
			if ($insertComma === TRUE)
				$set .= " , ";
			$set .= " " . user_fields_names::$SHARE_POINTS_META . "='" . $userdata[user_fields_names::$SHARE_POINTS_META] . "'";
			$insertComma = TRUE;
		}
		if (isset($userdata[user_fields_names::$POST_VOTES])) {
			if ($insertComma === TRUE)
				$set .= " , ";
			$set .= " " . user_fields_names::$POST_VOTES . "='" . $userdata[user_fields_names::$POST_VOTES] . "'";
			$insertComma = TRUE;
		}
		
		if (isset($userdata[user_fields_names::$COMMENT_VOTES])) {
			if ($insertComma === TRUE)
				$set .= " , ";
			$set .= " " . user_fields_names::$COMMENT_VOTES . "='" . $userdata[user_fields_names::$COMMENT_VOTES] . "'";
			$insertComma = TRUE;
		}
		
		if (isset($userdata[user_fields_names::$POST_POINTS_META])) {
			if ($insertComma === TRUE)
				$set .= " , ";
			$set .= " " . user_fields_names::$POST_POINTS_META . "='" . $userdata[user_fields_names::$POST_POINTS_META] . "'";
			$insertComma = TRUE;
		}
		
		if (isset($userdata[user_fields_names::$POST_POINTS])) {
			if ($insertComma === TRUE)
				$set .= " , ";
			$set .= " " . user_fields_names::$POST_POINTS . "=" . $userdata[user_fields_names::$POST_POINTS] . "";
			$insertComma = TRUE;
		}
		
		if (isset($userdata[user_fields_names::$SHARE_POINTS])) {
			if ($insertComma === TRUE)
				$set .= " , ";
			$set .= " " . user_fields_names::$SHARE_POINTS . "=" . $userdata[user_fields_names::$SHARE_POINTS] . "";
			$insertComma = TRUE;
		}
		
		if (isset($userdata[user_fields_names::$COMMENT_POINTS])) {
			if ($insertComma === TRUE)
				$set .= " , ";
			$set .= " " . user_fields_names::$COMMENT_POINTS . "=" . $userdata[user_fields_names::$COMMENT_POINTS] . "";
			$insertComma = TRUE;
		}

		if ($insertComma === FALSE) {
		} else {
			$query = "update $wpdb->pw_prefix" . "user_meta set $set where user_id=" . $user_id;
			//echo $query;
			$wpdb -> query($query);

		}

	}
	return $user_id;
}

/**
 * Adds a row to the favorites database table
 * @uses $wpdb Class
 * @param $post_id [integer] The post ID of the post to favorite
 * @param $user_id [integer] The user ID of user favoriting
 */
function pw_add_favorite($post_id, $user_id) {
	
	if( empty($post_id) || empty($user_id) )
		return false;

	global $wpdb;
	if( pw_dev_mode() )
		$wpdb -> show_errors();
	$query = "SELECT * FROM  $wpdb->pw_prefix"."favorites WHERE post_id=$post_id AND user_id=$user_id";
	$results = $wpdb->get_results($query);
	
	// If an entry with the same values doesn't already exist
	if( is_null($results) || count($results) === 0 ){
		$wpdb->insert(
			$wpdb -> pw_prefix . 'favorites',
			array(
				'user_id' 	=> $user_id,
				'post_id' 	=> $post_id,
				),
			array(
				'%d', 	// Insert as Integer
				'%d'	// Insert as Integer
				)
			);
	}
}

/**
 * Deletes a row from the favorites database table
 * @uses $wpdb Class
 * @param $post_id [integer] The post ID
 * @param $user_id [integer] The user ID
 */
function pw_delete_favorite($post_id, $user_id) {
	global $wpdb;
	if( pw_dev_mode() )
		$wpdb->show_errors();
	$query = "DELETE from " . $wpdb->pw_prefix."favorites" . " WHERE post_id=" . $post_id . " AND user_id=" . $user_id;
	$wpdb->query($query);
}


function pw_set_favorite( $switch = true, $post_id = null, $user_id = null ) {
	/*
	 Use pw_set_post_relationship() to set the post relationship for favorites
	 If $post_id is undefined
	 $switch is a boolean
	 pw_set_post_relationship( 'favorites', $post_id, $user_id, $switch )
	 return : boolean
	 */

	if (is_null($post_id)) {
		global $post;
		$post_id = $post -> ID;
	}

	if (is_null($user_id)) {
		$user_id = get_current_user_id();
	}

	return pw_set_post_relationship('favorites', $switch, $post_id, $user_id);
}

function pw_get_favorites($user_id = null) {
	/*
	Use pw_get_post_relationships() method to return just the favorite posts
	pw_get_post_relationships( $user_id, 'favorites' )
	return : Array (of post ids)
	 */
	if (is_null($user_id)) {
		$user_id = get_current_user_id();
	}
	return pw_get_post_relationships($user_id, 'favorites');

}

function pw_is_favorite($post_id = null, $user_id = null) {
	/*
	 Use pw_get_post_relationship() method to return the post relationship status for favorites
	 pw_get_post_relationship( 'favorites', $post_id, $user_id )
	 return : boolean
	 */

	if (is_null($post_id)) {
		global $post;
		$post_id = $post -> ID;
	}

	if (is_null($user_id)) {
		$user_id = get_current_user_id();
	}

	return pw_get_post_relationship('favorites', $post_id, $user_id);

}

function pw_is_viewed($post_id = null, $user_id = null) {
	/*
	 Use pw_get_post_relationship() method to return the post relationship status for viewed
	 pw_get_post_relationship( 'viewed', $post_id, $user_id )
	 return : boolean
	 */

	if (is_null($post_id)) {
		global $post;
		$post_id = $post -> ID;

	}

	if (is_null($user_id)) {
		$user_id = get_current_user_id();
	}
	return pw_get_post_relationship('viewed', $post_id, $user_id);

}

function pw_is_view_later($post_id = null, $user_id = null) {
	/*
	 Use pw_get_post_relationship() method to return the post relationship status for view_later
	 pw_get_post_relationship( 'viewed', $post_id, $user_id )
	 return : boolean
	 */

	if (is_null($post_id)) {
		global $post;
		$post_id = $post -> ID;
	}

	if (is_null($user_id)) {
		$user_id = get_current_user_id();
	}

	return pw_get_post_relationship('view_later', $post_id, $user_id);

}

function pw_is_post_relationship( $post_relationship, $post_id = null, $user_id = null) {
	/*
	 Use pw_get_post_relationship() method to return the post relationship status for $post_relationship
	 return : boolean
	 */

	if (is_null($post_id)) {
		global $post;
		$post_id = $post -> ID;
	}

	if (is_null($user_id)) {
		$user_id = get_current_user_id();
	}

	return pw_get_post_relationship( $post_relationship, $post_id, $user_id);
}

function pw_set_viewed( $switch = true, $post_id = null, $user_id = null ) {
	/*
	 Use pw_set_post_relationship() to set the post relationship for viewed
	 $switch is a boolean
	 pw_set_post_relationship( 'viewed', $switch, $post_id, $user_id )
	 return : boolean
	 */

	if (is_null($post_id)) {
		global $post;
		$post_id = $post -> ID;
	}

	if (is_null($user_id)) {
		$user_id = get_current_user_id();
	}

	return pw_set_post_relationship('viewed', $switch, $post_id, $user_id);
}

function pw_set_view_later($switch=TRUE, $post_id = null, $user_id = null) {

	/*Use pw_set_post_relationship() to set the post relationship for view_later
	 $switch is a boolean
	 pw_set_post_relationship( 'view_later', $switch, $post_id, $user_id )
	 return : boolean
	 */
	 
	 if (is_null($post_id)) {
		global $post;
		$post_id = $post -> ID;

	}

	if (is_null($user_id)) {
		$user_id = get_current_user_id();
	}
	 
	return pw_set_post_relationship('view_later', $switch, $post_id, $user_id );

}

function pw_get_viewed($user_id = null) {
	/*
	 Use pw_get_post_relationships() method to return just the viewed posts
	 pw_get_post_relationships($user_id, 'viewed')
	 return : Array (of post ids)
	 */
	if (is_null($user_id)) {
		$user_id = get_current_user_id();
	}

	return pw_get_post_relationships($user_id, 'viewed');

}

function pw_get_view_later($user_id = null) {
	/*
	 Use pw_get_post_relationships() method to return just the view later posts
	 pw_get_post_relationships($user_id, 'view_later')
	 return : Array (of post ids)
	 */
	if (is_null($user_id)) {
		$user_id = get_current_user_id();
	}

	return pw_get_post_relationships($user_id, 'view_later');

}

function pw_has_viewed($user_id, $post_id) {
	/*
	 • Checks to see if user has viewed a given post
	 • Values stored in array in pw_has_viewed in wp_postworld_user_meta
	 return : boolean */

	$post_ids = pw_get_viewed($user_id);

	//echo (json_encode($post_ids));

	$key = array_search($post_id, $post_ids, true);

	//echo('keey  : '.$key);
	if ($key !== FALSE)
		return true;
	else
		return false;
}

function pw_get_user_location($user_id) {
	/*
	 • From 'location_' columns in wp_postworld_user_meta
	 return : Object
	 city : {{city}}
	 country : {{country}}
	 region: {{region}}
	 * */

	global $wpdb;

	if( pw_dev_mode() )
		$wpdb->show_errors();

	$query = "select " . user_fields_names::$LOCATION_CITY . ", " . user_fields_names::$LOCATION_COUNTRY . ", " . user_fields_names::$LOCATION_REGION . " from " . $wpdb -> pw_prefix . 'user_meta' . " where user_id=" . $user_id;
	//echo($query);
	$location_obj = $wpdb -> get_results($query);

	foreach ($location_obj as $row) {
		$output = new get_user_location_output();
		$output -> city = $row -> location_city;
		$output -> country = $row -> location_country;
		$output -> region = $row -> location_region;

		return $output;
	}
	return null;

}

function pw_get_client_ip() {
	/*
	 * return : IP address of the client
	 * */
	return $_SERVER['REMOTE_ADDR'];
	//return $_SERVER['HTTP_X_FORWARDED_FOR'];
	// if from proxy, we should save both.
	//http://stackoverflow.com/questions/3003145/how-to-get-client-ip-address-in-php
}

function pw_get_user_role($user_id, $return_array = FALSE) {
	/*
	 • Returns user role(s) for the specified user

	 Parameters:
	 $return_array : boolean
	 • false (default) - Returns a string, with the first listed role
	 • true - Returns an Array with all listed roles

	 return : string / Array (set by $return_array)
	 */

	if (!$user_id)
		$user_id = get_current_user_id();

	$user = new WP_User($user_id);
	// this gives us access to all the useful methods and properties for this user
	if ($user) {
		$roles = $user -> roles;
		// returns an array of roles
		if ($return_array == true)
			return $roles;
		// return the array
		else {
			//print_r($roles);
			if (count($roles) > 0)
				return $roles[0];
			else
				return '';
		}	// return only a string of the first listed role
	} else {
		return false;
	}
}

/* Later*/
function pw_has_shared($user_id, $post_id) {
}

/**
 * Used to set a given user's relationship to a given post
 */
function pw_set_post_relationship( $relationship, $switch, $post_id = null, $user_id = null ) {

	/*
	 Parameters
	 ------------
	 * $relationship : string
	 The type of relationship to set
	 Options :
	 viewed
	 favorites
	 view_later

	 * $post_id : integer
	 * $user_id : integer
	 * $switch : boolean

	 true : Add the post_id to the relationship array
	 false : Remove the post_id from the relationship array
	 Process

	 Add/remove the given post_id to the given relationship array in post_relationships column in User Meta table

	 -Favorites

	 If $relationship == favorite : Add / remove a row to Favorites table

	 Usage

	 *
	 pw_set_post_relationship( 'favorites', true, '24', '101' )
	 Anatomy

	 JSON in post_relationships column in User Meta table
	 {
	 viewed:[12,25,23,16,47,24,58,112,462,78,234,25,128],
	 favorites:[12,16,25],
	 view_later:[58,78],
	 }
	 return : boolean

	 true - If successful set on
	 false - If successful set off
	 error - If error */
	//echo ($post_id);

	 $switch = pw_switch_value($switch);

	if(is_null($post_id)) {
		global $post;
		$post_id = $post->ID;
	}

	if (is_null($user_id)) {
		$user_id = get_current_user_id();
	}

	$relationship_db = pw_get_relationship_from_user_meta($user_id);
	$relationship_db_array = (array)json_decode($relationship_db);
	
	if ($relationship_db) {
		if ($switch == 'on') {
			if (!in_array($post_id, $relationship_db_array[$relationship])) {
				$relationship_db_array[$relationship][] = $post_id;
				pw_update_post_relationship($user_id, $relationship_db_array);
				//echo ($relationship);
				if ($relationship == 'favorites'){
					pw_add_favorite($post_id, $user_id);
				}
			}
			return TRUE;
		} else {
			if (in_array($post_id, $relationship_db_array[$relationship])) {

				$relationship_db_array[$relationship] = array_diff($relationship_db_array[$relationship], array($post_id));
				$relationship_db_array[$relationship]= array_values($relationship_db_array[$relationship]);
				pw_update_post_relationship($user_id, $relationship_db_array);
				if ($relationship == 'favorites')
					pw_delete_favorite($post_id, $user_id);
			}
			return FALSE;
		}
	} else {
		//add record to user meta or add relationship
		
		pw_insert_user_meta($user_id);
		if ($switch) {
			$relationship_db_array = array('viewed' => array(), "favorites" => array(), 'view_later' => array());
			$relationship_db_array[$relationship][] = $post_id;
			pw_update_post_relationship($user_id, $relationship_db_array);
			if ($relationship == 'favorites')
				pw_add_favorite($post_id, $user_id);
			return TRUE;
		} else {
			if ($relationship == 'favorites')
				pw_delete_favorite($post_id, $user_id);
			return FALSE;
		}
	}
	return 'error';

}

function pw_update_post_relationship($user_id, $relationship = null) {
	global $wpdb;
	if( pw_dev_mode() )
		$wpdb -> show_errors();
	$query = "update $wpdb->pw_prefix" . "user_meta set post_relationships='" . json_encode($relationship) . "' where user_id=" . $user_id;
	$wpdb -> query($query);
}

function pw_insert_user_meta($user_id) {
	global $wpdb;
	if( pw_dev_mode() )
		$wpdb->show_errors();

	$query = "select * from " . $wpdb -> pw_prefix . "user_meta where user_id=" . $user_id;
	$row = $wpdb -> get_row($query);

	if ($row == null) {

		//$user_role = pw_get_user_role($user_id);
		//if($relationship === null) $relationship='null';

		$query = "INSERT INTO `$wpdb->pw_prefix"."user_meta`
					(`user_id`,
					`post_points`,
					`post_points_meta`,
					`comment_points`,
					`share_points`,
					`share_points_meta`,
					`post_relationships`,
					`post_votes`,
					`comment_votes`,
					`location_city`,
					`location_region`,
					`location_country`)
					VALUES
					($user_id,0,null,0,0,null,null,null,null,null,null,null);
								";

		/*
		 $query = "insert into $wpdb->pw_prefix"."user_meta (`user_id`,
		 `user_role`,
		 `viewed`,
		 `favorites`,
		 `location_city`,
		 `location_region`,
		 `location_country`,
		 `view_karma`,
		 `share_karma`,
		 `post_points`,
		 `comment_points`,
		 `post_points_meta`,
		 `share_points`) values($user_id,'$user_role',null,null,null,null,null,0,0,0,0,null,0)";
		 */
		 //print_r($query);
		$wpdb -> query($query);
	}
}

function pw_get_post_relationship( $relationship, $post_id, $user_id) {

	/*
	 Used to get a given user's relationship to a given post
	 Parameters

	 $relationship : string

	 The type of relationship to set
	 Options :
	 all
	 viewed
	 favorites
	 view_later
	 $post_id : integer

	 $user_id : integer

	 Process

	 Check to see if the post_id is in the given relationship array in the post_relationships column in User Meta table
	 return : boolean

	 If $relationship = all : return an Array containing all the relationships it's in
	 array('viewed','favorites')
	 * */
	$relationship_array = pw_get_relationship_from_user_meta($user_id);
	//print_r($relationship_array);
	if (!is_null($relationship_array)) {
		/*array(
		 'viewed' => [12,25,23,16,47,24,58,112,462,78,234,25,128],
		 'favorites' => [12,16,25],
		 'view_later' => [58,78]
		 )*/

		$relationship_array = (array) json_decode($relationship_array);

		if ($relationship != 'all') {

			// If that relationship object doesn't exist
			if( !isset($relationship_array[$relationship]) )
				return FALSE;

			// If it exists, test it
			if ( in_array( $post_id, $relationship_array[$relationship] )) {
				return TRUE;
			} else
				return FALSE;
		}

		// ALL
		else {
			$output = array();
			if (in_array($post_id, $relationship_array['viewed']))
				$output[] = 'viewed';
			if (in_array($post_id, $relationship_array['favorites']))
				$output[] = 'favorites';
			if (in_array($post_id, $relationship_array['view_later']))
				$output[] = 'view_later';

			return $output;
		}
	} else {

		if ($relationship != 'all')
			return FALSE;
		else
			return FALSE;
	}

}

function pw_get_post_relationships($user_id = null, $relationship = null) {
	/*
	 Used to get a list of all post relationships of a specified user
	 Paramaters

	 $user_id : integer

	 $relationship : integer (optional)

	 Process

	 Reads the specified relationship Array from post_relationships column in User Meta table
	 If relationship is undefined, return entire post_relationships object
	 Decode from stored JSON, return PHP Array
	 Usage

	 Specified post relationship :

	 pw_get_post_relationships( '1', 'favorites' )
	 returns : Array of post IDs

	 array(24,48,128,256,512)
	 Un-specified post relationship :

	 pw_get_post_relationships( '1' )
	 returns : Contents of post_relationships

	 array(
	 'viewed' => [12,25,23,16,47,24,58,112,462,78,234,25,128],
	 'favorites' => [12,16,25],
	 'view_later' => [58,78]
	 )
	 POST RELATIONSHIP : "SET" ALIASES

	 If no $user_id is defined, use get_current_user_id() method to get user ID
	 If no $post_id is defined, use $post->ID method to get the post ID
	 *
	 */
	if (is_null($user_id)) {
		$user_id = get_current_user_id();
	}

	$relationships_db = pw_get_relationship_from_user_meta($user_id);
	$relationships_db_array = (array) json_decode($relationships_db);
	if (!is_null($relationships_db)) {
		if (!is_null($relationship)) {
			return $relationships_db_array[$relationship];
		} else {
			return $relationships_db_array;
		}
	}

	return array();
}

function pw_get_relationship_from_user_meta($user_id) {
	global $wpdb;
	if( pw_dev_mode() )
		$wpdb->show_errors();
	$query = "select " . user_fields_names::$POST_RELATIONSHIPS . " from " . $wpdb -> pw_prefix . 'user_meta' . " where user_id=" . $user_id;
	//echo($query);
	$relationshp = $wpdb -> get_var($query);
	return $relationshp;

}

function pw_count_user_posts( $author_id ){
	global $wpdb;
	$posts_query = "
	  SELECT
	    COUNT(*)
	  FROM
	    $wpdb->posts
	  WHERE
	    post_status = 'publish' AND post_author = '$author_id'
	  ";
	//$where = $wpdb->get_results($posts_query,ARRAY_A);
	return $wpdb->get_var( $posts_query );
}

?>