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


////////// THEME OPTIONS //////////
global $theme_admin;
global $pw;

function postworld_admin_menu(){
	global $pw;	
	$enabled_modules = pw_enabled_modules();
	$pw_slug = $pw['info']['slug'];

	$menu = array(
		'menu' => array(
			'page_title' => 'Postworld',
			'menu_title' => 'Postworld',
			'capability' => 'manage_options',
			'menu_slug' => $pw_slug,
			'function' => 'postworld_postworld_modules',
			//'icon_url' => '',//plugins_url( $migration_admin_folder.'/images/logo/pw_symbol-16.png' ),
			'menu_icon'	=>	'dashicons-art',
			'position' => ''
			),

		'submenu' => array(),
		);

	if( in_array( 'site', $enabled_modules ) )
		$menu['submenu']['site'] = array(
			'parent_slug' => $pw_slug,
			'page_title' => 'Site Options',
			'menu_title' => 'Site Options',
			'capability' => 'manage_options',
			'menu_slug' => $pw_slug.'-site',
			'function' => 'postworld_options_site',
			);

	if( in_array( 'layouts', $enabled_modules ) )
		$menu['submenu']['layout'] = array(
			'parent_slug' => $pw_slug,
			'page_title' => 'Layout',
			'menu_title' => 'Layout',
			'capability' => 'manage_options',
			'menu_slug' => $pw_slug.'-layout',
			'function' => 'postworld_options_layout',
			);

	if( in_array( 'sidebars', $enabled_modules ) )
		$menu['submenu']['sidebars'] = array(
			'parent_slug' => $pw_slug,
			'page_title' => 'Sidebars',
			'menu_title' => 'Sidebars',
			'capability' => 'manage_options',
			'menu_slug' => $pw_slug.'-sidebars',
			'function' => 'postworld_options_sidebars',
			);

	if( in_array( 'styles', $enabled_modules ) )
		$menu['submenu']['styles'] = array(
			'parent_slug' => $pw_slug,
			'page_title' => 'Styles',
			'menu_title' => 'Styles',
			'capability' => 'manage_options',
			'menu_slug' => $pw_slug.'-styles',
			'function' => 'postworld_options_styles',
			);

	if( in_array( 'social', $enabled_modules ) )
		$menu['submenu']['social'] = array(
			'parent_slug' => $pw_slug,
			'page_title' => 'Social',
			'menu_title' => 'Social',
			'capability' => 'manage_options',
			'menu_slug' => $pw_slug.'-social',
			'function' => 'postworld_options_social',
			);

	if( in_array( 'feeds', $enabled_modules ) )
		$menu['submenu']['feeds'] = array(
			'parent_slug' => $pw_slug,
			'page_title' => 'Feeds',
			'menu_title' => 'Feeds',
			'capability' => 'manage_options',
			'menu_slug' => $pw_slug.'-feeds',
			'function' => 'postworld_options_feeds',
			);

	/*
	if( in_array( 'term-feeds', $enabled_modules ) )
		$menu['submenu']['term-feeds'] = array(
			'parent_slug' => $pw_slug,
			'page_title' => 'Term Feeds',
			'menu_title' => 'Term Feeds',
			'capability' => 'manage_options',
			'menu_slug' => $pw_slug.'-term-feeds',
			'function' => 'postworld_options_term_feeds',
			);
	*/

	if( in_array( 'backgrounds', $enabled_modules ) )
		$menu['submenu']['backgrounds'] = array(
			'parent_slug' => $pw_slug,
			'page_title' => 'Backgrounds',
			'menu_title' => 'Backgrounds',
			'capability' => 'manage_options',
			'menu_slug' => $pw_slug.'-backgrounds',
			'function' => 'postworld_options_backgrounds',
			);

	//echo json_encode($pw['modules']);

	///// APPLY FILTERS /////
	// Allow themes to add sub menus
	$menu['submenu'] = apply_filters( 'pw_admin_submenu', $menu['submenu'] );	

	return $menu;

}

///// ADD ADMIN MENU PAGE /////
add_action( 'admin_menu', 'theme_admin_menu', 8 );
function theme_admin_menu(){

	$admin = postworld_admin_menu();
	
	///// MAIN MENU /////
	// http://codex.wordpress.org/Function_Reference/add_menu_page
    add_menu_page(
    	$admin['menu']['page_title'],
    	$admin['menu']['menu_title'],
    	$admin['menu']['capability'],
    	$admin['menu']['menu_slug'],
    	$admin['menu']['function'],
    	$admin['menu']['menu_icon']
    	);

    ///// SUB MENUS /////
    // http://codex.wordpress.org/Function_Reference/add_submenu_page
    foreach( $admin['submenu'] as $key => $value ){
    	add_submenu_page(
	    	$value['parent_slug'],
	    	$value['page_title'],
	    	$value['menu_title'],
	    	$value['capability'],
	    	$value['menu_slug'],
	    	$value['function']
	    	);
    }

}


///// REGISTER STYLES /////
//wp_register_style( 'pw_admin_css', plugins_url() . '/postworld/admin/css/pw-admin.css' );



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