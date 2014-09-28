<?php
/*____           _     ____                      _   
 |  _ \ ___  ___| |_  |  _ \ __ _ _ __ ___ _ __ | |_ 
 | |_) / _ \/ __| __| | |_) / _` | '__/ _ \ '_ \| __|
 |  __/ (_) \__ \ |_  |  __/ (_| | | |  __/ | | | |_ 
 |_|   \___/|___/\__| |_|   \__,_|_|  \___|_| |_|\__|
                                                     
////////////////////////////////////////////////////*/

////////////// ADD METABOX //////////////
// TODO : Check why this is being called twice each page view
add_action('admin_init','pw_metabox_init_post_parent');
function pw_metabox_init_post_parent(){    

	global $pwSiteGlobals;
	global $post;

	// Get the settings
	$metabox_settings = pw_get_obj( $pwSiteGlobals, 'wp_admin.metabox.post_parent' );
	if( !$metabox_settings || !is_array( $metabox_settings ) )
		return false;
	
	// Iterate through each of the metabox settings
    foreach( $metabox_settings as $metabox_setting ){
    	
    	// Get the query registered with the setting
    	$query = pw_get_obj( $metabox_setting, 'query' );
    	// If there's no query provided
    	if( !$query )
    		// Break to next iteration
    		break;

    	// Get the post types registered with the setting
    	$post_types = pw_get_obj( $metabox_setting, 'post_types' );
    	// If post types are not set
    	if( !$post_types )
    		// Break to next iteration
    		break;

    	// If Post Types is a string
    	if( is_string($post_types) )
    		// Turn into array
    		$post_types = array( $post_types );

		// Iterate through the post types
    	foreach( $post_types as $post_type ){
    		
			// Construct Variables for Callback
			$args = array(
				'query'	=>	$query,
				);

			// Add the metabox
			add_meta_box(
	        	'pw_post_parent_meta',
	        	'Post Parent',
	        	'pw_post_parent_meta_init',
	        	$post_type,
	        	'side',
	        	'core',
	        	$args //  Pass callback variables
	        	);

			// add a callback function to save any data a user enters in
			add_action( 'save_post','pw_post_parent_meta_save' );

    	} // End Foreach : Post Type
    	
    } // End Foreach : Setting

}

////////////// ADD SCRIPTS & STYLES //////////////
function pw_metabox_post_parent_scripts(){
	// Add Styles
    wp_enqueue_style( 'metabox-post_parent-style',
    	POSTWORLD_URI . 'admin/less/metabox-post_parent.less' );
}
add_action( 'admin_enqueue_scripts', 'pw_metabox_post_parent_scripts' );

////////////// CREATE UI //////////////
function pw_post_parent_meta_init( $post, $metabox ){
    global $post;
    global $pwSiteGlobals;

    pw_log( json_encode($vars) );

    // Apply filters for themes to over-ride
    $query = apply_filters( 'pw_post_parent_metabox_vars', $metabox['args']['query'] );

    // Define query return fields
    $fields = array(
    		'ID',
    		'post_title',
    		'image(all)',
    		'post_parent',
    		'post_permalink',
    		'edit_post_link',
    		);

    ///// GET PARENT POST /////
    // If the post already has a post parent
    if( $post->post_parent != 0 ){
    	// Define post fields
    	
    	// Get the post
    	$pw_parent_post = pw_get_post( $post->post_parent, $fields );

    } else{
    	$pw_parent_post = array('post_parent'=>0);
    }

    ///// PREPARE QUERY /////
   	$query['fields'] = $fields;

    /*
    ///// POST META MODEL /////
    // Define the default post meta model
	$init_post_parent_postmeta = array(
		'location'	=>	array(
			'name'				=>	'',
			'address'			=>	'',
			'region'			=>	'',
			'country'			=>	'',
			'postal_code'		=>	'',
			),
		'date'	=>	array(
			"start_date_obj"	=>	'',
			"end_date_obj"		=>	'',
			"start_date"		=>	'',
			"end_date"			=>	''
			),
		'organizer'				=>	array(
			'name'				=>	'',
			'phone'				=>	'',
			'email'				=>	'',
			'link_url'			=>	'',
			),
		'details'	=>	array(
			'cost'				=>	'',
			'link_url'			=>	'',
			),
		);
	// Apply filter for themes to over-ride default settings
	$init_post_parent_postmeta = apply_filters( 'pw_init_post_parent_postmeta', $init_post_parent_postmeta );
	*/

	///// INCLUDE TEMPLATE /////
	include "metabox-post_parent-controller.php";

}

////////////// SAVE POST //////////////
function pw_post_parent_meta_save( $post_id ){

	// Stop autosave to preserve meta data
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
        return $post_id;

	// Get the JSON string which represents the post to be saved 
	$post = $_POST['pw_post_parent_post'];

	// Strip slashes from the string
	$post = stripslashes( $post );
	//pw_log( "SAVE : ID : $post_id : $post \n" );

	// Decode the object from JSON into Array
	$post = json_decode( $post, true );

	// If the post is empty, or the decode fails
	if( empty($post) )
		return false;

	// Save Wordpress Postmeta
	if( isset( $post['post_meta'] ) ){
		// Write post_meta to pw_postmeta table
		pw_set_wp_postmeta_array( $post_id, $post['post_meta'] );
	}

	// Save the Postworld Postmeta
	pw_set_post_meta( $post_id, $post );

    return $post_id;
}


 

?>