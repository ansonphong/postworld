<?php

///// SHORTCODE /////
function pw_menu_shortcode( $atts, $content = null, $tag ) {

	// Set the internal defaults
	$shortcode_defaults = array(
		'template'	=>	'shortcode',
		'class' 	=>	'',
		'term'		=>	'', // feature term name
		'name'		=>	'', // menu name
	);

	// Get over-ride defaults from the theme
	$shortcode_defaults = apply_filters( 'pw_menu_shortcode_defaults', $shortcode_defaults, $tag );

	$vars = shortcode_atts( $shortcode_defaults, $atts );

	///// TEMPLATES ////
	$subdir = 'menus';
	$templates = pw_get_templates(
		array(
			'subdirs' => array($subdir),
			'path_type' => 'dir',
			'ext'=>'php',
			)
		);

	$default_template = 'menu-default';
	$template_id = 'menu-' . $vars['template'];
	$template = ( isset( $templates[$subdir][$template_id] ) ) ?
		$templates[$subdir][$template_id] :
		$templates[$subdir][$default_template];

	$vars['menu_template'] = $template_id;
	$vars['menu_id'] = $vars['name'];

	//$shortcode = "<pre>" . json_encode( $vars, JSON_PRETTY_PRINT ) . "</pre>";

	$shortcode = pw_ob_include( $template, $vars );

	// Return template
	return do_shortcode($shortcode);

}

add_shortcode( 'menu', 'pw_menu_shortcode' );

?>