<?php 

////////// INSTALL POSTWORLD ///////////

function postworld_install() {
  global $wpdb;
  global $postworld_db_version;
  global $pw_table_names;
//  global $pw_queries;
//  $wpdb -> show_errors();

  /* POSTS */
  $post_meta_table_name = $pw_table_names['post_meta'];
  $sql_postworld_post_meta = "CREATE TABLE $post_meta_table_name (
      post_id mediumint(9) NOT NULL,
      author_id BIGINT(20) UNSIGNED NOT NULL,
      post_class char(16) NOT NULL,
      post_format char(16) NOT NULL,
      link_url varchar(512) DEFAULT '' NOT NULL,
      post_points mediumint(8) DEFAULT '0' NOT NULL,
      rank_score mediumint(4) DEFAULT '0' NOT NULL,
      UNIQUE KEY post_id (post_id)
    );";

  $post_points_table_name = $pw_table_names['post_points'];
  $sql_postworld_post_points = "CREATE TABLE $post_points_table_name (
      post_id mediumint(9) NOT NULL,
      user_id BIGINT(20) UNSIGNED NOT NULL,
      post_points mediumint(8) DEFAULT '0' NOT NULL,
      time TIMESTAMP NOT NULL,
      UNIQUE KEY post_id_user_id (post_id,user_id)
     );";


 /* Comments */
  $comment_meta_table_name = $pw_table_names['comment_meta'];
  $sql_postworld_comment_meta= "CREATE TABLE $comment_meta_table_name (
      comment_id mediumint(8) NOT NULL,
      post_id mediumint(9) NOT NULL,
      comment_points mediumint(8) DEFAULT '0' NOT NULL
    );";
  
  $comment_points_table_name = $pw_table_names['comment_points'];
  $sql_postworld_comment_points= "CREATE TABLE $comment_points_table_name (
      user_id BIGINT(20) UNSIGNED NOT NULL,
      comment_post_id mediumint(9) NOT NULL,
      comment_author_id BIGINT(20) UNSIGNED NOT NULL,
      points mediumint(8) DEFAULT '0' NOT NULL,
      time TIMESTAMP NOT NULL
    );";
    
  
  $user_meta_table_name = $pw_table_names['user_meta'];
  $sql_postworld_user_meta = "CREATE TABLE $user_meta_table_name (
      user_id BIGINT(20) UNSIGNED NOT NULL,
      user_role char(16) NOT NULL,
      viewed MEDIUMTEXT NOT NULL, 
      favorites MEDIUMTEXT NOT NULL, 
      location_city char(24) NOT NULL,
      location_region char(24) NOT NULL,
      location_country char(24) NOT NULL,
      view_karma mediumint(8) DEFAULT '0' NOT NULL,
      share_karma mediumint(8) DEFAULT '0' NOT NULL,
      post_points mediumint(8) DEFAULT '0' NOT NULL,
      comment_points mediumint(8) DEFAULT '0' NOT NULL,
	  UNIQUE KEY user_id(user_id)
    );";

  $user_shares_table_name = $pw_table_names['user_shares'];
  $sql_postworld_user_shares = "CREATE TABLE $user_shares_table_name (
      user_id BIGINT(20) UNSIGNED NOT NULL,
      post_id mediumint(9) NOT NULL,
      recent_ips varchar(8000) DEFAULT '' NOT NULL,
      total_views mediumint(9) NOT NULL
    );";

  $user_roles_table_name = $pw_table_names['user_roles'];
  $sql_postworld_user_roles = "CREATE TABLE $user_roles_table_name (
      user_role char(16) NOT NULL,
      vote_points mediumint(6) NOT NULL
    );";

  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

  dbDelta( $sql_postworld_post_meta );
  dbDelta( $sql_postworld_post_points );
  dbDelta( $sql_postworld_comment_meta );
  dbDelta( $sql_postworld_comment_points );
  dbDelta( $sql_postworld_user_meta );
  dbDelta( $sql_postworld_user_shares );
  dbDelta( $sql_postworld_user_roles );
  
  add_option( "postworld_db_version", $postworld_db_version );
}


function postworld_install_data() {
  global $wpdb;
  global $pw_defaults;
  global $postworld_db_version;
  global $pw_table_names;

  ///// USER ROLE DATA /////
  // Pre-populate data for each role in >>> $pw_defaults['roles'] <<<
  foreach ( $pw_defaults['roles'] as $key => $value) {
    $add_rows = $wpdb->insert( $pw_table_names['user_roles'],
    array(
      'user_role' => $key,
      'vote_points' => $value['vote_points']
      )
    );
  }

}

//////  Foreign Keys   //////
function postworld_install_Foreign_keys(){
	global $pw_queries;
	global $wpdb;	
		
	$wpdb -> show_errors();
 	for ($i=0; $i < count($pw_queries['FK']); $i++) {
     	$result = $wpdb -> get_var($pw_queries['FK'][$i]['contraint_check']);
     	if($result == 0){
     		$wpdb -> query($wpdb -> prepare($pw_queries['FK'][$i]['query']));
     	}
 	}
 			
}

//////  Install Triggers   //////
function postworld_install_Triggers(){
	global $pw_queries;
	global $wpdb;	
		
	$wpdb -> show_errors();
 	for ($i=0; $i < count($pw_queries['Triggers']); $i++) {
		$wpdb -> query($wpdb -> prepare($pw_queries['Triggers'][$i]['drop']));
		$wpdb -> query($wpdb -> prepare($pw_queries['Triggers'][$i]['create']));
 	}
	
}
?>