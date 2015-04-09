<?php
/*                    ____             _       _   __  __          _ _       
  _ ____      __  _  / ___|  ___   ___(_) __ _| | |  \/  | ___  __| (_) __ _ 
 | '_ \ \ /\ / / (_) \___ \ / _ \ / __| |/ _` | | | |\/| |/ _ \/ _` | |/ _` |
 | |_) \ V  V /   _   ___) | (_) | (__| | (_| | | | |  | |  __/ (_| | | (_| |
 | .__/ \_/\_/   (_) |____/ \___/ \___|_|\__,_|_| |_|  |_|\___|\__,_|_|\__,_|
 |_|                                                                         
/////////////// ---------- SOCIAL MEDIA FUNCTIONS ---------- ///////////////*/

// SNIPPETS
//include(locate_template('views//taxonomy-page-setup.php'));

function pw_get_social_media_meta(){
	// Provide the unfiltered meta data for contact methods options

	$social_media_meta = array(
		'twitter'	=>	array(
			'icon'			=>	'pwi-twitter-square',
			'name'			=>	'Twitter',
			'label'			=>	'On Twitter',
			'share_label'	=>	'Share On Twitter',
			'description' 	=> 	'Twitter Username',
			'prepend_url'	=>	'http://twitter.com/',
			),
		'facebook'	=>	array(
			'icon'			=>	'pwi-facebook-square',
			'name'			=>	'Facebook',
			'label'			=>	'On Facebook',
			'share_label'	=>	'Share On Facebook',
			'description' 	=> 	'Facebook URL',
			'prepend_url'	=>	''
			),
		'instagram'	=>	array(
			'icon'			=>	'pwi-instagram-square',
			'name'			=>	'Instagram',
			'label'			=>	'On Instagram',
			'share_label'	=>	'Share On Instagram',
			'description' 	=> 	'Instagram Username',
			'prepend_url'	=>	'http://instagram.com/'
			),
		'google_plus'	=>	array(
			'icon'			=>	'pwi-google-plus-square',
			'name'			=>	'Google+',
			'label'			=>	'On Google+',
			'share_label'	=>	'Share On Google+',
			'description' 	=> 	'Google+ URL',
			'prepend_url'	=>	''
			),
		'pinterest'	=>	array(
			'icon'			=>	'pwi-pinterest-square',
			'name'			=>	'Pinterest',
			'label'			=>	'On Pinterest',
			'share_label'	=>	'Share On Pinterest',
			'description' 	=> 	'Pinterest URL',
			'prepend_url'	=>	''
			),
		'reddit'	=>	array(
			'icon'			=>	'pwi-reddit-square',
			'name'			=>	'Reddit',
			'label'			=>	'On Reddit',
			'share_label'	=>	'Share On Reddit',
			'description' 	=> 	'Reddit URL',
			'prepend_url'	=>	''
			),
		'deviant_art'	=>	array(
			'icon'			=>	'pwi-circle-medium',
			'name'			=>	'Deviant Art',
			'label'			=>	'On Deviant Art',
			'share_label'	=>	'Share On Deviant Art',
			'description' 	=> 	'Deviant Art URL',
			'prepend_url'	=>	''
			),
		'flickr'	=>	array(
			'icon'			=>	'pwi-circle-medium',
			'name'			=>	'Flickr',
			'label'			=>	'On Flickr',
			'share_label'	=>	'Share On Flickr',
			'description' 	=> 	'Flickr URL',
			'prepend_url'	=>	''
			),
		'youtube'	=>	array(
			'icon'			=>	'pwi-circle-medium',
			'name'			=>	'YouTube',
			'label'			=>	'On YouTube',
			'share_label'	=>	'Share On YouTube',
			'description' 	=> 	'YouTube URL',
			'prepend_url'	=>	''
			),
		'vimeo'	=>	array(
			'icon'			=>	'pwi-circle-medium',
			'name'			=>	'Vimeo',
			'label'			=>	'On Vimeo',
			'share_label'	=>	'Share On Vimeo',
			'description' 	=> 	'Vimeo URL',
			'prepend_url'	=>	''
			),
		'website'	=>	array(
			'icon'			=>	'pwi-globe',
			'name'			=>	'Website',
			'label'			=>	'Personal Website',
			'share_label'	=>	'Visit Personal Website',
			'description' 	=> 	'Website URL',
			'prepend_url'	=>	''
			),
		);

	// Allow the theme to filter the options meta
	return apply_filters( 'pw_get_social_media_meta', $social_media_meta );
}


////////// SOCIAL SHARE //////////
function pw_social_share( $post ){
	$template = pw_get_template ( 'social', 'share', 'php', 'dir' );
	return pw_ob_include( $template, $post);
}

function pw_get_social_share_meta( $vars ){
	global $pw;

	// Post from vars
	$post = pw_to_array( $vars );

	// Share Networks
	$share_networks = pw_get_option( array( "option_name" => PW_OPTIONS_SOCIAL, "key" => "share.networks" ) );

	///// IMAGE URL /////
	// Get the image url from the passed post object
	$image_url_from_post = _get( $post, 'image.sizes.full.url' );

	// If the image URL is not set
	if( $image_url_from_post === false ){
		// Gget it manually
		$featured_image_obj = pw_get_featured_image_obj( $post['ID'] );
		$image_url_from_post = $featured_image_obj['url'];
	}
		
		


	if( $image_url == null )
		$image_url = '';
	else
		$image_url = urlencode($image_url);

	///// PERMALINK /////
	if( is_array( $pw['view']['context'] ) &&
		in_array( 'archive', $pw['view']['context'] ) )
		$permalink = _get( $pw, 'view.url' );
	else
		$permalink = _get( $post, 'post_permalink' );
	
	if( $permalink == false )
		$permalink = get_permalink( $post['ID'] );
	$permalink = urlencode( $permalink );
	// If permalink doesn't exist, get the URL from PHP URL of current page

	///// TITLE & EXCERPT /////
	$site_name = urlencode( get_bloginfo( 'name' ) );

	// If Post Type Archive
	if( is_post_type_archive() ){
		$title = urlencode( wp_title( " | ", false, "right" ) );
		$title_and_site_name = $title;
	}
	// If a Post or otherwise
	else{
		$title = urlencode( $post['post_title'] );
		$title_and_site_name = $title . urlencode(" | ") . $site_name;
	}
	
	$excerpt = urlencode( $post['post_excerpt'] );
	
	///// SOCIAL SHARE OBJECT /////
	$s = array();

	///// FACEBOOK /////
	if( in_array( 'facebook', $share_networks ) ){
		$facebook_link = "https://www.facebook.com/sharer/sharer.php?u=".$permalink;
		$s = _set( $s, 'facebook.link', $facebook_link );
	}
	
	///// TWITTER /////
	if( in_array( 'twitter', $share_networks ) ){
		$twitter_user = pw_get_option(array( 'option_name' => PW_OPTIONS_SOCIAL, 'key' => 'networks.twitter' ));

		$twitter_via = ( $twitter_user ) ?
			'via='.urlencode($twitter_user).'&' : '';

		$twitter_related = ( $twitter_user ) ?
			'related='.urlencode($twitter_user).'&' : '';

		$twitter_hashtags = pw_get_option(array( 'option_name' => PW_OPTIONS_SOCIAL, 'key' => 'networks.twitter_hashtags' ));
		$twitter_hashtags = ( $twitter_hashtags ) ?
			'hashtags='.urlencode($twitter_hashtags) . '&' : '';

		$twitter_text = 'text=' . $title_and_site_name . '&';

		$twitter_url = 'url='.$permalink.'&';

		$twitter_link = "https://twitter.com/intent/tweet?" . $twitter_hashtags . $twitter_related . $twitter_text . $twitter_url . $twitter_via;

		$s = _set( $s, 'twitter.link', $twitter_link );
	}

	///// REDDIT LINK /////
	if( in_array( 'reddit', $share_networks ) ){
		$reddit_link = 'http://www.reddit.com/submit?url='.$permalink.'&title='.$title;
		$s = _set( $s, 'reddit.link', $reddit_link );
	}

	///// GOOGLE PLUS LINK /////
	if( in_array( 'google_plus', $share_networks ) ){
		$google_plus_link = 'https://plus.google.com/share?url=' . $permalink;
		$s = _set( $s, 'google_plus.link', $google_plus_link );
	}

	///// PINTEREST LINK /////
	if( in_array( 'pinterest', $share_networks ) ){
		$pinterest_link = 'https://pinterest.com/pin/create/button/?url='.$permalink.'&media='.$image_url.'&description='.$title_and_site_name;
		$s = _set( $s, 'pinterest.link', $pinterest_link );
	}

	return $s;

}


function pw_social_widgets( $meta = array() ){
	/*
		$meta = array(
			'post_id'		=>	[integer],
			'title'			=>	[string], 
			'url'			=>	[string],	
		);
	*/

	global $pw;
	global $post;
	$settings = array();

	// Get Title and URL from post_id
	$post_id = _get( $meta, 'post_id' );

	if( empty( $post_id )  ){
		$post_id = $post->ID;
		$meta['post_id'] = $post_id;
	}

	if( !empty( $post_id ) ){
		$meta['title'] = get_post( $post_id )->post_title;
		$meta['url'] = get_permalink( $post_id );
	}
	// Set default Meta data
	else{
		$default_meta = array(
			'title'	=>	$pw['view']['title'],
			'url' 	=>	$pw['view']['url'],
			);
		$meta = array_replace_recursive( $default_meta, $meta );
	}

	// Set meta into settings
	$settings['meta'] = $meta;


	// Apply filters
	$settings = apply_filters( 'pw_social_widgets', $settings );
	//pw_log( "pw_social_widgets : " . json_encode( $settings ) );

	pw_log( $settings );

	$output = "";
	if( is_array( $settings['networks'] ) )
		foreach( $settings['networks'] as $network ){
			switch( $network['network'] ){
				// FACEBOOK
				case 'facebook':
					$output .= pw_social_widget_facebook( $settings['meta'], $network );
				break;
				// TWITTER
				case 'twitter':
					$output .= pw_social_widget_twitter( $settings['meta'], $network );
				break;
				
			}
		}
	
	return $output;

}



////////// SOCIAL MEDIA WIDGETS //////////
function pw_default_social_widget_settings( $settings = array() ){
	global $post;
	global $pw;

	$global_settings = array(
		"meta"  =>  array(
			//"title"				=>  "",
			//"url"		      	=>  $pw['view']['url'],
			"before_network"  	=>  "<span class=\"social-widget %network%\">",
			"after_network"   	=>  "</span>"
			),
		"networks"  =>  array(
			array(
				"network"     =>  "facebook",
				"widget"      =>  "like-button",
				"appId"       =>  pw_get_option( array( 'option_name' => PW_OPTIONS_SOCIAL, 'key' => 'networks.facebook_app_id' ) ),
				"include_sdk" =>  true,
				"settings"  =>  array(
					"layout"    	=>  "button_count",
					"action"    	=>  "like",
					"show_faces"  	=>  "false",
					"share"     	=>  "true",
					"width"     	=>  "133",
					"height"    	=>  "24",
					"colorscheme" 	=> 	"light",
					),
				),
			array(
				"network"     =>  "twitter",
				"widget"      =>  "share",
				"include_script"=>  true,
				"settings"    =>  array(
					"via"       =>  pw_get_option( array( 'option_name' => PW_OPTIONS_SOCIAL, 'key' => 'networks.twitter' ) ), //"twitter_user",
					"related"   =>  pw_get_option( array( 'option_name' => PW_OPTIONS_SOCIAL, 'key' => 'networks.twitter' ) ),
					"hashtags"  =>  pw_get_option( array( 'option_name' => PW_OPTIONS_SOCIAL, 'key' => 'networks.twitter_hashtags' ) ), //"twitter_user",
					"size"      =>  "small",
					"lang"      =>  "en",
					"dnt"       =>  "true",
					),
				),

			),

		);

	$settings = array_replace_recursive( $global_settings, $settings);
	return $settings;
}
add_filter( 'pw_social_widgets', 'pw_default_social_widget_settings' );




/*
  _____          _ _   _            
 |_   _|_      _(_) |_| |_ ___ _ __ 
   | | \ \ /\ / / | __| __/ _ \ '__|
   | |  \ V  V /| | |_| ||  __/ |   
   |_|   \_/\_/ |_|\__|\__\___|_|   
                                    
////// ----- TWITTER ----- //////*/                         



function pw_social_widget_twitter( $meta, $network ){
	// Outputs a Twitter button

	global $pw;

	// META VALUES
	$defaultMeta = array(
		'title'				=>	$pw['view']['title'],
		'url'				=>	$pw['view']['url'],
		'before_network'	=>	'',
		'after_network'		=>	'',
		);
	$meta = array_replace_recursive( $defaultMeta, $meta );

	// NETWORK SETTINGS
	$defaultNetwork = array(
		"widget"      		=>  "share",	// Options 'share' / 'follow'
		"include_script"	=>  true,
		"username"			=>	false,		// Required for widget:'follow'
		"settings"    =>  array(
			"via"       =>  "",
			"related"   =>  "",
			"hashtags"  =>  "",
			"size"      =>  "small",
			"lang"      =>  "en",
			"dnt"       =>  "true",
			),
		);
	$network = array_replace_recursive( $defaultNetwork, $network );

	// SEARCH & REPLACE
	$meta['before_network'] = str_replace('%network%', 'twitter', $meta['before_network']); 

	// SETUP CONSTANTS
	$amp = "&amp;";
	
	// INITIALIZE OUTPUT
	$output = "";

	// INCLUDE SCRIPT
	if( $network['include_script'] != false )
		$output .= "<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>";

	////////// SHARE BUTTON //////////
	if( $network['widget'] == 'share' ){

		// URL
		$url = ( isset( $meta['url'] ) ) ? $meta['url'] : null ;
		$url = ( !empty( $url ) ) ?
			" data-url=\"".$url."\"" :
			null ;

		// TWEET TEXT
		$text = ( isset($meta['title']) ) ?
			" data-text=\"".$meta['title']."\"" :
			null ;

		// VIA
		$via = ( isset($network['settings']['via']) ) ?
			" data-via=\"".$network['settings']['via']."\"" :
			null ;

		// RELATED
		$related = ( isset($network['settings']['related']) ) ?
			" data-related=\"".$network['settings']['related']."\"" :
			null ;

		// HASHTAGS
		$hashtags = ( isset($network['settings']['hashtags']) ) ?
			" data-hashtags=\"".$network['settings']['hashtags']."\"" :
			null ;

		// LANGUAGE
		$lang = ( isset($network['settings']['lang']) ) ?
			" data-lang=\"".$network['settings']['lang']."\"" :
			null ;

		// SIZE
		$size = ( isset($network['settings']['size']) ) ?
			" data-size=\"".$network['settings']['size']."\"" :
			null ;

		// DO NOT TAILOR
		$dnt = ( isset($network['settings']['dnt']) ) ?
			" data-dnt=\"".$network['settings']['dnt']."\"" :
			null ;

		// PARSE
		$output .= "<a href=\"https://twitter.com/share\" class=\"twitter-share-button\" ";
		$output .= $via;
		$output .= $related;
		$output .= $hashtags;
		$output .= $url;
		$output .= $text;
		$output .= $lang;
		$output .= $size;
		$output .= $dnt;
		$output .= "></a>";

	}

	return $meta['before_network'] . $output . $meta['after_network'];

}


function pw_twitter_follow_button( $vars ){

	$defaultVars = array(
		'username'	=>	false,
		'include_script'	=>	true,
		'show_count'		=>	false,
		'size'				=>	'small',
		);

	$vars = array_replace_recursive( $defaultVars, $vars );

	// If no username
	if( _get($vars,'username') == false )
		return false;

	// Init Output
	$output = '<a href="https://twitter.com/'.$vars['username'].'" ';
	$output .= 'class="twitter-follow-button" ';

	$output .= 'data-show-count="'.pw_bool_to_string( _get( $vars, 'show_count' ) ).'" ';
	$output .= 'data-size="'._get( $vars, 'size' ).'">';
	$output .= 'Follow @'.$vars['username'].'</a>';
	
	// Include Script
	if( _get( $vars, 'include_script' ) )
		$output .= "<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>";

	return $output;

}




/*
  _____              _                 _    
 |  ___|_ _  ___ ___| |__   ___   ___ | | __
 | |_ / _` |/ __/ _ \ '_ \ / _ \ / _ \| |/ /
 |  _| (_| | (_|  __/ |_) | (_) | (_) |   < 
 |_|  \__,_|\___\___|_.__/ \___/ \___/|_|\_\
                                            
///////// ------ FACEBOOK ------ /////////*/



function pw_social_widget_facebook( $meta, $widget_settings ){

	// META VALUES
	extract( $meta );
	/*	array(
			"title"				=>	"Reality Sandwich",
			"url"				=>	"http://realitysandwich.com", // get_permalink(),
			"before_network"	=>	"<div class=\"social_widget %network%\">",
			"after_network"		=>	"</div>",
		)
	*/

	// WIDGET SETTINGS
	extract( $widget_settings );
	/*	array(
			"layout"		=>	"button_count",
			"action"		=>	"like",
			"show_faces"	=>	"false",
			"share"			=>	"true",
			"width"			=>	"200",
			"height"		=>	"24",
			"colorscheme"	=> "light",
		)
	*/

	// SEARCH & REPLACE
	$before_network = str_replace('%network%', 'facebook', $before_network); 

	// SETUP CONSTANTS
	$amp = "&amp;";

	// INITIALIZE OUTPUT
	$output = "";

	////////// LIKE BUTTON //////////
	if( $widget == 'like-button' ){

		// URL
		$url = urlencode( $meta['url'] );

		// LAYOUT
		$layout = ( isset($settings['layout']) ) ?
			$amp."layout=".$settings['layout'] :
			$amp."layout=standard" ;

		// ACTION
		$action = ( isset($settings['action']) ) ?
			$amp."action=".$settings['action'] :
			$amp."action=like" ;

		// SHOW FACES
		$show_faces = ( isset($settings['show_faces']) ) ?
			$amp."show_faces=".$settings['show_faces'] :
			$amp."show_faces=false" ;

		// SHARE
		$share = ( isset($settings['share']) ) ?
			$amp."share=".$settings['share'] :
			$amp."share=true" ;

		// WIDTH
		$default_width = "120";
		$width_int = ( isset($settings['width']) ) ?
			$settings['width'] :
			$default_width ;
		$width = ( isset($settings['width']) ) ?
			$amp."width=".$settings['width'] :
			null;

		// HEIGHT
		$default_height = "24";
		$height_int = ( isset($settings['height']) ) ?
			$settings['height'] :
			$default_height ;
		$height = ( isset($settings['height']) ) ?
			$amp."height=".$settings['height'] :
			$amp."height=".$default_height ;

		// APP ID
		$appId = ( isset($appId) ) ?
			$amp."appId=".$appId :
			null;

		// ATTRIBUTES
		$attributes = 'scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:'.$height_int.'px; width:'.$width_int.'px;" allowTransparency="true"';

		// PARSE
		$output  = '<iframe src="';
		$output .= '//www.facebook.com/plugins/like.php?href=';
		$output .= $url;
		$output .= $layout;
		$output .= $action;
		$output .= $show_faces;
		$output .= $share;
		$output .= $width;
		$output .= $height;
		$output .= $width;
		$output .= $appId;
		$output .= '" ';
		$output .= $attributes;
		$output .= '></iframe>';
	}

	return $before_network . $output . $after_network;

}












?>