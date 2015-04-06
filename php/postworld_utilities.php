<?php
/*_   _ _   _ _ _ _   _           
 | | | | |_(_) (_) |_(_) ___  ___ 
 | | | | __| | | | __| |/ _ \/ __|
 | |_| | |_| | | | |_| |  __/\__ \
  \___/ \__|_|_|_|\__|_|\___||___/
//////////////////////////////////*/

function pw_get_all_comment_ids(){
	global $wpdb;
	$query = "
		SELECT comment_ID
		FROM ".$wpdb->comments . "
		WHERE comment_approved = 1";
	$comments = $wpdb->get_results( $wpdb->prepare( $query ) );
	$ids = array();
	foreach( $comments as $comment ){
		$ids[] = $comment->comment_ID;
	}
	return $ids;
}

function pw_get_all_user_ids(){
	global $wpdb;
	$query = "SELECT ID FROM ".$wpdb->users;
	$users = $wpdb->get_results( $wpdb->prepare( $query ) );
	$ids = array();
	foreach( $users as $user ){
		$ids[] = $post->ID;
	}
	return $ids;
}

function pw_get_all_post_ids_in_post_type( $post_type, $post_status = '' ){
	// Returns a 1D array of all the post IDs in a post type
	global $wpdb;

	$post_status_query = ( !empty($post_status) && is_string($post_status) ) ?
		" AND post_status='" . $post_status . "'" :
		"";

	$query = "
		SELECT ID
		FROM ".$wpdb->posts."
		WHERE post_type ='".$post_type."'"
		. $post_status_query;

	$posts = $wpdb->get_results( $wpdb->prepare( $query ) );

	$ids = array();
	foreach( $posts as $post ){
		$ids[] = $post->ID;
	}
	return $ids;
}

function pw_get_post_gmt_timestamp( $post_id = null ){

	if( $post_id == null )
		return 0;

	global $wpdb;

	$post = $wpdb->get_row( "SELECT post_date_gmt FROM $wpdb->posts WHERE ID = " . $post_id, ARRAY_A);
	
	if ( empty( $post ) )
		return 0;

	$time = $post['post_date_gmt'];

	$time = mysql2date( 'U', $time, false );

	return $time;

}


/**
 * Starts a microtimer under the specified ID
 * @param $timer_id [string] The unique ID of the timer
 */
function pw_set_microtimer( $timer_id ){
	// Sets the microtime of a timer ID
	global $pw_microtimer;
	if( is_null( $pw_microtimer ) )
		$pw_microtimer = array();

	$pw_microtimer[ $timer_id ] = microtime();
}

/**
 * Gets the current microtimer from the specified timer ID
 * @param $timer_id [string] The unique ID of the timer
 * @return [float] The period of the timer (in seconds)
 */
function pw_get_microtimer( $timer_id ){
	// Gets the microtime of a timer ID, in seconds
	global $pw_microtimer;
	$timer = _get( $pw_microtimer, $timer_id );
	if( $timer_time !== false )
		return pw_microtime_diff( $timer );//$current_time - $timer_time;
	else
		return false;
}

/**
 * Logs the current microtimer from the specified timer ID
 * @param $timer_id [string] The unique ID of the timer
 * @param $log [string] (Optional) An additional note to log
 */
function pw_log_microtimer( $timer_id, $note = '' ){
	// Logs the difference of time in seconds
	$time = pw_get_microtimer( $timer_id );
	if( !empty($note) )
		$note = $note . ' : ';
	pw_log( 'MICROTIMER : '.$timer_id.' : ' . $note . $time );
}

/**
 * Calculates difference between two microtimes
 * @param $start [float] The value of the initial microtimer
 * @param $end [float] (optional) The value of the end microtimer
 *			- If no end time is provided, the current microtime is used
 * @return [float] The the difference between timers (in seconds)
 */
function pw_microtime_diff( $start, $end=NULL ) { 
	if( !$end ) { 
		$end= microtime(); 
	} 
	list($start_usec, $start_sec) = explode(" ", $start); 
	list($end_usec, $end_sec) = explode(" ", $end); 
	$diff_sec= intval($end_sec) - intval($start_sec); 
	$diff_usec= floatval($end_usec) - floatval($start_usec); 
	return floatval( $diff_sec ) + $diff_usec; 
} 

function pw_get_filename(){
	$parsed_url = parse_url( $_SERVER['REQUEST_URI'] );
	$path_info = pathinfo( $parsed_url['path'] );
	return $path_info['filename'];
}

function pw_is_filename( $filename ){
	return ( $filename == pw_get_filename() );
}

function pw_is_admin_ajax(){
	return pw_is_filename( 'admin-ajax' );
}

function pw_is_base( $mixed ){
	// @param $mixed = [ array / string ]
	// Returns boolean whether or not the user is on the given screen base(s)

	// Make sure we're working with an array
	if( is_string( $mixed ) )
		$screen_bases = array( $mixed );
	elseif( is_array( $mixed ) )
		$screen_bases = $mixed;
	else
		return false;

	// Get the current screen
	$screen = get_current_screen();
	
	// If the current screen base is one of those specified
	return in_array( $screen->base, $screen_bases );
}

function pw_log( $message, $data ){
	if( is_array( $message ) || is_object( $message ) )
		$message = 'JSON:' . json_encode($message, JSON_PRETTY_PRINT);

	if( $data !== null )
		$message .= json_encode($data, JSON_PRETTY_PRINT);

	error_log( $message . "\n", 3, POSTWORLD_PATH . "/log/php-log.txt");

}


/**
 * Generates a hash string from the contents of a file
 * @param $src [string] The absolute system path of the file
 * @param $type [string] The type of hash to generate, see PHP hash()
 * @return [string] The hash
 */
function pw_file_hash( $src, $type = 'sha256' ){
	// Get file contents
	$file_contents = file_get_contents( $src );
	// Make a hash for the file contents
	return hash( $type, $file_contents );
}


// Recursively count array
function pw_count_r($array, $i = 0){
    foreach($array as $k){
        if(is_array($k)){ $i += pw_count_r($k, 1); }
        else{ $i++; }
    }
    return $i;
}

function pw_filter_count( $filter_hook ){
	global $wp_filter;
	$filters = _get( $wp_filter, $filter_hook );
	
	if( is_array( $filters ) && !empty( $filters ) )
		return pw_count_r( $filters );
	else
		return 0;

}

function pw_dev_mode(){
	// Returns a boolean, true if Postworld is in dev mode
	return ( defined( 'POSTWORLD_MODE' ) && POSTWORLD_MODE == 'dev' );
}

function pw_bool_to_string( $bool ){
	return ( $bool ) ? 'true' : 'false';
}

function pw_remap_keys( $array = array(), $key_map = array(), $keep_old_keys = false ){
	// Remaps the keys in an array to new values
	/*
		$array = [ A_ARRAY ] // Associative array
		$key_map = array(
			'post_title' => 'title',	// 	Renames 'post_title' key to 'title'
			'post_permalink' => 'url'	//	Renames 'post_permalink' key to 'url'
			)
		$keep_old_keys = [ BOOLEAN ] // Whether or not to keep the old keys in the returned array
	*/

	$newArray = array();
	foreach( $array as $key => $value ){
		foreach( $key_map as $old_key => $new_key ){
			if( $key == $old_key )
				$newArray[ $new_key ] = $value;
			if( $key != $old_key || $keep_old_keys )
				$newArray[ $key ] = $value;
		}
	}
	return $newArray;
}

function pw_remap_keys_array( $arrays = array(), $key_map = array(), $keep_old_keys = false ){
	// Runs an array of arrays through pw_remap_array()
	$newArrays = array();
	foreach( $arrays as $array ){
		$newArrays[] = 	pw_remap_keys( $array, $key_map, $keep_old_keys );
	}
	return $newArrays;
}


function pw_mvc_class( $view, $model, $class = 'selected', $echo = true ){
	if( $view == $model ){
		if( $echo )
			echo $class;
		else
			return $class;
	}
	else
		return false;
}
	

function pw_get_posted_json( $post_var ){
	// Gets a JSON string format $_POST var
	// And converts it into an object / array
	global $_POST;
    // Get the JSON string which represents the post to be saved 
    $post = $_POST[ $post_var ];
    
   // pw_log( 'post from $post_var : ' . $post_var . " : " . $post  );

    // Strip slashes from the string
    $post = stripslashes( $post );
    // Decode the object from JSON into Array
    $post = json_decode( $post, true );
    // If the post is empty, or the decode fails
    if( empty($post) )
        return false;
    else
        return $post;
}

function pw_unique_key( $posts, $key = 'ID' ){
	$tmp = array();
	$unique_posts = array();
	foreach( $posts as $post ){
		if (!in_array($post[$key], $tmp)) {
			$unique_posts[] = $post;
			$tmp[] = $post[$key];
		}
	}
	return $unique_posts;
}


function pw_unique_sub_key( $posts, $sub_key = 'ID' ){
	$tmp = array();
	$unique_posts = array();
	foreach( $posts as $post ){
		$value = pw_get_obj( $post, $sub_key );
		
		if( $value == false )
			return $posts;

		if (!in_array($value, $tmp)) {
			$unique_posts[] = $post;
			$tmp[] = $value;
		}
	}
	return $unique_posts;
}

function pw_print_code( $var ){
	echo "<pre><code>";
	echo htmlspecialchars( json_encode( $var, JSON_PRETTY_PRINT ) );
	echo "</code></pre>";
}

function pw_is_associative( $arr ){
    return array_keys($arr) !== range(0, count($arr) - 1);
}

function pw_core_print_generation_time() {
	?>
	<!-- Generated in <?php timer_stop(1); ?> seconds. (<?php echo get_num_queries(); ?> q) -->
	<?php
}
if( !function_exists( 'bp_core_print_generation_time' ) )
	add_action( 'wp_footer', 'pw_core_print_generation_time' );


function pw_plugin_file( $path, $type = "dir" ){
	if( $type == "dir" )
		return WP_PLUGIN_DIR . "/postworld/" . $path;
	if( $type == "url" )
		return WP_PLUGIN_URL . "/postworld/" . $path;
}


function pw_post_id_exists( $post_id ){
	global $wpdb;
	$results = $wpdb->get_results( 'SELECT  ID  FROM ' . $wpdb->posts . ' WHERE ID = ' . $post_id );
	return !empty( $results );
}

function pw_user_id_exists( $user_id ){
	global $wpdb;
	$results = $wpdb->get_results( 'SELECT  ID  FROM ' . $wpdb->users . ' WHERE ID = ' . $user_id );
	return !empty( $results );
}


function pw_post_id_exists_alt( $post_id ){
	$post = get_post( $post_id );
	return ( $post != null ) ? true : false;
}


function pw_user_id_exists_alt($user_id){
    //global $wpdb;
    //$count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->users WHERE ID = '$user_id'"));
    //if($count == 1){ return true; } else { return false; }
	$user = get_user_by( "id", $user_id );
	return ( $user != false ) ? true : false;
}

function pw_check_user_id($user_id){
	// Checks if given user has permissions to edit usermeta
	// $user_id = the ID of the user whos usermeta is being edited

	///// USER ID /////
	$current_user_id = get_current_user_id();

	if( !isset( $user_id ) || !pw_user_id_exists( $user_id )  )
		$user_id = $current_user_id;

	if( $user_id == 0 )
		return array( 'error' => 'No user ID.' );

	// Security Layer
	// Mode whereby the system can access user meta for special operations
	global $pw;
	if( $pw['security']['mode'] !== 'system' )
		// Check if setting for current user, or if current user can edit users
		if(	$user_id != $current_user_id &&
			!current_user_can( 'edit_users' ) )
			return array( 'error' => 'No permissions.' );
	
	// If passed all tests, return user ID
	return $user_id;

}

function pw_check_user_post( $post_id, $mode = "edit" ){
	// Checks if given user has permissions to edit a post
	// $post_id = the ID of the post being edited

	///// USER ID /////
	$current_user_id = get_current_user_id();

	if( isset( $post_id ) && pw_post_id_exists( $post_id )  ){
		$post = get_post( $post_id );
		$post_author = $post->post_author;
	}
	else
		return array( 'error' => 'No post ID.' );

	// Security Layer
	// Check if setting for current user, or if current user can edit the post
	if(	$post_author != $current_user_id &&
		!current_user_can( $mode.'_others_posts' ) )

		// Check for custom role to edit custom post type
		if( !current_user_can( $mode.'_others_'.$post->post_type.'s' ) )

			return array( 'error' => 'No permissions.' );
	
	
	// If passed all tests, return post ID
	return $post_id;

}

function pw_empty_array( $format ){
	// Return an empty array
	if( $format == "A_ARRAY" )
		return array();
	else if( $format == "JSON" )
		return "{}";
}

function object_to_array($data){
    if (is_array($data) || is_object($data)){
        $result = array();
        foreach ($data as $key => $value){
            $result[$key] = object_to_array($value);
        }
        return $result;
    }
    return $data;
}


////////// EDIT POST HELP FUNCTIONS //////////

function pw_get_post_types( $args = array() ){
	//$user_role = get_user_role();

	if( empty( $args ) )
		$args = array(
		   'public'   => true,
		   //'_builtin' => true,
		   //'capability_type' => 'post',
		);

	$post_types_obj = get_post_types( $args, 'objects');

	$post_types = array();
	foreach ( $post_types_obj as $key => $value) {
		$slug = $key;
		$name = $value->labels->name;
		$post_types[$slug] = $name;
	}
	return $post_types;
}


//////////// BRANCH : Create a recursive branch from a flat object ////////////
function tree_obj( $object, $parent = 0, $depth = 0, $settings ){
	extract($settings);

	///// DEFAULTS /////
	if (empty($max_depth))	$max_depth = 10;
	if (empty($fields))		$fields = array('name');
	if (empty($id_key))		$id_key = 'id';
	if (empty($parent_key))	$parent_key = 'parent';
	if (empty($child_key))	$child_key = 'children';

	///// LOCAL BRANCH /////
	// Check Depth
	if($depth > $max_depth) return ''; // Make sure not to have an endless recursion

	 // Setup Local Branch
	 $branch = array();

	 // Cycle through each item in the Object
	 for($i=0, $ni=count($object); $i < $ni; $i++){
	 	// If the current item is the same as the current cycling parent, add the data
	 	if( $object[$i][$parent_key] == $parent ){
	 		// Setup / Clear Branch Child Array
			$branch_child = array();
			// Get all fields
			if( $fields == 'all' ){
				$branch_child = $object[$i];
			}
			// Get an array of particular fields
			else if( gettype($fields) == 'array' ){
				// Transfer individual fields
				foreach ($fields as $field) {
					$branch_child[$field] = $object[$i][$field];
				}
			}
			// Perform callback
			if ( $callback ){
				// If $callback_fields is included, pass that to the callback
				if (is_array($callback_fields)){
					// Get the live variable values of the callback array inputs 
					$callback_fields_live = array();
					foreach( $callback_fields as $field_name ){
						// Replace field request with the actual value.
						// Example : id >> 24
						// Derived from the original $object
						$field_value = $object[ $i ][ $field_name ];
						array_push( $callback_fields_live, $field_value );
					}
					$callback_data = call_user_func_array($callback,array($callback_fields_live));
				}
				// Otherwise run the callback with no inputs
				else {
					$callback_data = call_user_func($callback);
				}
				// Merge back the result of the callback
				$branch_child = array_merge($branch_child, $callback_data);
			}
	 		// Run Branch recursively and find children
	 		$children = tree_obj($object, $object[$i][$id_key], $depth+1, $settings);
	 		// If there are children, merge them into the branch_child as sub Array
	 		if (!empty($children)){
		 		$branch_child[$child_key] = $children;
	 		}
	 		// Push Branch Child data to Local Branch
		 	array_push($branch, $branch_child);
	 	}
	 }
	 return $branch;
}


////////// WP OBJECT TREE //////////
// Generates a hierarchical tree from a flat Wordpress object
function wp_tree_obj($args){
	extract($args);

	// OBJECT -> ARRAY()
	if ( is_object($object[0]) )
		$object = object_to_array($object);

		// ROOT
		$settings = array(
			'fields' => $fields,
			'id_key' => $id_key,
			'parent_key' => $parent_key,
			'child_key' => $child_key,
			'max_depth' => $max_depth,
			'callback' => $callback,
			'callback_fields' => $callback_fields,
			);

		$tree_obj = tree_obj( $object, 0, 0, $settings );

	return $tree_obj;
}


function extract_parenthesis_values ( $input, $force_array = true ){
	// Extracts comma deliniated values which are contained in parenthesis
	// Returns an Array of values that were previously comma deliniated,
	// unless $force_array is set TRUE.

	// Extract contents of (parenthesis)
	preg_match('#\((.*?)\)#', $input, $match);

	// Split into an Array
	if( isset($match[1]) )
		$value_array = explode(',', $match[1]);
	else
		$value_array = array();

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


function extract_bracket_values ( $input, $force_array = true ){
	// Extracts comma deliniated values which are contained in square brackets
	// Returns an Array of values that were previously comma deliniated,
	// unless $force_array is set TRUE.

	$match=array();

	// Extract contents of (parenthesis)
	preg_match('#\[(.*?)\]#', $input, $match);

	// Split into an Array
	if( isset($match[1]) )
		$value_array = explode(',', $match[1]);
	else
		$value_array = array();
	
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

	if( !is_array( $fields_array ) )
		return false;

	$values_array = array();
	foreach ($fields_array as $field) {
		if ( strpos( $field, $query_string ) !== FALSE )
			// Push $field into $values_array
		    array_push($values_array, $field);
	}
	return $values_array;
}


function extract_linear_fields( $fields_array, $query_string, $force_array = true ){
	// Extracts nested comma deliniated values starting with $query_string from $fields_array
	// and returns them in a new Array.
	$fields_request = extract_fields( $fields_array, $query_string );

	if (!empty($fields_request)){
		$extract_fields = array();
		// Process each request one at a time >> author(display_name,user_name,posts_url) 
		foreach ($fields_request as $field_request) 
			$extract_fields = array_merge( $extract_fields, extract_parenthesis_values($field_request, true) );

		// If only one value, return string
		if ( count($extract_fields) == 1 && $force_array == false )
			return $extract_fields[0];
		// If multiple values, return Array
		else
			return $extract_fields;
	}
	else
		return false;
}



function extract_hierarchical_fields( $fields_array, $query_string ){
	// Extracts nested comma deliniated values starting with $query_string from $fields_array
	// And nests inside it fields which are with it in square brackets
	// and returns them in a new Array.

	$fields_request = extract_fields( $fields_array, $query_string );
	// RESULT : ["taxonomy(category)[id,name]","taxonomy(topic,section)[id,slug]"]

	if (!empty($fields_request)){

		$extract_fields = array();

		///// ROOT VALUES /////
		// Process each request one at a time >> author(display_name,user_name,posts_url) 
		foreach ($fields_request as $field_request){

			$root_values = extract_parenthesis_values($field_request, true);

			///// PROCESS SUB-VALUES /////
			// If there are sub-fields defined inside [square,brackets]
				$sub_values = extract_bracket_values($field_request, true);

				// Cycle through each sub-value and apply it to the root field
				foreach ($root_values as $value) {
					$hierarchical_values[$value] = $sub_values;					
				}
				$extract_fields = array_merge( $extract_fields, $hierarchical_values);
		}
		return $extract_fields;
	}
	else
		return false;
}


// Previously get_avatar_url()
function pw_get_avatar_url( $user_id, $avatar_size ){
	// Get Buddypress Avatar Image
	if ( function_exists('bp_core_fetch_avatar') ) {
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

function post_time_ago($post_id) {
	$post_time = get_post_time( 'U', true, $post_id );
	return time_ago( $post_time );
}

function time_ago($timestamp){
    //type cast, current time, difference in timestamps
    $timestamp      = (int) $timestamp;
    $current_time   = time();
    $diff           = $current_time - $timestamp;
    
    //intervals in seconds
    $intervals      = array (
        'year' => 31556926, 'month' => 2629744, 'week' => 604800, 'day' => 86400, 'hour' => 3600, 'minute'=> 60
    );
    
    //now we just find the difference
    if ($diff == 0){
        return 'just now';
    }    

    if ($diff < 60){
        return $diff == 1 ? $diff . ' second ago' : $diff . ' seconds ago';
    }        

    if ($diff >= 60 && $diff < $intervals['hour']){
        $diff = floor($diff/$intervals['minute']);
        return $diff == 1 ? $diff . ' minute ago' : $diff . ' minutes ago';
    }        

    if ($diff >= $intervals['hour'] && $diff < $intervals['day']){
        $diff = floor($diff/$intervals['hour']);
        return $diff == 1 ? $diff . ' hour ago' : $diff . ' hours ago';
    }    

    if ($diff >= $intervals['day'] && $diff < $intervals['week']){
        $diff = floor($diff/$intervals['day']);
        return $diff == 1 ? $diff . ' day ago' : $diff . ' days ago';
    }    

    if ($diff >= $intervals['week'] && $diff < $intervals['month']){
        $diff = floor($diff/$intervals['week']);
        return $diff == 1 ? $diff . ' week ago' : $diff . ' weeks ago';
    }    

    if ($diff >= $intervals['month'] && $diff < $intervals['year']){
        $diff = floor($diff/$intervals['month']);
        return $diff == 1 ? $diff . ' month ago' : $diff . ' months ago';
    }    

    if ($diff >= $intervals['year']){
        $diff = floor($diff/$intervals['year']);
        return $diff == 1 ? $diff . ' year ago' : $diff . ' years ago';
    }
}

function username_exists_by_id($user_id){
	$userdata = get_userdata( $user_id );
	if($userdata != false){ return true; } else{ return false; }
}

// DEPRECIATED
function post_exists_by_id($post_id){
	$post = get_post( $post_id );
	if($post != null){ return true; } else{ return false; }
}

function pw_crop_string_to_word( $string, $max_chars = 200, $suffix = "..." ){
	if (strlen($string) > $max_chars) {
	    $string = substr($string, 0, $max_chars);
	    $string = substr($string, 0, strrpos($string, ' '));        
	    $string .= $suffix;
	}
	return $string;
	//return substr($string, 0, strrpos(substr($string, 0, $max_chars), ' '));
}

function extract_array_from_object( $object, $field_key ){
	// Extracts one series of field values
	// From an array of associative array/objects
	// Into a flat array

	// INPUT : $ids = [{"ID":"6427"},{"ID":"8979"},{"ID":"1265"}...]
	// FUNCTION : extract_array_from_object($ids, "ID")
	// OUTPUT : ["6427","8979","1265"...]

	$array = array();
	// Convert into ARRAY_A
	$object = json_decode(json_encode($object), true);
	foreach( $object as $item ){
		array_push( $array, $item[$field_key]  );
	}
	return $array;
}


function pw_type_cast( $value, $type ){

	// Type Cast
	if( $type == 'int' || $type == 'integer' )
		$value = (int) $value;
	else if( $type == 'float' || $type == 'double' || $type == 'real' )
		$value = (boolean) $value;
	else if( $type == 'string' )
		$value = (string) $value;
	else if( $type == 'boolean' || $type == 'bool' )
		$value = (boolean) $value;
	else if( $type == 'object' )
		$value = (object) $value;

	return $value;
}


function pw_switch_value( $switch ){
	// Convert 1/true to 'on', 0/false to 'off'
	$switch = (string) $switch;

	// Homogonize Switch Variable
	if( $switch == 'on' || $switch == '1'|| $switch == 'true' )
		$switch = 'on';
	else if( $switch == 'off' || $switch == '0' || $switch == 'false' )
		$switch = 'off';

	return $switch;

};

function pw_toggle_array( $args ){
	// Takes in an
	/*
		$args = array()
			'input'   => JSON string or ARRAY,
			'format'  => JSON / ARRAY,
			'value'	  => // which value to add or remove from the input
			'switch'  => // force the value on or off, if not set will toggle
			'type'    => // type (if any) which to force the value, ie. integer, string, etc.
			);
	*/

	extract($args);
	
	// Convert 1/true to 'on', 0/false to 'off'
	$switch = pw_switch_value( $switch );
	

	// Decode JSON
	if( $format == 'JSON' ){
		$input = json_decode( $input, 1 );
	}

	// Type Cast
	if( isset($type) ){
		$value = pw_type_cast($value, $type);
	}
		
	// Handle Switch
	switch ($switch) {
		case "on":
			// Add $value to $input
			if( !in_array( $value, $input ) )
				array_push( $input, $value );
			break;
		case "off":
			// Remove $value from $input
			$new_input = array();
			foreach ( $input as $input_item) { 
	            if ( $input_item != $value ) { 
	                array_push( $new_input, $input_item ); 
	            }
	        }
	        $input = $new_input;

			break;
		case "toggle":
			if( in_array( $value, $input ) )
				$input = array_diff( $input, $value );
			else
				array_push( $input, $value );
			break;
	}

	// Encode JSON
	if( $format == 'JSON' ){
		$input = json_encode( $input );
	}

	return $input;

}


function pw_set_html_attr( $attribute, $value, $add_string = '' ){
	$output = '';

	if( !empty($add_string) )
		$add_string = " " . $add_string;

	if( !empty($value) )
		$output .= " " . $attribute . "=\"" . $value . $add_string . "\" ";

	return $output;

}

function pw_print_html_attr( $attribute, $value, $add_string = '' ){
	echo pw_set_html_attr( $attribute, $value, $add_string);
}


function pw_include_h2o(){
	// Include h2o template engine
	//global $pw;
	//require_once $pw['paths']['postworld_dir'].'/lib/h2o/h2o.php';
}

function pw_set_defaults( $obj, $defaults ){
	// Sets default sub-key values of an object
	// Only does one level, with no recursion

	foreach( $defaults as $key => $value ){
		if( !isset($obj[$key]) )
			$obj[$key] = $value;
	}

	return $obj;

}

function pw_ob_include( $file, $vars = array() ){
	if( !empty( $vars ) && is_array( $vars ) )
		extract($vars);

	if( empty( $file ) )
		return "pw_ob_include : No file path provided.";

	ob_start();
	include $file;
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}

function pw_ob_include_template( $template_path, $vars = array() ){
	return pw_ob_include( locate_template( $template_path ), $vars );
}

function pw_ob_function( $function, $vars = array() ){
	ob_start();
	call_user_func( $function, $vars );
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}

function pw_get_post_ids( $posts ){
	// Returns just an array of IDs from an array of posts

	$posts = (array) $posts;
	$post_ids = array();

	foreach( $posts as $post ){
		if( isset($post['id']) )
			$id = $post['id'];
		else if( isset($post['ID']) ) 
			$id = $post['ID'];
		else
			continue;

		$post_ids[] = $id;

	}

	return $post_ids;

}


/**
 * @param string $code name of the shortcode
 * @param string $content
 * @return string content with shortcode striped
 */
function pw_strip_shortcode($code, $content)
{
    global $shortcode_tags;

    $stack = $shortcode_tags;
    $shortcode_tags = array($code => 1);

    $content = strip_shortcodes($content);

    $shortcode_tags = $stack;
    return $content;
}

// Strips the site URL from a URL
// Returning the relative/absolute path
function pw_strip_site_url( $url ){
	$path = str_replace( get_site_url(), '', $url );
	// Add "/" to the start, if it doesn't exist
	if( substr( $path, 0, 1 ) != "/" )
		$path = "/".$path;
	return $path;
}

// Wraps quotes around a string
function pw_wrap_quotes( $string ){
    return "\"" . $string . "\"";
}

function pw_random_hash(  $length = 8  ){
	$hash = hash('md5', rand( 0, 10000 ));
	$hash = substr( $hash, 1, $length );
	return $hash;
}

function pw_random_string( $length = 8 ){
	return pw_random_hash(  $length );
}


function pw_to_array($obj){
	// Recursively makes an object into an Associative Array
    if (is_object($obj)) $obj = (array)$obj;
    if (is_array($obj)) {
        $new = array();
        foreach ($obj as $key => $val) {
            $new[$key] = pw_to_array($val);
        }
    } else {
        $new = $obj;
    }

    return $new;
}

function pw_body_classes(){
	// Returns a string with the Wordpress body classes
	$body_classes = '';
	foreach( get_body_class() as $class ){
		$body_classes .= " " . $class;
	}
	return $body_classes;
}


function pw_get_menus(){
	$menus = get_terms( 'nav_menu' );

	// Convert some values to integers
	if( !empty($menus) ){
		$new_menus = array();
		foreach( $menus as $menu ){
			$menu->term_id = intval($menu->term_id);
			$menu->term_group = intval($menu->term_group);
			$menu->term_taxonomy_id = intval($menu->term_taxonomy_id);
			array_push($new_menus, $menu);
		}
		$menus = $new_menus;
	}

	return $menus;
}



///// DEPRECIATED /////

function pw_get_view_type(){
	// Determine the view type
	$view_type = "default";

	if( is_archive() && !is_date() )
		$view_type = 'archive-taxonomy';
	if( is_post_type_archive() )
		$view_type = 'archive-post-type'; 
	//else if( is_archive() && is_date() && !is_year() )
	//	$view_type = 'archive-date';
	if( is_year() )
		$view_type = 'archive-year';
	if( is_month() )
		$view_type = 'archive-month';
	if( is_day() )
		$view_type = 'archive-day';
	if( is_page() )
		$view_type = 'page';
	if( is_page() )
		$view_type = 'page';
	if( is_single() )
		$view_type = 'post';

	return $view_type;
}

///// DEPRECIATED /////
function pw_current_context_class(){
	/// DEFINE CLASS ///
	// home / archive / blog / page / single / attachment / default

	if( is_front_page() )
		$class = 'home';

	if( is_archive() )
		$class = 'archive'; 	

	if( is_search() )
		$class = 'search';

	if( is_tag() )
		$class = 'tag'; 		

	if( is_category() )
		$class = 'category'; 

	//if( is_blog_page() )
	//	$class = 'blog';

	if( is_page() )
		$class = 'page';

	if( is_single() )
		$class = 'single';

	if( is_attachment() )
		$class = 'attachment';

	if( !isset( $class ) )
		$class = 'default';

	return $class;

}



function pw_is_buddypress_active(){
	global $bp;
	return ( !empty( $bp ) && function_exists('bp_is_active') ) ? true : false;
}

function pw_autocorrect_layout( $layout ){
	// In the case of an old data model, auto-correct layout settings
	if( isset( $layout['layout'] ) && !isset( $layout['template'] ) )
		$layout['template'] = $layout['layout'];
	return $layout;
}



function pw_clean_input($input) {

  $search = array(
    '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
    '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
    '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
    '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
  );

    $output = preg_replace($search, '', $input);
    return $output;
  }

function pw_sanitize($input) {
    if (is_array($input)) {
        foreach($input as $var=>$val) {
            $output[$var] = pw_sanitize($val);
        }
    }
    else {
        if (get_magic_quotes_gpc()) {
            $input = stripslashes($input);
        }
        $input  = pw_clean_input($input);
        $output = mysql_real_escape_string($input);
    }
    return $output;
}

function pw_sanitize_key( $key ){
	// Convert string to lower case
	$key = strtolower($key);
	// Replace Spaces with Underscores ( _ )
	$key = preg_replace('/\s+/', '_', $key);
	
	// Sanitize (overkill)
	//$key = pw_sanitize($key);

	return $key;
}

function pw_sanitize_numeric( $val, $require_numeric = false ){
	// Casts all numeric calues as floats/numbers
	if( is_numeric( $val ) ){
		return (float) $val ;
	}
	else{
		if( $require_numeric )
			return false;
		else
			return $val;
	}

}

function pw_sanitize_numeric_array( $vals = array(), $require_numeric = false, $remove_non_numeric = true, $reindex = true ){
	// Numerically santize a flat array of values
	$new_vals = array();

	// Iterate through each value
	for( $i=0; $i < count($vals); $i++ ){
		$numeric = pw_sanitize_numeric( $vals[$i], $require_numeric );
		// If removing non-numerics, and it's not numeric
		if( $remove_non_numeric && $numeric == false )
			// Continue to next iteration
			continue;
		// If reindexing the array
		else if( $reindex )
			// Add it to the array with fresh index
			$new_vals[] = $numeric;
		// Otherwise
		else		
			// Add the value to the output array at the same index
			$new_vals[$i] = $numeric;

	}
	return $new_vals;
}

function pw_sanitize_numeric_a_array( $vals = array() ){
	// Numerically sanitize an associative array of values

	$sanitized = array();
	foreach( $vals as $key => $val ){
		$sanitized[$key] = pw_sanitize_numeric( $val );
	}
	return $sanitized;

}

function pw_sanitize_numeric_array_of_a_arrays( $vals ){
	// Numerically sanitize an array of associative arrays

	for( $i=0; $i < count($vals);  $i++ ){
		$vals[$i] = pw_sanitize_numeric_a_array( $vals[$i] );
	}
	return $vals;
}

function pw_find_where( $array, $key_value_pair = array( "key" => "value" ) ){
	// Looks through the list and returns the first value
	// That matches the key value pair listed in properties
	// TODO : Refactor to accept multipe key->value pairs

	// Get the first Key and Value
	foreach( $key_value_pair as $get_key => $get_value ){
		$key = $get_key;
		$value = $get_value;
		break;
	}

	// Search for the key/value in the given array
	foreach( $array as $sub_array ){
		if(	isset( $sub_array[$key] ) &&
			$sub_array[$key] == $value )
			return $sub_array;
	}
	return false;

}

function pw_reject( $list, $key_value_pair = array( "key" => "value" ) ){
	// Returns a list with items continaing the key->value pair removed
	// TODO : Refactor to accept multipe key->value pairs
	// TODO : Add Operator parameter, "AND" / "OR" for multiple key->value pairs


	pw_log('LIST : ' .count($list));

	// Get the first Key and Value
	foreach( $key_value_pair as $get_key => $get_value ){
		$key = $get_key;
		$value = $get_value;
		break;
	}

	$new_list = array();

	foreach( $list as $item ){
		if(	isset( $item[$key] ) &&
			$item[$key] == $value )
			continue;
		else
			$new_list[] = $item;
	}

	pw_log('NEW LIST : ' .count($new_list));

	return $new_list;

}

function pw_array_order_by(){
	// Orders items in an array by key
	// $ordered = pw_array_order_by( $items, $order_key, SORT_DESC / SORT_ASC )

	$args = func_get_args();
	$data = array_shift($args);
	foreach ($args as $n => $field) {
		if (is_string($field)) {
			$tmp = array();
			foreach ($data as $key => $row)
				$tmp[$key] = $row[$field];
			$args[$n] = $tmp;
			}
	}
	$args[] = &$data;
	call_user_func_array('array_multisort', $args);
	return array_pop($args);
}


function pw_reset_less_php_cache(){
	$ghost_less_file = get_infinite_directory() .'/less/ghost.less';
	$file = fopen( $ghost_less_file ,"w" );
	fwrite($file,"// Reset PHP LESS Cache");
	fclose($file);
	chmod($pwGlobalsJsFile, 0755);
	return true;
}


function pw_in_string( $haystack, $needle ){
	return ( strpos( $haystack, $needle ) == false ) ? true : false;
}


/*
function pw_get_post_types(){
	$args = array(
		 'public'   => true,
		  '_builtin' => false
	);

	$output = 'names'; // names or objects, note names is the default
	$operator = 'and'; // 'and' or 'or'

	$post_types = get_post_types( $args, $output, $operator ); 
	print_r($post_types);
}
*/

?>