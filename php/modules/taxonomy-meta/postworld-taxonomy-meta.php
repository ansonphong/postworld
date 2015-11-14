<?php
////////// INIT TAXONOMY METADATA //////////
// Load the is_plugin_active() function
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
// If the Postworld taxonomy-meta module is enabled
if( pw_module_is_enabled('taxonomy-meta') &&
	// And the Taxonomy Metadata class doesn't exist
	!class_exists('Taxonomy_Metadata') &&
	// And the Taxonomy Metadata plugin isn't activated
	!is_plugin_active( 'taxonomy-metadata/taxonomy-metadata.php' ) ){

	// Include the Taxonomy Metadata core
	include "taxonomy-metadata.php";

	///// CHECK IF TABLE EXISTS /////
	global $wpdb;
	$tables = $wpdb->get_results("show tables like '{$wpdb->prefix}taxonomymeta'");
	// If table doesn't exist
	if (!count($tables)){
		// Create the table
	    postworld_activate_taxonomy_meta();
	}
}


///// ACTIVATE THEME /////
// When switching to the theme with Postworld activated
add_action("after_switch_theme", 'postworld_activate_taxonomy_meta');
// Create the database tables 
function postworld_activate_taxonomy_meta(){
	if( class_exists('Taxonomy_Metadata') ){
		$taxonomy_metadata = new Taxonomy_Metadata;
		$taxonomy_metadata->activate();
	}
}

// Add Core supported input types
add_filter( 'pw_admin_taxonomy_meta_input_types', 'pw_admin_core_taxonomy_meta_input_types' );
function pw_admin_core_taxonomy_meta_input_types( $types ){
	$types = array_merge( $types, array(
		'icon',
		'image-id',
		'editor',
		'select',
		'text',
		'number'
		)
	);
	return $types;
}

function pw_init_module_taxonomy_meta(){
	// Initialize Taxonomy Meta module

	// If the module isn't enabled, return here
	if( !pw_module_is_enabled('taxonomy-meta') )
		return false;

	// Get the instances of taxonomy metadata in the admin
	global $pwSiteGlobals;
	$instances = _get( $pwSiteGlobals, 'wp_admin.taxonomy_meta' );
	// If no instances defined, return here
	if( empty($instances) )
		return false;

	////////// INSTANCES //////////
	// Iterate through each of the instances
	foreach( $instances as $instance ){
		// Get the taxonomies key
		$taxonomies = _get( $instance, 'taxonomies' );
		// If taxonomies or fields aren't arrays, return here
		// TODO : Add support for 'all' string value
		if( !is_array( $taxonomies ) )
			continue;
		////////// TAXONOMIES //////////
		foreach( $taxonomies as $taxonomy ){
			// Call the processor on each of the taxonomy edit form fields
			// TODO : Have option also to add fields to new term input
			// Insert the form
			add_action( $taxonomy.'_edit_form_fields', 'pw_admin_taxonomy_meta_fields', 10, 2);
			// Save the data
			add_action( 'edited_'.$taxonomy, 'pw_admin_taxonomy_meta_save_fields', 10, 2);
		}
	}


}
add_action( 'init', 'pw_init_module_taxonomy_meta' );


function pw_admin_taxonomy_meta_fields( $tag, $taxonomy ){
	// Add the supported meta-fields to taxonomy admin meta editing screens

	//pw_log('pw_admin_taxonomy_meta_fields : ' . json_encode($tag) . ' : ' . json_encode($taxonomy) );

	// Get the instances of taxonomy metadata in the admin
	global $pwSiteGlobals;
	$instances = _get( $pwSiteGlobals, 'wp_admin.taxonomy_meta' );

	// Allow themes to add support for input types
	$input_types = apply_filters( 'pw_admin_taxonomy_meta_input_types', array() );

	////////// INSTANCES //////////
	foreach( $instances as $instance ){

		// If the current taxonomy isn't in the array, continue
		if( !in_array( $taxonomy, $instance['taxonomies'] ) )
			continue;

		// Get the fields array
		$fields = _get( $instance, 'fields' );
		// If no fields or it's not an array, continue
		if( $fields == false || !is_array( $fields ) )
			continue;

		////////// FIELDS //////////
		foreach( $fields as $field ){
	
			// If the field type isn't supported, continue
			if( !in_array( $field['type'], $input_types ) )
				continue;

			// Load in the meta value
			$field['meta_value'] = get_term_meta( $tag->term_id, $field['meta_key'], true );

			// Setup variables to send to template
			$vars = array(
				'taxonomy'		=> $taxonomy,
				'term'			=> $tag,
				'field'			=> $field,
				'input_name'	=> 'pw_taxonomy_meta['.$field['meta_key'].']',
				);

			//pw_log( $vars );
			// Include the template
			echo pw_ob_admin_template( 'taxonomy-meta-input-'.$field['type'], $vars );

		}

	}

}

function pw_admin_taxonomy_meta_save_fields( $term_id ){
	// Save the taxonomy meta data into the database

	// Get the posted data
	$term_meta = _get($_POST,'pw_taxonomy_meta');
	//pw_log( 'term meta submission', $term_meta );

	// If it's not an array, return here
	if( !is_array($term_meta) )
		return false;

	// Save each field
	foreach( $term_meta as $meta_key => $meta_value){
		if( !empty($meta_value) )
			update_term_meta( $term_id, $meta_key, $meta_value );
		else
			delete_term_meta( $term_id, $meta_key );
	}

}


?>