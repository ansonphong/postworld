<?php
add_action( 'wp_enqueue_scripts', 'i_include_styles' );
function i_include_styles(){
	// BOOSTRAP LESS
	wp_enqueue_style( 'bootstrap-less', get_infinite_directory_uri() . '/packages/bootstrap/less/bootstrap.less' );
    //wp_enqueue_style( 'bootstrap-cdn', '//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css' );

	// INFINITE LESS
	wp_enqueue_style( 'infinite-style-less', get_infinite_directory_uri() . '/less/style.less' );
}


add_action( 'admin_enqueue_scripts', 'i_include_admin_styles' );
function i_include_admin_styles(){
	// BOOSTRAP LESS
	//wp_enqueue_style( 'bootstrap-less', get_stylesheet_directory_uri() . '/packages/bootstrap/less/bootstrap.less' );

	// MAIN LESS
	wp_enqueue_style( 'infinite-style-admin-less', get_infinite_directory_uri() . '/admin/less/style.less' );

	// FONT AWESOME
	//wp_enqueue_style( 'font-awesome', get_infinite_directory_uri() . '/packages/Font-Awesome/css/font-awesome.min.css' );

}

// Prepare URL for Less Variable
function i_less_prepare_url( $url ){
    if( function_exists( 'pw_wrap_quotes' ) )  
       return pw_wrap_quotes( $url ) ;
   else
    return $url;
}

// pass variables into all .less files
add_filter( 'less_vars', 'my_less_vars', 10, 2 );
function my_less_vars( $vars, $handle ) {

	////////// IMPORT STYLES //////////
    $i_styles = i_get_styles();

    //echo "<pre>". json_encode( $i_styles, JSON_PRETTY_PRINT ) ."</pre>" ;

	///// Bootstrap Vars /////
    //$vars['body-bg'] = $i_styles['element']['body']['background-color'];
    $vars['grid-gutter-width'] = $i_styles['var']['bootstrap']['grid-gutter-width'];

    ///// Directory Paths /////
    global $i_paths;
    $vars['infinite-theme'] = i_less_prepare_url( $i_paths['infinite']['url'] );
    $vars['child-theme'] = i_less_prepare_url( $i_paths['child_theme']['url'] ); 

    $vars['i-templates-override'] = i_less_prepare_url( $i_paths['templates']['url']['override'] );
    $vars['i-templates-default'] = i_less_prepare_url( $i_paths['templates']['url']['default'] );

    ///// Infinite Style Vars /////
    // Systematically define all variables
    // Value of : "elements -> h1 -> color" >> becomes LESS variable >> '@h1-color'

    ///// TYPES /////
    foreach( $i_styles as $typeSlug => $typeObject ){
        ///// ELEMENTS & CLASSES /////
        if( $typeSlug == 'element' || $typeSlug == 'class' ){
            foreach( $typeObject as $elementSlug => $elementObject ){
                ///// PROPERTIES /////
                foreach( $elementObject as $propertySlug => $propertyValue ){
                    // Define the variable name
                    $property_var = $elementSlug . '-' . $propertySlug;
                    $property_var = strtolower( str_replace( ':', '-', $property_var ) );
                    $vars[$property_var] = $i_styles[$typeSlug][$elementSlug][$propertySlug];
                }
            }
        }
        ///// VARIABLES /////
        if( $typeSlug == 'var' ){
            foreach( $typeObject as $elementSlug => $elementObject ){
                ///// PROPERTIES /////
                foreach( $elementObject as $propertySlug => $propertyValue ){
                    // Use the variable name
                    $vars[$propertySlug] = $i_styles[$typeSlug][$elementSlug][$propertySlug];
                }
            }
        }
    }
    
    //echo json_encode($vars);

    return $vars;
}



?>