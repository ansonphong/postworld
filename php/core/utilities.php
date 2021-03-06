<?php
/*_   _ _   _ _ _ _   _           
 | | | | |_(_) (_) |_(_) ___  ___ 
 | | | | __| | | | __| |/ _ \/ __|
 | |_| | |_| | | | |_| |  __/\__ \
  \___/ \__|_|_|_|\__|_|\___||___/
///////////////////////////////////////*/
/**
 * @todo Cleanup and document the functions in this file.
 */

/**
 * Gets the current Postworld Mode
 */
function pw_mode(){
	return ( defined('POSTWORLD_MODE') ) ?
		POSTWORLD_MODE : 'deploy';
}

/**
 * Refreshes the page.
 */
function pw_refresh(){
	if( !headers_sent() ){
		header("Refresh:0");
		exit;
	}
}

/**
 * Sorts an array of associative arrays by one of the key values.
 *
 * @example sort_array_of_array($inventory, 'price');
 */
function pw_sort_array_of_arrays(&$array, $subfield, $order = 'ASC' ){
	if( !is_array($array) )
		return false;
	if( $order == 'ASC' )
		$order = SORT_ASC;
	else
		$order = SORT_DESC;

	$sortarray = array();
	foreach ($array as $key => $row){
		$sortarray[$key] = $row[$subfield];
	}
	array_multisort($sortarray, $order, $array);
}



function pw_typecast_if_boolean( $value ){
	if( $value == 'true' )
		$value = true;
	elseif( $value == 'false' )
		$value = false;
	return $value; 
}

/**
 * Outputs the specified admin template to the footer.
 */
function pw_ob_admin_footer_script( $template_id, $vars = array() ){
	$output = pw_ob_admin_template( $template_id, $vars );
	return pw_register_footer_script( $output );
}

/**
 * Outputs the specified template to the footer.
 *
 * @param string $template_path Template path relative to theme, ie. 'views/sliders/slider-scripts.php'
 */
function pw_ob_footer_script( $template_path, $vars ){
	$output = pw_ob_include_template( $template_path, $vars );
	return pw_register_footer_script( $output );
}

/**
 * Used to begin wrapping a script which is to be placed
 * Into the footer using pw_register_footer_script()
 * This must be closed with pw_end_footer_script();
 */
function pw_start_footer_script(){
	// End any existing output buffering
	ob_end_clean();
	// Start a fresh output buffering
	ob_start();
	return;
}

/**
 * Used to end wrapping a script which is to be placed
 * Into the footer using pw_register_footer_script()
 * This must be follow with pw_start_footer_script();
 */
function pw_end_footer_script(){
	$output = ob_get_contents();
	// End output buffering
	ob_end_clean();
	pw_register_footer_script( $output );
	return;
}

/**
 * Prints pre-defined scripts to the footer
 */
function pw_register_footer_script( $script ){
	$GLOBALS['pw_footer_scripts'][] = $script;
}
add_action('wp_print_footer_scripts','pw_print_footer_scripts', 100);
add_action('admin_print_footer_scripts','pw_print_footer_scripts', 100);
function pw_print_footer_scripts(){
	foreach($GLOBALS['pw_footer_scripts'] as $script){
		echo $script;
		echo "\n";
	}
}

/**
 * Inject PHP data into an arbitrary Angular Controller.
 *
 * @param string $vars['controller'] The name of the Angular controller
 * @param array $vars['vars'] A series of key value pairs which are output to $scope as JSON
 */
function pw_make_ng_controller( $vars = array() ){
	$include_script_tags = _get( $vars, 'include_script_tags' );

	if( !isset($vars['app']) )
		$vars['app'] = 'postworld'; // postworld | postworldAdmin

	$output = '';

	if( $include_script_tags )
		$output .="<script>\n";

	$output .= $vars['app'].".controller('".$vars['controller']."',function(\$scope){\n";
	foreach( $vars['vars'] as $key => $value ){
		$print_value = json_encode($value);
		$output .= "\$scope.".$key." = ".$print_value.";\n";
	}
	$output .= "})\n";

	if( $include_script_tags )
		$output .="</script>\n";

	return $output;
}

/**
 * Prints an Angular controller in the footer of the page.
 */
function pw_print_ng_controller($vars){
	$vars['include_script_tags'] = true;
	$script = pw_make_ng_controller($vars);
	//wp_add_inline_script( $vars['controller'].pw_random_string(), $script );
	pw_register_footer_script( $script );
}


/**
 * Gets the URI for the Postworld directory
 * @return string The URI
 */
function postworld_directory_uri(){
	$template_uri = get_template_directory_uri();
	$template_dir = get_template_directory();
	$postworld_dir = POSTWORLD_PATH;

	// Subtract the Infinite Directory from the Template Dir
	$relative_path = str_replace( $template_dir, '', $postworld_dir );

	// Add the difference to the Template URI
	return $template_uri . $relative_path;
}

function pw_access_protected($obj, $prop) {
  $reflection = new ReflectionClass($obj);
  $property = $reflection->getProperty($prop);
  $property->setAccessible(true);
  return $property->getValue($obj);
}



/**
 * ** DEPRECIATED **
 * @use pw_theme_slug()
 *
 * Gets the submenu slug used to group admin menu items
 * Under the desired main menu item.
 * @return string
 */
function pw_admin_submenu_slug(){
	return pw_theme_slug();
}

/**
 * Returns the date in the requested format, a period of time ago
 * @param $period_ago [integer] Number of seconds ago to return the date of
 * @param $format [string] PHP date() format to return
 * @return [string] The date the period ago in the requested format
 */
function pw_date_seconds_ago( $seconds_ago, $format ){
	$seconds_ago = (int) $seconds_ago;

	if( !is_integer( $seconds_ago ) || !is_string( $format ) )
		return false;

	$now = time();
	$date_ago = $now - $seconds_ago;

	return date( $format, $date_ago );
}

/**
 * Calculates the number of seconds in the reqested period
 * @param $multiplier [float] Number to multiply period by
 * @param $period [string] The type period length
 * @return [integer] Number of seconds
 */
function pw_seconds_in( $multiplier = 0, $period = 'day' ){

	// Return early if no multiplier
	if( !is_float( $multiplier ) )
		$multiplier = (float) $multiplier;
	if( $multiplier === 0 || is_NaN( $multiplier ) )
		return 0;

	// Localize Time Units Variables
	$t = pw_time_units();
	extract($t);

	// Switch period
	switch( $period ){
		case 'minute':
		case 'minutes':
			$base = $ONE_MINUTE;
			break;
		case 'hour':
		case 'hours':
			$base = $ONE_HOUR;
			break;
		case 'day':
		case 'days':
			$base = $ONE_DAY;
			break;
		case 'week':
		case 'weeks':
			$base = $ONE_WEEK;
			break;
		case 'month':
		case 'months':
			$base = $ONE_MONTH;
			break;
		case 'year':
		case 'years':
			$base = $ONE_YEAR;
			break;
	}

	if( !isset( $base ) )
		return 0;

	else
		return $base * $multiplier;
}

/**
 * Gets a particular pre-formatted date unit from a period ago
 * @see pw_seconds_in()
 * @param $unit [string] Type of date unit, day/month/year
 * @param $period [string] The type period length
 * @return [integer] Number of seconds
 */
function pw_date_unit_at_ago( $unit, $multiplier, $period ){

	$seconds_ago = pw_seconds_in( $multiplier, $period );
	switch( $unit ){
		case 'day':
		case 'days':
			$format = 'j';
			break;
		case 'month':
		case 'months':
			$format = 'n';
			break;
		case 'year':
		case 'years':
			$format = 'Y';
			break;
	}
	if( !isset($format) )
		return false;
	
	return pw_date_seconds_ago( $seconds_ago, $format );
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
 * Display the classes for the post div.
 * Like WP native post_class, without the 'class=' part,
 * And doesn't echo, instead returns string.
 *
 * @param string|array $class   One or more classes to add to the class list.
 * @param int|WP_Post  $post_id Optional. Post ID or post object. Defaults to the global `$post`.
 * @return string A string of post classes.
 */
function pw_post_class( $class, $post_id ){
	return join( ' ', get_post_class( $class, $post_id ) );
};


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
	$timer = _get( $GLOBALS['pw_microtimer'], $timer_id );
	if( $timer !== false )
		return pw_microtime_diff( $timer ); //$current_time - $timer_time;
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

function pw_log( $message, $data = null ){
	if( is_array( $message ) || is_object( $message ) )
		$message = 'JSON:' . json_encode($message, JSON_PRETTY_PRINT);

	if( !empty($data) )
		$message .= " : " . json_encode($data, JSON_PRETTY_PRINT);

	$log_path = POSTWORLD_PATH . "/log";

	// If the log directory doesn't exist, create it
	if (!file_exists($log_path)){
		$mkdir = mkdir($log_path, 0777, true);
		// If the directory can't be created, end here 
		if( !$mkdir )
			return false;
	}

	error_log( $message . "\n", 3, $log_path . "/pw-log-php.txt" );

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
	$post = _get( $_POST, $post_var );
	if( !$post )
		return false;
	
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
if( !function_exists( 'bp_core_print_generation_time' ) &&
	function_exists( 'add_action' ) )
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

/**
 * Checks if given user has permissions to edit usermeta
 * @param integer $user_id The ID of the user whos usermeta is being edited
 */
function pw_check_user_id($user_id){
	///// USER ID /////
	$current_user_id = get_current_user_id();

	if( !isset( $user_id ) || !pw_user_id_exists( $user_id )  )
		$user_id = $current_user_id;

	if( $user_id == 0 )
		return new WP_Error( '400', 'No user ID.' );

	// Security Layer
	// Mode whereby the system can access user meta for special operations
	global $pw;
	if( $pw['security']['mode'] !== 'system' )
		// Check if setting for current user, or if current user can edit users
		if(	$user_id != $current_user_id &&
			!current_user_can( 'edit_users' ) )
			return new WP_Error( '401', 'Insufficient permissions.' );
	
	// If passed all tests, return user ID
	return $user_id;

}

/**
 * Checks if given user has permissions to edit a post
 * @param integer $post_id The ID of the post being edited
 * @return bool|integer
 */
function pw_check_user_post( $post_id, $mode = "edit" ){
	$current_user_id = get_current_user_id();

	if( isset( $post_id ) && pw_post_id_exists( $post_id )  ){
		$post = get_post( $post_id );
		$post_author = $post->post_author;
	}
	else
		return new WP_Error( '400', 'No post ID.' );

	// Security Layer
	// Check if setting for current user, or if current user can edit the post
	if(	$post_author != $current_user_id &&
		!current_user_can( $mode.'_others_posts' ) )

		// Check for custom role to edit custom post type
		if( !current_user_can( $mode.'_others_'.$post->post_type.'s' ) )
			return new WP_Error( '401', 'Insufficient permissions.' );
	
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

function pw_object_to_array($data){
	if (is_array($data) || is_object($data)){
		$result = array();
		foreach ($data as $key => $value){
			$result[$key] = pw_object_to_array($value);
		}
		return $result;
	}
	return $data;
}


////////// EDIT POST HELP FUNCTIONS //////////
/**
 * Extends WordPress native method for retreiving post types.
 * 
 * @param $args Array Passed to WordPress' get_post_types() method
 * @param $fields string (optional) What data to be returned Options, 'default' (default) / 'slugs'
 */
function pw_get_post_types( $args = array(), $fields = 'default' ){
	//$user_role = pw_get_user_role();

	if( empty( $args ) )
		$args = array(
			'public'	=> true,
			//'_builtin' => true,
			//'capability_type' => 'post',
		);

	$post_types_obj = get_post_types( $args, 'objects' );

	$post_types = array();

	foreach( $post_types_obj as $key => $value) {
		if( $fields === 'default' ){
			$slug = $key;
			$name = $value->labels->name;
			$post_types[$slug] = $name;
		}
		if( $fields === 'slugs' ){
			$post_types[] = $key;
		}
	}

	return $post_types;
}


/**
 * BRANCH : Create a recursive branch from a flat object
 * @todo Refactor not using extract, use default array merge method.
 */
function pw_make_tree_obj( $object, $parent = 0, $depth = 0, $settings = array() ){
	$default_settings = array(
		'fields' => array('name'),
		'id_key' => 'id',
		'parent_key' => 'parent',
		'child_key' => 'children',
		'max_depth' => 10,
		'callback' => null,
		'callback_fields' => null,
		);
	$s = array_replace( $default_settings, $settings );

	///// LOCAL BRANCH /////
	// Check Depth to prevent endless recursion
	if( $depth > $s['max_depth'])
		return '';

	 // Setup Local Branch
	 $branch = array();

	 // Cycle through each item in the Object
	 for($i=0, $ni=count($object); $i < $ni; $i++){
		// If the current item is the same as the current cycling parent, add the data
		if( $object[$i][ $s['parent_key'] ] == $parent ){
			// Setup / Clear Branch Child Array
			$branch_child = array();
			// Get all fields
			if( $s['fields'] == 'all' ){
				$branch_child = $object[$i];
			}
			// Get an array of particular fields
			else if( gettype($s['fields']) == 'array' ){
				// Transfer individual fields
				foreach ($s['fields'] as $field) {
					$branch_child[$field] = $object[$i][$field];
				}
			}
			// Perform callback
			if( isset($s['callback']) && !empty($s['callback']) ){
				// If $callback_fields is included, pass that to the callback
				if( is_array( $s['callback_fields'] ) ){
					// Get the live variable values of the callback array inputs 
					$callback_fields_live = array();
					foreach( $s['callback_fields'] as $field_name ){
						// Replace field request with the actual value.
						// Example : id >> 24
						// Derived from the original $object
						$field_value = $object[ $i ][ $field_name ];
						array_push( $callback_fields_live, $field_value );
					}
					$callback_data = call_user_func_array( $s['callback'], array($callback_fields_live) );
				}
				// Otherwise run the callback with no inputs
				else {
					$callback_data = call_user_func( $callback );
				}
				// Merge back the result of the callback
				$branch_child = array_merge( $branch_child, $callback_data );
			}
			// Run Branch recursively and find children
			$children = pw_make_tree_obj( $object, $object[$i][ $s['id_key'] ], $depth+1, $settings );
			// If there are children, merge them into the branch_child as sub Array
			if (!empty($children)){
				$branch_child[ $s['child_key'] ] = $children;
			}
			// Push Branch Child data to Local Branch
			array_push($branch, $branch_child);
		}
	 }
	 return $branch;
}


////////// WP OBJECT TREE //////////
// Generates a hierarchical tree from a flat Wordpress object
function wp_tree_obj( $args ){
	extract($args);

	// OBJECT -> ARRAY()
	if ( is_object($object[0]) )
		$object = pw_object_to_array($object);

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

		$tree_obj = pw_make_tree_obj( $object, 0, 0, $settings );

	return $tree_obj;
}


function pw_extract_parenthesis_values ( $input, $force_array = true ){
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


function pw_extract_bracket_values ( $input, $force_array = true ){
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


function pw_extract_fields( $fields_array, $query_string ){
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


function pw_extract_linear_fields( $fields_array, $query_string, $force_array = true ){
	// Extracts nested comma deliniated values starting with $query_string from $fields_array
	// and returns them in a new Array.
	$fields_request = pw_extract_fields( $fields_array, $query_string );

	if (!empty($fields_request)){
		$extract_fields = array();
		// Process each request one at a time >> author(display_name,user_name,posts_url) 
		foreach ($fields_request as $field_request) 
			$extract_fields = array_merge( $extract_fields, pw_extract_parenthesis_values($field_request, true) );

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



function pw_extract_hierarchical_fields( $fields_array, $query_string ){
	// Extracts nested comma deliniated values starting with $query_string from $fields_array
	// And nests inside it fields which are with it in square brackets
	// and returns them in a new Array.

	$fields_request = pw_extract_fields( $fields_array, $query_string );
	// RESULT : ["taxonomy(category)[id,name]","taxonomy(topic,section)[id,slug]"]

	if (!empty($fields_request)){

		$extract_fields = array();

		///// ROOT VALUES /////
		// Process each request one at a time >> author(display_name,user_name,posts_url) 
		foreach ($fields_request as $field_request){

			$root_values = pw_extract_parenthesis_values($field_request, true);

			///// PROCESS SUB-VALUES /////
			// If there are sub-fields defined inside [square,brackets]
				$sub_values = pw_extract_bracket_values($field_request, true);

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

/**
 * Takes a string of comma delimited values
 * And converts it into an array
 *
 * @param string $string A string of delimited values.
 * @param string $delimiter
 * @return array The delimited values.
 */
function pw_delimited_to_array( $string, $delimiter = ',' ){
	if( !is_string( $string ) )
		return false;

	$array = explode( $delimiter, $string );

	// Change numeric to float values
	for ($i=0; $i < count( $array ); $i++) { 
		if( is_numeric( $array[$i] ) )
			$array[$i] = (float) $array[$i];
	}

	return $array;

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

/**
 * Returns the boolean value of a string.
 *
 * @todo Refactor for performance.
 */
function pw_to_bool( $value ){
	if( gettype( $value ) === 'boolean' )
		return $value;
	$switch = pw_switch_value($value);
	return ( $switch === 'on' ) ? true : false;
}

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
	global $pw;

	///// CACHING LAYER /////
	if( in_array( 'layout_cache', pw_enabled_modules() ) ){
		$hash_array = array(
			'file' 		=> $file,
			'vars' 		=> $vars,
			'device' 	=> pw_device_meta(),
			'view' 		=> $pw['view'],
			'_get'		=> $_GET,
			//'user_id'		=> get_current_user_id()
			);
		//pw_log( 'hash_array', $hash_array );
		$cache_hash = hash( 'sha256', json_encode( $hash_array ) );
		//pw_log( 'cache_hash', $cache_hash );
		$get_cache = pw_get_cache( array( 'cache_hash' => $cache_hash ) );

		// If cached content, echo it here and return
		if( !empty( $get_cache ) ){
			$cache_content = $get_cache['cache_content'];
			echo $cache_content;
			return;
		}
	}

	// If no cached content, do regular ob_include
	if( !empty( $vars ) && is_array( $vars ) )
		extract($vars);
	if( empty( $file ) )
		return "pw_ob_include : No file path provided.";
	ob_start();
	include $file;
	$content = ob_get_contents();
	ob_end_clean();

	///// CACHING LAYER /////
	if( in_array( 'layout_cache', pw_enabled_modules() ) )
		pw_set_cache( array(
			'cache_type'	=>	'layout',
			'cache_name'	=> 	'include:'.$file,
			'cache_hash' 	=> 	$cache_hash,
			'cache_content'	=>	$content,
			));

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

function pw_html_classes(){
	// Returns a string with the Wordpress body classes
	$classes = array();
	$output = '';

	if( pw_module_enabled('devices') )
		$classes = array_merge( $classes, pw_device_classes() );
	
	foreach( $classes as $class ){
		$output .= " " . $class;
	}
	return $output;
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


/**
 * **DEPRECIATED**
 * Determine the view type
 */
function pw_get_view_type(){
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

/**
 * **DEPRECIATED**
 * Define Context Class
 */
function pw_current_context_class(){
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

/**
 * Return boolean wether or not BuddyPress is active.
 */
function pw_is_buddypress_active(){
	global $bp;
	return ( !empty( $bp ) && function_exists('bp_is_active') ) ? true : false;
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
	return $key;
}

function pw_sanitize_numeric( $val, $require_numeric = false ){
	// Casts all numeric calues as floats/numbers
	if( is_numeric( $val ) ){
		return (float) $val;
	}
	else{
		if( $require_numeric )
			return false;
		else
			return $val;
	}
}

/**
 * Numerically santize a flat array of values
 */
function pw_sanitize_numeric_array( $vals = array(), $require_numeric = false, $remove_non_numeric = true, $reindex = true ){
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

/**
 * Numerically sanitize an associative array of values
 */
function pw_sanitize_numeric_a_array( $vals = array() ){
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

/**
 * Converts a string containing delimited integers
 * into an array of integers.
 *
 * @param string $string A string of comma delimited integers
 * @param string $delimited The boundary string.
 *
 * @return array An array of integers 
 */
function pw_integer_string_to_array( $string, $delimiter = "," ){
	if( !is_string( $string ) )
		return false;
	if( empty( $string ) )
		return array();

	$string_parts = explode( $delimiter, $string);
	$integers = array();
	foreach ($string_parts as $string_part){
		$integers[] = intval( trim($string_part) );
	}

	return $integers;
}

/**
 * Looks through the list and returns the first value
 * That matches the key value pair listed in properties
 *
 * @todo Refactor to accept multipe key->value pairs
 */
function pw_find_where( $array, $key_value_pair = array( "key" => "value" ) ){
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

/**
 * Returns a list with items continaing the key->value pair removed
 *
 * @todo Refactor to accept multipe key->value pairs
 * @todo Add Operator parameter, "AND" / "OR" for multiple key->value pairs
 */
function pw_reject( $list, $key_value_pair = array( "key" => "value" ) ){
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
	return $new_list;
}

/**
 * Sorts an associative array by the value of a specified key.
 *
 * @param A_ARRAY $array
 * @param string $key Key to order by.
 * @param string $order How to order, SORT_DESC|SORT_ASC
 *
 * @example pw_array_order_by( $items, $score_key, SORT_DESC );
 */
function pw_array_order_by(){
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




function pw_in_string( $haystack, $needle ){
	return (bool) strpos( $haystack, $needle );
}


/**
 * Sanitizes numbers for use with tel links.
 *
 * @param string $tel The telephone number to santize.
 * @param string $area_code The area code to enforce 
 * @example
 * <a href="tel:<?php echo pw_theme_sanitize_tel($phone)"></a>
 */
function pw_theme_sanitize_tel( $tel, $area_code = '1' ){
	if( !is_string( $tel ) && !is_numeric( $tel ) )
		return '';

	$tel = (string) $tel;

	// Replace common spacers
	$tel = str_replace('.', '', $tel);
	$tel = str_replace('-', '', $tel);
	$tel = str_replace(' ', '', $tel);

	// If it doesn't already start with the area code
	// Or + area code, add the area code to the number
	if( is_string($area_code) &&
		substr( $string, 0, strlen($area_code) ) !== $area_code &&
		substr( $string, 0, strlen('+'.$area_code) + 1 ) !== '+'.$area_code )
		$tel = $area_code . $tel;

	return $tel;

}

/**
 * WordPress' missing is_blog_page() function.  Determines if the currently viewed page is
 * one of the blog pages, including the blog home page, archive, category/tag, author, or single
 * post pages.
 *
 * @return bool
 */
function is_blog_page() {
	global $post;

	//Post type must be 'post'.
	$post_type = get_post_type($post);

	//Check all blog-related conditional tags, as well as the current post type, 
	//to determine if we're viewing a blog page.
	return (
		( is_home() || is_archive() || is_single() )
		&& ($post_type == 'post')
	) ? true : false ;

}


/**
 * Gets only the fields which contain a certain substring
 *
 * @param string $substring The substring to match in each field
 * @param array $fields A Postworld field model array
 * @return array The items from the array containing the substring
 */
function pw_fields_where( $substring, $fields ){
	
	if( !is_array( $fields ) )
		return array();

	$matches = array();
	foreach( $fields as $field ){
		if( strpos( $field, $substring ) !== false  )
			$matches[] = $field;
	}
	return $matches;
}


/**
 * Add an additional CSS class to menu item
 * If the item's url is contained with the current URL.
 * Useful for in-site custom links.
 *
 * @example To impliment on theme:
 * 	add_filter('nav_menu_css_class' , 'pw_nav_menu_css_class' , 10 , 2);
 */
function pw_nav_menu_css_class($classes, $item){
	global $pw;
	$item_url = $item->url;

	if( empty($item_url) )
		return $classes;

	/**
	 * If the url starts with a /, implying that it's
	 * relative to the base site url.
	 */
	if (substr($item_url, 0, 1) === '/'){
		// The item's full url without protocol
		$item_url = $_SERVER['SERVER_NAME'] . $item_url;
	}
	if (strpos($pw['view']['url'],$item_url) !== false) {
		$classes[] = 'current-menu-ancestor';
	}
	return $classes;
}

/**
 * Stop script execution with an error message.
 *
 * Copied from Yahnis Elsts' wp-update-server
 * @link https://github.com/YahnisElsts/wp-update-server
 * 
 * @param string $message Error message.
 * @param int $httpStatus Optional HTTP status code. Defaults to 500 (Internal Server Error).
 */
function pw_exit_with_error($message = '', $httpStatus = 500) {
	$statusMessages = array(
		// This is not a full list of HTTP status messages. We only need the errors.
		// [Client Error 4xx]
		400 => '400 Bad Request',
		401 => '401 Unauthorized',
		402 => '402 Payment Required',
		403 => '403 Forbidden',
		404 => '404 Not Found',
		405 => '405 Method Not Allowed',
		406 => '406 Not Acceptable',
		407 => '407 Proxy Authentication Required',
		408 => '408 Request Timeout',
		409 => '409 Conflict',
		410 => '410 Gone',
		411 => '411 Length Required',
		412 => '412 Precondition Failed',
		413 => '413 Request Entity Too Large',
		414 => '414 Request-URI Too Long',
		415 => '415 Unsupported Media Type',
		416 => '416 Requested Range Not Satisfiable',
		417 => '417 Expectation Failed',
		// [Server Error 5xx]
		500 => '500 Internal Server Error',
		501 => '501 Not Implemented',
		502 => '502 Bad Gateway',
		503 => '503 Service Unavailable',
		504 => '504 Gateway Timeout',
		505 => '505 HTTP Version Not Supported'
	);
	
	if ( !isset($_SERVER['SERVER_PROTOCOL']) || $_SERVER['SERVER_PROTOCOL'] === '' ) {
		$protocol = 'HTTP/1.1';
	} else {
		$protocol = $_SERVER['SERVER_PROTOCOL'];
	}

	//Output a HTTP status header.
	if ( isset($statusMessages[$httpStatus]) ) {
		header($protocol . ' ' . $statusMessages[$httpStatus]);
		$title = $statusMessages[$httpStatus];
	} else {
		header('X-Ws-Update-Server-Error: ' . $httpStatus, true, $httpStatus);
		$title = 'HTTP ' . $httpStatus;
	}
	
	if ( $message === '' ) {
		$message = $title;
	}

	//And a basic HTML error message.
	printf(
		'<html>
			<head> <title>%1$s</title> </head>
			<body> <h1>%1$s</h1> <p>%2$s</p> </body>
		 </html>',
		$title, $message
	);
	exit;
}


/*
 * Replacement for get_adjacent_post()
 *
 * This supports only the custom post types you identify and does not
 * look at categories anymore. This allows you to go from one custom post type
 * to another which was not possible with the default get_adjacent_post().
 * Orig: wp-includes/link-template.php 
 * 
 * @param string $direction: Can be either 'prev' or 'next'
 * @param multi $post_types: Can be a string or an array of strings
 */
function pw_get_adjacent_post( $post_id, $direction = 'prev', $post_types = 'post') {
	global $wpdb;
	$post = get_post($post_id);
	if(empty($post_id)) return NULL;
	if(empty($post)) return NULL;
	if(!$post_types) return NULL;

	if(is_array($post_types)){
		$txt = '';
		for($i = 0; $i <= count($post_types) - 1; $i++){
			$txt .= "'".$post_types[$i]."'";
			if($i != count($post_types) - 1)
				$txt .= ', ';
		}
		$post_types = $txt;
	}
	else {
		$post_types = "'".trim($post_types, "'")."'";
	}

	$current_post_date = $post->post_date;

	$join = '';
	$in_same_cat = FALSE;
	$excluded_categories = '';
	$adjacent = $direction == 'prev' ? 'previous' : 'next';
	$op = $direction == 'prev' ? '<' : '>';
	$order = $direction == 'prev' ? 'DESC' : 'ASC';

	$join  = apply_filters( "get_{$adjacent}_post_join", $join, $in_same_cat, $excluded_categories );
	$where = apply_filters( "get_{$adjacent}_post_where", $wpdb->prepare("WHERE p.post_date $op %s AND p.post_type IN({$post_types}) AND p.post_status = 'publish'", $current_post_date), $in_same_cat, $excluded_categories );
	$sort  = apply_filters( "get_{$adjacent}_post_sort", "ORDER BY p.post_date $order LIMIT 1" );

	//$query = "SELECT p.* FROM $wpdb->posts AS p $join $where $sort";
	$query = "SELECT p.* FROM $wpdb->posts AS p $join $where $sort";
	$query_key = 'adjacent_post_' . md5($query);
	$result = wp_cache_get($query_key, 'counts');
	
	if ( false !== $result )
		return $result;

	$result = $wpdb->get_row($query);
	if ( null === $result )
		$result = '';

	wp_cache_set($query_key, $result, 'counts');
	return $result;
}




/**
 * Checks array_key_exists() on multiple values.
 *
 * @param array $keys An array of keys to check for.
 * @param $array $array A key->value paired array to check keys for.
 * @param bool $match_all If false, any match will return true. If true, will require all to match.
 */
function pw_array_keys_exist( $keys, $array, $match_all = false ){
	foreach( $keys as $key ){
		// (bool) Check if the key exists in the array keys
		$has_match = array_key_exists( $key, $array );

		if( !$match_all ){
			if( $has_match === true )
				return true;
			else
				continue;
		}

		if( $match_all ){
			if( $has_match === false )
				return false;
			else
				continue;
		}

	}

	if( $match_all )
		return true;
	else
		return false;
}


/**
 * Gets the mime type of a file based on it's file extension
 */
function pw_mime_content_type( $filename ){

	$mime_types = array(

		'txt' => 'text/plain',
		'htm' => 'text/html',
		'html' => 'text/html',
		'php' => 'text/html',
		'css' => 'text/css',
		'js' => 'application/javascript',
		'json' => 'application/json',
		'xml' => 'application/xml',
		'swf' => 'application/x-shockwave-flash',
		'flv' => 'video/x-flv',

		// images
		'png' => 'image/png',
		'jpe' => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'jpg' => 'image/jpeg',
		'gif' => 'image/gif',
		'bmp' => 'image/bmp',
		'ico' => 'image/vnd.microsoft.icon',
		'tiff' => 'image/tiff',
		'tif' => 'image/tiff',
		'svg' => 'image/svg+xml',
		'svgz' => 'image/svg+xml',

		// archives
		'zip' => 'application/zip',
		'rar' => 'application/x-rar-compressed',
		'exe' => 'application/x-msdownload',
		'msi' => 'application/x-msdownload',
		'cab' => 'application/vnd.ms-cab-compressed',

		// audio/video
		'mp3' => 'audio/mpeg',
		'qt' => 'video/quicktime',
		'mov' => 'video/quicktime',

		// adobe
		'pdf' => 'application/pdf',
		'psd' => 'image/vnd.adobe.photoshop',
		'ai' => 'application/postscript',
		'eps' => 'application/postscript',
		'ps' => 'application/postscript',

		// ms office
		'doc' => 'application/msword',
		'rtf' => 'application/rtf',
		'xls' => 'application/vnd.ms-excel',
		'ppt' => 'application/vnd.ms-powerpoint',

		// open office
		'odt' => 'application/vnd.oasis.opendocument.text',
		'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
	);

	$ext = strtolower(array_pop(explode('.',$filename)));
	if (array_key_exists($ext, $mime_types)) {
		return $mime_types[$ext];
	}
	elseif (function_exists('finfo_open')) {
		$finfo = finfo_open(FILEINFO_MIME);
		$mimetype = finfo_file($finfo, $filename);
		finfo_close($finfo);
		return $mimetype;
	}
	else {
		return 'application/octet-stream';
	}
}


/**
 * Get Current Post Type in Any Setting
 */
function pw_get_current_post_type() {
    global $post, $typenow, $current_screen;
    $get_post_id = $_GET['post'];

    // Check to see if a post object exists
    if ($post && $post->post_type)
        return $post->post_type;

    // Check if the current type is set
    elseif ($typenow)
        return $typenow;

    // Check to see if the current screen is set
    elseif ($current_screen && $current_screen->post_type)
        return $current_screen->post_type;

    // Check if we're on a post editing page and post ID is defined
    elseif ( !empty( $get_post_id ) ){
    	$get_post = get_post( $get_post_id );
    	if( $get_post->post_type )
    		return $get_post->post_type;
    }

    // Finally make a last ditch effort to check the URL query for type
    elseif (isset($_REQUEST['post_type']))
        return sanitize_key($_REQUEST['post_type']);

	


    return null;
    
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
	//print_r($post_types);
}
*/


/*
function pw_add_terms( $terms, $taxonomy ){
	// Adds a series of terms up to two levels of depth

	foreach( $terms as $term ){
	
		$term_exists = term_exists( $term['term'], $taxonomy );

		// Add top level terms
		if( !$term_exists ){
			// Insert the term
			$term_ids = wp_insert_term(
				$term['term'],
				$taxonomy,
				$term['meta']
				);
		} else{
			$term_ids = $term_exists;
		}

		// Add Child Terms
		if( isset( $term['children'] ) ){

			// Iterate through each child term
			foreach( $term['children'] as $child_term ){

				// Check if Terms Exists
				$term_exists = term_exists( $child_term['term'], $taxonomy );

				if( !$term_exists ){
					// Define the parent term
					$child_term['meta']['parent'] = $term_ids['term_id'];

					// Insert the term
					$child_term_ids = wp_insert_term(
						$child_term['term'],
						$taxonomy,
						$child_term['meta']
						);

				}

			}

		}

	}

}
*/
