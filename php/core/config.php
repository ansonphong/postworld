<?php
/**
 * Returns a specific key from the
 * Postworld Configuration.
 * 
 * @param string $key The position of the key to get or set, ie. 'key.subkey'
 * @param mixed $value (optional) The value to set. If no value set, will just return current value.
 */
function pw_config( $key = null, $value = null ){
	if( $key === null ){
		return $GLOBALS[ POSTWORLD_CONFIG ];
	}
	elseif( $key !== null && $value === null ){
		return _get( $GLOBALS[ POSTWORLD_CONFIG ], $key );
	}
	elseif( $value !== null ){
		$GLOBALS[ POSTWORLD_CONFIG ] = _set( $GLOBALS[ POSTWORLD_CONFIG ], $key, $value );
		return pw_config( $key );
	}
}

/**
 * Sets a specific key in the
 * Postworld Site Globals.
 */
function pw_set_config( $key, $value ){
	global $pw_config;
	$pw_config = _set( $pw_config, $key, $value );
	return $pw_config;
}

/**
 * Pushes a value to an array in the Postworld Config.
 */
function pw_push_config( $key, $value ){
	global $pw_config;
	$pw_config = _push( $pw_config, $key, $value );
	return $pw_config;
}

/**
 * Check if needle is in an array within the Postworld Config
 *
 * @param any $needle A value to check for in the array.
 * @param string $haystack_key A dot notation path to the key in Postworld Config
 * @return any|bool The value found, or false if none or not an array 
 */
function pw_config_in_array( $needle, $haystack_key ){
	$haystack = pw_config( $haystack_key );
	if( !is_array( $haystack ) )
		return false;
	return in_array( $needle, $haystack );
}

/**
 * Check if the Postworld DB configuration 
 * Has a particular table.
 */
function pw_config_db_has_table( $table ){
	global $wpdb;
	$table_name = $wpdb->pw_prefix.$table;
	return ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name);

	/*
	$tables = pw_config('database.tables');
	if($tables === false)
		return true;
	return pw_config_in_array($table, 'db.tables' );
	*/
}

/**
 * Which tables are configured to be created.
 *
 * @return array Array of table names, minus prefix.
 */
function pw_config_db_tables(){
	$tables = pw_config('database.tables');
	// Default tables, if none set
	if( empty($tables) )
		$tables = array(
			'post_meta',
			'post_points',
			'comment_meta',
			'comment_points',
			'user_meta',
			'user_shares',
			'favorites',
			'cron_logs',
			'shares',
			'cache',
			'ips',
			);
	return $tables;
}

/**
 * Tells whether or not the specified table is configured.
 *
 * @param string $table Table shortname, ie. 'post_meta' for wp_postworld_post_meta
 * @return boolean
 */
function pw_config_in_db_tables( $table ){
	return in_array( $table, pw_config_db_tables() );
}


/**
 * Add a post parent metabox.
 */
function pw_add_metabox_post_parent( $vars ){
	/*
	$vars = array(
		'labels'	=>	array(
			'title'		=>	'Theme',
			'search'	=>	'Search themes...'
			),
		'post_types' 	=> array( 'theme_version' ),
		'query'	=>	array(
			'post_type'			=>	'theme',
			),
		)
	 */
	return pw_push_config( 'wp_admin.metabox.post_parent', $vars );
}

/**
 * Add a WP Postmeta metabox.
 */
function pw_add_metabox_wp_postmeta( $vars ){
	/*
	$vars = array(
		'post_types' => array('blog'),
		'metabox'		=>	array(
			'title'		=>	__('Post Options','postworld'),
			'context'	=>	'normal',
			),
		'fields' => array(),
		),
	 */
	return pw_push_config( 'wp_admin.metabox.wp_postmeta', $vars );
}