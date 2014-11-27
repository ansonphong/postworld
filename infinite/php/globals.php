<?php


function iGlobals(){
	global $post;

	// Import Options
	// TODO : Use i_get_option() function
	//$i_options = pw_get_option( array( 'option_name' => 'i-options' ) );
	$i_sidebars = pw_get_option( array( 'option_name' => PW_OPTIONS_SIDEBARS ) );
	$i_social = pw_get_option( array( 'option_name' => PW_OPTIONS_SOCIAL ) );
	$i_layouts = pw_get_option( array( 'option_name' => PW_OPTIONS_LAYOUTS ) );

	///// WRAP /////
	////////// DEFINE GLOBALS //////////
	global $iGlobals;
	$iGlobals = array(
		//"options" 	=> $i_options,
		"layouts" 	=> $i_layouts,
		//"context"	=> $context,
		"sidebars"	=> $i_sidebars,
		"social"	=> $i_social,
		);

	//echo htmlentities( json_encode( $iGlobals['layout'] ) );

	return $iGlobals;

}
?>