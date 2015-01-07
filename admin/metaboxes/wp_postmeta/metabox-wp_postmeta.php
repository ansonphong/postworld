<?php
/*_        ______    ____           _                  _        
 \ \      / /  _ \  |  _ \ ___  ___| |_ _ __ ___   ___| |_ __ _ 
  \ \ /\ / /| |_) | | |_) / _ \/ __| __| '_ ` _ \ / _ \ __/ _` |
   \ V  V / |  __/  |  __/ (_) \__ \ |_| | | | | |  __/ || (_| |
    \_/\_/  |_|     |_|   \___/|___/\__|_| |_| |_|\___|\__\__,_|
                                                                
///////////////////////////////////////////////////////////////*/

////////////// ADD METABOX //////////////
// TODO : Check why this is being called twice each page view

add_action('admin_init','pw_metabox_init_wp_postmeta');
function pw_metabox_init_wp_postmeta(){    

	global $pw;
    global $pwSiteGlobals;
	global $post;

	// Get the settings
	$metabox_settings = pw_get_obj( $pwSiteGlobals, 'wp_admin.metabox.wp_postmeta' );
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
    		
			// Construct Variables for Callback
			$vars = array(
				'fields'	=>	$fields,
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
    global $pwSiteGlobals;

    // Unpack fields into variable
    $fields = _get( $vars, 'args.fields' );

    // Populate previously saved postmeta into fields array
    for( $i=0; $i<count($fields); $i++ ){
        // Localize the current field
        $field = $fields[$i];
        // Get the meta key
        $meta_key = _get( $field, 'meta_key' );
        // If it's empty, continue
        if( empty($meta_key) )
            continue;
        // Get the meta value
        $meta_value = get_post_meta( $post->ID, $meta_key, true );
        // Populate the model with the meta value
        $fields[$i]['meta_value'] = $meta_value;
    }

	///// INCLUDE TEMPLATE /////
    // Include the UI template
    $metabox_template = pw_get_template ( 'admin', 'metabox-wp-postmeta', 'php', 'dir' );
    include $metabox_template;

}

////////////// SAVE POST //////////////
function pw_wp_postmeta_meta_save( $post_id ){

	// Stop autosave to preserve meta data
	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) )
      return $post_id;
	
    // Get the fields from the http post
    // This is the only way to know what fields to fetch
    $fields = json_decode( stripslashes( $_POST['pw_wp_postmeta_fields'] ), true );
    
    // Return Early if there are no fields
    if( empty($fields) )
        return $post_id;

    ///// COLLECT POSTMETA /////
    $postmeta = array();

    // Iterate through each field
    foreach( $fields as $field ){
        // Get the meta key
        $meta_key = $field['meta_key'];
        // Define the key under which the value is posted
        $http_post_key = 'pw_wp_postmeta_'.$meta_key;
        // Collect the posted data an associative array
        $postmeta[ $meta_key ] = $_POST[ $http_post_key ];   
    }

    // Return Early if there is no postmeta
    if( empty( $postmeta ) )
         return $post_id;

    ///// SAVE POSTMETA /////
    foreach( $postmeta as $meta_key => $meta_value ){
        // Update Post Meta
        update_post_meta( $post_id, $meta_key, $meta_value );
        // If the value is provided and empty
        if( is_string( $meta_value ) && empty( $meta_value ) )
            // Delete post meta
            delete_post_meta( $post_id, $meta_key );
    }

    return $post_id;

}


?>