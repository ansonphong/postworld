<?
$walker = new Menu_With_Description;
$custom_menu_config = array(
	'theme_location'  => '',
	'menu'            => $menu_id, //$OPTIONS['menu_slug'],
	'container'       => 'div',
	'container_class' => '',
	'container_id'    => '',
	'menu_class'      => 'menu pw-shortcode ' . $vars['class'],
	'menu_id'         => '',
	'echo'            => true,
	'fallback_cb'     => 'wp_page_menu',
	'before'          => '',
	'after'           => '',
	'link_before'     => '',
	'link_after'      => '',
	'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
	'depth'           => 0,
	'walker'          => $walker,
	'walker_vars'	  => array(
		'item_template_path' => dirname( __FILE__ ) . "/".$menu_template."/item.php",
		),
);

wp_nav_menu( $custom_menu_config );

?>