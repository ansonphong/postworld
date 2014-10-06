<?php

////////// SAVE SIDEBARS //////////
add_action('admin_init', 'i_add_sidebar_init' );

function i_add_sidebar_init(){
	add_action( 'admin_post_i_add_sidebar', 'i_add_sidebar' );
}

function i_add_sidebar() {
	// Load Globals
	global $theme_admin;
	global $i_admin_urls;
	// Load Settings
	$settings = array(
		"name"			=>	$_GET['name'],
		"id"			=>	$_GET['id'],
		"description"	=>	$_GET['description'],
		"class"			=>	"",
		"before_widget"	=>	"",
		"after_widget"	=>	"",
		"before_title"	=>	"",
		"after_title"	=>	""
		);

	// Validate Form Data
	if ( empty($settings['name']) ){
		// Error if no name
		wp_redirect( add_query_arg( array('message'=> 'add-error'), $i_admin_urls['sidebars'] ) );
		return false;
	}

	// Load Infinite Sidebars Class
	$I_Sidebars = new I_Sidebars();
	// Run New Function
	$I_Sidebars->new_sidebar($settings);
	// Redirect
	wp_redirect( add_query_arg( array('message'=> 'add-success'), $i_admin_urls['sidebars'] ) );
}


////////// UPDATE SIDEBARS //////////
add_action('admin_init', 'i_update_sidebar_init' );

function i_update_sidebar_init(){
	add_action( 'admin_post_i_update_sidebar', 'i_update_sidebar' );
}

function i_update_sidebar() {
	// Load Globals
	global $theme_admin;
	global $i_admin_urls;
	// Load Settings
	$settings = array(
		"name"			=>	$_GET['name'],
		"id"			=>	$_GET['id'],
		"description"	=>	$_GET['description'],
		"class"			=>	$_GET['class'],
		"before_widget"	=>	$_GET['before_widget'],
		"after_widget"	=>	$_GET['after_widget'],
		"before_title"	=>	$_GET['before_title'],
		"after_title"	=>	$_GET['after_title']
		);

	echo "<code>";
	print_r($settings['before_widget']);
	echo "</code>";

	// Load Infinite Sidebars Class
	$I_Sidebars = new I_Sidebars();
	// Run Update Function
	$I_Sidebars->update_sidebar($settings);
	// Redirect
	wp_redirect( add_query_arg( array( 'message'=> 'update-success'), $i_admin_urls['sidebars'] ));
}


////////// SIDEBARS CLASS //////////
class I_Sidebars {

	/*	Create New Sidebars
	 * 	Arguments: name, id, description, class, before_widget, after_widget, before_title, after_title
	 */

	private $option = "i-sidebars";
	
	public function new_sidebar ( $settings ){

		// Initialize
		$vars = get_object_vars( $this );
		$option = $vars['option'];
		$settings = (array) $settings;

		// Check to see if the option exists
		if(
			get_option($option) == false ||
			get_option($option) == "" ||
			get_option($option) == null || 
			get_option($option) == "null"
			){
			// Create the option as an empty JSON array
			add_option( $option, "[]" );
			// Update in the case that the option was corrupted or null
			update_option( $option, "[]" );
		}

		// Get the value of the option
		$option_value = array();
		// Decode JSON as Associative Array
		$option_value = json_decode(get_option($option), true);

		// Increment the value of the sidebar ID
		function increment_sidebar_id( $new_sidebar_id, $existing_sidebars, $increment = 0 ){
			if( $increment > 0 )
				$new_sidebar_id_incremented = $new_sidebar_id . "-" . $increment;
			else
				$new_sidebar_id_incremented = $new_sidebar_id;
			// Go through each existing sidebar
			foreach( $existing_sidebars as $sidebar ){
				// Check to see if the sidebar with the same ID exists
				if( $sidebar["id"] == $new_sidebar_id_incremented ){
					// If so, loop the function with increased increment value
					$increment += 1;
					return increment_sidebar_id( $new_sidebar_id, $existing_sidebars, $increment );
				}
			}
			// Return the unique ID
			return $new_sidebar_id_incremented;
		}

		// Sanitize the ID
		$trimmed_id = trim($settings["id"]);
		if( empty( $trimmed_id ) )
			$settings["id"] = sanitize_title($settings["name"]);
		// Sanitize the Name
		$settings["name"] = sanitize_text_field($settings["name"]);
		// Sanitize the Class
		if( isset($settings["class"]) )
			$settings["class"] = sanitize_html_class($settings["class"]);
		
		// Add to the options
		array_push( $option_value, $settings );

		// Print for test purposes
		//print_r($option_value);

		// Convert to JSON string
		$option_value = json_encode($option_value);

		// Save the option
		update_option( $option, $option_value );

		return true;
	
	}


	/*	Update a Sidebar
	 * 	Arguments: 
	 *	• ID cannot be edited
	 */

	function update_sidebar ( $settings ){
		//print_r( $settings );
		// Initialize
		$vars = get_object_vars( $this );
		$option = $vars['option'];
		$settings = (array) $settings;

		// Get the value of the option
		$option_value = array();
		// Decode JSON as Associative Array
		$option_value = json_decode(get_option($option), true);
		
		// Update Data
		$updated_sidebars = array();
		foreach( $option_value as $sidebar ){
			if( $sidebar['id'] == $settings['id'] ){
				$sidebar = $settings;
			}
			array_push( $updated_sidebars, $sidebar );
		}

		
		// Convert to JSON string
		$option_value = json_encode($updated_sidebars, JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE);
		// Save the option
		update_option( $option, $option_value );

		return true;
	}


	/*	Get All Sidebars
	 * 	Arguments: 
	 *	• ID cannot be edited
	 */

	function get_sidebars ( ){
		$vars = get_object_vars( $this );
		$option = $vars['option'];

		if( get_option($option) != false )
			return (array) json_decode(get_option($option), true);	
		else
			return false;
	}


	/*	Delete a Sidebar
	 * 	Arguments: id
	 */

	function delete_sidebar ( $id ){
		$vars = get_object_vars( $this );
		$option = $vars['option'];

		// Decode JSON as Associative Array
		$option_value = json_decode(get_option($option), true);		

		// If option doesn't exist
		if($option_value == false)
			return false;

		$deleted = 0;
		$new_option_value = array();
		// Cycle through each sidebar
		foreach( $option_value as $sidebar ){
			if( $sidebar["id"] != $id ){
				// Keep the ones which aren't being deleted
				array_push( $new_option_value, $sidebar );
			} else{
				// Increment the number which are deleted
				$deleted += 1;
			}
		}

		// Save the option
		update_option( $option, json_encode($new_option_value) );
		return $deleted;
	}

}


?>