<?php

class PW_Database{

	/**
	 * Renames all values under the meta_key column in the wp_postmeta table
	 * From an old value to a new value.
	 * Generally used for migrations where the key name is changing.
	 *
	 * @param string $oldname Current/old key name to change, ie. 'link_target'
	 * @param string $newname New name of the key, ie. 'myprefixed_link_target'
	 *
	 * @return mixed The result of the DB query operation.
	 */
	public function rename_postmeta_keys( $oldname, $newname ){
		global $wpdb;
		return $this->replace( array(
			'table_name' => $wpdb->postmeta,
			'column_name' => 'meta_key',
			'old_value' => $oldname,
			'new_value' => $newname,
			));
	}

	/**
	 * Renames all values under the meta_key column in the wp_usermeta table
	 * From an old value to a new value.
	 * Generally used for migrations where the key name is changing.
	 *
	 * @param string $oldname Current/old key name to change, ie. 'biography'
	 * @param string $newname New name of the key, ie. 'myprefixed_biography'
	 *
	 * @return mixed The result of the DB query operation.
	 */
	public function rename_usermeta_keys( $oldname, $newname ){
		global $wpdb;
		return $this->replace( array(
			'table_name' => $wpdb->usermeta,
			'column_name' => 'meta_key',
			'old_value' => $oldname,
			'new_value' => $newname,
			));
	}

	/**
	 * Renames all values under the meta_key column in the wp_termmeta table
	 * From an old value to a new value.
	 * Generally used for migrations where the key name is changing.
	 *
	 * @param string $oldname Current/old key name to change, ie. 'icon'
	 * @param string $newname New name of the key, ie. 'myprefixed_icon'
	 *
	 * @return mixed The result of the DB query operation.
	 */
	public function rename_termmeta_keys( $oldname, $newname ){
		global $wpdb;
		return $this->replace( array(
			'table_name' => $wpdb->termmeta,
			'column_name' => 'meta_key',
			'old_value' => $oldname,
			'new_value' => $newname,
			));
	}

	/**
	 * Renames an option_name value in the wp_options table.
	 *
	 * @param string $oldname Current/old option name to change, ie. 'postworld-styles-theme'
	 * @param string $newname New option name, ie. 'theme-styles'
	 *
	 * @return mixed The result of the DB query operation.
	 */
	public function rename_option( $oldname, $newname ){
		global $wpdb;
		return $this->replace( array(
			'table_name' => $wpdb->options,
			'column_name' => 'option_name',
			'old_value' => $oldname,
			'new_value' => $newname,
			));
	}

	/**
	 * Used for replaceing whole values in the database,
	 * which is especially handy for renaming keys.
	 */
	public function replace( $vars = array() ){
		$default_vars = array(
			'table_name' => null,
			'column_name' => null,
			'old_value'	=> null,
			'new_value'	=> null
			);
		$vars = array_replace($default_vars, $vars);
		extract($vars);

		global $wpdb;
		$query = "
			UPDATE ".$table_name . "
			SET " . $column_name . " = '". $new_value ."'
			WHERE " . $column_name . " = '" . $old_value . "'";
		return $wpdb->query( $query );

	}

	/**
	 * Check if a database table exists.
	 * @return boolean
	 */
	public function table_exists( $table_name ){
		global $wpdb;
		return ($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'") == $table_name);
	}

	/**
	 * Renames a table.
	 */
	public function rename_table( $oldname, $newname ){
		if( !$this->table_exists($oldname) )
			return false;
		global $wpdb;
		$query = "RENAME TABLE " . $oldname . " TO " . $newname;
		return $wpdb->query( $query );
	}

	/**
	 * Search and replace any value in any column in any table.
	 *
	 * @param string $oldname Current/old option name to change, ie. 'postworld-styles-theme'
	 * @param string $newname New option name, ie. 'theme-styles'
	 *
	 * @return mixed The result of the DB query operation.
	 */
	public function search_and_replace( $vars = array() ){
		global $wpdb;
		$default_vars = array(
			'table_name' => null,
			'column_name' => null,
			'search_value' => null,
			'replace_value' => null,
			'where_row' => null,
			'where_value' => array()
			);

		$vars = array_replace($default_vars, $vars);

		// Construct WHERE clause
		if( !empty( $where_row ) && !empty( $where_value ) ){
			// Make $where_value into an array
			if( !is_array( $where_value ) )
				$where_value = array( $where_value );
			// Make $where_value array into a comma delimited string
			$in = implode( ',', $where_value );
			// Define WHERE clause
			$WHERE = " WHERE " . $where_row . " in (" . $in . ") " ;
		}
		else{
			$WHERE = '';
		}

		extract($vars);
		
		$query = "
			UPDATE " . $table_name . "
			SET " . $column_name . " =
			REPLACE( " . $column_name . ", '" . $search_value . "', '" . $replace_value . "' ) " .
			$WHERE;

		return $wpdb->query( $query );

	}

	/**
	 * Returns an array of associative arrays with all the values
	 * In a table in the following format : [{column:'value'},{column:'value'}]
	 */
	public function get_all_table_values( $table_name ){
		if( !$this->table_exists( $table_name ) )
			return false;
		global $wpdb;
		return $wpdb->get_results( "SELECT * FROM ".$table_name );
	}

	/**
	 * Drop/delete a table if it exists.
	 */
	public function drop_table( $table_name ){
		global $wpdb;
		return $wpdb->query( "DROP TABLE IF EXISTS ".$table_name );
	}

}

/**
 * GENERAL DB RELATED FUNCTIONS
 * @todo Refactor into PW_Database Class
 */

function pw_get_all_comment_ids(){
	global $wpdb;
	$query = "
		SELECT comment_ID
		FROM ".$wpdb->comments . "
		WHERE comment_approved = 1";
	$comments = $wpdb->get_results( $query );
	$ids = array();
	foreach( $comments as $comment ){
		$ids[] = $comment->comment_ID;
	}
	return $ids;
}

function pw_get_all_user_ids(){
	global $wpdb;
	$query = "SELECT ID FROM ".$wpdb->users;
	$users = $wpdb->get_results( $query );
	$ids = array();
	foreach( $users as $user ){
		$ids[] = $post->ID;
	}
	return $ids;
}

function pw_get_all_post_ids_in_post_type( $post_type, $post_status = '' ){
	// Returns a 1D array of all the post IDs in a post type
	global $wpdb;

	$post_status_query = ( !empty($post_status) && is_string($post_status) ) ?
		" AND post_status='" . $post_status . "'" :
		"";

	$query = "
		SELECT ID
		FROM ".$wpdb->posts."
		WHERE post_type ='".$post_type."'"
		. $post_status_query;

	$posts = $wpdb->get_results( $query );

	$ids = array();
	foreach( $posts as $post ){
		$ids[] = $post->ID;
	}
	return $ids;
}

