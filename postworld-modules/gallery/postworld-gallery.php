<?php

///// POSTORLD SLIDER /////

function pw_gallery_shortcode( $atts, $content = null, $tag ) {

	$shortcode = pw_print_gallery( $atts );	

	return $shortcode;

}

function pw_print_gallery( $gallery ){

	///// Setup /////
	$gallery_defaults = array(
		"template"		=> "gallery-inline",
		"id" 			=> hash( "md5", "1" ),
		"ids"			=> "", // The IDs of the attachments
		"class" 		=> "gallery-slider",
		"columns"		=>	3,
		);

	$gallery = array_replace_recursive( $gallery_defaults, $gallery );

	///// TEMPLATES ////
	$default_template = "gallery-inline";
	$template_id = $gallery['template'];
	$templates = pw_get_templates(
		array(
			'subdirs' => array('galleries'),
			'path_type' => 'dir',
			'ext'=>'php',
			)
		)['galleries'];

	// Set the Template ID
	$template_id = $gallery['template'];

	// Get the template file path
	$template = ( isset( $templates[$template_id] ) ) ?
		$templates[$template_id] :
		$templates[$default_template];

	///// SETUP QUERY /////
	// This will be problematic when doing a feed
	// Pass in the Post ID Elsewhere ??
	global $post;

	// Extract the IDs into an array 
	$gallery_ids = explode( ',', $gallery['ids'] );

	// Convert the IDs from strings to integers
	$ids_integers = array();
	foreach( $gallery_ids as $id ){
		$id = (int) $id;
		array_push( $ids_integers, $id );
	}
	$gallery_ids = $ids_integers;

	// FIELDS
	$query['fields'] = "gallery";

	///// RUN QUERY /////
	// Get Post Data for Attachments
	$gallery['posts'] = pw_get_posts( $gallery_ids, $query['fields'] );

	///// INSTANCE /////
	// Generate random ID for the Instance
	$random_hash = hash('md5', json_encode( $query . rand ( 1, 99*99 ) ));
	$gallery['instance'] = substr( $random_hash, 1, 8 );

	///// INCLUDE TEMPLATE /////
	// Include the template
	$content = pw_ob_include( $template, $gallery );

	// Return with everything in a string
	return $content;
	
}


?>