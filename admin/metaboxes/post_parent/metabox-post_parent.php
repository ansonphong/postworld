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

    	///// LABELS /////
    	// Define default Labels
    	$default_labels = array(
    		'title'			=>	'Post Parent',
    		'search'		=>	'Search Posts...',
    		'search_icon'	=>	'icon-search',
    		'loading_icon'	=>	'icon-spinner-2 icon-spin',
            'edit'          =>  'Edit',
            'edit_icon'     =>  'icon-edit',
            'view'          =>  'View',
            'view_icon'     =>  'icon-arrow-up-right-thin',
            'remove'        =>  'Remove',
            'remove_icon'   =>  'icon-close',
    		);

    	// Get labels from the site config
    	$labels = pw_get_obj( $metabox_setting, 'labels' );
    	if( !$labels )
    		$labels = array();

    	// Override default labels with site labels
    	$labels = array_replace_recursive( $default_labels, $labels);

    	// If Post Types is a string
    	if( is_string($post_types) )
    		// Turn into array
    		$post_types = array( $post_types );

		// Iterate through the post types
    	foreach( $post_types as $post_type ){
    		
			// Construct Variables for Callback
			$args = array(
				'query'		=>	$query,
				'labels'	=>	$labels,
				);

			// Add the metabox
			add_meta_box(
	        	'pw_post_parent_meta',
	        	$labels['title'],
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
    	POSTWORLD_URI . '/admin/less/metabox-post_parent.less' );
}
add_action( 'admin_enqueue_scripts', 'pw_metabox_post_parent_scripts' );

////////////// CREATE UI //////////////
function pw_post_parent_meta_init( $post, $metabox ){
    global $post;
    global $pwSiteGlobals;

    extract( $metabox['args'] );

    //pw_log( json_encode($vars) );

    // Apply filters for themes to over-ride
    $query = apply_filters( 'pw_post_parent_metabox_vars', $query );

    // Define query return fields
    $fields = array(
    		'ID',
    		'post_title',
    		'image(all)',
    		'post_parent',
    		'post_permalink',
    		'edit_post_link',
    		);

    // Apply filter for themes to over-ride default settings
	$fields = apply_filters( 'pw_metabox_post_parent_fields', $fields );

    ///// GET PARENT POST /////
    // If the post already has a post parent
    if( $post->post_parent != 0 ){
    	// Define the post
    	$pw_post = array( 'post_parent' => (int) $post->post_parent );
    	// Get the parent post
    	$pw_parent_post = pw_get_post( $post->post_parent, $fields );
    
    // If there is no post parent
    } else{
    	// Define the post
    	$pw_post = array( 'post_parent' => 0 );
		// Set the parent post
    	$pw_parent_post = false;
    }

    ///// PREPARE QUERY /////
    // Define default query values
    $default_query = array(
        'posts_per_page'    =>  20,
        'post_status'       =>  'publish',
        'fields'            =>  $fields,
        );

    // Fill in default values
    $query = array_replace_recursive( $default_query, $query );

	///// INCLUDE TEMPLATE /////
	include "metabox-post_parent-controller.php";

}

////////////// SAVE POST //////////////
function pw_post_parent_meta_save( $post_id ){
	// NOTE : Everything in the top portion of this function will be executed twice

	// Stop autosave to preserve meta data
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
        return $post_id;
	// Get the JSON string which represents the post to be saved 
	$post = $_POST['pw_post_parent_post'];
	// Strip slashes from the string
	$post = stripslashes( $post );
	// Decode the object from JSON into Array
	$post = json_decode( $post, true );
	// If the post is empty, or the decode fails
	if( empty($post) )
		return false;

	///// SAVE POST /////
	// Because this function fires on save_post action after wp_insert_post
	// Recalling this function unconditionally would cause an infinite loop
	// So check here if the post parent of the post is already set and the same value
	// Only if this is not so, insert the post
	$get_post = get_post( $post_id, ARRAY_A );

	// If the post parent value is different
	if( $get_post['post_parent'] != $post['post_parent'] ){
		// Set the Post ID
		$post['ID'] = $post_id;
		// Update the post
		$post_id = wp_update_post( $post ); 
	}
		
    return $post_id;
}


 

?>