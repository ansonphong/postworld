<?php
/**
 * Initialize Visual Composer class if the module is enabled
 */
if( pw_module_enabled('visual-composer') ){
	add_action( 'vc_after_init', array( 'PW_Visual_Composer', 'init' ) );
}
/**
 * Wrapper function to instantiate PW Visual Composer class
 */
function pw_vc_init(){
	$pw_vc = new PW_Visual_Composer();
	$pw_vc->init();

	// Define the default shortcodes being activated
	do_action('pw_vc_init');


}

class PW_Visual_Composer{

	public function init(){
		/**
		 * Register Visual Composer VC Mapping
		 */
		//do_action();

	}



}
