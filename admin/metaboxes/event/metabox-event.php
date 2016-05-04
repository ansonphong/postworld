<?php
/*_____                 _     __  __      _        
 | ____|_   _____ _ __ | |_  |  \/  | ___| |_ __ _ 
 |  _| \ \ / / _ \ '_ \| __| | |\/| |/ _ \ __/ _` |
 | |___ \ V /  __/ | | | |_  | |  | |  __/ || (_| |
 |_____| \_/ \___|_| |_|\__| |_|  |_|\___|\__\__,_|

//////////////////////////////////////////////////*/

////////////// ADD METABOX //////////////
if( !pw_is_admin_ajax() )
	add_action('admin_init','pw_metabox_init_event');

function pw_metabox_init_event(){    

	// Add to Post Types
	$metabox_post_types = pw_get_obj( pw_config(), 'wp_admin.metabox.event.post_types' );
	
	// Set the default Post Types
	if( !$metabox_post_types )
		$metabox_post_types = array( 'event' );

	// Add Metabox to each specified Post Type
    foreach( $metabox_post_types as $post_type ){
        add_meta_box(
        	'pw_event_meta',
        	'Event Details',
        	'pw_event_meta_init',
        	$post_type,
        	'normal',
        	'high'
        	);
    }

    // add a callback function to save any data a user enters in
    add_action( 'save_post','pw_event_meta_save' );
}

////////////// CREATE UI //////////////
function pw_event_meta_init(){
    global $post;

    // Get the Postworld event postmeta key
    $event_postmeta_key = _get( pw_config(), 'database.wp_postmeta.meta_keys.event' );
    if( !$event_postmeta_key )
    	$event_postmeta_key = 'pw_event';

    ///// POST META MODEL /////
    // Define the default post meta model
	$init_event_postmeta = array(
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
			"end_date"			=>	'',
			"all_day"			=>	false,
			),
		/*
		'timezone'				=>	array(
			'raw_offset'		=>	0,
			'time_zone_id'		=>	'',
			'time_zone_name'	=>	'',
			),
		*/
		'organizer'				=>	array(
			'name'				=>	'',
			'phone'				=>	'',
			'email'				=>	'',
			'link_url'			=>	'',
			),
		'details'	=>	array(
			'tickets_cost'		=>	'',
			'tickets_url'		=>	'',
			'link_label'		=>	'',
			'link_url'			=>	'',
			),
		);
	// Apply filter for themes to over-ride default settings
	$init_event_postmeta = apply_filters( 'pw_metabox_event_postmeta', $init_event_postmeta );

	///// CONSTRUCT POST OBJECT /////
	global $pw_event_post;

	$fields = array(
			'ID',
			'event_start',
			'event_end',
			'geo_latitude',
			'geo_longitude',
			'post_meta('.$event_postmeta_key.')'
			);

	// Apply filter for themes to over-ride default settings
	$fields = apply_filters( 'pw_metabox_event_fields', $fields );

	// Get the pre-existing post data to populate the object
	$pw_event_post = pw_get_post( $post->ID, $fields );

	// If the post meta is still in the form of a JSON string
	$event_post_meta = pw_get_obj( $pw_event_post, 'post_meta.'.$event_postmeta_key );
	if( is_string( $event_post_meta ) ){
		// Decode it from JSON
		$pw_event_post['post_meta'][$event_postmeta_key] = json_decode($event_post_meta);
	}

	// Setup the post model
	$pw_event_post_model = array(
		'event_start'	=>	'',
		'event_end'		=>	'',
		'geo_latitude'	=>	'',
		'geo_longitude'	=>	'',
		'post_meta'		=>	array(),
	);
	// Setup the event postmeta key
	$pw_event_post_model['post_meta'][$event_postmeta_key] = $init_event_postmeta;

	// Merge in the model with the post data
	$pw_event_post = array_replace_recursive( $pw_event_post_model, $pw_event_post );

	///// INCLUDE TEMPLATE /////

	include "metabox-event-wrapper.php";


}

////////////// SAVE POST //////////////
function pw_event_meta_save( $post_id ){

	// Stop autosave to preserve meta data
	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) )
        return $post_id;

    // Security Layer 
    if ( !current_user_can( 'edit_post', $post_id ) )
        return $post_id;

	// Get the JSON string which represents the post to be saved 
	$post = _get( $_POST, 'pw_event_post' );

	if( !$post )
		return false;

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