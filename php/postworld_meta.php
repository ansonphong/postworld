<?php

global $pw_post_meta_fields;
$pw_post_meta_fields = array(
	'post_class',
	'link_format',
	'link_url',
	'post_author',
	'event_start',
	'event_end',
	'geo_longitude',
	'geo_latitude',
	'related_post'
	);

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
		     • link_format
		     • link_url
		
		Usage:
		$post_meta = array(
		     'post_id' => integer,
		     'post_class' => string,
		     'link_format' => string,
		     'link_url' => string
		);
	 */
	 
	global $wpdb;
	//$wpdb -> show_errors(); 
	
	add_record_to_post_meta($post_id);

	$query = "update $wpdb->pw_prefix"."post_meta set ";
	$insertComma = FALSE;

	// POST AUTHOR AS AUTHOR ID
	if( isset($post_meta['post_author']) ){
		if($insertComma === TRUE) $query.=" , ";
		$query .= "author_id='".$post_meta['post_author']."' ";
		$insertComma = TRUE;
	}

	// POST CLASS
	if( isset($post_meta['post_class']) ){
		if($insertComma === TRUE) $query.=" , ";
		$query .= "post_class='".$post_meta['post_class']."' ";
		$insertComma = TRUE;
	}

	// LINK FORMAT
	if( isset($post_meta['link_format']) ){
		if($insertComma === TRUE) $query.=" , ";
		$query .= "link_format='".$post_meta['link_format']."' ";
		$insertComma = TRUE;
	}

	// LINK URL
	if( isset($post_meta['link_url']) ){
		if($insertComma === TRUE) $query.=" , ";
		$query .= "link_url='".$post_meta['link_url']."' ";
		$insertComma = TRUE;
	}

	// EVENT START
	if( isset($post_meta['event_start']) ){
		if($insertComma === TRUE) $query.=" , ";
		$query .= "event_start='".$post_meta['event_start']."' ";
		$insertComma = TRUE;
	}

	// EVENT END
	if( isset($post_meta['event_end']) ){
		if($insertComma === TRUE) $query.=" , ";
		$query .= "event_end='".$post_meta['event_end']."' ";
		$insertComma = TRUE;
	}

	// GEO LATITUDE
	if( isset($post_meta['geo_latitude']) ){
		if($insertComma === TRUE) $query.=" , ";
		$query .= "geo_latitude='".$post_meta['geo_latitude']."' ";
		$insertComma = TRUE;
	}

	// GEO LONGITUDE
	if( isset($post_meta['geo_longitude']) ){
		if($insertComma === TRUE) $query.=" , ";
		$query .= "geo_longitude='".$post_meta['geo_longitude']."' ";
		$insertComma = TRUE;
	}

	// RELATED POST
	if( isset($post_meta['related_post']) ){
		if($insertComma === TRUE) $query.=" , ";
		$query .= "related_post='".$post_meta['related_post']."' ";
		$insertComma = TRUE;
	}


	if( $insertComma == FALSE ){
		return false;
	}
	
	else{

		$query.= " where post_id=".$post_id ;
	 	$wpdb->query($query);

	 	return $post_id;
	}

	
}
?>