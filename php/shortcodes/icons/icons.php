<?php

///// SHORTCODE /////
function pw_icons_shortcode( $atts, $content = null, $tag ) {
	extract( shortcode_atts( array(
		'icon' => '',
		'class' => '',
		'color' => '',
		'size' => '',
	), $atts ) );
	
	// Start Output Buffering
	ob_start();
	// Get the template
	include pw_get_shortcode_template( "icons" );
	// Set included file into a string/variable
	$shortcode = ob_get_contents();
	// End Output Buffering
	ob_end_clean();

	// Remove any line breaks
	$shortcode = str_replace(array("\r", "\n", "\t"), "", $shortcode);

	// Return template
	return do_shortcode($shortcode);

}



//////////// ICONS : LINGEAGE CODE //////////

/*
///// GET ICONS /////
function pw_get_css_icons(
	$icon_prefix = "icon-",
	$css_file = "/lib/icomoon/style.css",
	$return = "all",
	$output = "array"
	){
	
	// Returns an Array of all the icon css classes in a given css file
	 

	$pattern = '/\.('.$icon_prefix.'(?:\w+(?:-)?)+):before\s+{\s*content:\s*"(.+)";\s+}/';
	$subject = file_get_contents( $css_file );

	preg_match_all( $pattern, $subject, $matches, PREG_SET_ORDER );

	$icons = array();

	foreach($matches as $match){
	    $icons[$match[1]] = $match[2];
	}

	if( $return == 'keys' ){
		$icon_keys = array();

		foreach( $icons as $key => $value ){
			$icon_keys[] = $key;
		}

		$icons = $icon_keys;
	}
	
	if( $output == "string" ){
		// Enable this to generate string for icon Array
		$icons = var_export($icons, TRUE);
		$icons = stripslashes($icons);
	}
	

	return $icons;
	
}
*/



//add_shortcode( 'icon', 'pw_icons_shortcode' );

/*
function pw_shortcode_load_iconset( $all_icons, $iconset ){
	// Define the path to the iconset
	$iconset_file = plugin_dir_path(__FILE__) . "iconset-".$iconset.".php";
	// Return if icon set doesn't exist
	if( !file_exists( $iconset_file ) )
		return $all_icons;
	// Include the file with the icons
	include $iconset_file;
	// Merge them
	$all_icons = array_merge( $all_icons, $icons );
	return $all_icons;
}

global $pw_shortcode_icons;
$pw_shortcode_icons = array();

///// LOAD ICON SETS /////
//$pw_shortcode_icons = pw_shortcode_load_iconset( $pw_shortcode_icons, 'font-awesome-3' );
$pw_shortcode_icons = pw_shortcode_load_iconset( $pw_shortcode_icons, 'icomoon' );

function pw_get_shortcode_icons( ){
	global $pw_shortcode_icons;
	return $pw_shortcode_icons;
}
*/

// TODO : find a way to do this conditionally based on injections
//if( in_array( 'font-awesome-3', pw_injections() ) ){
//}

/*
///// LOAD CUSTOM ICONS /////
$pw_shortcode_custom_icons = (
	isset( $pwSiteGlobals['icons']['shortcodes'] ) &&
	is_array( $pwSiteGlobals['icons']['shortcodes'] )
	) ? 
	$pwSiteGlobals['icons']['shortcodes'] : array();

//global $pwSiteGlobals;
//echo json_encode( isset( $pwSiteGlobals['icons']['shortcodes'] ) );

///// ADD SHORTCODES /////
foreach( $pw_shortcode_icons as $icon ){
	add_shortcode( $icon, 'pw_icons_shortcode' );
}
*/


?>