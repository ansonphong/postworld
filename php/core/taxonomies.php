<?php

function pw_get_taxonomies( $args = array(), $output = 'names', $operator = 'and'  ){

	$default_args = array(
		//'_builtin'	=>	false,
		'public'	=>	true,
		);

	$args = array_replace_recursive($default_args, $args);

	// Gives a proper output of get_taxonomies names
	$taxonomies = get_taxonomies( $args, $output, $operator );

	///// OUTPUT /////
	switch( $output ){

		case 'names':
			// Process the names into a proper array
			// Because for unknown reasons, the WP native function
			// Outputs an associative array with the same values and keys
			// Which doesn't make any sense, so this will fix that
			$new_taxonomies = array();
			foreach( $taxonomies as $key => $value ){
				$new_taxonomies[] = $key;
			}
			$taxonomies = $new_taxonomies;
			break;

		case 'objects':
			break;

	}

	//pw_log( $taxonomies );

	return $taxonomies;

}

////////// POSTWORLD QUERY TERMS //////////
function pw_query_terms( $args ){
  extract($args);
  global $wpdb;

  $query_term = $search;
  $tags_query = "
	SELECT
	  $wpdb->terms.term_id,
	  $wpdb->terms.name,
	  $wpdb->terms.slug
	FROM
	  $wpdb->terms
	  INNER JOIN
		$wpdb->term_taxonomy
	  ON
		$wpdb->terms.term_id = $wpdb->term_taxonomy.term_id
	WHERE
	  name LIKE '$query_term%' AND taxonomy = '$taxonomy'
	";

  return $wpdb->get_results($tags_query,ARRAY_A);

}


////////// TAXONOMIES OUTLINE MIXED //////////
function taxonomies_outline_mixed( $taxonomy_options ){
	// Wrapper function for taxonomies_outline() Method
	// Takes mixed options per taxonomy
	// Returns a single object
	$tax_outline_mixed = array();

	// FOR EACH INPUT TAXONOMY
	foreach ($taxonomy_options as $taxonomy => $options) {
		if( !isset( $options['filter'] ) )
			$options['filter'] = true;

		$tax = taxonomies_outline( array($taxonomy), $options['max_depth'], $options['fields'], $options['filter'] );
		$tax_outline_mixed = array_merge( $tax_outline_mixed, $tax );//array_push( $tax_outline_mixed, $tax_outline );
	}
	
	return $tax_outline_mixed;
}


////////// CALLBACK FUNCTION //////////
	// Get Taxonomy Term Meta
	function tax_term_meta($input) {
		$term_id = (int)$input[0];
		$taxonomy = $input[1];
		$term_meta['url'] = get_term_link($term_id, $taxonomy);
		return $term_meta;
	}


////////// TAXONOMIES OUTLINE //////////
function taxonomies_outline($taxonomies, $max_depth = 2, $fields = 'all', $filter = false ) {



	// If Taxonomies is not defined or 'all'
	// Get all Public Taxonomies
	if (!$taxonomies || $taxonomies == 'all') {
		// QUERY TAXONOMIES
		$taxonomy_args = array('public' => true);
		$taxonomies = get_taxonomies($taxonomy_args, 'names');
	}

	// Default Fields
	if (!$fields or $fields == 'all')
		$fields = array('term_id', 'name', 'slug', 'description', 'parent', 'count', 'taxonomy', 'url');

	// Define Callback to get URL
	if (in_array('url', $fields)) {
		$callback = 'tax_term_meta';
		$callback_fields = array('term_id', 'taxonomy');
	}

	// Setup Taxonomy Outline Object
	$tax_outline = array();

	///// TAXONOMIES : Iterate through each Taxonomy //////
	foreach ($taxonomies as $taxonomy) {
		// Get the Taxonomy Object
		$tax_obj = get_taxonomy($taxonomy);

		// If it's not hierarchical, skip it
		//if (!$tax_obj->hierarchical)
		//	continue;

		// Process and order Terms Recursively
		$tax_terms = get_terms($taxonomy, 'hide_empty=0');

		// Setup Terms Array
		$tax_outline[$taxonomy]['terms'] = array();

		// DEFAULTS
		if( !isset($callback_fields) ) $callback_fields = "";
		if( !isset($callback) ) $callback = "";

		// WP TREE_OBJ COMMAND
		$tax_terms = get_terms($taxonomy, 'hide_empty=0');

		// Sanitize numeric values
		$tax_terms = pw_sanitize_numeric_array_of_a_arrays( $tax_terms );

		//pw_log( json_encode($tax_terms) );

		$args = array('object' => $tax_terms, 'fields' => $fields, 'id_key' => 'term_id', 'parent_key' => 'parent', 'child_key' => 'terms', 'max_depth' => $max_depth, 'callback' => $callback, 'callback_fields' => $callback_fields, );

		$tax_outline[$taxonomy]['terms'] = wp_tree_obj($args);


		///// SET LABEL VALUES /////
		// Set the Outline Values
		$tax_outline[$taxonomy]['labels'] = $tax_obj->labels;
		//$tax_outline[$taxonomy]['cap'] = $tax_obj->cap;

	}

	////////// OUTLINE FILTERS //////////
	if ($filter != false){
		
		///// LABEL GROUP FILTER /////
		// Filters up to one level of children
		// Strips the tree down to linear level
		// With terms having an additional 'parent_[key]':'value' pair
		// For each included $field, ie. parent_slug, parent_name, parent_term_id, etc.
		// For use with AngularJS "label group" <select> <options> comprehension expression 

		if ( $filter == "label_group" ){
			// FOR EACH TAXONOMY
			foreach ( $tax_outline as $taxonomy => $tax_value ) {
				
				// SETUP FLAT TERMS CONTAINER STRUCTURE
				$flat_terms = array();

				// FOR EACH ROOT TERM
				$root_terms = $tax_value['terms'];
				foreach ( $root_terms as $root_term ) {

					// IF THE TERM HAS CHILD TERMS
					if ( !empty($root_term["terms"]) ){

						// FOR EACH CHILD TERM
						foreach ( $root_term["terms"] as $term ) {
							
							// SETUP FLAT TERM
							$flat_term = array();

							// FOR EACH FIELD
							foreach ( $fields as $field ) {
								// PREPENDING "parent_"
								$parent_field = "parent_".$field;
								// TRANSFER THE PARENT TERM FIELDS TO THE FLAT TERM
								$flat_term[$parent_field] = $root_term[$field];
								// TRANSFER THE TERM FIELDS TO THE FLAT TERM
								$flat_term[$field] = $term[$field];
								
							} // END FOR EACH FIELD

							// PUSH THE FLAT TERM TO THE STACK
							array_push( $flat_terms, $flat_term );

						} // END FOR EACH CHILD TERM


					} // END IF CHILD TERMS


				} // END FOR EACH ROOT TERM
				
				// REWRITE THE ORIGINAL OUTLINE
				$tax_outline[$taxonomy]['terms'] = $flat_terms;

			} // END FOR EACH TAXONOMY


		} // END LABEL GROUP FILTER


	}

	return $tax_outline;


}

////////// POSTWORLD INSERT TERMS //////////
function pw_insert_terms($terms_array, $input_format = ARRAY_A, $force_slugs = FALSE) {
//print_r($terms_array);
//echo"<br><br>";

	if($input_format=="JSON"){
		
		$terms_array_decoded = json_decode($terms_array,TRUE);
	
		$terms_array = $terms_array_decoded;
		
	}

//print_r($terms_array);
//echo("<br><br><br>");
	$taxonomy_term_names = array_keys($terms_array);
	//print_r($taxonomy_term_names);
	//echo("<br><br><br>");
	$number_of_taxonomy_terms = count($taxonomy_term_names);

	for ($i = 0; $i < $number_of_taxonomy_terms; $i++) {
		//print_r($terms_array[$taxonomy_term_names[$i]]);
		//echo("<br><br><br>");
		$current_object = $terms_array[$taxonomy_term_names[$i]];
		for ($j = 0; $j < count($current_object); $j++) {
			//print_r($current_object);
			//echo("<br><br><br>");
			//if($input_format=="JSON")
			//$current_object[$j] = get_object_vars($current_object[$j]);
			if (isset($current_object[$j]['slug'])) {
				//print_r($taxonomy_term_names[$i]);

				$term_in_tax_id = get_term_by('slug', $current_object[$j]['slug'], $taxonomy_term_names[$i], ARRAY_A);
				//($current_object[$j]['name'],$taxonomy_term_names[$i]);
				//print_r($term_in_tax_id);
				if ($term_in_tax_id === FALSE) {//doesn't exist in same taxonomy
						//check is same slug found but different tax
						$results = check_term_slug_exists($current_object[$j]['slug']);
						if (!is_null($results) && count($results)>0) {
							//echo("<br><br><br>");	
							//print_r($results);
							//echo("<br><br><br>");
							if ($force_slugs) {
								//$inc_number = count($results);
								$current_object[$j]['slug'] = $current_object[$j]['slug'] .'-1';
							}
						}
						//if not found at all or in defferent tax, insert	
							//echo "<br><br> not found at all or found in differen tax<br><br>";
							$insert_term_output = (wp_insert_term(
							   $current_object[$j]["name"], // the term 
							   $taxonomy_term_names[$i], // the taxonomy
							   array(
								 'slug' => $current_object[$j]["slug"],
							   )
							));
							$current_term_id=-1;
							//print_r($insert_term_output);
							if(gettype($insert_term_output)=='array')
								$current_term_id = $insert_term_output['term_id'];
							else if(isset($insert_term_output->error_data['term_exists'])){
								$current_term_id = $insert_term_output->error_data['term_exists'];
							}else{ // invalid tax
								//register_taxonomy( $taxonomy_term_names[$i], 'post', $args );
								//echo('TAXONOMYY NOY FOUND');
								$current_term_id=-1;
								
							}
				} else {
					// found exactly
					//echo "<br><br> found Exactly<br><br>";
					$current_term_id = $term_in_tax_id['term_id'];
					//update_name
					wp_update_term($current_term_id, $taxonomy_term_names[$i], array('name' => $current_object[$j]['name']));
				}
				
				if (isset($current_object[$j]['children']) && ($current_term_id!==-1)) {
					//echo "<br><br> inserting children<br><br>";
					//$current_term_id = $insert_term_output['term_id'];
					//print_r($current_object[$j]['children']);
					//if($input_format=="JSON")
					//$current_object[$j]['children'] = get_object_vars($current_object[$j]['children']);
					$childres_names = array_keys($current_object[$j]['children']);
					//print_r($childres_names);
					$number_of_childres_names = count($childres_names);
					//print_r($childres_names);
					for ($k = 0; $k < $number_of_childres_names; $k++) {
						//echo"<br> dddddddddddd". $current_object[$j]["children"][$childres_names[$k]];
						//echo "<br>".$current_term_id."<br>";
						$child_term_in_tax_id = term_exists($current_object[$j]["children"][$childres_names[$k]], $taxonomy_term_names[$i],$current_term_id);
						//echo ($child_term_in_tax_id);
						if ($child_term_in_tax_id === 0 || is_null($child_term_in_tax_id)) {
							//echo "<br>mal2ahoosh<br>";
							
								$output = (wp_insert_term($current_object[$j]["children"][$childres_names[$k]], // the term
								$taxonomy_term_names[$i], // the taxonomy
								array('slug' => $childres_names[$k], 'parent' => $current_term_id)));
							
							//print_r($output);
						}
					}
				}
			}
		}

	}

}

function check_term_slug_exists($slug) {
	global $wpdb;
	$wpdb -> show_errors();

	$query = "SELECT * FROM wp_terms WHERE slug = '" . $slug . "'";
	$results = $wpdb -> get_results($query);
	return $results;
}


////////// GET CHILD TERMS META //////////
// Add URL / meta fields to taxonomy
function pw_get_child_terms_meta( $term_query, $taxonomy ){
	// Get child terms
	$sub_terms = get_terms( $taxonomy, $term_query );
	// Add Meta data to terms
	$sub_terms_meta = array();
	foreach( $sub_terms as $sub_term ){
		$sub_term = (array) $sub_term;
		// Santize numeric strings into numbers
		$sub_term = pw_sanitize_numeric_a_array($sub_term);
		// Add URL
		$sub_term['url'] = get_term_link( intval($sub_term['term_id']) , $taxonomy );
		array_push( $sub_terms_meta, $sub_term );
	}
	return $sub_terms_meta;
}




/*
 * Replace Taxonomy slug with Post Type slug in url
 * Version: 1.1
 * • Allows custom taxonomies to share the same slug as custom post types
 * • Use like this : add_filter('generate_rewrite_rules', 'pw_taxonomy_slug_rewrite');
 * • Both the taxonomy and post_type registrations must contain a value for rewrite['slug']
 */

function pw_taxonomy_slug_rewrite($wp_rewrite) {
	global $wp_rewrite;
	$rules = array();
	// get all custom taxonomies
	$taxonomies = get_taxonomies(array('_builtin' => false), 'objects');
	// get all custom post types
	$post_types = get_post_types(array('public' => true, '_builtin' => false), 'objects');
	 
	foreach ($post_types as $post_type) {
		foreach ($taxonomies as $taxonomy) {
		 
			// check if taxonomy is registered for this custom type
			if ( $taxonomy->rewrite['slug'] == $post_type->rewrite['slug'] ) {

				// get category objects
				$terms = get_categories(array( 'taxonomy' => $taxonomy->name, 'hide_empty' => 0));
				//'type' => $object_type,

				// make rules
				foreach ($terms as $term) {
					$rules[ $taxonomy->rewrite['slug'] . '/' . $term->slug . '/?$'] = 'index.php?' . $term->taxonomy . '=' . $term->slug;
				}
			}

		}
	}

	// merge with global rules
	$wp_rewrite->rules = $rules + $wp_rewrite->rules;
}
