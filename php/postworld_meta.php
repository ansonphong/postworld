<?php

function pw_get_post_meta($post_id){
	global $wpdb;
	$post_meta_table = $wpdb->pw_prefix.'post_meta';
	$meta = $wpdb->get_row("SELECT * FROM $post_meta_table WHERE post_id = $post_id", ARRAY_A);
	return $meta;
}

function pw_set_post_meta($post_id, $post_meta){
	/*
	  • Used to set Postworld values in the wp_postworld_

		Parameters:
		All parameters, except post_id, are optional.
		
		$post_id : integer (required)
		
		$post_meta : Array
		     • post_class
		     • post_format
		     • link_url
		
		Usage:
		$post_meta = array(
		     'post_id' => integer,
		     'post_class' => string,
		     'post_format' => string,
		     'link_url' => string
		);
	 */
	 
	 global $wpdb;
	 //$wpdb -> show_errors(); 
	 $insertComma = FALSE;
	 
	 add_record_to_post_meta($post_id);

	 $query = "Update $wpdb->pw_prefix"."post_meta set ";
	 if($post_meta['post_class'] !=null ){
	 	$query.="post_class='".$post_meta['post_class']."' ";
		 $insertComma= TRUE;
	 }
	 if($post_meta['post_format'] !=null ){
	 	if($insertComma === TRUE) $query.=" , ";
	 	
	 	$query.="post_format='".$post_meta['post_format']."' ";
	 	$insertComma= TRUE;
	 }
	 
	 if($post_meta['link_url'] !=null ){
	 	if($insertComma === TRUE) $query.=" , ";
	 	$query.="link_url='".$post_meta['link_url']."' ";
	 }

	 if($insertComma === FALSE && $post_meta['link_url']==null){return "insufficient Parameters";}
	 else{
	 	$query.=" where post_id=".$post_id ;
		//echo $query;
	 	$wpdb->query($query);
	 }
}
?>