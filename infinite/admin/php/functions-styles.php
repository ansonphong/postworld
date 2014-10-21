<?php

function i_get_styles(){
	$i_styles = i_get_option( array('option_name' => 'i-styles') );

	if( empty($i_styles) ){
		$i_styles = array();
	}

	$i_styles = apply_filters( 'iOptions-i-styles', $i_styles );

	return $i_styles;
}

?>