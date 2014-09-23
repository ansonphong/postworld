<?php

function pw_update_pw1_event_meta( $options = array() ) {
	// This function safely migrates and deletes all RSV2 lineage event meta data

	/*
		$options = array(
			"mode"	=>	"test / migrate",
		)
	*/

	// Only admins can run this
	if( !is_super_admin() )
		return false;

	////////// SET DEFALUT OPTIONS //////////

	if( !isset( $options['mode'] ) )
		$options['mode'] = "test";


	////////// GET POST IDS //////////
	// Get a list of the effected Post IDs
	$query = array(
		'posts_per_page' => -1,
		'post_type'	=>	'event',
		'fields' => array( "ID", "post_title" ),
		);
	$query_results = pw_query( $query );
	$posts = $query_results->posts;
	$post_ids = array();
	foreach( $posts as $post ){
		$post_ids[] = $post['ID'];
	}

	// Generate Report
	$report = array();

	////////// SEQUENCE THROUGH EACH ID //////////
	foreach( $post_ids as $post_id ){

		////////// + Check for old post meta post meta for `event_obj` and `date_obj`
		// Old Meta Keys
		$old_meta_keys = array(
			"venue",
			"event_address",
			"event_phone",
			"event_cost",
			"event_city",
			"event_region",
			"event_country",
			"event_postcode",
			"event_start_date_obj",
			"event_end_date_obj",
			"event_start_date",
			"event_end_date",
			"event_all_day"
			);

		// Iterate through each old meta field
		$old_meta = array();
		foreach( $old_meta_keys as $old_meta_key ){
			// Get value
			$old_meta_value = get_post_meta( $post_id, $old_meta_key, true );
			
			// Set it into $old_meta array
			if( !empty( $old_meta_value ) )
				$old_meta[ $old_meta_key ] = $old_meta_value;

		}

		////////// + Check for new post meta fields `event_obj` and `date_obj`
		$new_meta_keys = array(
			"event",
			);

		// Iterate through each new meta field
		$new_meta = array();
		foreach( $new_meta_keys as $new_meta_key ){
			$new_meta_value = get_post_meta( $post_id, $new_meta_key, true );

			// Set it into $new_meta array
			if( !empty( $new_meta_value ) )
				$new_meta[ $new_meta_key ] = json_decode( $new_meta_value, true );

		}

		////////// + Remap old fields to new fields
		// LOCATION : {"city":"Portland","city_code":"Portland","region":"Oregon","region_code":"OR","country":"United States","country_code":"US","location_name":"Portland, OR, USA"}
		// DATE : {"event_start_date_obj":"2014-02-26T08:00:00.000Z","event_start_date":"2014-02-26 00:00","event_end_date_obj":"2014-02-28T08:00:00.000Z","event_end_date":"2014-02-28 00:00"}
		
		$remap_keys = array(
			// Location
			"venue" => 					"event.location.name",
			"event_address" => 			"event.location.address",
			"event_city" =>				"event.location.city",
			"event_region" => 			"event.location.region",
			"event_country" => 			"event.location.country",
			"event_postcode" => 		"event.location.postal_code",

			// Date
			"event_start_date_obj" => 	"event.date.start_date_obj",
			"event_end_date_obj" => 	"event.date.end_date_obj",
			"event_start_date" => 		"event.date.start_date",
			"event_end_date" => 		"event.date.end_date",
			"event_all_day" => 			"event.date.all_day",

			// Orgnizer
			"event_phone" => 			"event.organizer.phone",

			// Details
			"event_cost" => 			"event.details.cost",

			);

		// New Data to Insert in the DB
		$pw_postmeta = array();
		$new_meta_return = array();
		//$pw_postmeta_keys = array( "event_start_date", "event_end_date" );

		// Remap each key
		foreach( $remap_keys as $old_key => $new_key ){

			// If the new key is empty ( there is no previously saved value )
			// And the value exists
			if( pw_get_obj( $new_meta, $new_key ) == false &&
				isset( $old_meta[ $old_key ] ) ){
			
				// Set the new meta value
				$new_meta = pw_set_obj( $new_meta, $new_key, $old_meta[ $old_key ] );

				// Handle new Postworld Meta DB fields ( wp_postworld_post_meta )
				if( $old_key == 'event_start_date' )
					$pw_postmeta['event_start'] = strtotime( $old_meta[ $old_key ] );
				if( $old_key == 'event_end_date' )
					$pw_postmeta['event_end'] = strtotime( $old_meta[ $old_key ] );


				///// INSERT NEW POST META /////
				if( $options['mode'] == "migrate" ){

					// Make Label
					$new_meta_return_label = $new_key;

					// strip "event." from the head of the subkey string identifier
					// ie. event.date.start_date_obj -> date.start_date_obj
					$new_key = str_replace( "event.", "", $new_key );

					// Add the post meta
					$new_post_meta_return = pw_set_wp_postmeta(
						array(
							"post_id"	=>	$post_id,
							"sub_key"	=>	$new_key,
							"value" 	=>	$old_meta[ $old_key ],
							"meta_key" 	=>	"event",
							)
						);

					// Return the report
					$new_meta_return[ $old_key ] = $new_meta_return_label;

				}
			}


			///// DELETE OLD META KEYS /////
			if( $options['mode'] == "migrate" ){

				// If value is set
				if( isset( $old_meta[ $old_key ] ) ){
					// Delete the meta key in the DB
					delete_post_meta( $post_id, $old_key );
				}

			}
			


		}

		////////// INSERT DATA //////////

		///// Insert Postworld Post Meta //////
		if( $options['mode'] == "migrate" )
			$pw_set_post_meta = pw_set_post_meta($post_id, $pw_postmeta);

		////////// + 1. create new model, 2. delete the old post meta entry
		////////// + Save UNIX entries in wp_postworld_post_meta -> event_start / event_end
		////////// + Save the new post_meta fields as JSON

		// Compile Report
		$report[] = array(
			"ID"					=>	$post_id,
			"old_meta"				=>	$old_meta,
			"new_meta"				=>	$new_meta,
			"new_meta_return"		=>	$new_meta_return,
			"pw_postmeta"			=>	$pw_postmeta,
			"pw_postmeta_return"	=>	$pw_set_post_meta,
			);

	}	


	return $report;
}

?>