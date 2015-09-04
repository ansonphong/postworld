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

function pw_get_all_terms( $columns = "term_taxonomy_id, count" ){
	global $wpdb;
	$results = $wpdb->get_results( "SELECT ".$columns." FROM ".$wpdb->prefix."term_taxonomy", ARRAY_A );
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
	$results = pw_get_all_terms( 'term_taxonomy_id, count' );

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
	$results = pw_get_all_terms( 'term_taxonomy_id, taxonomy, count' );

	/**
	 * Iterate through the terms, and delete empty terms
	 */
	$items = array();
	foreach( $results as $row ){
	
		if( $row['count'] == 0 ){
			wp_delete_term( $row['term_taxonomy_id'], $row['taxonomy'] );
			$items[] = array( 'term_id' => $row['term_taxonomy_id'], 'taxonomy' => $row['taxonomy'] );
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