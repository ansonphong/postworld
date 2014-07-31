<?php

///// SHORTCODE /////
function pw_menu_shortcode( $atts, $content = null, $tag ) {

	$vars = shortcode_atts( array(
		'template'	=>	'shortcode',
		'class' 	=>	'',
		'term'		=>	'', // feture term name
		'name'		=>	'', // menu name
	), $atts );

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