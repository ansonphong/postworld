<?php
////////// INFINITE INCLUDES //////////
include 'i_language.php';
include 'admin-modules.php';
include 'admin-panel.php';
include 'functions-sidebars.php';
include 'functions-styles.php';
include 'functions-layouts.php';
include 'functions-ajax.php';
include 'style-model.php';
include 'social-model.php';

////////// POSTWORLD //////////
/*
if( function_exists('postworld_includes') ){
	postworld_includes(array(
		"mode"	=>	"deploy",
		//"dep"	=>	array("create.js")
		));
}
*/

////////// THEME OPTIONS //////////
global $theme_admin;

global $theme_admin_menu_name;
$theme_admin_menu_name = 'theme-options';

$theme_admin = array(
	'main' => array(
		'page_title' => 'Theme Options',
		'menu_title' => 'Theme Options',
		'capability' => 'manage_options',
		'menu_slug' => $theme_admin_menu_name,
		'function' => 'infinite_options_main',
		'icon_url' => '',//plugins_url( $migration_admin_folder.'/images/logo/pw_symbol-16.png' ),
		'position' => ''
		),

	'layout' => array(
		'parent_slug' => $theme_admin_menu_name,
		'page_title' => 'Layout',
		'menu_title' => 'Layout',
		'capability' => 'manage_options',
		'menu_slug' => $theme_admin_menu_name.'-layout',
		'function' => 'infinite_options_layout',
		),

	'sidebars' => array(
		'parent_slug' => $theme_admin_menu_name,
		'page_title' => 'Sidebars',
		'menu_title' => 'Sidebars',
		'capability' => 'manage_options',
		'menu_slug' => $theme_admin_menu_name.'-sidebars',
		'function' => 'infinite_options_sidebars',
		),

	'styles' => array(
		'parent_slug' => $theme_admin_menu_name,
		'page_title' => 'Styles',
		'menu_title' => 'Styles',
		'capability' => 'manage_options',
		'menu_slug' => $theme_admin_menu_name.'-styles',
		'function' => 'infinite_options_styles',
		),

	'social' => array(
		'parent_slug' => $theme_admin_menu_name,
		'page_title' => 'Social',
		'menu_title' => 'Social',
		'capability' => 'manage_options',
		'menu_slug' => $theme_admin_menu_name.'-social',
		'function' => 'infinite_options_social',
		),
	
	);

///// ADD ADMIN MENU PAGE /////
add_action( 'admin_menu', 'theme_admin_menu', 8 );
function theme_admin_menu(){
	global $theme_admin;
	//$page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position
	
    add_menu_page(
    	$theme_admin['main']['page_title'],
    	$theme_admin['main']['menu_title'],
    	$theme_admin['main']['capability'],
    	$theme_admin['main']['menu_slug'],
    	$theme_admin['main']['function'],
    	$theme_admin['main']['icon_url']
    	);

    add_submenu_page(
    	$theme_admin['layout']['parent_slug'],
    	$theme_admin['layout']['page_title'],
    	$theme_admin['layout']['menu_title'],
    	$theme_admin['layout']['capability'],
    	$theme_admin['layout']['menu_slug'],
    	$theme_admin['layout']['function']
    	);

    add_submenu_page(
    	$theme_admin['sidebars']['parent_slug'],
    	$theme_admin['sidebars']['page_title'],
    	$theme_admin['sidebars']['menu_title'],
    	$theme_admin['sidebars']['capability'],
    	$theme_admin['sidebars']['menu_slug'],
    	$theme_admin['sidebars']['function']
    	);

    add_submenu_page(
    	$theme_admin['styles']['parent_slug'],
    	$theme_admin['styles']['page_title'],
    	$theme_admin['styles']['menu_title'],
    	$theme_admin['styles']['capability'],
    	$theme_admin['styles']['menu_slug'],
    	$theme_admin['styles']['function']
    	);

    add_submenu_page(
    	$theme_admin['social']['parent_slug'],
    	$theme_admin['social']['page_title'],
    	$theme_admin['social']['menu_title'],
    	$theme_admin['social']['capability'],
    	$theme_admin['social']['menu_slug'],
    	$theme_admin['social']['function']
    	);

    //call register settings function
	//add_action( 'admin_init', 'register_theme_settings' );

}


///// REGISTER STYLES /////
//wp_register_style( 'pw_admin_css', plugins_url() . '/postworld/admin/css/pw-admin.css' );






////////// OPTIONS //////////
global $iAdmin;

///// PARSE OPTIONS /////
// LAYOUT OPTIONS : including 'default'
//$layout_options_default = $i_options["layout"]["options"];
//array_push( $layout_options_default, $i_options["layout"]["default"] );

///// OPTIONS ARRAY /////
$iAdmin = array(
	"layouts"	=>	array(
		array(
			"label"	=>	"Layouts",
			"name"	=>	"i-layouts",
			"icon"	=>	"icon-circle-medium",
			),
		),

	"sidebars"	=>	array(
		array(
			"label"	=>	"Sidebars",
			"name"	=>	"i-sidebars",
			"icon"	=>	"icon-circle-medium",
			),
		),

	"styles"	=>	array(
		array(
			"label"	=>	"Styles",
			"name"	=>	"i-styles",
			"icon"	=>	"icon-image",
			),
		),

	);


///// ADMIN URLS /////
global $i_admin_urls;
$i_admin_urls = array(
	'sidebars'	=>	add_query_arg( array('page' => $theme_admin['sidebars']['menu_slug']), admin_url("admin.php") ),
	);


///// STRUCTURE OF LAYOUT MODEL /////
$i_layouts_model = array(

	"default"	=>	array(
		"layout"	=>	"left-right",
		"sidebars"	=> array(
			"left"		=>	array(
				"id" =>		"sidebar-1",
				"width"	=>	array(
					"xs"	=>	12,
					"sm"	=>	12,
					"md"	=>	3,
					"lg"	=>	4
					),
				),
			"right"		=>	array(
				//...
				),
			),
		"footer"	=>	array(
			"show"	=>	true,
			"template"	=>	"footer1"
			),
		),
	"blog"	=>	array(
		//...
		),

	);



///// ADMIN SCRIPTS /////
// Scripts which are inserted into the header in the admin
// These contain globals accessible by the JS window object

function i_admin_scripts(){
  $iGlobals = array(
  	"paths"	=>	array(
  		"ajax_url" => admin_url( 'admin-ajax.php' ),
  		),
  	);
  ?>
	<script  type="text/javascript">
	//<![CDATA[
		var iGlobals = <?php echo json_encode($iGlobals); ?>;
	//]]>
	</script>
  <?php
}

?>