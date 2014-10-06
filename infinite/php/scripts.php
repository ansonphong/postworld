<?php
add_action( 'wp_enqueue_scripts', 'i_include_scripts' );
add_action( 'admin_enqueue_scripts', 'i_include_scripts' );
add_action( 'admin_enqueue_scripts', 'i_include_admin_scripts' );


function i_include_admin_scripts(){
	global $angularDep;
	// CONTROLLERS : ADMIN
	wp_enqueue_script('Infinite-Admin-Options', get_infinite_directory_uri().'/js/controllers-admin/options.js', $angularDep );
	wp_enqueue_script('Infinite-Admin-Layouts', get_infinite_directory_uri().'/js/controllers-admin/layouts.js', $angularDep );
	wp_enqueue_script('Infinite-Admin-Styles', get_infinite_directory_uri().'/js/controllers-admin/styles.js', $angularDep );
	wp_enqueue_script('Infinite-Admin-Sidebars', get_infinite_directory_uri().'/js/controllers-admin/sidebars.js', $angularDep );
	
	// DIRECTIVES : ADMIN
	wp_enqueue_script('Infinite-Save-Options', get_infinite_directory_uri().'/js/directives-admin/iSaveOption.js', $angularDep );
	
	// SERVICES : ADMIN
	wp_enqueue_script('Infinite-Admin-Options-Data', get_infinite_directory_uri().'/js/services-admin/iOptionsData.js', $angularDep );

}

function i_include_scripts(){

	///// JQUERY /////
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');

	// UNDERSCORE JS
	wp_enqueue_script('Underscore-JS', get_infinite_directory_uri().'/packages/underscore.js/underscore.min.js' );

	// ANGULAR JS
	//wp_enqueue_script('AngularJS', get_infinite_directory_uri().'/packages/AngularJS/angular.min.js', 'jquery' );

	// Already included in POSTWORLD

	// ANGULAR EXTENSIONS
	//wp_enqueue_script('AngularJS-Resource', get_template_directory_uri().'/packages/AngularJS/angular-resource.min.js', 'AngularJS' );
	//wp_enqueue_script('AngularJS-Sanitize', get_template_directory_uri().'/packages/AngularJS/angular-sanitize.min.js', 'AngularJS' );
	
	// ANGULAR DEPENDENCIES
	$angularDep = array(); //, 'Angular-JS' 'Infinite-App'

	// APP
	wp_enqueue_script('Infinite-App', get_infinite_directory_uri().'/js/app.js', $angularDep );

	// SERVICES
	wp_enqueue_script('Infinite-iData', get_infinite_directory_uri().'/js/services/iData.js', $angularDep );
	wp_enqueue_script('Infinite-Admin-Options-Data', get_infinite_directory_uri().'/js/services-admin/iOptionsData.js', $angularDep );

	// DIRECTIVES
	wp_enqueue_script('Infinite-Directives', get_infinite_directory_uri().'/js/directives/iDirectives.js', $angularDep );
	

	// CHILD THEME SCRIPTS
	wp_enqueue_script( 'Custom-Scripts', get_infinite_directory_uri() . '/js/scripts.js' );

}


?>