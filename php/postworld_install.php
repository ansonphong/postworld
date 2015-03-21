<?php 
////////// INSTALL POSTWORLD ///////////

function postworld_install(){
	global $wpdb;
	global $pw;

	/* POSTS */
	$post_meta_table_name = $wpdb->pw_prefix.'post_meta';
	$sql_postworld_post_meta = "CREATE TABLE $post_meta_table_name (
			post_id BIGINT(20) unsigned NOT NULL,
			author_id BIGINT(20) UNSIGNED NOT NULL,
			post_class char(16) NOT NULL,
			link_format char(16) NOT NULL,
			link_url varchar(512) DEFAULT '' NOT NULL,
			post_points mediumint(8) DEFAULT '0' NOT NULL,
			rank_score mediumint(4) DEFAULT '0' NOT NULL,
			post_shares mediumint(9) DEFAULT '0' NOT NULL,
			geo_latitude DECIMAL(10, 8) NOT NULL,
			geo_longitude DECIMAL(11, 8) NOT NULL,
			event_start int(11) NOT NULL,
			event_end int(11) NOT NULL,
			related_post BIGINT(20) unsigned NOT NULL,
			UNIQUE KEY post_id (post_id)
		);";
		//author_id BIGINT(20) UNSIGNED NOT NULL,
		//favorites mediumint(9) DEFAULT '0' NOT NULL,

	$post_points_table_name = $wpdb->pw_prefix.'post_points';
	$sql_postworld_post_points = "CREATE TABLE $post_points_table_name (
			post_id BIGINT(20) unsigned NOT NULL,
			user_id BIGINT(20) UNSIGNED NOT NULL,
			post_points mediumint(8) DEFAULT '0' NOT NULL,
			time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			UNIQUE KEY post_id_user_id (post_id,user_id)
		 );";

 /* Comments */
	$comment_meta_table_name = $wpdb->pw_prefix.'comment_meta';
	$sql_postworld_comment_meta= "CREATE TABLE $comment_meta_table_name (
			comment_id mediumint(8) NOT NULL,
			post_id BIGINT(20) unsigned NOT NULL,
			comment_points mediumint(8) DEFAULT '0' NOT NULL,
			UNIQUE KEY comment_id (comment_id)
		);";
	
	$comment_points_table_name = $wpdb->pw_prefix.'comment_points';
	$sql_postworld_comment_points= "CREATE TABLE $comment_points_table_name (
			comment_id BIGINT(20) UNSIGNED NOT NULL,
			user_id BIGINT(20) UNSIGNED NOT NULL,
			comment_post_id mediumint(9) NOT NULL,
			comment_author_id BIGINT(20) UNSIGNED NOT NULL,
			points mediumint(8) DEFAULT '0' NOT NULL,
			time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			UNIQUE KEY comment_id_user_id (comment_id,user_id)
		);";
		
	$user_meta_table_name = $wpdb->pw_prefix.'user_meta';
	$sql_postworld_user_meta = "CREATE TABLE $user_meta_table_name (
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
		);";
		
	$user_shares_table_name = $wpdb->pw_prefix.'user_shares';
	$sql_postworld_user_shares = "CREATE TABLE $user_shares_table_name (
			user_id BIGINT(20) UNSIGNED NOT NULL,
			post_id BIGINT(20) unsigned NOT NULL,
			recent_ips varchar(8000) DEFAULT '' NOT NULL,
			total_views mediumint(9) NOT NULL
		);";

	$favorites_table_name = $wpdb->pw_prefix.'favorites';
	$sql_postworld_favorites = "CREATE TABLE $favorites_table_name (
			user_id BIGINT(20) UNSIGNED NOT NULL,
			post_id BIGINT(20) unsigned NOT NULL,
			time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			UNIQUE KEY user_id_post_id (user_id,post_id)
		);";
		
	$feeds_table_name = $wpdb->pw_prefix.'feeds';
	$sql_postworld_feeds = "CREATE TABLE $feeds_table_name (
			feed_id char(128) NOT NULL,
			feed_query TEXT NOT NULL,
			feed_outline MEDIUMTEXT  NULL,
			feed_json LONGTEXT  NULL,
			time_start TIMESTAMP  NULL,
			time_end TIMESTAMP  NULL,
			timer INT  NULL,
			UNIQUE KEY feed_id (feed_id)
		);"; 

	$cron_logs_table_name = $wpdb->pw_prefix.'cron_logs';
	$sql_postworld_cron_logs = "CREATE TABLE $cron_logs_table_name (
			cron_run_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			function_type CHAR(50) NOT NULL,
			process_id char(16) NULL,
			time_start TIMESTAMP NOT NULL,
			time_end TIMESTAMP NOT NULL,
			timer INT NOT NULL,
			posts INT NULL,
			query_args MEDIUMTEXT  NULL,
			UNIQUE KEY cron_run_id (cron_run_id)
		);";  
	
	$shares_table_name = $wpdb->pw_prefix.'shares';
	$sql_postworld_shares = "CREATE TABLE $shares_table_name (
			user_id BIGINT(20) UNSIGNED NOT NULL,
			post_id BIGINT(20) UNSIGNED NOT NULL,
			author_id BIGINT(20) UNSIGNED NOT NULL,
			recent_ips TEXT NULL,
			shares INT NULL,
			last_time TIMESTAMP NULL
		);"; 
		
	$cache_table_name = $wpdb->pw_prefix.'cache';
	$sql_postworld_cache = "CREATE TABLE $cache_table_name (
			cache_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			cache_type TEXT NULL,
			cache_name TEXT NULL,
			cache_hash TEXT NULL,
			cache_content LONGBLOB NULL,
			UNIQUE KEY cache_id (cache_id)
		);";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	dbDelta( $sql_postworld_post_meta );
	dbDelta( $sql_postworld_post_points );
	dbDelta( $sql_postworld_comment_meta );
	dbDelta( $sql_postworld_comment_points );
	dbDelta( $sql_postworld_user_meta );
	dbDelta( $sql_postworld_user_shares );
	dbDelta( $sql_postworld_favorites );
	dbDelta( $sql_postworld_feeds );
	dbDelta( $sql_postworld_cron_logs );
	dbDelta( $sql_postworld_shares );
	dbDelta( $sql_postworld_cache );
	
	add_option( PW_DB_VERSION, $pw['info']['db_version'] );
	
}


function pw_db_version_is_old(){
	// Return true if running an old version of the DB
	global $pw;
	// Get the current version
	$current_version = floatval( get_option( PW_DB_VERSION, 0 ) );
	// If the version of Postworld is old
	return ( $pw['info']['db_version'] > $current_version );
}

///// IF OLD DB /////
if( pw_db_version_is_old() ){
	// Re-install Postworld
	postworld_install();
}

function postworld_install_data() {
	global $wpdb;
	global $pwSiteGlobals;
 
	/* OBSOLETE
	///// USER ROLE DATA /////
	// Pre-populate data for each role in >>> $pwSiteGlobals['roles'] <<<
	foreach ( $pwSiteGlobals['roles'] as $key => $value) {
		$add_rows = $wpdb->insert( $wpdb->pw_prefix.'user_roles',
		array(
			'user_role' => $key,
			'vote_points' => $value['vote_points']
			)
		);
	}
	*/

}


//////  Foreign Keys   //////
function postworld_install_Foreign_keys(){
	global $pw_queries;
	global $wpdb;	
		
	$wpdb -> show_errors();
	for ($i=0; $i < count($pw_queries['FK']); $i++) {
			$result = $wpdb -> get_var($pw_queries['FK'][$i]['contraint_check']);
		//log_me($result);
			if($result == 0){
			//	log_me('here');
				$wpdb -> query($pw_queries['FK'][$i]['query']);
			}
	}
			
}

//////  Install Triggers   //////
function postworld_install_Triggers(){
	global $pw_queries;
	global $wpdb;	
		
	$wpdb -> show_errors();
	for ($i=0; $i < count($pw_queries['Triggers']); $i++) {
		$wpdb -> query($pw_queries['Triggers'][$i]['drop']);
		$wpdb -> query($pw_queries['Triggers'][$i]['create']);
	}
	
}

?>