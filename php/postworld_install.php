<?php 

////////// INSTALL POSTWORLD ///////////

function postworld_install() {
  global $wpdb;
  global $postworld_db_version;
  global $pw_table_names;

  $meta_table_name = $pw_table_names['meta'];
  $sql_postworld_meta = "CREATE TABLE $meta_table_name (
      post_id mediumint(9) NOT NULL,
      post_class char(16) NOT NULL,
      post_format char(16) NOT NULL,
      link_url varchar(512) DEFAULT '' NOT NULL,
      points mediumint(8) DEFAULT '0' NOT NULL,
      rank_score mediumint(4) DEFAULT '0' NOT NULL,
      UNIQUE KEY post_id (post_id)
    );";

  $points_table_name = $pw_table_names['points'];
  $sql_postworld_points = "CREATE TABLE $points_table_name (
      post_id mediumint(9) NOT NULL,
      user_id mediumint(8) NOT NULL,
      author_id mediumint(8) NOT NULL,
      points mediumint(8) DEFAULT '0' NOT NULL,
      time TIMESTAMP NOT NULL
    );";

  $points_comments_table_name = $pw_table_names['points_comments'];
  $sql_postworld_points_comments = "CREATE TABLE $points_comments_table_name (
      user_id mediumint(8) NOT NULL,
      comment_post_id mediumint(9) NOT NULL,
      comment_author_id mediumint(8) NOT NULL,
      points mediumint(8) DEFAULT '0' NOT NULL,
      time TIMESTAMP NOT NULL
    );";

  $user_meta_table_name = $pw_table_names['user_meta'];
  $sql_postworld_user_meta = "CREATE TABLE $user_meta_table_name (
      user_id mediumint(9) NOT NULL,
      user_role char(16) NOT NULL,
      viewed MEDIUMTEXT NOT NULL, 
      favorites MEDIUMTEXT NOT NULL, 
      location_city char(24) NOT NULL,
      location_region char(24) NOT NULL,
      location_country char(24) NOT NULL,
      view_karma mediumint(8) DEFAULT '0' NOT NULL,
      share_karma mediumint(8) DEFAULT '0' NOT NULL,
      UNIQUE (user_id)
    );";

  $user_shares_table_name = $pw_table_names['user_shares'];
  $sql_postworld_user_shares = "CREATE TABLE $user_shares_table_name (
      user_id mediumint(9) NOT NULL,
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

  dbDelta( $sql_postworld_meta );
  dbDelta( $sql_postworld_points );
  dbDelta( $sql_postworld_points_comments );
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



?>