<?php
/*_        ______    ____           _                  _        
 \ \      / /  _ \  |  _ \ ___  ___| |_ _ __ ___   ___| |_ __ _ 
  \ \ /\ / /| |_) | | |_) / _ \/ __| __| '_ ` _ \ / _ \ __/ _` |
   \ V  V / |  __/  |  __/ (_) \__ \ |_| | | | | |  __/ || (_| |
	\_/\_/  |_|     |_|   \___/|___/\__|_| |_| |_|\___|\__\__,_|
																
///////////////////////////////////////////////////////////////*/

////////////// ADD METABOX //////////////
/**
 * @todo Check why this is being called twice each page view (?)
 */

add_action('admin_init','pw_metabox_init_wp_postmeta');
function pw_metabox_init_wp_postmeta(){    
	global $pw;
	global $post;
	global $wp;
	$current_post_type = pw_get_current_post_type();

	// Get the settings
	$metabox_settings = pw_config('wp_admin.metabox.wp_postmeta');
	if( !$metabox_settings || !is_array( $metabox_settings ) )
		return false;
	
	// Iterate through each of the metabox settings
	foreach( $metabox_settings as $metabox_setting ){
		
		// Get the fields registered with the setting
		$fields = pw_get_obj( $metabox_setting, 'fields' );
		// If there's no fields provided
		if( !$fields )
			// Break to next iteration
			break;

		// Get the post types registered with the setting
		$post_types = pw_get_obj( $metabox_setting, 'post_types' );
		// If post types are not set
		if( !$post_types )
			// Break to next iteration
			break;

		///// METABOX SETTINGS /////
		// Define default metabox settings
		$default_metabox = array(
			'title'			=>	'Meta',
			'context'		=>	'normal',
			);

		// Get metabox from the site config
		$metabox = pw_get_obj( $metabox_setting, 'metabox' );
		if( !$metabox )
			$metabox = array();

		// Override default metabox with site metabox
		$metabox = array_replace_recursive( $default_metabox, $metabox);

		// If Post Types is a string
		if( is_string($post_types) )
			// Turn into array
			$post_types = array( $post_types );


		// Iterate through the post types
		foreach( $post_types as $post_type ){

			/**
			 * Apply post-types filter to fields
			 * subtractively multiplying the fields post type setting
			 * So if you want the field to only appear on a specific post type
			 * Make a post_types key in the field, and add an array of post types
			 * to exlusively filter that field on.
			 */
			if( !empty( $fields ) ){
				$filtered_fields = array();
				foreach( $fields as $field ){
					$check_post_types = _get( $field, 'post_types' );
					// If post types are not defined, add the field
					if( empty($check_post_types) ){
						$filtered_fields[] = $field;
					}
					// If the current post type is in the array, add the field
					elseif( in_array( $current_post_type, $check_post_types ) ){
						$filtered_fields[] = $field;
					}
				}
			}
			else{
				$filtered_fields = $fields;
			}


			// Construct Variables for Callback
			$vars = array(
				'fields'	=>	$filtered_fields,
				);

			// Add the metabox
			add_meta_box(
				'pw_wp_postmeta_meta',
				$metabox['title'],
				'pw_wp_postmeta_ui',
				$post_type,
				$metabox['context'],
				'core',
				$vars //  Pass callback variables
				);

		} // End Foreach : Post Type

		// add a callback function to save any data a user enters in
		add_action( 'save_post','pw_wp_postmeta_meta_save' );

	} // End Foreach : Setting

}

////////////// CREATE UI //////////////
function pw_wp_postmeta_ui( $post, $vars ){
	global $post;

	// Unpack fields into variable
	$fields_src = _get( $vars, 'args.fields' );

	// Populate previously saved postmeta into fields array
	$fields = array();

	for( $i=0; $i<count($fields_src); $i++ ){

		// Localize the current field
		$field = $fields_src[$i];

		// Add supports key if it doesn't exist
		if( !isset($field['supports']) )
			$field['supports'] = array();

		// Get the meta key
		$meta_key = _get( $field, 'meta_key' );
		if( empty($meta_key) )
			continue;

		/**
		 * Get Meta Value
		 */
		if( isset($field['sub_key']) )
			$meta_value = pw_get_wp_postmeta(array(
				'post_id' => $post->ID,
				'meta_key' => $field['meta_key'],
				'sub_key' => $field['sub_key']
				));
		else
			$meta_value = get_post_meta( $post->ID, $meta_key, true );

		/**
		 * Default Values
		 * If no value set, set the default value
		 */
		// If the field supports custom defaults, get from saved defaults
		if( in_array('custom_default', $field['supports'] ) ){
			
			$sub_key = ( isset( $field['sub_key'] ) ) ?
			'.'.$field['sub_key'] :
			'';

			$saved_custom_default_value = pw_get_option(array(
				'option_name' => PW_OPTIONS_DEFAULTS,
				'key' => 'wp_postmeta.'.$meta_key.$sub_key
				));

			/**
			 * If the saved value isn't empty
			 * overwrite the default custom default value.
			 */
			if( !empty( $saved_custom_default_value ) )
				$field['custom_default_value'] = $saved_custom_default_value;
			
		}

		// If no custom default saved, get the configured default
		$default_value = _get( $field, 'default_value' );
		if( empty( $meta_value ) && !empty( $default_value ) )
			$meta_value = $default_value;

		// Populate the model with the meta value
		$field['meta_value'] = $meta_value;

		// Restructure Fields Array, from array into object
		// Using the meta_key as the primary key
		$fields[$meta_key] = $field;		

	}

	///// INCLUDE TEMPLATE /////
	// Include the UI template
	$metabox_template = pw_get_template ( 'admin', 'metabox-wp-postmeta', 'php', 'dir' );
	
	include 'metabox-wp_postmeta-controller.php';

}

////////////// SAVE POST //////////////
function pw_wp_postmeta_meta_save( $post_id ){

	// Stop autosave to preserve meta data
	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) )
	  return $post_id;
	
	// Security Layer 
	if ( !current_user_can( 'edit_post', $post_id ) )
		return $post_id;

	// Get the fields from the http post
	// This is the only way to know what fields to fetch
	$pw_wp_postmeta_fields = _get( $_POST, 'pw_wp_postmeta_fields' );
	if( !$pw_wp_postmeta_fields )
		return false;

	$fields = json_decode( stripslashes( $pw_wp_postmeta_fields ), true );
	
	// Return Early if there are no fields
	if( empty($fields) )
		return $post_id;

	///// SAVE POSTMETA /////
	foreach( $fields as $meta_key => $field ){

		/**
		 * Get Meta Value
		 */
		if( isset($field['sub_key']) )
			pw_set_wp_postmeta(array(
				'post_id' => $post_id,
				'meta_key' => $field['meta_key'],
				'sub_key' => $field['sub_key'],
				'value' => $field['meta_value']
				));
		else
			// Update Post Meta
			update_post_meta( $post_id, $meta_key, $field['meta_value'] );

		// If the value is provided and empty and it's not a sub key
		if( is_string( $meta_value ) &&
			empty( $field['meta_value'] ) &&
			!isset( $field['sub_key'] ) )
			// Delete post meta
			delete_post_meta( $post_id, $meta_key );

		// Save the custom default
		if(	in_array( 'custom_default', $field['supports'] ) &&
			isset( $field['default_value'] ) &&
			!empty( $field['default_value'] ) ){
			$sub_key = ( isset( $field['sub_key'] ) ) ?
				'.'.$field['sub_key'] :
				'';
			$set_default = pw_set_option( array(
				'option_name' => PW_OPTIONS_DEFAULTS,
				'key' => 'wp_postmeta.'.$meta_key.$sub_key,
				'value' => $field['custom_default_value']
				));
		}

	}

	return $post_id;

}


?>