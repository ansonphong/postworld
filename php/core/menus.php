<?php
class PW_Menu_With_Description extends Walker_Nav_Menu {

    public function getTemplatePath() {
        return $this->template_path; 
    }

    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {

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

        // For Development : print the Item Properties
        //$output .= json_encode( $item );

        ///// LINK META /////
        // Parse the Meta Data for the Link
        $link_meta = array();
        $link_meta['label'] = $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;

        $link_meta['description'] = ( !empty($item->description) ) ?
            $item->description : false;

        ///// LINK ATTRIBUTES /////
        // Parse the Attributes for the Link
        $link_attr = array();
        $link_attr['title'] = !empty( $item->attr_title ) ?
            esc_attr( $item->attr_title ) : false;

        $link_attr['target'] = !empty( $item->target ) ?
            esc_attr( $item->target ) : false;
        
        $link_attr['rel'] = !empty( $item->xfn ) ?
            esc_attr( $item->xfn ) : false;

        $link_attr['href'] = !empty( $item->url ) ?
            esc_attr( $item->url ) : false;

        // Init Item Output
        $output .= $indent;
        $output .= '<li id="menu-item-'.$item->ID.'" '.$class_names.' >';
        $output .= $args->before;
        
        // Print the template body
        ob_start();
        include $args->walker_vars['item_template_path'];
        $item_output = ob_get_contents();
        ob_end_clean();

        $output .= $args->after;
        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    
    }
}