<?php

function pw_update_pw1_event_meta() {

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

			// Event
			"event_phone" => 			"event.phone",
			"event_cost" => 			"event.cost",

			// Date
			"event_start_date_obj" => 	"event.date.start_date_obj",
			"event_end_date_obj" => 	"event.date.end_date_obj",
			"event_all_day" => 			"event.date.all_day",
			);

		// Remap each key
		foreach( $remap_keys as $old_key => $new_key ){

			// If the new key is empty
			// And the value exists
			if( pw_get_obj( $new_meta, $new_key ) == false &&
				isset( $old_meta[ $old_key ] ) )
				// Set the new value
				$new_meta = pw_set_obj( $new_meta, $new_key, $old_meta[ $old_key ] );
		}

		// Create UNIX Times


		// Compile Report
		$report[] = array(
			"ID"		=>	$post_id,
			"old_meta"	=>	$old_meta,
			"new_meta"	=>	$new_meta,
			);


		////////// + 1. create new model, 2. delete the old post meta entry
		////////// + Save UNIX entries in wp_postworld_post_meta -> event_start / event_end
		////////// + Save the new post_meta fields as JSON

	}	


	return $report;
}

?>