<?php

	///// POST TYPE PAGE SETUP /////
	$post_type = get_query_var( 'post_type' ); //get_post_type()
	$post_type_obj = get_post_type_object( $post_type );
	$post_type_name = $post_type_obj->labels->name; 
	$post_type_name = $post_type_obj->labels->name; 


	///// TAXONOMY PAGE PHP VARS SETUP /////
	$taxonomy = get_query_var( 'taxonomy' );
	$term_id = get_queried_object()->term_id;
	$term = get_term( $term_id, $taxonomy );
	$term_title = $term->name; 
	$term_slug = $term->slug; 
	$term_parent = $term->parent; 

	// If parent term exists
	if( $term_parent != 0 ){
		$term_parent = get_term( $term_parent, $taxonomy );
		$term_parent->url = get_term_link( intval($term_parent->term_id) , $taxonomy );
	}
	//echo $taxonomy;

	//echo "<pre>".json_encode($taxonomy, JSON_PRETTY_PRINT)."</pre>";

?>