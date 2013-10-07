<?php

function taxonomies_outline( $taxonomies, $max_depth = 2, $fields = 'all' ){

	// If Taxonomies is not defined or 'all'
	// Get all Public Taxonomies
	if(!$taxonomies || $taxonomies == 'all'){
		// QUERY TAXONOMIES
		$taxonomy_args = array('public'   => true ); 
        $taxonomies = get_taxonomies($taxonomy_args, 'names');
	}

	// Default Fields
	if (!$fields or $fields == 'all')
		$fields = array('term_id','name','slug','description','parent','count', 'taxonomy', 'url');

	////////// CALLBACK FUNCTION //////////
	// Get Taxonomy Term Meta
	function tax_term_meta( $input ){
		$term_id = (int)$input[0];
		$taxonomy = $input[1];
		$term_meta['url'] = get_term_link( $term_id, $taxonomy );
		return $term_meta;
	}

	// Define Callback to get URL
	if( in_array('url',$fields) ){
		$callback = 'tax_term_meta';
		$callback_fields = array( 'term_id', 'taxonomy' );
	}

	// Setup Taxonomy Outline Object
	$tax_outline = array();
	///// TAXONOMIES : Cycle through each Taxonomy //////
	foreach ($taxonomies as $taxonomy) {
		// Get the Taxnomy Object
		$tax_obj = get_taxonomy($taxonomy);

		// If it's not hierarchical, skip it
		if(!$tax_obj->hierarchical)
			continue;

		// Set the Outline Values
		$tax_outline[$taxonomy]['label'] = $tax_obj->label;
		//$tax_outline[$taxonomy]['cap'] = $tax_obj->cap;

		// Process and order Terms Recursively
		$tax_terms =  get_terms($taxonomy, 'hide_empty=0');
		
		// Setup Terms Array
		$tax_outline[$taxonomy]['terms'] = array();

		// WP TREE_OBJ COMMAND
		$tax_terms =  get_terms( $taxonomy , 'hide_empty=0');
		$args = array(
			'object' => $tax_terms,
			'fields' => $fields,
			'id_key' => 'term_id',
			'parent_key' => 'parent',
			'child_key' => 'terms',
			'max_depth' => $max_depth,
			'callback' => $callback,
			'callback_fields' => $callback_fields,
		);

		$tax_outline[$taxonomy] = wp_tree_obj( $args );

	}

	return $tax_outline;

}






?>