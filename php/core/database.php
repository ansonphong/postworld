<?php
class PW_Database{

	/**
	 * Renames all values under the meta_key column
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
		$query = "
			UPDATE ".$wpdb->postmeta . "
			SET meta_key = '". $newname ."'
			WHERE meta_key = '" . $oldname . "'";
		return $wpdb->query( $query );
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
		$query = "
			UPDATE ".$wpdb->options . "
			SET option_name = '". $newname ."'
			WHERE option_name = '" . $oldname . "'";
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

		$default_vars = array(
			'tablename' => null,
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

		global $wpdb;
		$query = "
			UPDATE " . $tablename . "
			SET " . $column_name . " =
			REPLACE( " . $column_name . ", '" . $search_value . "', '" . $replace_value . "' ) " .
			$WHERE;

		return $wpdb->query( $query );

	}

}

