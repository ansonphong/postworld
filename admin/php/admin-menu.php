<?php
/**
 * Add item to admin menu bar on the top.
 */
global $pw;
if( _get( $pw['config'], 'wp_admin.admin_bar_menu.enable' ) &&
	current_user_can('manage_options') )
	add_action( 'admin_bar_menu', 'pw_admin_bar_menu', 999 );

function pw_admin_bar_menu_item_meta( $id ){
	$class = ( strpos( $_SERVER['QUERY_STRING'], $id ) ) ? 'active' : '';

	return array(
		'class' => $class
		);
}

function pw_admin_bar_menu($wp_admin_bar){

	global $pw;
	$enabled_modules = pw_enabled_modules();
	$pw_slug = $pw['info']['slug'];
	$submenu_slug = apply_filters( 'pw_admin_submenu_slug', $pw_slug );
	$theme_url = get_admin_url(null,'admin.php?page='.$submenu_slug);

	$menu_name = 'postworld-menu';
	$menu_title = _get( $pw['config'], 'wp_admin.admin_bar_menu.title' );
	if( empty( $menu_title ) )
		$menu_title = 'Postworld';

	// Primary Menu Item
	$args = array(
		'id'     	=> 	$menu_name,
		'title'		=>	'<span class="ab-icon"></span> '.$menu_title,
		'meta'   	=> 	array( 'class' => 'first-toolbar-group' ),
		'href'		=>	$theme_url,
	);
	$wp_admin_bar->add_node( $args );	

	// Sub menu items
	$args = array();

	array_push($args,array(
		'id'		=>	'theme_settings',
		'title'		=>	'Theme Settings',
		'href'		=>	$theme_url,
		'parent'	=>	$menu_name,
	));

	if( in_array( 'site', $enabled_modules ) )
		array_push($args,array(
			'id'     	=>	'site_options',
			'title'		=>	'Site Options',
			'href'		=>	$theme_url.'-site',
			'parent' 	=>	$menu_name,
			//'meta'   	=>	array( 'class' => 'theme-menu-item' ),
			'meta'		=>	pw_admin_bar_menu_item_meta('site')
		));

	if( in_array( 'layouts', $enabled_modules ) )
		array_push($args,array(
			'id'		=>	'site_layout',
			'title'		=>	'Layout',
			'href'		=>	$theme_url.'-layout',
			'parent'	=>	$menu_name,
			'meta'		=>	pw_admin_bar_menu_item_meta('layout')
		));
	
	if( in_array( 'sidebars', $enabled_modules ) )
		array_push($args,array(
			'id'		=>	'site_sidebars',
			'title'		=>	'Sidebars',
			'href'		=>	$theme_url.'-sidebars',
			'parent'	=>	$menu_name,
			'meta'		=>	pw_admin_bar_menu_item_meta('sidebars')
		));
	
	if( in_array( 'styles', $enabled_modules ) )
		array_push($args,array(
			'id'		=>	'site_styles',
			'title'		=>	'Styles',
			'href'		=>	$theme_url.'-styles',
			'parent'	=>	$menu_name,
			'meta'		=>	pw_admin_bar_menu_item_meta('styles')
		));

	if( in_array( 'social', $enabled_modules ) )
		array_push($args,array(
			'id'		=>	'site_social',
			'title'		=>	'Social',
			'href'		=>	$theme_url.'-social',
			'parent'	=>	$menu_name,
			'meta'		=>	pw_admin_bar_menu_item_meta('social')
		));

	if( in_array( 'comments', $enabled_modules ) )
		array_push($args,array(
			'id'		=>	'site_comments',
			'title'		=>	'Comments',
			'href'		=>	$theme_url.'-comments',
			'parent'	=>	$menu_name,
			'meta'		=>	pw_admin_bar_menu_item_meta('comments')
		));

	if( in_array( 'feeds', $enabled_modules ) )
		array_push($args,array(
			'id'		=>	'site_feeds',
			'title'		=>	'Feeds',
			'href'		=>	$theme_url.'-feeds',
			'parent'	=>	$menu_name,
			'meta'		=>	pw_admin_bar_menu_item_meta('feeds')
		));

	if( in_array( 'backgrounds', $enabled_modules ) )
		array_push($args,array(
			'id'		=>	'site_backgrounds',
			'title'		=>	'Backgrounds',
			'href'		=>	$theme_url.'-backgrounds',
			'parent'	=>	$menu_name,
			'meta'		=>	pw_admin_bar_menu_item_meta('backgrounds')
		));

	if( in_array( 'iconsets', $enabled_modules ) )
	array_push($args,array(
		'id'		=>	'site_iconsets',
		'title'		=>	'Iconsets',
		'href'		=>	$theme_url.'-iconsets',
		'parent'	=>	$menu_name,
		'meta'		=>	pw_admin_bar_menu_item_meta('iconsets')
	));

	if( in_array( 'shortcodes', $enabled_modules ) )
		array_push($args,array(
			'id'		=>	'site_shortcodes',
			'title'		=>	'Shortcodes',
			'href'		=>	$theme_url.'-shortcodes',
			'parent'	=>	$menu_name,
			'meta'		=>	pw_admin_bar_menu_item_meta('shortcodes')
		));

	array_push($args,array(
		'id'		=>	'site_database',
		'title'		=>	'Database',
		'href'		=>	$theme_url.'-database',
		'parent'	=>	$menu_name,
		'meta'		=>	pw_admin_bar_menu_item_meta('database')
	));

	// Add 'Plugins' to the main frontend admin menu
	array_push($args,array(
		'id'		=>	'plugins',
		'title'		=>	'Plugins',
		'href'		=>	get_admin_url(null,'plugins.php'),
		'parent'	=>	'site-name',
	));

	// Add 'Media' to the main frontend admin menu
	array_push($args,array(
		'id'		=>	'media',
		'title'		=>	'Media',
		'href'		=>	get_admin_url(null,'upload.php'),
		'parent'	=>	'site-name',
	));


	// Filter here for themes to add / modify
	$args = apply_filters( 'pw_admin_bar_menu', $args );

	for($a=0;$a<count($args);$a++){
		$wp_admin_bar->add_node($args[$a]);
	}
	
} 