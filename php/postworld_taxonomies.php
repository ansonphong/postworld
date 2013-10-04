<?php


function taxonomies_outline( $taxonomies, $depth = 2 ){

	// If Taxonomies is not defined
	// Get all Public Taxonomies
	if(!$taxonomies || $taxonomies == 'all'){
		// QUERY TAXONOMIES
		$taxonomy_args = array('public'   => true ); 
        $taxonomies = get_taxonomies($taxonomy_args, 'names');
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

		///// TERMS : Cycle through each Term /////
		foreach ( $tax_terms as $term => $value ){

			// Setup Terms Array
			$term_obj = array();
			// If Term has no parent
			if ( $value->parent == '0' ){
				// Get Term Values
				$term_obj['name'] = $value->name;
				$term_obj['slug'] = $value->slug;
				$term_obj['description'] = $value->description;
				$term_obj['url'] = get_term_link( $value );
				
				///// CHILD TERMS : Cycle through each Term searching for children /////
				if ($depth > 1){
					// Setup Child Terms Array
					$child_terms = array();
					///// CHILDREN : Cycle through to find it's Children /////
					foreach( $tax_terms as $child_term => $child_value ){
						
						// If child has set parent as this term_id
						if ($child_value->parent == $value->term_id){
							// Get Child Term Values
							$child_term_obj['name'] = $child_value->name;
							$child_term_obj['slug'] = $child_value->slug;
							$child_term_obj['description'] = $child_value->description;
							$child_term_obj['url'] = get_term_link( $child_value );
							array_push( $child_terms, $child_term_obj);
						}
					}

					// If there were Child terms, add them to 'terms' Array
					if(!empty($child_terms)){
						$term_obj['terms'] = $child_terms;
					}
				}

				// Push Taxonomy Array to Main Array
				array_push( $tax_outline[$taxonomy]['terms'], $term_obj);

			}
		}

	}

	return $tax_outline;

}


?>