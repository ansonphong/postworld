<?php

function i_social_menu( $vars ){
	extract($vars);

	// Set Default Size
	$size = ( isset($size) ) ?
		$size : 32;

	// Set Default Variant
	$style = ( isset($style) ) ?
		$style : 'default';

	// Set Default Template
	$template = ( isset($template) ) ?
		$template : 'views/networks/social-menu.php';

	// Set Globals
	global $i_social_meta;

	// Get Option Values & decode from JSON
	$i_social = pw_get_option( array( 'option_name'	=> PW_OPTIONS_SOCIAL ));

	// Get Networks Social Meta
	$networks_social_meta = i_find_where( $i_social_meta, array( 'id' => 'networks' ) );
	$networks = $networks_social_meta["fields"];	

	ob_start();
	/////////////////////////////	
		$skip_networks = array( 'facebook_app_id', 'twitter_hashtags' );

		// Iterate through each network
		foreach( $networks as $network ){
			
			// Get the saved value
			$network_value = i_get_obj( $i_social, 'networks.'.$network['id'] );
	
			// If no value set, continue with next network
			if( empty( $network_value ) || in_array( $network["id"], $skip_networks ) )
				continue;
			
			// Generate the link URL
			$network_link = $network['prepend_url'].$network_value;

			// Get Logo Image URL
			$network_image_url = i_image_url('logos/'.$network["id"].'/'.$network["id"].'-'.$style.'-'.$size.'.png');

			// Specify Variables
			$network_id = $network['id'];
			$network_name = $network['name'];

			// Generate HTML from template
			include i_locate_template( $template );


		}

	/////////////////////////////
	$return = ob_get_contents();
	ob_end_clean();
	return $return;
}


?>