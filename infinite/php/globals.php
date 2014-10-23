<?php


function iGlobals(){
	global $post;

	// Import Options
	// TODO : Use i_get_option() function
	$i_options = json_decode(get_option('i-options'),true);

	$i_sidebars = json_decode(get_option('i-sidebars'),true);
	$i_social = json_decode(get_option('i-social'),true);

	////////// CONTEXT //////////
	$context = array();
	$context['class'] = pw_current_context_class();


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