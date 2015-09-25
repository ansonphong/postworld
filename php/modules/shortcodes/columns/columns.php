<?php
///// COLUMNS /////
function pw_css_columns_shortcode( $atts, $content = null, $tag ) {
	
	// Extract Shortcode Attributes
	extract( shortcode_atts( array(
		"class" => "",
		//"count" => $count,
		//"gap" => "20px",
		//"rule" => "none",
	), $atts ) );

	// Generate column class
	switch($tag){
		case "2-columns":
			$column_count = 2;
			break;
		case "3-columns":
			$column_count = 3;
			break;
		case "4-columns":
			$column_count = 5;
			break;
	}
	$column_class = "columns-".$column_count;

	// Remove <BR> tags from beginning of content
	$content = preg_replace('/^(?:<br\s*\/?>\s*)+/', '', $content);

	// Start Output Buffering
	ob_start();
	// Get the template
	include pw_get_shortcode_template( "columns-css" );
	// Set included file into a string/variable
	$shortcode = ob_get_contents();
	// End Output Buffering
	ob_end_clean();

	// Return template
	return do_shortcode($shortcode);

}

add_shortcode( '2-columns', 'pw_css_columns_shortcode' );
add_shortcode( '3-columns', 'pw_css_columns_shortcode' );
add_shortcode( '4-columns', 'pw_css_columns_shortcode' );


////////// COLUMNS SHORTCODE //////////
///// COLUMNS /////
function pw_columns_row_shortcode( $atts, $content = null, $tag ) {
	
	// Extract Shortcode Attributes
	extract( shortcode_atts( array(
		"class" => "",
	), $atts ) );

	// Remove <BR> tags from beginning of content
	$content = preg_replace('/^(?:<br\s*\/?>\s*)+/', '', $content);

	// Start Output Buffering
	ob_start();
	// Get the template
	include pw_get_shortcode_template( 'columns-row' );
	// Set included file into a string/variable
	$shortcode = ob_get_contents();
	// End Output Buffering
	ob_end_clean();

	// Return template
	return do_shortcode($shortcode);

}

///// COLUMNS /////
function pw_columns_column_shortcode( $atts, $content = null, $tag ) {
	
	// Extract Shortcode Attributes
	extract( shortcode_atts( array(
		"class" => "",
	), $atts ) );

	// Remove <BR> tags from beginning of content
	$content = preg_replace('/^(?:<br\s*\/?>\s*)+/', '', $content);

	// Start Output Buffering
	ob_start();
	// Get the template
	include pw_get_shortcode_template( 'columns-column' ); 
	// Set included file into a string/variable
	$shortcode = ob_get_contents();
	// End Output Buffering
	ob_end_clean();

	// Return template
	return do_shortcode($shortcode);

}

add_shortcode( 'columns', 'pw_columns_row_shortcode' );

add_shortcode( 'column-half', 'pw_columns_column_shortcode' );
add_shortcode( 'column-third', 'pw_columns_column_shortcode' );
add_shortcode( 'column-quarter', 'pw_columns_column_shortcode' );

?>