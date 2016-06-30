<?php

global $pw_post_meta_fields;
$pw_post_meta_fields = array(
	'post_class',
	'post_author',
	'event_start',
	'event_end',
	'geo_longitude',
	'geo_latitude',
	'related_post'
	);

function pw_get_post_meta( $post_id ){
	if( !pw_config_in_db_tables('post_meta') )
		return array();
	global $wpdb;
	$post_meta_table = $wpdb->postworld_prefix.'post_meta';
	$meta = $wpdb->get_row("SELECT * FROM $post_meta_table WHERE post_id = $post_id", ARRAY_A);
	return $meta;
}

function pw_get_column_values( $table_name, $column, $unique = false ){
	// Gets a 1D array of all values in a DB column
	global $wpdb;

	$distinct = ( $unique ) ? "DISTINCT" : "";

	$array = $wpdb->get_col(  
		"
		SELECT " .$distinct. " ".$column."
		FROM 	".$table_name."
		");
	return $array;
}

function pw_delete_rows_by_post_id( $table, $post_id ){
	global $wpdb;
	$where = array( 'post_id' => $post_id );
	return $wpdb->delete(
		$table,
		$where
		);
}

function pw_delete_rows_by_user_id( $table, $user_id ){
	global $wpdb;
	$where = array( 'user_id' => $user_id );
	return $wpdb->delete(
		$table,
		$where
		);
}

// DELETE *WORDPRESS* *POSTMETA* ROWS BY ID
function pw_delete_wp_postmeta_by_id( $post_id ){
	global $wpdb;
	return pw_delete_rows_by_post_id( $wpdb->postmeta,  $post_id );
}

// DELETE *WORDPRESS* *USERMETA* ROWS BY ID
function pw_delete_wp_usermeta_by_id( $user_id ){
	global $wpdb;
	return pw_delete_rows_by_user_id( $wpdb->usermeta,  $user_id );
}

// DELETE *POSTWORLD* *POSTMETA* ROWS BY ID
add_action( 'delete_post', 'pw_delete_pw_postmeta_by_id' );
function pw_delete_pw_postmeta_by_id( $post_id ){
	global $wpdb;
	return pw_delete_rows_by_post_id( $wpdb->postworld_prefix . 'post_meta',  $post_id );
}


function pw_cleanup_meta( $type ){
	// Iterates through each of the respective meta rows, and checks if the associated item exists
	// If not, all meta associated with that ID is removed

	$supported_types = array( 'postmeta', 'postworld_postmeta', 'usermeta' );
	// TODO : Support Taxonomy Meta, Comment Meta

	///// ERROR HANDLING
	if( is_null( $type ) )
		return array( 'error' => 'No type provided.' );
	if( !in_array( $type, $supported_types ) )
		return array( 'error' => 'Type not supported.' );

	// Start timer
	pw_set_microtimer( 'pw_cleanup_pw_postmeta' );

	///// GET ITEM IDS /////
	global $wpdb;
	switch( $type ){
		case 'postmeta':
			$item_ids = pw_get_column_values( $wpdb->postmeta, 'post_id', true );
			break;
		case 'postworld_postmeta':
			$item_ids = pw_get_column_values( $wpdb->postworld_prefix . 'post_meta', 'post_id', true );
			break;
		case 'usermeta':
			$item_ids = pw_get_column_values( $wpdb->usermeta, 'user_id', true );
			break;
	}

	///// CLEANUP ITEMS /////
	$cleaned_items = array();
	// Iterate through each post ID
	foreach( $item_ids as $item_id ){
		$item_id = (int) $item_id;

		/// CHECK IF ASSOCIATED ITEM EXISTS ///
		switch( $type ){
			case 'postmeta':
			case 'postworld_postmeta':
				// Check if the post exists
				$item_exists = pw_post_id_exists( $item_id );
				break;

			case 'usermeta':
				$item_exists = pw_user_id_exists( $item_id );
				break;
		}

		///// REMOVE ITEMS /////
		// If the post doesn't exist
		if( !$item_exists ){
			// Add it to the cleaned array
			$cleaned_items[] = $item_id;

			switch( $type ){
				case 'postmeta':
					// Delete WordPress Postmeta
					pw_delete_wp_postmeta_by_id( $item_id );
					break;
				case 'postworld_postmeta':
					// Delete Postworld Postmeta
					pw_delete_pw_postmeta_by_id( $item_id );
					break;
				case 'usermeta':
					// Delete WordPress Usermeta
					pw_delete_wp_usermeta_by_id( $item_id );
					break;
			}

		}

	}

	// Get the timer
	$timer = pw_get_microtimer( 'pw_cleanup_pw_postmeta' );

	// Count number of cleaned items
	$cleaned_items_count = count( $cleaned_items );

	return array(
		'type'					=>	$type,
		'timer'					=>	$timer,
		'cleaned_items'			=>	$cleaned_items,
		'cleaned_items_count'	=>	$cleaned_items_count,
		);

}


/**
 * Set data in the wp_postmeta table, 
 */
function pw_set_post_meta_pw_postmeta( $post_id, $post_meta ){

}


/**
 * Used to set Postworld values in the wp_postworld_post_meta table
 *
 * @param int $post_id
 * @param array $post_meta Key value pairs, where key is the column in the _post_meta table
 */
function pw_set_post_meta($post_id, $post_meta){
	
	// If post_meta table isn't being used
	// Enter the data prefixed with theme slug into wp_postmeta
	if( !pw_config_in_db_tables('post_meta') ){
		$key_prefix = pw_theme_slug().'_';
		foreach( $post_meta as $key => $value ){
			pw_set_wp_postmeta( array(
				'post_id' => $post_id,
				'meta_key' => $key_prefix.$key,
				'meta_value' => $value,
				));
		}
		return true;
	}

	global $wpdb;
	// Add a new record if it doesn't exist
	pw_insert_post_meta($post_id);

	$query = "update $wpdb->postworld_prefix"."post_meta set ";
	$insertComma = FALSE;

	// POST AUTHOR AS AUTHOR ID
	if( isset($post_meta['post_author']) ){
		if($insertComma === TRUE) $query.=" , ";
		$query .= "author_id='".$post_meta['post_author']."' ";
		$insertComma = TRUE;
	}

	// POST CLASS
	if( isset($post_meta['post_class']) ){
		if($insertComma === TRUE) $query.=" , ";
		$query .= "post_class='".$post_meta['post_class']."' ";
		$insertComma = TRUE;
	}

	// EVENT START
	if( isset($post_meta['event_start']) ){
		if($insertComma === TRUE) $query.=" , ";
		$query .= "event_start='".$post_meta['event_start']."' ";
		$insertComma = TRUE;
	}

	// EVENT END
	if( isset($post_meta['event_end']) ){
		if($insertComma === TRUE) $query.=" , ";
		$query .= "event_end='".$post_meta['event_end']."' ";
		$insertComma = TRUE;
	}

	// GEO LATITUDE
	if( isset($post_meta['geo_latitude']) ){
		if($insertComma === TRUE) $query.=" , ";
		$query .= "geo_latitude='".$post_meta['geo_latitude']."' ";
		$insertComma = TRUE;
	}

	// GEO LONGITUDE
	if( isset($post_meta['geo_longitude']) ){
		if($insertComma === TRUE) $query.=" , ";
		$query .= "geo_longitude='".$post_meta['geo_longitude']."' ";
		$insertComma = TRUE;
	}

	// RELATED POST
	if( isset($post_meta['related_post']) ){
		if($insertComma === TRUE) $query.=" , ";
		$query .= "related_post='".$post_meta['related_post']."' ";
		$insertComma = TRUE;
	}


	if( $insertComma == FALSE ){
		return false;
	}
	
	else{
		$query.= " where post_id=".$post_id ;
	 	$wpdb->query($query);
	 	return $post_id;
	}
	
}
