<?php
/**
 * Wrapper for various taxonomy operations.
 * @param String $type The type of operation to perform.
 */
function pw_taxonomy_operation( $type, $vars ){
	
	/**
	 * @todo Increase security versatility here.
	 */
	if( !current_user_can('manage_categories') )
		return false;

	return call_user_func( 'pw_taxonomy_operation_' . $type, $vars );

}

function pw_get_all_table_rows( $table = 'term_taxonomy', $columns = "term_taxonomy_id, term_id, count" ){
	global $wpdb;
	$results = $wpdb->get_results( "SELECT ".$columns." FROM ".$wpdb->prefix.$table, ARRAY_A );
	$results = pw_sanitize_numeric_array_of_a_arrays( $results );
	return $results;
}

/**
 * Updates all term post counts.
 */
function pw_taxonomy_operation_update_term_count( $vars = array() ){

	pw_set_microtimer( 'taxonomy_operation_update_term_count' );

	global $wpdb;

	/**
	 * Get all the terms in the whole terms table.
	 */
	$results = pw_get_all_table_rows( 'term_taxonomy', 'term_taxonomy_id, count' );

	/**
	 * Iterate through the terms, and fix the count column.
	 */
	$items = array();
	foreach( $results as $row ){
		$term_taxonomy_id = $row['term_taxonomy_id'];
		$count = $wpdb->get_var( "SELECT count(*) FROM ".$wpdb->prefix."term_relationships WHERE term_taxonomy_id = ".$term_taxonomy_id );
		$count = pw_sanitize_numeric($count);

		/**
		 * If the count on record is different than the actual count, fix it.
		 * @todo Account for trashed posts.
		 */
		if( $row['count'] !== $count ){
			$items[] = array( 'term' => $term_taxonomy_id, 'diff' => $count - $row['count']  );

			$wpdb->update(
				$wpdb->prefix."term_taxonomy",
				array(
					'count' => $count,
					),
				array(
					'term_taxonomy_id' => $term_taxonomy_id,
					)
				);

		}

	}	

	$timer = pw_get_microtimer('taxonomy_operation_update_term_count');

	return array(
		'timer' => $timer,
		'total_terms' => count($results),
		'count' => count($items),
		'items' => $items,
		);
}


/**
 * Deletes all terms with the post count of 0
 */
function pw_taxonomy_operation_delete_empty_terms( $vars = array() ){
	
	pw_set_microtimer( 'delete_empty_terms' );
	
	global $wpdb;

	/**
	 * Get all the terms in the whole terms table.
	 */
	$results = pw_get_all_table_rows( 'term_taxonomy', 'term_taxonomy_id, term_id, taxonomy, count' );

	/**
	 * Iterate through the terms, and delete empty terms
	 */
	$items = array();
	foreach( $results as $row ){
	
		if( $row['count'] == 0 ){

			// Formally delete the term via WordPress method
			wp_delete_term( $row['term_id'], $row['taxonomy'] );

			$items[] = array( 'term_id' => $row['term_id'], 'taxonomy' => $row['taxonomy'] );
		
			// Delete all entries with that term ID in term_taxonomy table, as a final measure
			$wpdb->delete(
				$wpdb->prefix."term_taxonomy",
				array( 'term_id' => $term_id ),
				array( '%d' )
				);

		}

	}

	$timer = pw_get_microtimer('delete_empty_terms');

	return array(
		'timer' => $timer,
		'total_terms' => count($results),
		'count' => count($items),
		'items' => $items,
		);
	
}

/**
 * Deletes all rows in the term_taxonomy table which don't have term entries in wp_terms,
 * in the case that their terms were deleted from the wp_terms table manually.
 */
function pw_taxonomy_operation_cleanup_term_taxonomy_table(){

	pw_set_microtimer( 'cleanup_term_taxonomy_table' );

	global $wpdb;

	/**
	 * Get all the rows in the whole terms table.
	 */
	$results = pw_get_all_table_rows( 'term_taxonomy', 'term_taxonomy_id, term_id, taxonomy, count' );

	$items = array();
	foreach( $results as $row ){

		// Check the count of how many terms exist with that ID in the terms table
		$term_id = $row['term_id'];
		$count = $wpdb->get_var( "SELECT count(*) FROM ".$wpdb->prefix."terms WHERE term_id = ".$term_id );
		$count = pw_sanitize_numeric($count);

		// If it exist, count will be 1, if not 0
		$term_exists = ( $count > 0 );

		// If the term doesn't exist
		if( !$term_exists ){
			$items[] = array( 'term_id' => $row['term_taxonomy_id'], 'taxonomy' => $row['taxonomy'] );

			// Delete all entries with that term ID in term_taxonomy table
			$wpdb->delete(
				$wpdb->prefix."term_taxonomy",
				array( 'term_id' => $term_id ),
				array( '%d' )
				);

		}

	}

	$timer = pw_get_microtimer('cleanup_term_taxonomy_table');

	return array(
		'timer' => $timer,
		'total_terms' => count($results),
		'count' => count($items),
		'items' => $items,
		);

}


/**
 * Deletes all rows in the term_relationships table which don't have entries
 * In the term_taxonomy table.
 */
function pw_taxonomy_operation_cleanup_term_relationships_table(){

	pw_set_microtimer( 'cleanup_term_relationships_table' );

	global $wpdb;

	/**
	 * Get all the rows in the wp_term_relationships table
	 */
	$results = pw_get_all_table_rows( 'term_relationships', 'term_taxonomy_id' );

	$items = array();
	foreach( $results as $row ){

		// Check the count of how many rows exist with that term_taxonomy_id in the term_taxonomy table
		$term_taxonomy_id = $row['term_taxonomy_id'];
		$count = $wpdb->get_var( "SELECT count(*) FROM ".$wpdb->prefix."term_taxonomy WHERE term_taxonomy_id = ".$term_taxonomy_id );
		$count = pw_sanitize_numeric($count);

		// If it exist, count will be 1, if not 0
		$relationship_exists = ( $count > 0 );

		// If the term relationship doesn't exist
		if( !$relationship_exists ){
			$items[] = array( 'term_taxonomy_id' => $row['term_taxonomy_id'] );

			// Delete all entries with that term ID in term_taxonomy table
			$wpdb->delete(
				$wpdb->prefix."term_relationships",
				array( 'term_taxonomy_id' => $term_taxonomy_id ),
				array( '%d' )
				);

		}

	}

	$timer = pw_get_microtimer('cleanup_term_relationships_table');

	return array(
		'timer' => $timer,
		'total_terms' => count($results),
		'count' => count($items),
		'items' => $items,
		);

}


// Remove term relationships from terms that no longer exist



/*
	////////// FIX COMMENT COUNTS //////////
	/*
	$result = mysql_query("SELECT ID FROM ".$wpdb->prefix."posts");
	while ($row = mysql_fetch_array($result)) {
		$post_id = $row['ID'];
		echo "post_id: ".$post_id." count = ";
		$countresult = mysql_query("SELECT count(*) FROM ".$wpdb->prefix."comments WHERE comment_post_ID = '$post_id' AND comment_approved = 1");
		$countarray = mysql_fetch_array($countresult);
		$count = $countarray[0];
		echo $count."<br />";

		mysql_query("UPDATE ".$wpdb->prefix."posts SET comment_count = '$count' WHERE ID = '$post_id'");
	}
*/


?>