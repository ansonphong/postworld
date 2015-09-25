<?php

// Prepare URL for Less Variable
function i_less_prepare_url( $url ){
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
    global $i_paths;
    $vars['infinite-theme'] = i_less_prepare_url( $i_paths['infinite']['url'] );
    $vars['child-theme'] = i_less_prepare_url( $i_paths['child_theme']['url'] ); 

    $vars['i-templates-override'] = i_less_prepare_url( $i_paths['templates']['url']['override'] );
    $vars['i-templates-default'] = i_less_prepare_url( $i_paths['templates']['url']['default'] );

    ///// CACHE /////
    $phpLessVarsCache = $vars;

   // pw_log( $vars );

    return $vars;
}



?>