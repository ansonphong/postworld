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
	public function rename_all_postmeta_keys( $oldname, $newname ){
		global $wpdb;
		$query = "
			UPDATE ".$wpdb->postmeta . "
			SET meta_key = '". $newname ."'
			WHERE meta_key = '" . $oldname . "'";
		return $wpdb->query( $query );
	}

}

