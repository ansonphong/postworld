<?php

function i_get_postmeta_model(){
	$iMeta_defaults = apply_filters( 'i_postmeta_model', array() );
	return $iMeta_defaults;
}

function i_get_postmeta( $post_id, $meta_key = "i_meta", $type = "json" ){

	///// CACHING LAYER /////
	// Make a global to cache data at runtime
	// To prevent making multiple queries on the same postmeta
	global $i_postmeta_cache;

	// If cached data is already found, return it instantly
	if( isset( $i_postmeta_cache[$post_id][$meta_key] ) )
		return $i_postmeta_cache[$post_id][$meta_key];


	///// GET POST META /////
	// Get Post Meta
	$metadata = get_post_meta( $post_id, $meta_key, true );

	// Convert from JSON to A_ARRAY
	if( $type == "json" )
		$metadata = json_decode( $metadata, true );


	///// I_META /////
	// If this is the default i_meta object handle it specially
	if( $meta_key == "i_meta" ){
		// Run filter, so other functions can change the default model
		$iMeta_defaults = i_get_postmeta_model();

		// If there is no saved postmeta	
		if( empty( $metadata ) ){
			// Set Defaults
			$metadata = $iMeta_defaults;
		// If loaded saved postmeta
		} else {
			// Merge Saved Settings with Model
			$metadata = array_replace_recursive( $iMeta_defaults, $metadata );
		
		}
	}
	

	///// CACHING LAYER /////
	// Store meta data in a runtime cache
	if( !isset( $i_postmeta_cache[$post_id] ) ) 
		$i_postmeta_cache[$post_id] = array();
	$i_postmeta_cache[$post_id][$meta_key] = $i_meta;

	return $metadata;

}

?>