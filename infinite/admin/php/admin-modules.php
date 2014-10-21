<?php

///// SAVE BUTTON /////
function i_save_option_button( $option_name, $option_model ){
	$vars = array(
		'option_name'	=>	$option_name,
		'option_model'	=>	$option_model,
		);
	echo i_ob_include_template( 'admin/modules/button-save-option.php', $vars );
}

///// SELECT A MENU /////
function i_select_menus( $vars ){
	/*
	$vars = array(
		'options_model' => 	[string] // Angular expression, where to store the results
		'ng_model' => 		[string] // Angular expression, where to set the option
		'null_option' =>	[string] // What to label the null option
		);
	*/
	return i_ob_include_template( 'admin/modules/select-menu.php', $vars );
}

///// DOWNLOAD IMAGE /////
function i_download_image_option( $vars = array( "context" => "quickEdit" ) ){
	/*
		$vars = array(
			'context'	=>	"quickEdit"
		);
	*/
	switch( $vars['context'] ){
		///// SITE-WIDE SETTINGS /////
		case 'siteAdmin': 
				$vars['options_model'] = "options.general.doubleSwitch";
				$vars['ng_model'] = "iOptions.posts.post.post_meta.i_meta.image.download";
			break;

		///// PER-POST ADMIN SETTINGS /////
		case 'postAdmin':
		default:
				$vars['options_model'] = "options.general.tripleSwitch";
				$vars['ng_model'] = "iMeta.image.download";
			break;

		///// QUICK EDIT SETTINGS /////
		case 'quickEdit':
		default:
				$vars['options_model'] = "options.general.tripleSwitch";
				$vars['ng_model'] = "post.post_meta.i_meta.image.download";
			break;
	}

	return i_ob_include_template( 'admin/modules/meta-image-download.php', $vars );

}


///// POST CONTENT COLUMNS /////
function i_content_columns_option( $vars = array( "context" => "quickEdit" ) ){

	$vars['options_model'] = "options.post_content.columns";

	switch($vars['context']){
		///// SITE-WIDE SETTINGS /////
		case 'siteAdmin': 
				$vars['ng_model'] = "iOptions.posts.post.post_meta.i_meta.post_content.columns";
			break;
		///// PER-POST ADMIN SETTINGS /////
		case 'postAdmin':
		default:
				$vars['ng_model'] = "iMeta.post_content.columns";
			break;
		///// QUICK EDIT SETTINGS /////
		case 'quickEdit':
		default:
				$vars['ng_model'] = "post.post_meta.i_meta.post_content.columns";
			break;
	}

	return i_ob_include_template( 'admin/modules/meta-content-columns.php', $vars );

}


function i_share_social_options(){
	$vars['options_model'] = "options.share.meta";
	$vars['option_key'] = "social.share.networks";
	$vars['ng_model'] = "iOptions.".$vars['option_key'];

	return i_ob_include_template( 'admin/modules/share-social.php', $vars );
}


function i_gallery_options( $vars = array( "context" => "quickEdit" ) ){

	//$vars['options_model'] = "options.post_content.columns";

	switch( $vars['context'] ){
		///// SITE-WIDE SETTINGS /////
		case 'siteAdmin': 
				//$vars['ng_model'] = "iOptions.posts.post.post_meta.i_meta.post_content.columns";
			break;
		///// PER-POST ADMIN SETTINGS /////
		case 'postAdmin':
		default:
				$vars['ng_model'] = "iMeta.gallery";
			break;
		///// QUICK EDIT SETTINGS /////
		case 'quickEdit':
		default:
				$vars['ng_model'] = "post.post_meta.i_meta.gallery";
			break;
	}

	return i_ob_include_template( 'admin/modules/meta-gallery-options.php', $vars );

}


function i_link_url_options( $vars = array( "context" => "quickEdit" ) ){

	if( !isset($vars['options_model']) )
		$vars['options_model'] = array();

	switch( $vars['context'] ){
		///// SITE-WIDE SETTINGS /////
		case 'siteAdmin': 
				$vars['ng_model'] = "iOptions.posts.post.post_meta.i_meta.link_url";
				$vars['options_model']['show'] = "options.general.customSwitch";
				$vars['options_model']['tooltip_show'] = "options.general.none";
				$vars['options_model']['highlight'] = "options.general.doubleSwitch";
				$vars['options_model']['new_target'] = "options.general.doubleSwitch";
			break;
		///// PER-POST ADMIN SETTINGS /////
		case 'postAdmin':
		default:
				$vars['ng_model'] = "iMeta.link_url";
				$vars['options_model']['show'] = "options.general.defaultAndCustomDoubleSwitch";
				$vars['options_model']['tooltip_show'] = "options.general.defaultCustomSwitch";
				$vars['options_model']['highlight'] = "options.general.tripleSwitch";
				$vars['options_model']['new_target'] = "options.general.tripleSwitch";
			break;
		///// QUICK EDIT SETTINGS /////
		case 'quickEdit':
		default:
				$vars['ng_model'] = "post.post_meta.i_meta.link_url";
				$vars['options_model']['show'] = "options.general.defaultAndCustomDoubleSwitch";
				$vars['options_model']['tooltip_show'] = "options.general.defaultCustomSwitch";
				$vars['options_model']['highlight'] = "options.general.tripleSwitch";
				$vars['options_model']['new_target'] = "options.general.tripleSwitch";
			break;
	}

	return i_ob_include_template( 'admin/modules/meta-link-url-options.php', $vars );

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

function i_select_slider_settings( $vars ){
	/*
	 *	$vars = array(
	 * 		'ng_model'	=> 	[string]	// Angular expression ie. 'iOptions.home.slider',
	 *		'show'		=>	[ARRAY]		// Array of options to show : array( 'height', 'interval', 'max_slides', 'transition', 'no_pause' )
	 *	)
	 */

	return i_ob_include_template( 'admin/modules/select-slider-settings.php', $vars );
}

function i_select_featured_image_options( $vars ){
	/*
	 *	$vars = array(
	 * 		'ng_model'	=> 	[string]	// Angular expression ie. 'iOptions.home.slider',
	 *	)
	 */

	return i_ob_include_template( 'admin/modules/select-header-image-options.php', $vars );
}


///// SLIDER OPTIONS : META FUNCTION /////
function i_admin_slider_options( $vars = array() ){
	// TODO:
	// - Create a hash ID for the controller instance
	// - Pass in unique model prefix, to allow for multiple instances

	return i_ob_include_template( "admin/php/options-slider.php", $vars );

}



///// POST CONTENT COLUMNS /////
function i_layout_single_options( $vars = array( "context" => "quickEdit" ) ){

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
				$vars['ng_model'] = "post.post_meta." . pw_postmeta_key . ".layout";
			break;

	}

	return i_ob_include_template( 'admin/modules/layout-single.php', $vars );

}



?>