<?php
/******************************************
Plugin Name: Post World
Plugin URI: htp://phong.com/plugins/postworld
Description: Settings for Post World 2 Wordpress Configuration
Version: 2.0
Author: phong
Author URI: http://phong.com
License: GPL2
******************************************/

////////// INSTALL POSTWORLD ///////////
//include 'postworld_install.php';


global $postworld_db_version;
$postworld_db_version = "1.0";

function postworld_install() {
  global $wpdb;
  global $postworld_db_version;

  $pw_prefix = "postworld_";

  $meta_table_name = $wpdb->prefix . $pw_prefix .  "meta";
  $points_table_name = $wpdb->prefix . $pw_prefix . "points";
  $user_meta_table_name = $wpdb->prefix . $pw_prefix . "user_meta";
  $user_shares_table_name = $wpdb->prefix . $pw_prefix . "user_shares";
    
  $sql_postworld_meta = "CREATE TABLE $meta_table_name (
      id mediumint(9) NOT NULL,
      post_class char(16) NOT NULL,
      post_format char(16) NOT NULL,
      link_url varchar(512) DEFAULT '' NOT NULL,
      points mediumint(8) DEFAULT '0' NOT NULL,
      rank_score mediumint(4) DEFAULT '0' NOT NULL,
      UNIQUE KEY id (id)
    );";

  $sql_postworld_points = "CREATE TABLE $points_table_name (
      id mediumint(9) NOT NULL,
      user_id mediumint(8) NOT NULL,
      points mediumint(8) DEFAULT '0' NOT NULL,
      time TIMESTAMP NOT NULL
    );";

  $sql_postworld_user_meta = "CREATE TABLE $user_meta_table_name (
      user_id mediumint(9) NOT NULL,
      user_role char(16) NOT NULL,
      viewed MEDIUMTEXT NOT NULL, 
      favorites MEDIUMTEXT NOT NULL, 
      location_city char(24) NOT NULL,
      location_country char(24) NOT NULL,
      view_karma mediumint(8) DEFAULT '0' NOT NULL,
      share_karma mediumint(8) DEFAULT '0' NOT NULL,
      UNIQUE (user_id)
    );";

  $sql_postworld_user_shares = "CREATE TABLE $user_shares_table_name (
      user_id mediumint(9) NOT NULL,
      post_id mediumint(9) NOT NULL,
      recent_ips varchar(8000) DEFAULT '' NOT NULL,
      total_views mediumint(9) NOT NULL
    );";

  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

  dbDelta( $sql_postworld_meta );
  dbDelta( $sql_postworld_points );
  dbDelta( $sql_postworld_user_meta );
  dbDelta( $sql_postworld_user_shares );
  
  add_option( "postworld_db_version", $postworld_db_version );
}

/*
function postworld_install_data() {
  global $wpdb;
  $welcome_name = "Mr. WordPress";
  $welcome_text = "Congratulations, you just completed the installation!";

  $rows_affected = $wpdb->insert( $table_name, array( 'time' => current_time('mysql'), 'name' => $welcome_name, 'text' => $welcome_text ) );
}
*/

register_activation_hook( __FILE__, 'postworld_install' );
//register_activation_hook( __FILE__, 'jal_install_data' );

?>