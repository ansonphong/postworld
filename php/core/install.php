<?php 
/**
 * Installs Postworld tables into the database.
 */
function postworld_install(){
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	global $wpdb;
	
	$tables = pw_config_db_tables();

	if( in_array( 'post_meta', $tables ) ){
		$post_meta_table_name = $wpdb->pw_prefix.'post_meta';
		dbDelta(
			"CREATE TABLE $post_meta_table_name (
				post_id BIGINT(20) unsigned NOT NULL,
				author_id BIGINT(20) UNSIGNED NOT NULL,
				post_class char(16) NOT NULL,
				link_format char(16) NOT NULL,
				link_url varchar(512) DEFAULT '' NOT NULL,
				post_points mediumint(10) DEFAULT '0' NOT NULL,
				rank_score mediumint(10) DEFAULT '0' NOT NULL,
				post_shares mediumint(10) DEFAULT '0' NOT NULL,
				geo_latitude DECIMAL(10, 8) NOT NULL,
				geo_longitude DECIMAL(11, 8) NOT NULL,
				event_start int(11) NOT NULL,
				event_end int(11) NOT NULL,
				related_post BIGINT(20) unsigned NOT NULL,
				UNIQUE KEY post_id (post_id)
			);" );
	}

	if( in_array( 'post_points', $tables ) ){
		$post_points_table_name = $wpdb->pw_prefix.'post_points';
		dbDelta(
			"CREATE TABLE $post_points_table_name (
				post_id BIGINT(20) unsigned NOT NULL,
				user_id BIGINT(20) UNSIGNED NOT NULL,
				post_points mediumint(8) DEFAULT '0' NOT NULL,
				time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				UNIQUE KEY post_id_user_id (post_id,user_id)
			 );" );
	}

	if( in_array( 'comment_meta', $tables ) ){
		$comment_meta_table_name = $wpdb->pw_prefix.'comment_meta';
		dbDelta(
			"CREATE TABLE $comment_meta_table_name (
				comment_id mediumint(8) NOT NULL,
				post_id BIGINT(20) unsigned NOT NULL,
				comment_points mediumint(8) DEFAULT '0' NOT NULL,
				UNIQUE KEY comment_id (comment_id)
			);" );
	}
	
	if( in_array( 'comment_points', $tables ) ){
		$comment_points_table_name = $wpdb->pw_prefix.'comment_points';
		dbDelta(
			"CREATE TABLE $comment_points_table_name (
				comment_id BIGINT(20) UNSIGNED NOT NULL,
				user_id BIGINT(20) UNSIGNED NOT NULL,
				comment_post_id mediumint(9) NOT NULL,
				comment_author_id BIGINT(20) UNSIGNED NOT NULL,
				points mediumint(8) DEFAULT '0' NOT NULL,
				time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				DROP UNIQUE KEY comment_id,
				DROP INDEX user_id
			);" );
	}

	if( in_array( 'user_meta', $tables ) ){
		$user_meta_table_name = $wpdb->pw_prefix.'user_meta';
		dbDelta(
			"CREATE TABLE $user_meta_table_name (
				user_id BIGINT(20) UNSIGNED NOT NULL,
				post_points mediumint(8) DEFAULT '0' NULL,
				post_points_meta MEDIUMTEXT NULL,
				comment_points mediumint(8) DEFAULT '0' NULL,
				share_points mediumint(8) DEFAULT '0' NULL,
				share_points_meta MEDIUMTEXT NULL,
				post_relationships TEXT NULL,
				post_votes TEXT NULL,
				comment_votes TEXT NULL,
				location_city char(24) NULL,
				location_region char(24) NULL,
				location_country char(24) NULL,
				UNIQUE KEY user_id (user_id)
			);" );
	}

	if( in_array( 'user_shares', $tables ) ){
		$user_shares_table_name = $wpdb->pw_prefix.'user_shares';
		dbDelta(
			"CREATE TABLE $user_shares_table_name (
				user_id BIGINT(20) UNSIGNED NOT NULL,
				post_id BIGINT(20) unsigned NOT NULL,
				recent_ips varchar(8000) DEFAULT '' NOT NULL,
				total_views mediumint(9) NOT NULL
			);" );
	}

	if( in_array( 'favorites', $tables ) ){
		$favorites_table_name = $wpdb->pw_prefix.'favorites';
		dbDelta(
			"CREATE TABLE $favorites_table_name (
				user_id BIGINT(20) UNSIGNED NOT NULL,
				post_id BIGINT(20) unsigned NOT NULL,
				time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				UNIQUE KEY user_id_post_id (user_id,post_id)
			);" );
	}

	if( in_array( 'cron_logs', $tables ) ){
		$cron_logs_table_name = $wpdb->pw_prefix.'cron_logs';
		dbDelta(
			"CREATE TABLE $cron_logs_table_name (
				cron_run_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				function_type CHAR(50) NOT NULL,
				process_id char(16) NULL,
				time_start TIMESTAMP NOT NULL,
				time_end TIMESTAMP NOT NULL,
				timer DECIMAL(8,4) NOT NULL,
				posts INT NULL,
				query_args MEDIUMTEXT  NULL,
				UNIQUE KEY cron_run_id (cron_run_id)
			);" );
	}

	if( in_array( 'shares', $tables ) ){
		$shares_table_name = $wpdb->pw_prefix.'shares';
		dbDelta(
			"CREATE TABLE $shares_table_name (
				user_id BIGINT(20) UNSIGNED NOT NULL,
				post_id BIGINT(20) UNSIGNED NOT NULL,
				author_id BIGINT(20) UNSIGNED NOT NULL,
				recent_ips TEXT NULL,
				shares INT NULL,
				last_time TIMESTAMP NULL
			);" );
	}

	if( in_array( 'cache', $tables ) ){
		$cache_table_name = $wpdb->pw_prefix.'cache';
		dbDelta(
			"CREATE TABLE $cache_table_name (
				cache_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				cache_type TEXT NULL,
				cache_name TEXT NULL,
				cache_hash TEXT NULL,
				cache_content LONGBLOB NULL,
				cache_expire BIGINT(10),
				UNIQUE KEY cache_id (cache_id)
			);" );
	}

	if( in_array( 'ips', $tables ) ){
		$ips_table_name = $wpdb->pw_prefix.'ips';
		dbDelta(
			"CREATE TABLE $ips_table_name (
				ipv4 INT UNSIGNED,
				PTR CHAR(15),
				reason CHAR(32),
				time TIMESTAMP NULL
			);" );
	}

	
	// Update the DB with the new postworld DB version
	global $pw;
	update_option( PW_DB_VERSION, $pw['info']['db_version'] );
	
}

function postworld_uninstall(){
	//global $wpdb;
    //$table = $wpdb->prefix."your_table_name";

    //Delete any options thats stored also?
	//delete_option('wp_yourplugin_version');
	//$wpdb->query("DROP TABLE IF EXISTS $table");
}

function pw_db_version_is_old(){
	// Return true if running an old version of the DB
	global $pw;
	// Get the current version
	$current_version = floatval( get_option( PW_DB_VERSION, 0 ) );
	// Get the new version
	$new_version = floatval($pw['info']['db_version']);
	// If the version of Postworld is old
	/**
	 * @todo Replace this with PHP version_compare() function
	 * @link http://php.net/manual/en/function.version-compare.php
	 */
	$version_is_old = (bool) ( $new_version > $current_version );
	return $version_is_old;
}

if( pw_db_version_is_old() ){
	postworld_install();
}


?>