<?php

function pw_oembed_get( $vars = array() ){
	// Adds some variables to wp_oembed_get

	$defaultVars = array(
		'url'		=>	'',
		'autoplay'	=>	false,

		'youtube'	=>	array(
			'theme'		=>	'dark',
			'color'		=>	'red',	// Options: 'red' / 'white'
			'controls'	=>	1,		// Options : 1 / 2 / 3
			),

		'vimeo'	=>	array(	
			'color'		=>	'00adef', // Not yet implimented
			/*
			'badge'		=>	1,
			'byline'	=>	1,
			'loop'		=>	0,
			'portrait'	=>	1,
			'title'		=>	1,
			*/
			),
		);

	// Filter the default variables
	$defaultVars = apply_filters( 'pw_oembed_get', $defaultVars );

	// Replace with provided variables
	$vars = array_replace_recursive($defaultVars, $vars);

	// GET OEMBED
	$oEmbed = wp_oembed_get( $vars['url'] );

	//pw_log( json_encode($vars) );

	/// YOUTUBE ///
	if (strpos($oEmbed, 'youtube') !== false) {
		// AUTOPLAY
		if( $vars['autoplay'] == true )
			$oEmbed = pwDomElAttrAppend( $oEmbed, 'iframe', 'src', '&autoplay=1' );
		
		// THEME
		$oEmbed = pwDomElAttrAppend( $oEmbed, 'iframe', 'src', '&theme=' . _get($vars,'youtube.theme') );

		// COLOR
		$oEmbed = pwDomElAttrAppend( $oEmbed, 'iframe', 'src', '&color=' . _get($vars,'youtube.color') );
	
		// CONTROLS
		$oEmbed = pwDomElAttrAppend( $oEmbed, 'iframe', 'src', '&controls=' . _get($vars,'youtube.controls') );

	}

	/// VIMEO ///
	if (strpos($oEmbed, 'vimeo') !== false) {
		// AUTOPLAY
	  	if( $vars['autoplay'] == true ){
	   		$oEmbed = pwDomElAttrAppend( $oEmbed, 'iframe', 'src', '?autoplay=1' );
		}
	}

	/// SOUNDCLOUD ///
	if (strpos($oEmbed, 'soundcloud') !== false) {
		// AUTOPLAY
	  	if( $vars['autoplay'] == true ){
	   		//$oEmbed = str_replace( 'auto_play=false', 'auto_play=true', $oEmbed );
			$oEmbed = pwDomElAttrAppend( $oEmbed, 'iframe', 'src', '&auto_play=true' );
		}
	}

	return $oEmbed;

}



function pwDomElAttrAppend( $element, $tag, $attribute, $append ){
	// Postworld DOM Element Attribute Append
	// Takes a single element HTML DOM string
	// And appends a string to the specified attribute value

	// Init new DOM Class
	$dom = new DOMDocument();
	// Load the element
	$dom->loadHTML( $element );
	// Get the attribute to transform
	$attr_src = $dom->getElementsByTagName( $tag )->item(0)->getAttribute( $attribute );
	// Append the values
	$attr_src = $attr_src . $append;
	// Insert the value back into the element
	$dom->getElementsByTagName( $tag )->item(0)->setAttribute($attribute, $attr_src);
	// Return the element as a string
	return $dom->saveHTML();

}

?>