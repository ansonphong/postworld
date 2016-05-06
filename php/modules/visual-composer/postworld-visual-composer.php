<?php
include 'visual-composer-shortcodes.php';

/**
 * Initialize Postworld Visual Composer module
 */
add_action( 'vc_before_init', 'pw_vc_init', 5 );
function pw_vc_init(){
	if( !pw_module_enabled('visual_composer') ||
		!pw_vc_is_active() )
		return false;

	// Define the default shortcodes being activated
	do_action('pw_vc_init');

	// Get the configured supported shortcodes 
	$supported_shortcodes = pw_module_config('visual_composer.shortcodes.supported');

	foreach( $supported_shortcodes as $shortcode ){
		do_action( 'pw_vc_shortcode_'.$shortcode );
	}

	/**
	 * Disable frontend editor.
	 */
	if( pw_module_config('visual_composer.disable_frontend') &&
		function_exists( 'vc_disable_frontend' ) ){
		vc_disable_frontend();
	}

	/**
	 * Force Visual Composer to initialize as "built into the theme".
	 * This will hide certain tabs under the Settings->Visual Composer page
	 */
	if( function_exists('vc_set_as_theme') )
		vc_set_as_theme();

	/**
	 * Initialize Visual Composer
	 */
	$pw_vc = new PW_Visual_Composer();
	$pw_vc->init();

}

/**
 * Returns a boolean, whether or not Visual Composer is active.
 */
function pw_vc_is_active(){
	return PW_Visual_Composer::is_active();
}

/**
 * Maps a shortcode in Visual Composer and
 * Registers the shortcode with WordPress.
 * @link https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332
 *
 * @param array $params Parameters otherwise passed to vc_map()
 */
function pw_vc_map( $params ){
	if( !function_exists('vc_map') )
		return false;
	// Add the shortcode to Visual Composer
	vc_map( $params );
	// Add the shortcode to WordPress
	add_shortcode( $params['base'], $params['function'] );
}

/**
 * Postworld Visual Composer Class
 */
class PW_Visual_Composer{

	public function init(){
		/**
		 * Register Visual Composer VC Mapping
		 */
		//do_action();

	}

	/**
	 * Detect if WPBakery Visual Composer is active.
	 *
	 * @return boolean
	 */
	public static function is_active(){
		return defined('WPB_VC_VERSION');
	}

	/**
	 * Check if Visual Composer is being used on a particular post.
	 *
	 * @param integer $post_id The ID of the post to check.
	 * @return boolean
	 */
	public static function enabled_on_post( $post_id = null ){
		global $post;

		if( $post_id === null || empty( $post_id ) )
			$post_id = $post->ID;

		if( pw_vc_is_active() )
			return pw_to_bool( get_post_meta( $post_id, '_wpb_vc_js_status', true ) );
		else
			return false;
	}


}
