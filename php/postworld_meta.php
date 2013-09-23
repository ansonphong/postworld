<?php

function pw_get_post_meta($post_id){
	global $wpdb;
	

	$post_meta_table = $wpdb->pw_prefix.'post_meta';

	$meta = $wpdb->get_row("SELECT * FROM $post_meta_table WHERE post_id = $post_id", ARRAY_A);
	return $meta;
}


?>