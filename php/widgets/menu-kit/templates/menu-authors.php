<?php

$AUTHORS_HIDE_EMPTY =	apply_filters( 'widget_menu-authors_hide_empty', $OPTIONS['authors_hide_empty'] );
$AUTHORS_SHOW_ADMINS = 	apply_filters( 'widget_menu-authors_show_admins', $OPTIONS['authors_show_admins'] );
$AUTHORS_AVATAR_SIZE = 	apply_filters( 'widget_menu-authors_avatar_size', $OPTIONS['authors_avatar_size'] );
$AUTHORS_ROLE = 		apply_filters( 'widget_menu-authors_role', $OPTIONS['authors_role'] );
$AUTHORS_ORDER_BY = 	apply_filters( 'widget_menu-authors_order_by', $OPTIONS['authors_order_by'] );
$AUTHORS_ORDER = 		apply_filters( 'widget_menu-authors_order', $OPTIONS['authors_order'] );

$OPTIONS = array(
	'AUTHORS_HIDE_EMPTY' => $AUTHORS_HIDE_EMPTY,
	'AUTHORS_SHOW_ADMINS' => $AUTHORS_SHOW_ADMINS,
	'AUTHORS_AVATAR_SIZE' => $AUTHORS_AVATAR_SIZE,
	'AUTHORS_ROLE' => $AUTHORS_ROLE,
	'AUTHORS_ORDER_BY' => $AUTHORS_ORDER_BY,
	'AUTHORS_ORDER' => $AUTHORS_ORDER,
	);


menu_kit_authors($OPTIONS);

?>