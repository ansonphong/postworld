<?php
//////////////////// REGISTER SIDEBARS ////////////////////
global $pw;

if( in_array( 'sidebars', $pw['info']['modules'] ) )
	add_action( 'widgets_init', 'pw_register_sidebars' );

function pw_register_sidebars(){
	
	$sidebars = pw_get_option( array( 'option_name' => PW_OPTIONS_SIDEBARS ) );
	
	if( is_array( $sidebars ) ){
		foreach($sidebars as $sidebar){
			register_sidebar( $sidebar );
		}
	}

}

?>