<?php

function pw_available_modules(){

	$modules = array(

		array(
			'name'			=>	'Site Options',
			'slug'			=>	'site',
			'icon'			=>	'icon-globe',
			'description'	=>	'',
			),

		array(
			'name'			=>	'Layouts',
			'slug'			=>	'layouts',
			'icon'			=>	'icon-th-large',
			'description'	=>	'',
			),

		array(
			'name'	=>	'Sidebars',
			'slug'	=>	'sidebars',
			'icon'	=>	'icon-map',
			'description'	=>	'',
			),

		array(
			'name'			=>	'Styles',
			'slug'			=>	'styles',
			'icon'			=>	'icon-brush',
			'description'	=>	'',
			),
		
		array(
			'name'	=>	'Social',
			'slug'	=>	'social',
			'icon'	=>	'icon-heart',
			'description'	=>	'',
			),

		array(
			'name'	=>	'Feeds',
			'slug'	=>	'feeds',
			'icon'	=>	'icon-file',
			'description'	=>	'',
			),

		array(
			'name'	=>	'Backgrounds',
			'slug'	=>	'backgrounds',
			'icon'	=>	'icon-paint-format',
			'description'	=>	'',
			),

		);

	// Apply filters so themes can override / add new modules
	//$modules = apply_filters( PW_MODULES, $modules );	

	return $modules;
}

function pw_enabled_modules( $enabled_modules = array() ){
	global $pwSiteGlobals;

	if( empty($enabled_modules) )
		// Get the saved enabled modules array
		// Filter must be set to false otherwise it triggers infinite recursion
		$enabled_modules = pw_get_option( array( 'option_name' => PW_OPTIONS_MODULES, 'filter' => false ) );
	
	// If the modules option hasn't been saved yet
	if( !get_option( PW_OPTIONS_MODULES ) ){
		// Check the Postworld Config for default modules
		$default_modules = _get( $pwSiteGlobals, 'modules' );
		// If there are no modules set in the Postworld Config
		if( !$default_modules )
			// Configure the default Postworld modules
			$default_modules = array(
				'layouts',
				'sidebars',
				);
		$enabled_modules = $default_modules;
	}
	
	return $enabled_modules;

}

add_filter( PW_OPTIONS_MODULES, 'pw_enabled_modules' );


function pw_set_modules(){


}


?>