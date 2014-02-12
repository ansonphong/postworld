<?php


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

function pw_get_post_types(){
	//$user_role = get_user_role();

	$args = array(
	   'public'   => true,
	   '_builtin' => false,
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
			// Transfer data
			foreach ($fields as $field) {
				$branch_child[$field] = $object[$i][$field];
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


function extract_bracket_values ( $input, $force_array = true ){
	// Extracts comma deliniated values which are contained in square brackets
	// Returns an Array of values that were previously comma deliniated,
	// unless $force_array is set TRUE.

	$match=array();

	// Extract contents of (parenthesis)
	preg_match('#\[(.*?)\]#', $input, $match);

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


function get_avatar_url( $user_id, $avatar_size ){
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

function post_exists_by_id($post_id){
	$post = get_post( $post_id );
	if($post != null){ return true; } else{ return false; }
}

function crop_string_to_word( $string, $max_chars = 200 ){
	
	if (strlen($string) > $max_chars) {
	    $string = substr($string, 0, $max_chars);
	    $string = substr($string, 0, strrpos($string, ' '));        
	    $string .= '...';
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

?>