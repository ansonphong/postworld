<?php

/*
 * PHP / USER FUNCTIONS
 * */
	class user_fields_names{
 		public static $USER_ID='user_id';
		public static $USER_ROLE='user_role';
		public static $VIEWED='viewed';
		public static $FAVORITES='favorites';
		public static $LOCATION_CITY='location_city';
		public static $LOCATION_COUNTRY='location_country';
		public static $LOCATION_REGION ='location_region';
		public static $VIEW_KARMA='view_karma';
		public static $SHARE_KARMA='share_karma';
			
 	}
 
 
	class get_user_location_output{
		public $city='';
		public $country='';
		public $region='';
	} 
	

	function pw_get_userdata ( $user_id, $fields ){
		
		$wordpress_user_fields = array(
			'user_login',
			'user_nicename',
			'user_email',
			'user_url',
			'user_registered',
			'display_name',
			'user_firstname',
			'user_lastname',
			'nickname',
			'user_description',
			'wp_capabilities',
			'admin_color',
			'closedpostboxes_page',
			'primary_blog',
			'rich_editing',
			'source_domain',
			'roles',
			'capabilities',
			);

		$postworld_user_fields = array(
			'viewed',
			'favorites',
			'location_city',
			'location_region',
			'location_country',
			'post_points',
			'comment_points',
			'post_points_meta'
			);

		$user_data = array();

		// If Fields is empty or 'all', add all fields
		if (!$fields || $fields == 'all'){
			$fields = array_merge( $wordpress_user_fields, $postworld_user_fields );
		}

		// WORDPRESS USER FIELDS
		// Check to see if any requested fields are standard Wordpress User Fields
		foreach ($fields as $value) {
			// If a requested field is provided by WP get_userdata() Method, collect all the data
			if ( in_array($value, $wordpress_user_fields) ){
				$wordpress_user_data = get_userdata($user_id);

				// Transfer the user data into $user_data
				foreach ( $wordpress_user_data->data as $key => $value)
					$user_data[$key] = $value;
				
				// Get user Roles
				if (in_array('roles', $fields))
					$user_data['roles'] = $wordpress_user_data->roles;
				
				// Get user Capabilities
				if (in_array('capabilities', $fields))
					$user_data['capabilities'] = $wordpress_user_data->allcaps;
				
				// Break out of foreach
				break;
			}
		}

		// POSTWORLD USER FIELDS
		// Check to see if requested fields are custom Postworld User Fields
		foreach ($fields as $value) {
			// If a requested field is custom Postworld, get the user's row in *user_meta* table
			if ( in_array($value, $postworld_user_fields) ){

				global $wpdb;
				$wpdb -> show_errors();
				$query = "select * from ".$wpdb->pw_prefix.'user_meta'." where user_id=".$user_id;
				//echo $query;
				//esult will be output as an numerically indexed array of associative arrays, using column names as keys
				$postworld_user_data = $wpdb->get_results( $query, ARRAY_A  );

				// Transfer the user data into $user_data
				foreach ( $postworld_user_data as $key => $value)
					$user_data[$key] = $value;

				break;
			}
		}

		return $user_data;

	}
	
	//TODO
	function pw_update_user( $userdata ){
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
		 
		if ( is_a( $userdata, 'stdClass' ) )
			$userdata = get_object_vars( $userdata );
		elseif ( is_a( $userdata, 'WP_User' ) )
			$userdata = $userdata->to_array();
	
		$ID = (int) $userdata['ID'];
	
		// First, get all of the original fields
		$user_obj = get_userdata( $ID );
		if ( ! $user_obj )
			return new WP_Error( 'invalid_user_id', __( 'Invalid user ID.' ) );
	
		$user = $user_obj->to_array();
	
		// Add additional custom fields
		foreach ( _get_additional_user_keys( $user_obj ) as $key ) {
			$user[ $key ] = get_user_meta( $ID, $key, true );
		}
	
		// Escape data pulled from DB.
		$user = add_magic_quotes( $user );
	
		// If password is changing, hash it now.
		if ( ! empty($userdata['user_pass']) ) {
			$plaintext_pass = $userdata['user_pass'];
			$userdata['user_pass'] = wp_hash_password($userdata['user_pass']);
		}
	
		wp_cache_delete($user[ 'user_email' ], 'useremail');
	
		// Merge old and new fields with new fields overwriting old ones.
		$userdata = array_merge($user, $userdata);
		$user_id = wp_insert_user($userdata);
	
		// Update the cookies if the password changed.
		$current_user = wp_get_current_user();
		if ( $current_user->ID == $ID ) {
			if ( isset($plaintext_pass) ) {
				wp_clear_auth_cookie();
				wp_set_auth_cookie($ID);
			}
		}
	
		return $user_id;		 
			 
	}
	function pw_insert_user( $userdata ) {
		global $wpdb;
	
		if ( is_a( $userdata, 'stdClass' ) )
			$userdata = get_object_vars( $userdata );
		elseif ( is_a( $userdata, 'WP_User' ) )
			$userdata = $userdata->to_array();
	
		extract( $userdata, EXTR_SKIP );
	
		// Are we updating or creating?
		if ( !empty($ID) ) {
			$ID = (int) $ID;
			$update = true;
			$old_user_data = WP_User::get_data_by( 'id', $ID );
		} else {
			$update = false;
			// Hash the password
			$user_pass = wp_hash_password($user_pass);
		}
	
		$user_login = sanitize_user($user_login, true);
		$user_login = apply_filters('pre_user_login', $user_login);
	
		//Remove any non-printable chars from the login string to see if we have ended up with an empty username
		$user_login = trim($user_login);
	
		if ( empty($user_login) )
			return new WP_Error('empty_user_login', __('Cannot create a user with an empty login name.') );
	
		if ( !$update && username_exists( $user_login ) )
			return new WP_Error( 'existing_user_login', __( 'Sorry, that username already exists!' ) );
	
		if ( empty($user_nicename) )
			$user_nicename = sanitize_title( $user_login );
		$user_nicename = apply_filters('pre_user_nicename', $user_nicename);
	
		if ( empty($user_url) )
			$user_url = '';
		$user_url = apply_filters('pre_user_url', $user_url);
	
		if ( empty($user_email) )
			$user_email = '';
		$user_email = apply_filters('pre_user_email', $user_email);
	
		if ( !$update && ! defined( 'WP_IMPORTING' ) && email_exists($user_email) )
			return new WP_Error( 'existing_user_email', __( 'Sorry, that email address is already used!' ) );
	
		if ( empty($nickname) )
			$nickname = $user_login;
		$nickname = apply_filters('pre_user_nickname', $nickname);
	
		if ( empty($first_name) )
			$first_name = '';
		$first_name = apply_filters('pre_user_first_name', $first_name);
	
		if ( empty($last_name) )
			$last_name = '';
		$last_name = apply_filters('pre_user_last_name', $last_name);
	
		if ( empty( $display_name ) ) {
			if ( $update )
				$display_name = $user_login;
			elseif ( $first_name && $last_name )
				/* translators: 1: first name, 2: last name */
				$display_name = sprintf( _x( '%1$s %2$s', 'Display name based on first name and last name' ), $first_name, $last_name );
			elseif ( $first_name )
				$display_name = $first_name;
			elseif ( $last_name )
				$display_name = $last_name;
			else
				$display_name = $user_login;
		}
		$display_name = apply_filters( 'pre_user_display_name', $display_name );
	
		if ( empty($description) )
			$description = '';
		$description = apply_filters('pre_user_description', $description);
	
		if ( empty($rich_editing) )
			$rich_editing = 'true';
	
		if ( empty($comment_shortcuts) )
			$comment_shortcuts = 'false';
	
		if ( empty($admin_color) )
			$admin_color = 'fresh';
		$admin_color = preg_replace('|[^a-z0-9 _.\-@]|i', '', $admin_color);
	
		if ( empty($use_ssl) )
			$use_ssl = 0;
	
		if ( empty($user_registered) )
			$user_registered = gmdate('Y-m-d H:i:s');
	
		if ( empty($show_admin_bar_front) )
			$show_admin_bar_front = 'true';
	
		$user_nicename_check = $wpdb->get_var( $wpdb->prepare("SELECT ID FROM $wpdb->users WHERE user_nicename = %s AND user_login != %s LIMIT 1" , $user_nicename, $user_login));
	
		if ( $user_nicename_check ) {
			$suffix = 2;
			while ($user_nicename_check) {
				$alt_user_nicename = $user_nicename . "-$suffix";
				$user_nicename_check = $wpdb->get_var( $wpdb->prepare("SELECT ID FROM $wpdb->users WHERE user_nicename = %s AND user_login != %s LIMIT 1" , $alt_user_nicename, $user_login));
				$suffix++;
			}
			$user_nicename = $alt_user_nicename;
		}
	
		$data = compact( 'user_pass', 'user_email', 'user_url', 'user_nicename', 'display_name', 'user_registered' );
		$data = wp_unslash( $data );
	
		if ( $update ) {
			$wpdb->update( $wpdb->users, $data, compact( 'ID' ) );
			$user_id = (int) $ID; // code here
		} else {
			$wpdb->insert( $wpdb->users, $data + compact( 'user_login' ) );
			$user_id = (int) $wpdb->insert_id;
		}
	
		$user = new WP_User( $user_id );
	
		foreach ( _get_additional_user_keys( $user ) as $key ) {
			if ( isset( $$key ) )
				update_user_meta( $user_id, $key, $$key );
		}
	
		if ( isset($role) )
			$user->set_role($role);
		elseif ( !$update )
			$user->set_role(get_option('default_role'));
	
		wp_cache_delete($user_id, 'users');
		wp_cache_delete($user_login, 'userlogins');
	
		if ( $update )
			do_action('profile_update', $user_id, $old_user_data);
		else
			do_action('user_register', $user_id);
	
		return $user_id;
	}

	
	function add_favorite($post_id,$user_id){
		
		// add favorite to wp_postworld_favorites
		global $wpdb;
	
		$wpdb -> show_errors();	
		$query = "insert into ".$wpdb->pw_prefix.'favorites'." values (".$user_id.",".$post_id.")";
		$wpdb -> query($wpdb -> prepare($query));
		
		
		// increment post count in wp_postworld_post_meta	
		$query = "update ".$wpdb->pw_prefix.'post_meta'." set favorites = favorites +1 where post_id=".$post_id;
		$result = $wpdb -> query($wpdb -> prepare($query));
		if($result === FALSE){
			add_recored_to_post_meta($post_id,0,0,1); 	
		}
		
	}
	
	function delete_favorite($post_id,$user_id){
		global $wpdb;
	
		$wpdb -> show_errors();	
		$query = "delete from ".$wpdb->pw_prefix.'favorites'." where post_id=".$post_id." and user_id=".$user_id;
		$wpdb -> query($wpdb -> prepare($query));
		
		
		// increment post count in wp_postworld_post_meta	
		$query = "update ".$wpdb->pw_prefix.'post_meta'." set favorites = favorites -1 where post_id=".$post_id;
		$result = $wpdb -> query($wpdb -> prepare($query));
		
		if($result === FALSE){
			add_recored_to_post_meta($post_id,0,0,0); 	
		}
		
	}
	
	
//	function set_favorite ( $post_id, $add_remove ){
		/*
		• Add or remove the given post id, from the array in favourites column in wp_postworld_user_meta of the given user
		• Add or remove row in pw_postworld_favorites, with user_id and post_id
		• If it was added or removed, add 1 or subtract 1 from table wp_postworld_post_meta  in column 
	 
	 	Parameters:
		$add_remove
		     •  1 - add it to favourites
		     • -1 - remove it from favorites
		return :
		     1  - added successfully
		     -1 - removed successfully
		     0  - nothing happened
		 * 
		 */
		/*$user_id = get_current_user_id();
		$post_ids = get_favorites($user_id);
		//echo (json_encode($post_ids));
		
		
		$key = array_search($post_id, $post_ids,true);
		$changed = 0;
		
	
		if($key !==FALSE){ // found		
			if($add_remove===-1){
				//array_diff($post_ids, $post_id);
				unset($post_ids[$key]);
				$changed= -1;	
			}
			
		}
		else{
			if($add_remove===1){
				$post_ids[count($post_ids)] = $post_id;
				$changed= 1;	
			}
			
		}
		
		if($changed==1 || $changed==-1){
			global $wpdb;
		
			$wpdb -> show_errors();
			$query ="update ".$wpdb->pw_prefix.'user_meta'." set favorites ='".implode(',',$post_ids)."' where user_id=".$user_id;	
			//echo($query);
			$wpdb -> query($wpdb -> prepare($query));
			//return $changed;
			
			if($changed ==1)//add fav
				add_favorite($post_id, $user_id);
			
			if($changed ==-1)//add fav
				delete_favorite($post_id, $user_id);
			
			
			
			
		}
		//else return $changed;
		return $changed;
		
		
		
		
	}*/
	
		function get_favorites ( $user_id ){
			/*
			 • Return array from the favourites column in wp_postworld_user_meta of the given user
			return : array (of post ids)
			 */
			
			global $wpdb;
		
			$wpdb -> show_errors();
				
			$query = "select favorites from ".$wpdb->pw_prefix.'user_meta'." where user_id=".$user_id;
			//echo($query);
			$favorites_array = $wpdb -> get_var($query);
			//echo(json_encode($viewed_array));
			if ($favorites_array != null) {
				$post_ids = array_map("intval", explode(",", $favorites_array));
				//echo(json_encode($post_ids));
		
				return $post_ids;
			} else
				return array();
		
		}
	
		function is_favorite ( $post_id, $user_id ){
		/*
		 • Checks the favorites column in wp_postworld_user_meta of the given user to see if the user has set the post as a favorite
		 return : boolean 
		 */
		$post_ids = get_favorites($user_id);
		$key = array_search($post_id, $post_ids,true);
		//echo('keey  : '.$key);
		if($key !==FALSE)
		return true;
		else return false;
		
		
	}
	
	function set_viewed ( $post_id, $viewed ){
		/*
		 • Adds to removes to the array in has_viewed in wp_postworld_user_meta 
		 • If $viewed == true, check if the post_id is already in the array. If not, add it.
		 • If $viewed == false, check if the post_id is already in the array. If so, remove it.
			return : boolean (true) 
		*/
		
		$user_id = get_current_user_id();
		//echo($user_id);
		$post_ids = get_viewed($user_id);
		//echo (json_encode($post_ids));
		
		
		$key = array_search($post_id, $post_ids,true);
		$changed = false;
		
		//echo("keyyy:".$key);
		if($key !==FALSE){ // found
			//echo 'founddd';
			if(!$viewed){
				//array_diff($post_ids, $post_id);
				unset($post_ids[$key]);
				$changed= true;	
			}
			
		}
		else{
			if($viewed){
				//echo 'NOTfounddd';
				$post_ids[count($post_ids)] = $post_id;
				$changed= true;	
			}
			
		}
		//echo(implode(',',$post_ids));
		if($changed){
			global $wpdb;
		
			$wpdb -> show_errors();
			$query ="update ".$wpdb->pw_prefix.'user_meta'." set viewed ='".implode(',',$post_ids)."' where user_id=".$user_id;	
			//echo($query);
			$wpdb -> query($wpdb -> prepare($query));
			return true;
		}
		else return false;
		
		
	}
	
	function get_viewed ( $user_id ){
		/*• Gets list of posts by id which the user has viewed
			return : array
		*/
		global $wpdb;
	
		$wpdb -> show_errors();
		$query = "select viewed from ".$wpdb->pw_prefix.'user_meta'." where user_id=".$user_id;
		//echo($query);
		$viewed_array = $wpdb -> get_var($query);
		//echo(json_encode($viewed_array));
		if($viewed_array!=null){
			$post_ids = array_map("intval", explode(",",$viewed_array));
			//echo (json_encode($post_ids));
			
			return $post_ids;
		}
		else return array();
		
	}
	
	function has_viewed ( $user_id, $post_id ){
		/*
		 • Checks to see if user has viewed a given post
		 • Values stored in array in has_viewed in wp_postworld_user_meta
			return : boolean */
		
		$post_ids = get_viewed($user_id);
		
		//echo (json_encode($post_ids));
		
		
		$key = array_search($post_id, $post_ids,true);
		
		//echo('keey  : '.$key);
		if($key !==FALSE)
		return true;
		else return false;
	}
	
	function get_user_location ( $user_id ){
		/*
		  • From 'location_' columns in wp_postworld_user_meta
			return : Object
	     	city : {{city}}
	     	country : {{country}}
		 	region: {{region}}
		 * */		
			
			
		global $wpdb;
	
		$wpdb -> show_errors();
		
		$query = "select ".user_fields_names::$LOCATION_CITY.", ".user_fields_names::$LOCATION_COUNTRY.", ".user_fields_names::$LOCATION_REGION." from ".$wpdb->pw_prefix.'user_meta'." where user_id=".$user_id;
		//echo($query);
		$location_obj = $wpdb -> get_results($query);
		
		foreach ($location_obj as $row) {
			$output = new get_user_location_output();
			$output->city = $row->location_city;
			$output->country = $row->location_country;
			$output->region = $row->location_region;
			
			return $output;
		}
		return null;
	
	}
	
	function get_client_ip (){
		/*
		 * return : IP address of the client
		 * */
		return  $_SERVER['REMOTE_ADDR'];
		//return $_SERVER['HTTP_X_FORWARDED_FOR']; 
		// if from proxy, we should save both.
		//http://stackoverflow.com/questions/3003145/how-to-get-client-ip-address-in-php
	}	

	function get_user_role( $user_id, $return_array = false )  {
		/*
		  • Returns user role(s) for the specified user

			Parameters:
			$return_array : boolean
			     • false (default) - Returns a string, with the first listed role
			     • true - Returns an Array with all listed roles
			
			return : string / Array (set by $return_array)
		 */
		
		
		$user = new WP_User( $user_id ); // this gives us access to all the useful methods and properties for this user
		if ( $user ) {
			$roles = $user->roles;	// returns an array of roles
			if ($return_array == true)
				return $roles;		// return the array
			else
				return $roles[0];	// return only a string of the first listed role
		}  else {
			return false;
		}
	}

	/* Later*/
	function has_shared ( $user_id, $post_id ){}


	

?>