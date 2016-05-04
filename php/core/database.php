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
		return $this->replace( array(
			'table_name' => $wpdb->postmeta,
			'column_name' => 'meta_key',
			'old_value' => $oldname,
			'new_value' => $newname,
			));
	}

	/**
	 * Renames all values under the meta_key column
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
	 * Used for renaming keys in the database.
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
	 * Search and replace any value in any column in any table.
	 *
	 * @param string $oldname Current/old option name to change, ie. 'postworld-styles-theme'
	 * @param string $newname New option name, ie. 'theme-styles'
	 *
	 * @return mixed The result of the DB query operation.
	 */
	public function search_and_replace( $vars = array() ){

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

		global $wpdb;
		$query = "
			UPDATE " . $table_name . "
			SET " . $column_name . " =
			REPLACE( " . $column_name . ", '" . $search_value . "', '" . $replace_value . "' ) " .
			$WHERE;

		return $wpdb->query( $query );

	}

}

