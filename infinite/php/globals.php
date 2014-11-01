<?php


function iGlobals(){
	global $post;

	// Import Options
	// TODO : Use i_get_option() function
	$i_options = pw_get_option( array( 'option_name' => 'i-options' ) );
	$i_sidebars = pw_get_option( array( 'option_name' => PW_OPTIONS_SIDEBARS ) );
	$i_social = pw_get_option( array( 'option_name' => PW_OPTIONS_SOCIAL ) );

	// Layout
	$layout = pw_get_current_layout();

	///// WRAP /////
	////////// DEFINE GLOBALS //////////
	global $iGlobals;
	$iGlobals = array(
		"options" 	=> $i_options,
		"layouts" 	=> $i_layouts,
		"layout"	=> $layout,
		//"context"	=> $context,
		"sidebars"	=> $i_sidebars,
		"social"	=> $i_social,
		);

	return $iGlobals;

}
?>