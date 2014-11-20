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
	$modules = apply_filters( PW_MODULES, $modules );	

	return $modules;
}

function pw_enabled_modules(){
	return 	pw_get_option( array( 'option_name' => PW_OPTIONS_MODULES ) );	
}

function pw_set_modules(){


}


?>