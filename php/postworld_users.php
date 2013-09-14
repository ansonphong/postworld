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
	
	
	 
	function get_user_data ( $user_id, $fields ){
		/*
		 • Gets meta data from the wp_postworld_user_meta table
		Parameters:
		$user_id : integer
		$fields : string / Array
	• Possible values:
	     • viewed
	     • favorites
	     • location_country
	     • location_region
	     • location_city
	
		return : Array (keys: fields, values: values) */
		
		//assume that $fields is an array
		$fields_for_query = implode(",", $fields);
		
		global $pw_table_names;
		global $wpdb;
		$wpdb -> show_errors();
		$query = "select ".$fields_for_query." from ".$pw_table_names['user_meta']." where user_id=".$user_id;
		//echo $query;
		//esult will be output as an numerically indexed array of associative arrays, using column names as keys
		$result = $wpdb->get_results( $query, ARRAY_A  );
		return $result;
		
		
	}
	
	function set_favorites ( $post_id, $add_remove ){
		/*
		• Add or remove the given post id, from the array in favourites column in wp_postworld_user_meta of the given user
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
		$user_id = get_current_user_id();
		$post_ids = get_favorites($user_id);
		//echo (json_encode($post_ids));
		
		
		$key = array_search($post_id, $post_ids,true);
		$changed = 0;
		
		//echo("keyyy:".$key);
		if($key !==FALSE){ // found
			//echo 'founddd';
			if($add_remove===-1){
				//array_diff($post_ids, $post_id);
				unset($post_ids[$key]);
				$changed= -1;	
			}
			
		}
		else{
			if($add_remove===1){
				//echo 'NOTfounddd';
				//echo("ddddd".count($post_ids));
				$post_ids[count($post_ids)] = $post_id;
				$changed= 1;	
			}
			
		}
		//echo(implode(',',$post_ids));
		if($changed==1 || $changed==-1){
			global $wpdb;
			global $pw_table_names;
			$wpdb -> show_errors();
			$query ="update ".$pw_table_names['user_meta']." set favorites ='".implode(',',$post_ids)."' where user_id=".$user_id;	
			//echo($query);
			$wpdb -> query($wpdb -> prepare($query));
			//return $changed;
		}
		//else return $changed;
		return $changed;
		
		
		
		
	}
	
		function get_favorites ( $user_id ){
			/*
			 • Return array from the favourites column in wp_postworld_user_meta of the given user
			return : array (of post ids)
			 */
			
			global $wpdb;
			global $pw_table_names;
			$wpdb -> show_errors();
				
			$query = "select favorites from ".$pw_table_names['user_meta']." where user_id=".$user_id;
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
			global $pw_table_names;
			$wpdb -> show_errors();
			$query ="update ".$pw_table_names['user_meta']." set viewed ='".implode(',',$post_ids)."' where user_id=".$user_id;	
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
		global $pw_table_names;
		$wpdb -> show_errors();
		$query = "select viewed from ".$pw_table_names['user_meta']." where user_id=".$user_id;
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
		global $pw_table_names;
		$wpdb -> show_errors();
		
		$query = "select ".user_fields_names::$LOCATION_CITY.", ".user_fields_names::$LOCATION_COUNTRY.", ".user_fields_names::$LOCATION_REGION." from ".$pw_table_names['user_meta']." where user_id=".$user_id;
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