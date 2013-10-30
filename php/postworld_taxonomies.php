<?php

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

	////////// CALLBACK FUNCTION //////////
	// Get Taxonomy Term Meta
	function tax_term_meta($input) {
		$term_id = (int)$input[0];
		$taxonomy = $input[1];
		$term_meta['url'] = get_term_link($term_id, $taxonomy);
		return $term_meta;
	}

	// Define Callback to get URL
	if (in_array('url', $fields)) {
		$callback = 'tax_term_meta';
		$callback_fields = array('term_id', 'taxonomy');
	}

	// Setup Taxonomy Outline Object
	$tax_outline = array();
	///// TAXONOMIES : Cycle through each Taxonomy //////
	foreach ($taxonomies as $taxonomy) {
		// Get the Taxnomy Object
		$tax_obj = get_taxonomy($taxonomy);

		// If it's not hierarchical, skip it
		if (!$tax_obj -> hierarchical)
			continue;

		// Set the Outline Values
		$tax_outline[$taxonomy]['label'] = $tax_obj -> label;
		//$tax_outline[$taxonomy]['cap'] = $tax_obj->cap;

		// Process and order Terms Recursively
		$tax_terms = get_terms($taxonomy, 'hide_empty=0');

		// Setup Terms Array
		$tax_outline[$taxonomy]['terms'] = array();

		// WP TREE_OBJ COMMAND
		$tax_terms = get_terms($taxonomy, 'hide_empty=0');
		$args = array('object' => $tax_terms, 'fields' => $fields, 'id_key' => 'term_id', 'parent_key' => 'parent', 'child_key' => 'terms', 'max_depth' => $max_depth, 'callback' => $callback, 'callback_fields' => $callback_fields, );

		$tax_outline[$taxonomy] = wp_tree_obj($args);

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
			foreach ( $tax_outline as $taxonomy => $root_terms ) {
				
				// SETUP FLAT TERMS CONTAINER STRUCTURE
				$flat_terms = array();

				// FOR EACH ROOT TERM
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
				$tax_outline[$taxonomy] = $flat_terms;

			} // END FOR EACH TAXONOMY


		} // END LABEL GROUP FILTER


	}

	return $tax_outline;

}

function pw_insert_terms($terms_array, $input_format = ARRAY_A, $force_slugs = FALSE) {


	if($input_format=="JSON"){
		$terms_array = get_object_vars(json_decode($terms_array));
		print_r($terms_array);
	}

	$taxonomy_term_names = array_keys($terms_array);
	$number_of_taxonomy_terms = count($taxonomy_term_names);

	for ($i = 0; $i < $number_of_taxonomy_terms; $i++) {
		//print_r($terms_array[$taxonomy_term_names[$i]]);
		$current_object = $terms_array[$taxonomy_term_names[$i]];
		for ($j = 0; $j < count($current_object); $j++) {
			//print_r($current_object);
			if($input_format=="JSON")
			$current_object[$j] = get_object_vars($current_object[$j]);
			if (isset($current_object[$j]['slug'])) {
				//print_r($taxonomy_term_names[$i]);

				$term_in_tax_id = get_term_by('slug', $current_object[$j]['slug'], $taxonomy_term_names[$i], ARRAY_A);
				//($current_object[$j]['name'],$taxonomy_term_names[$i]);

				if ($term_in_tax_id === FALSE) {//doesn't exist in same taxonomy
						//check is same slug found but different tax
						$results = check_term_slug_exists($current_object[$j]['slug']);
						if (!is_null($results) && count($results)>0) {
							//print_r($results);
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
							
							//print_r($insert_term_output);
							if(gettype($insert_term_output)=='array')
								$current_term_id = $insert_term_output['term_id'];
							else{
								$current_term_id = $insert_term_output->error_data['term_exists'];
							}
				} else {
					// found exactly
					//echo "<br><br> found Exactly<br><br>";
					$current_term_id = $term_in_tax_id['term_id'];
					//update_name
					wp_update_term($current_term_id, $taxonomy_term_names[$i], array('name' => $current_object[$j]['name']));
				}

				if (isset($current_object[$j]['children'])) {
					//echo "<br><br> inserting children<br><br>";
					//$current_term_id = $insert_term_output['term_id'];
					//print_r($current_object[$j]['children']);
					if($input_format=="JSON")
					$current_object[$j]['children'] = get_object_vars($current_object[$j]['children']);
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
?>