<?php
// EXTRACT SETTINGS FROM ADMIN & COMPRESS SETTINGS INTO ARRAY

$TAXONOMY = apply_filters( 'widget_menu-taxonomy', $OPTIONS['taxonomy'] );
$POST_TYPE = apply_filters( 'widget_menu-post_type', $OPTIONS['post_type'] );

$TAXONOMY_LAYOUT = apply_filters( 'widget_menu-taxonomy_layout', $OPTIONS['taxonomy_layout'] );

$TAXONOMY_HIERARCHICAL = apply_filters( 'widget_menu-taxonomy_hierarchical', $OPTIONS['taxonomy_hierarchical'] );
$TAXONOMY_HIDE_EMPTY = apply_filters( 'widget_menu-taxonomy_hide_empty', $OPTIONS['taxonomy_hide_empty'] );

$CONTAINER = "menu-taxonomy-".$TAXONOMY_LAYOUT;

$OPTIONS = array(
	'POST_TYPE'		=> $POST_TYPE,
	'TAXONOMY'  	=> $TAXONOMY,
	'CONTAINER' 	=> '#'.$CONTAINER,
	'ITEM_CLASS'	=> 'menu-item',
	'POST_STATUS'	=> 'publish',
	'HIDE_EMPTY'	=> $TAXONOMY_HIDE_EMPTY,
	'HIERARCHICAL' 	=> $TAXONOMY_HIERARCHICAL,
	'DEPTH' 		=> 5,
	'SHOW_COUNT'	=> 0,
	'EXCLUDE_'		=> null,
	'NUMBER'		=> null,
	'TITLE'			=> '',
	'STYLE'			=> 'list',
	'LAYOUT'		=> $TAXONOMY_LAYOUT
);


// RENDER MENU KIT PAGES

echo "<ul id='".$CONTAINER."'>";
menu_kit_categories($OPTIONS);
echo "</ul>";

?>