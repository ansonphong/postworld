<?php
/*__  __                    _  ___ _   
 |  \/  | ___ _ __  _   _  | |/ (_) |_ 
 | |\/| |/ _ \ '_ \| | | | | ' /| | __|
 | |  | |  __/ | | | |_| | | . \| | |_ 
 |_|  |_|\___|_| |_|\__,_| |_|\_\_|\__|

////////////// MENU KIT //////////////*/

// MODULES
include 'menu-kit-pages.php';
include 'menu-kit-categories.php';
include 'menu-kit-authors.php';
include 'menu-kit-custom-menu.php';
include 'menu-kit-widget.php';

///// ADD CLASSES TO SELECTED MENU ITEMS /////
add_filter('nav_menu_css_class' , 'special_nav_class' , 10 , 2);
function special_nav_class($classes, $item){
     if( in_array('current-menu-item', $classes) ){
             $classes[] = 'selected ';
     }
     return $classes;
}

?>