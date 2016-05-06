<?php
include 'visual-composer-shortcodes.php';

/**
 * Initialize Postworld Visual Composer module
 */
add_action( 'vc_before_init', 'pw_vc_init', 5 );
function pw_vc_init(){

	// If module is not enabled or VC not active, end here
	if( !pw_module_enabled('visual_composer') ||
		!pw_vc_is_active() )
		return false;

	// Fire Action Hook
	do_action('pw_vc_init');

	// Localize frontent disabled option
	$disable_frontend = pw_module_config('visual_composer.disable_frontend');

	// Map Postworld core shortcodes if in admin or frontend
	if( (is_admin() || !$disable_frontend) && is_user_logged_in() ){
		// Get the configured supported shortcodes 
		$supported_shortcodes = pw_module_config('visual_composer.shortcodes.supported');
		// Collect all the parameters
		$elements = apply_filters( 'pw_vc_map_shortcodes', array() );
		foreach ($elements as $key => $params){
			vc_map( $params );
		}
	}
	
	/**
	 * Disable frontend editor.
	 */
	if( $disable_frontend &&
		function_exists( 'vc_disable_frontend' ) ){
		vc_disable_frontend();
	}

	/**
	 * Force Visual Composer to initialize as "built into the theme".
	 * This will hide certain tabs under the Settings->Visual Composer page
	 */
	if( function_exists('vc_set_as_theme') )
		vc_set_as_theme();

}

/**
 * Detect if WPBakery Visual Composer is active.
 *
 * @return boolean
 */
function pw_vc_is_active(){
	return defined('WPB_VC_VERSION');
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
 * Visual Composer Shortcode
 * Used when registering a shortcode.
 * Uses the template from /templates/shortcodes/[tagname].php
 */
function pw_vc_shortcode( $atts, $content, $tag ){
	$vars = array(
		'atts' => $atts,
		'content' => $content,
		'tag' => $tag
		);
	$template_path = pw_get_shortcode_template( $tag );
	$output = pw_ob_include( $template_path, $vars );
	return do_shortcode($output);
}

/**
 * Check if Visual Composer is being used on a particular post.
 *
 * @param integer $post_id The ID of the post to check.
 * @return boolean
 */
function pw_vc_enabled_on_post( $post_id = null ){
	global $post;

	if( $post_id === null || empty( $post_id ) )
		$post_id = $post->ID;

	if( pw_vc_is_active() )
		return pw_to_bool( get_post_meta( $post_id, '_wpb_vc_js_status', true ) );
	else
		return false;
}
