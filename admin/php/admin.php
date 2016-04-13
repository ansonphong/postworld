<?php
include 'admin-menu.php';
include 'admin-modules.php';
include 'admin-filters.php';
include 'admin-panel.php';
include 'functions-sidebars.php';
include 'functions-styles.php';
include 'functions-layouts.php';
include 'functions-ajax.php';
include 'social-model.php';

global $theme_admin;
global $pw;

function postworld_admin_menu(){
	global $pw;	
	$enabled_modules = pw_enabled_modules();
	$pw_slug = $pw['info']['slug'];
	$submenu_slug = pw_admin_submenu_slug();

	$menu = array(
		'menu' => array(
			'page_title' => 'Postworld',
			'menu_title' => 'Postworld',
			'capability' => 'manage_options',
			'menu_slug' => $pw_slug,
			'function' => 'postworld_postworld_modules',
			//'icon_url' => '',//plugins_url( $migration_admin_folder.'/images/logo/pw_symbol-16.png' ),
			'menu_icon'	=>	'dashicons-admin-generic',
			'position' => ''
			),
		'submenu' => array(),
		);

	if( in_array( 'site', $enabled_modules ) )
		$menu['submenu']['site'] = array(
			'parent_slug' => $submenu_slug,
			'page_title' => _x('Site Options','module','postworld'),
			'menu_title' => _x('Site Options','module','postworld'),
			'capability' => 'manage_options',
			'menu_slug' => $submenu_slug.'-site',
			'function' => 'postworld_options_site',
			);

	if( in_array( 'layouts', $enabled_modules ) )
		$menu['submenu']['layout'] = array(
			'parent_slug' => $submenu_slug,
			'page_title' => _x('Layout','module','postworld'),
			'menu_title' => _x('Layout','module','postworld'),
			'capability' => 'manage_options',
			'menu_slug' => $submenu_slug.'-layout',
			'function' => 'postworld_options_layout',
			);

	if( in_array( 'sidebars', $enabled_modules ) )
		$menu['submenu']['sidebars'] = array(
			'parent_slug' => $submenu_slug,
			'page_title' => _x('Sidebars','module','postworld'),
			'menu_title' => _x('Sidebars','module','postworld'),
			'capability' => 'manage_options',
			'menu_slug' => $submenu_slug.'-sidebars',
			'function' => 'postworld_options_sidebars',
			);

	if( in_array( 'styles', $enabled_modules ) )
		$menu['submenu']['styles'] = array(
			'parent_slug' => $submenu_slug,
			'page_title' => _x('Styles','module','postworld'),
			'menu_title' => _x('Styles','module','postworld'),
			'capability' => 'manage_options',
			'menu_slug' => $submenu_slug.'-styles',
			'function' => 'postworld_options_styles',
			);

	if( in_array( 'social', $enabled_modules ) )
		$menu['submenu']['social'] = array(
			'parent_slug' => $submenu_slug,
			'page_title' => _x('Social','module','postworld'),
			'menu_title' => _x('Social','module','postworld'),
			'capability' => 'manage_options',
			'menu_slug' => $submenu_slug.'-social',
			'function' => 'postworld_options_social',
			);

	if( in_array( 'comments', $enabled_modules ) )
		$menu['submenu']['comments'] = array(
			'parent_slug' => $submenu_slug,
			'page_title' => _x('Comments','module','postworld'),
			'menu_title' => _x('Comments','module','postworld'),
			'capability' => 'manage_options',
			'menu_slug' => $submenu_slug.'-comments',
			'function' => 'postworld_options_comments',
			);

	if( in_array( 'feeds', $enabled_modules ) )
		$menu['submenu']['feeds'] = array(
			'parent_slug' => $submenu_slug,
			'page_title' => _x('Feeds','module','postworld'),
			'menu_title' => _x('Feeds','module','postworld'),
			'capability' => 'manage_options',
			'menu_slug' => $submenu_slug.'-feeds',
			'function' => 'postworld_options_feeds',
			);

	if( in_array( 'backgrounds', $enabled_modules ) )
		$menu['submenu']['backgrounds'] = array(
			'parent_slug' => $submenu_slug,
			'page_title' => _x('Backgrounds','module','postworld'),
			'menu_title' => _x('Backgrounds','module','postworld'),
			'capability' => 'manage_options',
			'menu_slug' => $submenu_slug.'-backgrounds',
			'function' => 'postworld_options_backgrounds',
			);

	if( in_array( 'iconsets', $enabled_modules ) )
		$menu['submenu']['iconsets'] = array(
			'parent_slug' => $submenu_slug,
			'page_title' => _x('Iconsets','module','postworld'),
			'menu_title' => _x('Iconsets','module','postworld'),
			'capability' => 'manage_options',
			'menu_slug' => $submenu_slug.'-iconsets',
			'function' => 'postworld_options_iconsets',
			);

	if( in_array( 'shortcodes', $enabled_modules ) )
		$menu['submenu']['shortcodes'] = array(
			'parent_slug' => $submenu_slug,
			'page_title' => _x('Shortcodes','module','postworld'),
			'menu_title' => _x('Shortcodes','module','postworld'),
			'capability' => 'manage_options',
			'menu_slug' => $submenu_slug.'-shortcodes',
			'function' => 'postworld_options_shortcodes',
			);

	$menu['submenu']['database'] = array(
		'parent_slug' => $submenu_slug,
		'page_title' => _x('Database','module','postworld'),
		'menu_title' => _x('Database','module','postworld'),
		'capability' => 'manage_options',
		'menu_slug' => $submenu_slug.'-database',
		'function' => 'postworld_options_database',
		);

	// If a custom slug is defined, nest postworld under it at the bottom
	if( $submenu_slug !== 'postworld' ){
		$menu['menu']['parent_slug'] = $submenu_slug;
		$menu['menu']['menu_slug'] = $submenu_slug.'-postworld';
		$menu['submenu'][] = $menu['menu'];
		$menu['menu'] = array();
	}

	// Apply filters to allow themes to add sub menus
	$menu['submenu'] = apply_filters( 'pw_admin_submenu', $menu['submenu'] );	

	return $menu;

}

/**
 * ADD ADMIN MENU PAGE
 */
add_action( 'admin_menu', 'pw_theme_admin_menu', 10 );
function pw_theme_admin_menu(){
	$admin = postworld_admin_menu();
	
	/**
	 * Main Manu
	 * @link http://codex.wordpress.org/Function_Reference/add_menu_page
	 */
	if( isset( $admin['menu'] ) && !empty( $admin['menu'] ) )
		add_menu_page(
			$admin['menu']['page_title'],
			$admin['menu']['menu_title'],
			$admin['menu']['capability'],
			$admin['menu']['menu_slug'],
			$admin['menu']['function'],
			$admin['menu']['menu_icon']
			);

	/**
	 * Sub Menus
	 * @link http://codex.wordpress.org/Function_Reference/add_submenu_page
	 */
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

/**
 * Admin Styles
 */
add_action('admin_print_styles', 'postworld_admin_icon_styles');
function postworld_admin_icon_styles(){
	?>
	<style>
		#toplevel_page_postworld .dashicons-before:before{
			content: "\e612";
			font-family: "Postworld-Icons"
		}
	</style>
	<?php
}


/**
 * Structure of layout model
 */
/*
$pw_layouts_model = array(
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
		),

	);
*/


?>