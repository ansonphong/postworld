<?php
class Menu_With_Description extends Walker_Nav_Menu {

    public function getTemplatePath() {
        return $this->template_path; 
    }

    function start_el(&$output, $item, $depth, $args) {

        global $wp_query;

        // Add Tabs
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
        
        // Setup Class Names
        $class_names = $value = '';

        // Localize Item Classes
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;

        // Filter Classes
        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
        
        // Contextualize Classes as HTML Attribute
        $class_names = ' class="' . esc_attr( $class_names ) . '"';

        // Define the label in the link
        $link_label = $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;

        // For Dev, check out the Item Properties
        //$output .= json_encode( $item );

        // Parse the Attributes for the Link
        $link_attributes =  ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) .'"' : '';
        $link_attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) .'"' : '';
        $link_attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) .'"' : '';
        $link_attributes .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) .'"' : '';

        // Define Description
        $description = ( !empty($item->description) ) ?
            $item->description : false;

        // Init Item Output
        $output .= $indent;
        $output .= '<li id="menu-item-'.$item->ID.'" '.$class_names.' >';
        $output .= $args->before;

        // Localize the template ID (neccessary?)
        $menu_template = $args->walker_vars['menu_template'];

        // Print the template body
        ob_start();
        include $args->walker_vars['template_path'];
        $item_output = ob_get_contents();
        ob_end_clean();

        $output .= $args->after;
        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    
    }
}



function pw_menu_kit_get_templates(
    $vars = array(
        "type" => "custom_menu"
        )
    ){

    $templates = pw_get_templates(
        array(
            'subdirs' => array( 'menu-kit' ),
            'ext' => 'php',
            'path_type' =>  'dir',
            )
        );
    $menu_templates = $templates['menus'];


}



?>