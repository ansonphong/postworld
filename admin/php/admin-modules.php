<?php

/**
 * Utility function for including custom setting modules.
 * Settings modules are found in templates/admin/select-{{ vars.setting }}
 */
function pw_select_setting( $vars = array() ){
	$default_vars = array(
		'ng_model' => null,
		'setting' => null,
		);
	$vars = array_replace($default_vars, $vars);
	if( empty( $vars['ng_model'] ) || empty( $vars['setting'] ) )
		return false;
	echo pw_ob_admin_template( 'select-'.$vars['setting'], $vars );
}

///// SAVE BUTTON /////
function pw_save_option_button( $option_name, $option_model, $callbacks=array() ){
	$vars = array(
		'option_name'	=>	$option_name,
		'option_model'	=>	$option_model,
		'callbacks'		=>	$callbacks,
		);
	echo pw_ob_admin_template( 'button-save-option', $vars );
}

function i_save_option_button( $option_name, $option_model ){
	// DEPRECIATED
	pw_save_option_button( $option_name, $option_model );
}

///// SELECT IMAGE ID /////
function pw_select_image_id( $vars = array() ){
	$defaultVars = array(
		'ng_model'		=>	null,
		'slug'			=>	pw_random_hash( $length = 4 ),	// [string] The unique slug where the image object is kept temporatily for display
		'label'			=>	'Image',						// [string] The label to put on the button and title
		'width'			=>	'400px',						// [string] The width to display the image
		'remove'		=>	true,							// [bool] 	Whether or not to include a button to un-set the background image
		'attributes'	=>	'',								// [string]	Arbitrary attributes to be added to the image object
		'display'		=>	true,							// [bool] 	Whether or not to display the selected image in-line
		);
	$vars = array_replace_recursive( $defaultVars, $vars );
	return pw_ob_admin_template( 'select-image-id', $vars );
}

///// SELECT USER AUTOCOMPLETE /////
function pw_select_user_autocomplete( $vars = array() ){
	$defaultVars = array(
		'ng_model'		=>	'searchTerm',
		'class'			=>	'',
		'on_select'		=>	'',
		'limit_to'		=>	'20',
		);
	$vars = array_replace_recursive( $defaultVars, $vars );
	return pw_ob_admin_template( 'select-user-autocomplete', $vars );
}

///// SELECT SITE LOGO /////
function i_select_image_logo( $vars = array() ){
	$defaultVars = array(
		'option_var'	=>	'pwOptions',
		'option_subkey'	=>	'images.logo',
		);
	$vars = array_replace_recursive( $defaultVars, $vars );
	return pw_ob_admin_template( 'select-image-logo', $vars );
}

///// SELECT SITE FAVICON /////
function i_select_image_favicon( $vars = array() ){
	$defaultVars = array(
		'option_var'	=>	'pwOptions',
		'option_subkey'	=>	'images.favicon',
		);
	$vars = array_replace_recursive( $defaultVars, $vars );
	return pw_ob_admin_template( 'select-image-favicon', $vars );
}

///// SELECT A MENU /////
function i_select_menus( $vars ){
	// DEPRECIATED
	return pw_select_menus( $vars );
}

function pw_select_menus( $vars ){
	$default_vars = array(
		'options_model' => 	'options.menus',	//[string] // Angular expression, where to store the results
		'ng_model' 		=> 	'',					//[string] // Angular expression, where to set the option
		'null_option' 	=>	'No Menu',			//[string] // What to label the null option
		);
	$vars = array_replace($default_vars, $vars);
	return pw_ob_admin_template( 'select-menu', $vars );
}

///// DOWNLOAD IMAGE /////
function i_download_image_option( $vars ){
	// DEPRECIATED
	return pw_download_image_option( $vars );
}
function pw_download_image_option( $vars = array( "context" => "quickEdit" ) ){
	/*
		$vars = array(
			'context'	=>	"quickEdit"
		);
	*/
	switch( $vars['context'] ){
		///// SITE-WIDE SETTINGS /////
		case 'siteAdmin': 
				$vars['options_model'] = "options.general.doubleSwitch";
				$vars['ng_model'] = "pwOptions.posts.post.post_meta.".PW_POSTMETA_KEY.".image.download";
			break;

		///// PER-POST ADMIN SETTINGS /////
		case 'postAdmin':
		default:
				$vars['options_model'] = "options.general.tripleSwitch";
				$vars['ng_model'] = "pwMeta.image.download";
			break;

		///// QUICK EDIT SETTINGS /////
		case 'quickEdit':
		default:
				$vars['options_model'] = "options.general.tripleSwitch";
				$vars['ng_model'] = "post.post_meta.".PW_POSTMETA_KEY.".image.download";
			break;
	}

	return pw_ob_admin_template( 'meta-image-download', $vars );
}

///// POST CONTENT COLUMNS /////
function i_content_columns_option( $vars ){
	// DEPRECIATED
	return pw_content_columns_option( $vars );
}
function pw_content_columns_option( $vars = array( "context" => "quickEdit" ) ){
	$vars['options_model'] = "options.post_content.columns";

	if( is_string( _get( $vars, 'ng_model' ) ) )
		$vars['context'] = 'custom';

	switch($vars['context']){
		///// SITE-WIDE SETTINGS /////
		case 'siteAdmin': 
			$vars['ng_model'] = "pwOptions.posts.post.post_meta.".PW_POSTMETA_KEY.".post_content.columns";
			break;
		///// PER-POST ADMIN SETTINGS /////
		case 'postAdmin':
		default:
			$vars['ng_model'] = "pwMeta.post_content.columns";
			break;
		///// QUICK EDIT SETTINGS /////
		case 'quickEdit':
		default:
			$vars['ng_model'] = "post.post_meta.".PW_POSTMETA_KEY.".post_content.columns";
			break;

		///// CUSTOM NG MODEL /////
		default:
			// Keep current ng_model
			break;
	}
	return pw_ob_admin_template( 'meta-content-columns', $vars );
}

function i_share_social_options(){
	// DEPRECIATED
	return pw_share_social_options();
}
function pw_share_social_options(){
	$vars = array();
	$vars['options_model'] = "options.share.meta";
	$vars['model_var'] = "pwSocial";
	$vars['model_key'] = "share.networks";
	$vars['ng_model'] = $vars['model_var'] . '.' . $vars['model_key'];

	return pw_ob_admin_template( 'share-social', $vars );
}


function pw_featured_image_placement_options( $vars = array( "context" => "quickEdit" ) ){
	
	$vars['options_model'] = "options.featured_image.placement";

	if( isset($vars['ng_model']) && is_string( $vars['ng_model'] ) )
		$vars['context'] = 'custom';

	switch($vars['context']){
		///// SITE-WIDE SETTINGS /////
		case 'siteAdmin': 
			$vars['ng_model'] = "pwOptions.posts.post.post_meta.".PW_POSTMETA_KEY.".featured_image.placement";
			break;
		///// PER-POST ADMIN SETTINGS /////
		case 'postAdmin':
		default:
			$vars['ng_model'] = "pwMeta.featured_image.placement";
			break;
		///// QUICK EDIT SETTINGS /////
		case 'quickEdit':
		default:
			$vars['ng_model'] = "post.post_meta.".PW_POSTMETA_KEY.".featured_image.placement";
			break;
		///// CUSTOM NG MODEL /////
		default:
			// Keep current ng_model
			break;
	}
	return pw_ob_admin_template( 'select-featured-image-placement-options', $vars );
}


function pw_select_modules(){
	$vars = array();
	$vars['options_model'] = "options.modules";
	$vars['model_var'] = "pwModules";
	//$vars['model_key'] = "share.networks";
	//$vars['ng_model'] = $vars['model_var'] . '.' . $vars['model_key'];
	return pw_ob_admin_template( 'select-modules', $vars );
}

function i_gallery_options( $vars ){
	// DEPRECIATED
	return pw_gallery_options( $vars );
}
function pw_gallery_options( $vars = array( "context" => "quickEdit", 'gallery_options'=>array(), 'gallery_meta' => true ) ){

	//$vars['options_model'] = "options.post_content.columns";

	/**
	 * Set ng-model based on the context variable
	 */
	if( !isset( $vars['ng_model'] ) )
		switch( $vars['context'] ){
			///// SITE-WIDE SETTINGS /////
			case 'siteAdmin': 
					//$vars['ng_model'] = "pwOptions.posts.post.post_meta.".PW_POSTMETA_KEY.".post_content.columns";
				break;
			///// PER-POST ADMIN SETTINGS /////
			case 'postAdmin':
			default:
					$vars['ng_model'] = "pwMeta.gallery";
				break;
			///// QUICK EDIT SETTINGS /////
			case 'quickEdit':
			default:
					$vars['ng_model'] = "post.post_meta.".PW_POSTMETA_KEY.".gallery";
				break;
		}

	return pw_ob_admin_template( 'meta-gallery-options', $vars );
}

function i_link_url_options( $vars ){
	// DEPRECIATED
	return pw_link_url_options( $vars );
}
function pw_link_url_options( $vars = array( "context" => "quickEdit" ) ){

	if( !isset($vars['options_model']) )
		$vars['options_model'] = array();

	switch( $vars['context'] ){
		///// SITE-WIDE SETTINGS /////
		case 'siteAdmin': 
				$vars['ng_model'] = "pwOptions.posts.post.post_meta.".PW_POSTMETA_KEY.".link_url";
				$vars['options_model']['show'] = "options.general.customSwitch";
				$vars['options_model']['tooltip_show'] = "options.general.none";
				$vars['options_model']['highlight'] = "options.general.doubleSwitch";
				$vars['options_model']['new_target'] = "options.general.doubleSwitch";
			break;
		///// PER-POST ADMIN SETTINGS /////
		case 'postAdmin':
		default:
				$vars['ng_model'] = "pwMeta.link_url";
				$vars['options_model']['show'] = "options.general.defaultAndCustomDoubleSwitch";
				$vars['options_model']['tooltip_show'] = "options.general.defaultCustomSwitch";
				$vars['options_model']['highlight'] = "options.general.tripleSwitch";
				$vars['options_model']['new_target'] = "options.general.tripleSwitch";
			break;
		///// QUICK EDIT SETTINGS /////
		case 'quickEdit':
		default:
				$vars['ng_model'] = "post.post_meta.".PW_POSTMETA_KEY.".link_url";
				$vars['options_model']['show'] = "options.general.defaultAndCustomDoubleSwitch";
				$vars['options_model']['tooltip_show'] = "options.general.defaultCustomSwitch";
				$vars['options_model']['highlight'] = "options.general.tripleSwitch";
				$vars['options_model']['new_target'] = "options.general.tripleSwitch";
			break;
	}

	return pw_ob_admin_template( 'meta-link-url-options', $vars );

}

function option_check( $option, $test ){
	if( $option == $test )
		return " checked ";
	else
		return null;
}

function radio_select( $option_name, $options, $attributes = '' ){
	foreach( $options as $option ){
		$checked = '';
		$checked = option_check( get_option($option_name), $option['slug'] );
		echo '<label><input name="'. $option_name .'" type="radio" value="'. $option['slug'] .'" ' . $checked . ' ' . $attributes . '/>' . $option['label'] . '</label>';
	}
}

function radio_image_select( $option_name, $options, $attributes = '' ){
	$html = "";
	$vars = array(
		'option_name'	=>	$option_name,
		'options'		=>	$options,
		'attributes'	=>	$attributes,
		);

	foreach( $options as $option ){
		$vars['option'] = $option;
		$vars['checked'] = '';
		$vars['checked'] = option_check( get_option( $vars['option_name'] ), $vars['option']['slug'] );
		$html .= i_ob_include_template( 'admin/modules/radio-image.php', $vars );
	}

	return $html;

}



function i_select_featured_image_options( $vars ){
	// DEPRECIATED
	return pw_select_featured_image_options( $vars );
}
function pw_select_featured_image_options( $vars ){
	/*
	 *	$vars = array(
	 * 		'ng_model'	=> 	[string]	// Angular expression ie. 'pwOptions.home.slider',
	 *		'show' 		=> 	[array] 	// Which fields to show options for
	 *	)
	 */
	return pw_ob_admin_template( 'select-header-image-options', $vars );
}

///// SLIDER OPTIONS : META FUNCTION /////
function i_admin_slider_options( $vars = array() ){
	// DEPRECIATED
	return pw_admin_slider_options( $vars );
}
function pw_admin_slider_options( $vars = array() ){
	// TODO:
	// - Create a random ID for the controller instance
	// - Pass in unique model prefix, to allow for multiple instances
	return pw_ob_admin_template( 'options-slider', $vars );
}

///// SINGLE LAYOUT OPTIONS /////
function pw_layout_single_options( $vars = array( "context" => "quickEdit" ) ){
	//$vars['options_model'] = "options.post_content.columns";
	switch($vars['context']){
		///// SITE-WIDE SETTINGS /////
		case 'siteAdmin': 
				$vars['ng_model'] = "iLayouts[context.name]";
			break;
		///// EDIT POST SETTINGS /////
		case 'postAdmin':
		case 'quickEdit':
		default:
				$vars['ng_model'] = "pw_layout_post.post_meta." . pw_postmeta_key . ".layout";
			break;
	}
	return pw_ob_admin_template( 'layout-single', $vars );

}

///// SINGLE SELECT BACKGROUND OPTIONS /////
function pw_background_select( $vars = array( "context" => "quickEdit" ) ){
	//$vars['options_model'] = "options.post_content.columns";
	switch($vars['context']){
		///// SITE-WIDE SETTINGS /////
		case 'siteAdmin': 
				$vars['ng_model'] = "pwBackgroundContexts[ context.name ]";
			break;
		///// EDIT POST SETTINGS /////
		case 'postAdmin':
		case 'quickEdit':
		default:
				$vars['ng_model'] = "pw_background_post.post_meta." . pw_postmeta_key . ".background";
			break;
	}
	return pw_ob_admin_template( 'background-select', $vars );

}

///// SINGLE CUSTOMIZE BACKGROUND OPTIONS /////
// For all the controls of customizing a background
function pw_background_single_options( $vars = array( "context" => "quickEdit" ) ){
	//$vars['options_model'] = "options.post_content.columns";
	switch($vars['context']){
		///// SITE-WIDE SETTINGS /////
		case 'siteAdmin': 
				$vars['ng_model'] = "selectedItem";
			break;
		///// EDIT POST SETTINGS /////
		case 'postAdmin':
		case 'quickEdit':
		default:
				$vars['ng_model'] = "post.post_meta." . pw_postmeta_key . ".background";
			break;
	}
	return pw_ob_admin_template( 'background-single', $vars );

}

///// FEED TEMPLATE OPTIONS /////
function pw_feed_template_options( $vars = array( "ng_model" => "selectedItem" ) ){
	return pw_ob_admin_template( 'feed-template-options', $vars );
}

///// FEED QUERY OPTIONS /////
function pw_feed_query_options( $vars = array( "ng_model" => "selectedItem" ) ){
	return pw_ob_admin_template( 'feed-query-options', $vars );
}

///// FEED VARIABLE OPTIONS /////
function pw_feed_variable_options( $vars = array( "ng_model" => "selectedItem" ) ){
	return pw_ob_admin_template( 'feed-variable-options', $vars );
}

///// SELECT ICON /////
function i_select_icon_options( $vars ){
	// DEPRECIATED
	return pw_admin_select_icon( $vars );
}
function pw_select_icon_options( $vars ){
	// DEPRECIATED
	return pw_admin_select_icon( $vars );
}
function pw_admin_select_icon( $vars = array( "ng_model" => "pwMeta.icon.class" ) ){
	return pw_ob_admin_template( 'select-icon', $vars );
}

function i_select_slider_settings( $vars ){
	// DEPRECIATED
	return pw_select_slider_settings( $vars );
}
function pw_select_slider_settings( $vars ){
	/*
	 *	$vars = array(
	 * 		'ng_model'	=> 	[string]	// Angular expression ie. 'pwOptions.home.slider',
	 *		'show'		=>	[ARRAY]		// Array of options to show : array( 'height', 'interval', 'max_slides', 'transition', 'no_pause' )
	 *	)
	 */
	return pw_ob_admin_template( 'select-slider-settings', $vars );
}


function i_select_blocks_settings( $vars ){
	// DEPRECIATED
	return pw_select_blocks_settings( $vars );
}
function pw_select_blocks_settings( $vars ){
	/*
	 *	$vars = array(
	 *		'option_var'	=> [string]
	 *		'option_key'	=> [string]
	 *		'show'			=> [ARRAY]	// Array of options to show : array( 'height', 'interval', 'max_slides', 'transition', 'no_pause' )
	 *	)
	 */

	return pw_ob_admin_template( 'select-blocks-settings', $vars );
}


?>