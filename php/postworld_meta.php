<?php

function pw_get_post_meta($post_id){
	global $wpdb;
	global $pw_table_names;

	$post_meta_table = $pw_table_names['post_meta'];

	$meta = $wpdb->get_row("SELECT * FROM $post_meta_table WHERE post_id = $post_id", ARRAY_A);
	return $meta;
}


?>