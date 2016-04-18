<?php

function pw_sanitize_hex_color_value( $value ){
	$value = sanitize_hex_color( $value );
	pw_reset_less_php_cache();
	return $value;
}

/**
 * Reset the LESS cache by updating updated time
 * on a ghost file. Include this ghost file in any LESS file.
 * Call this function after changing dynamic style variables.
 */
function pw_reset_less_php_cache(){
	//global $pwGlobalsJsFile;
	$ghost_less_file = POSTWORLD_PATH .'/less/ghost.less';
	$file = fopen( $ghost_less_file ,"w" );
	fwrite($file,"// Reset PHP LESS Cache"); // . date("Y-m-d H:i:s"));
	fclose($file);
	//if( file_exists( $pwGlobalsJsFile ) )
	//	chmod($pwGlobalsJsFile, 0755);
	return true;
}

// Prepare URL for Less Variable
function pw_less_prepare_url( $url ){
	if( function_exists( 'pw_wrap_quotes' ) )  
	   return pw_wrap_quotes( $url ) ;
   else
	return $url;
}

// pass variables into all .less files
add_filter( 'less_vars', 'pw_less_vars', 10, 2 );
function pw_less_vars( $vars, $handle ) {

	///// CACHE /////
	global $phpLessVarsCache;
	if( is_array( $phpLessVarsCache ) )
		return $phpLessVarsCache;

	////////// IMPORT STYLES //////////
	$pwStyles = pw_get_option( array( 'option_name' => PW_OPTIONS_STYLES ) );
	//pw_log( 'pwStyles', $pwStyles );

	///// Bootstrap Vars /////
	//$vars['body-bg'] = $pwStyles['element']['body']['background-color'];

	// RECENTLY HIDDEN    
	//$vars['grid-gutter-width'] = $pwStyles['var']['bootstrap']['grid-gutter-width'];

	///// Infinite Style Vars /////
	// Systematically define all variables
	// Value of : "elements -> h1 -> color" >> becomes LESS variable >> '@h1-color'

	///// TYPES /////
	foreach( $pwStyles as $typeSlug => $typeObject ){

		foreach( $typeObject as $elementSlug => $elementObject ){
			///// PROPERTIES /////
			foreach( $elementObject as $propertySlug => $propertyValue ){
				// Use the variable name
				$vars[$propertySlug] = $pwStyles[$typeSlug][$elementSlug][$propertySlug];
			}
		}

	}

	///// Directory Paths /////
	$vars['template-url'] = pw_less_prepare_url( get_template_directory_uri() ); 
	$vars['theme-url'] = $vars['template-url'];
	$vars['postworld-url'] = pw_less_prepare_url( pw_config('paths.postworld.url') );

	///// CACHE /////
	$phpLessVarsCache = $vars;

   // pw_log( $vars );

	return $vars;
}


/**
 * IN DEVELOPMENT
 */
function pw_register_style_set( $vars = array() ){
	$defaultVars = array(
		'name' => 'Demo A',
		'id' => 'demo-a',
		'styles' => array(

			),        
		);
}


/**
 * Filters style options, and sets defaults
 */
/*
//add_filter( PW_OPTIONS_STYLES, 'pw_inject_default_styles', 5 );
function pw_inject_default_styles_DEV( $styles ){
	// Get Default Styles
	$defaults = apply_filters( PW_STYLES_DEFAULT, array() );
	if( empty( $defaults ) )
		return $styles;
	foreach( $defaults as $type_key => $type_val ){
		foreach( $type_val as $elem_key => $elem_val ){
			foreach( $elem_val as $prop_key => $prop_val ){
				$key_path = $type_key . '.' . $elem_key . '.' . $prop_key;
				$style_val = _get( $styles, $key_path );
				if( empty( $style_val ) ){
					$styles = _set( $styles, $key_path, $prop_val );
				}
			}
		}
	}
	return $styles;
}
*/
