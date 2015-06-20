<?php
add_shortcode( 'pw-module', 'pw_module_shortcode' );
function pw_module_shortcode( $atts, $content=null, $tag ) {
	extract( shortcode_atts( array(
		'class' => '',
		'id'	=>	'',
	), $atts ) );

	$module_template = pw_get_module_template( $atts['id'] );

	if( !empty( $module_template ) ){
		echo '<div class="pw-module '.$atts['id'].'">';
		echo pw_ob_include( $module_template, $atts );
		echo "</div>";
	}
	else{
		if( pw_dev_mode() )
			echo "<b>Module '" . $atts['id'] . "' not found</b><br> File may have moved or been deleted.";
	}
	// Return template
	return do_shortcode($shortcode);
}

?>