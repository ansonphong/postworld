<?php

function i_get_styles(){
	$i_styles = get_option("i-styles");
	if( empty($i_styles) ){
		$i_styles = json_encode( i_style_model() );
	}
	return $i_styles;
}

?>