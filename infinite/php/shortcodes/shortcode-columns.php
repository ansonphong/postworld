<?php

////////// CSS COLUMN PROPERTY SHORTCODE //////////

///// COLUMNS /////
function i_css_columns_shortcode( $atts, $content = null, $tag ) {
	
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
	include i_locate_template("/views/shortcodes/columns-css.php");
	// Set included file into a string/variable
	$shortcode = ob_get_contents();
	// End Output Buffering
	ob_end_clean();

	// Return template
	return do_shortcode($shortcode);

}

add_shortcode( '2-columns', 'i_css_columns_shortcode' );
add_shortcode( '3-columns', 'i_css_columns_shortcode' );
add_shortcode( '4-columns', 'i_css_columns_shortcode' );


////////// COLUMNS SHORTCODE //////////

///// COLUMNS /////
function i_columns_row_shortcode( $atts, $content = null, $tag ) {
	
	// Extract Shortcode Attributes
	extract( shortcode_atts( array(
		"class" => "",
	), $atts ) );

	// Remove <BR> tags from beginning of content
	$content = preg_replace('/^(?:<br\s*\/?>\s*)+/', '', $content);

	// Start Output Buffering
	ob_start();
	// Get the template
	include i_locate_template("/views/shortcodes/columns-row.php");
	// Set included file into a string/variable
	$shortcode = ob_get_contents();
	// End Output Buffering
	ob_end_clean();

	// Return template
	return do_shortcode($shortcode);

}

add_shortcode( 'columns', 'i_columns_row_shortcode' );

///// COLUMNS /////
function i_columns_column_shortcode( $atts, $content = null, $tag ) {
	
	// Extract Shortcode Attributes
	extract( shortcode_atts( array(
		"class" => "",
	), $atts ) );

	// Remove <BR> tags from beginning of content
	$content = preg_replace('/^(?:<br\s*\/?>\s*)+/', '', $content);

	// Start Output Buffering
	ob_start();
	// Get the template
	include i_locate_template("/views/shortcodes/columns-column.php");
	// Set included file into a string/variable
	$shortcode = ob_get_contents();
	// End Output Buffering
	ob_end_clean();

	// Return template
	return do_shortcode($shortcode);

}

add_shortcode( 'column-half', 'i_columns_column_shortcode' );
add_shortcode( 'column-third', 'i_columns_column_shortcode' );
add_shortcode( 'column-quarter', 'i_columns_column_shortcode' );

?>