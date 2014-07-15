<?php

///// POSTORLD SLIDER /////

function pw_gallery_shortcode( $atts, $content = null, $tag ) {

	// Extract Shortcode Attributes, set defaults
	extract( shortcode_atts( array(
		"template"		=> "gallery-inline",
		"id" 			=> hash( "md5", "1" ),
		"ids"			=> "", // The IDs of the attachments
		"class" 		=> "gallery-slider",
	), $atts ) );

	// Setup Feed Query
	$gallery_args = array(
		"template"	=>	$atts['template'],
		"id"		=>	$atts['id'],
		"ids"		=>	$atts['ids'],
		"class"		=>	$atts['class'],
		);

	$shortcode = pw_print_gallery( $gallery_args );	

	return $shortcode;

}

function pw_print_gallery( $gallery ){

	///// Setup /////
	// Localize Variables

	///// Set Defaults /////
	$default_template = "gallery-inline";

	// Re-iterate defaults incase the function is called outside a shortcode
	$gallery_defaults = array(
		"template"	=>	$gallery['template'],
		"id"		=>	$gallery['id'],
		"ids"		=>	$gallery['ids'],
		"class"		=>	$gallery['class'],
		);

	$gallery = pw_set_defaults( $gallery, $gallery_defaults ); 


	///// TEMPLATES ////
	$template_id = $gallery['template'];
	$templates = pw_get_templates(
		array(
			'subdirs' => array('galleries'),
			'path_type' => 'dir',
			'ext'=>'php',
			)
		);

	// Set the Template ID
	$template_id = $gallery['template'];

	// Get the template file path
	$template = ( isset( $templates['galleries'][$template_id] ) ) ?
		$templates['galleries'][$template_id] :
		$templates['galleries'][$default_template];



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
	$query['fields'] = array(
		'ID',
		'post_title',
		'post_excerpt',
		'post_type',
		'post_parent',
		'post_permalink',
		'post_excerpt',
		'image(all)',
		'image(stats)',
		'image(tags)',
		);

	///// RUN QUERY /////
	// Get Post Data for Attachments
	$gallery['posts'] = pw_get_posts( $gallery_ids, $query['fields'] );

	///// INSTANCE /////
	// Generate random ID for the Instance
	$random_hash = hash('md5', json_encode( $query . rand ( 1, 99*99 ) ));
	$gallery['instance'] = substr( $random_hash, 1, 8 );

	///// INCLUDE TEMPLATE /////
	// Include the template
	ob_start();
	include $template;
	$content = ob_get_contents();
	ob_end_clean();

	//$content = "GALLERY TEMPLATE : " . json_encode( $templates ) . " // TEMPLATE : " . json_encode( $template );

	// Return with everything in a string
	return $content;
	
}


?>