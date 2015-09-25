<?php

// EXTRACT SETTINGS FROM ADMIN & COMPRESS SETTINGS INTO ARRAY
$OPTIONS['show_parent_pages'] = apply_filters( 'widget_menu-show_parent_pages', $OPTIONS['show_parent_pages'] );
$OPTIONS['show_sibling_pages'] = apply_filters( 'widget_menu-show_sibling_pages', $OPTIONS['show_sibling_pages'] );
$OPTIONS['show_child_pages'] = apply_filters( 'widget_menu-show_child_pages', $OPTIONS['show_child_pages'] );

// RENDER MENU KIT PAGES
menu_kit_pages( $OPTIONS );

?>