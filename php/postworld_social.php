<?php

/*
                      ____             _       _   __  __          _ _       
  _ ____      __  _  / ___|  ___   ___(_) __ _| | |  \/  | ___  __| (_) __ _ 
 | '_ \ \ /\ / / (_) \___ \ / _ \ / __| |/ _` | | | |\/| |/ _ \/ _` | |/ _` |
 | |_) \ V  V /   _   ___) | (_) | (__| | (_| | | | |  | |  __/ (_| | | (_| |
 | .__/ \_/\_/   (_) |____/ \___/ \___|_|\__,_|_| |_|  |_|\___|\__,_|_|\__,_|
 |_|                                                                         
/////////////// ---------- SOCIAL MEDIA FUNCTIONS ---------- ///////////////*/

// SNIPPETS
//include(locate_template('views//taxonomy-page-setup.php'));


function pw_social_widgets( $settings ){

	$output = "";
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



/*
  _____          _ _   _            
 |_   _|_      _(_) |_| |_ ___ _ __ 
   | | \ \ /\ / / | __| __/ _ \ '__|
   | |  \ V  V /| | |_| ||  __/ |   
   |_|   \_/\_/ |_|\__|\__\___|_|   
                                    
////// ----- TWITTER ----- //////*/                         



function pw_social_widget_twitter( $meta, $widget_settings ){

	// META VALUES
	extract( $meta );
	/*	array(
			"title"				=>	"Reality Sandwich",
			"url"				=>	"http://realitysandwich.com", // get_permalink(),
			"before_network"	=>	"<div class=\"social_widget %network%\">",
			"after_network"		=>	"</div>"
		)
	*/

	// WIDGET SETTINGS
	extract( $widget_settings );
	/*	array(
			"via"			=>	"realitysandwich",
			"related"		=>	"realitysandwich",
			"hashtags"		=>	"realitysandwich",
			"size"			=>	"large",
			"lang"			=>	"en",
			"dnt"			=>	"true",
		)
	*/


	// SEARCH & REPLACE
	$before_network = str_replace('%network%', 'twitter', $before_network); 

	// SETUP CONSTANTS
	$amp = "&amp;";
	
	// INITIALIZE OUTPUT
	$output = "";

	// INCLUDE SCRIPT
	if( $include_script != false )
		$output .= "<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>";

	////////// SHARE BUTTON //////////
	if( $widget == 'share' ){

		// URL
		$url_link = ( isset( $url ) ) ? $url : null ;
		$url = ( isset( $url ) ) ?
			" data-url=\"".$url."\"" :
			null ;

		// VIA
		$via = ( isset($settings['via']) ) ?
			" data-via=\"".$settings['via']."\"" :
			null ;

		// RELATED
		$related = ( isset($settings['related']) ) ?
			" data-related=\"".$settings['related']."\"" :
			null ;

		// HASHTAGS
		$hashtags = ( isset($settings['hashtags']) ) ?
			" data-hashtags=\"".$settings['hashtags']."\"" :
			null ;

		// LANGUAGE
		$lang = ( isset($settings['lang']) ) ?
			" data-lang=\"".$settings['lang']."\"" :
			null ;

		// SIZE
		$size = ( isset($settings['size']) ) ?
			" data-size=\"".$settings['size']."\"" :
			null ;

		// DO NOT TAILOR
		$dnt = ( isset($settings['dnt']) ) ?
			" data-dnt=\"".$settings['dnt']."\"" :
			null ;

		// PARSE
		$output .= "<a href=\"https://twitter.com/share\" class=\"twitter-share-button\" ";
		$output .= $via;
		$output .= $related;
		$output .= $hashtags;
		$output .= $url;
		$output .= $lang;
		$output .= $size;
		$output .= $dnt;
		$output .= "></a>";

	}

	return $before_network . $output . $after_network;

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