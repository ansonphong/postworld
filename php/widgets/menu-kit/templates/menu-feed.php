<?php
$live_feed_vars = array(
	'feed' => array(
		'posts'	=>	pw_get_menu_posts( $OPTIONS['menu_feed_id'], 'preview' ),
		'view' => array(
			'current'	=>	$OPTIONS['menu_feed_view'],
			),
		),
	);
echo pw_live_feed( $live_feed_vars );
?>
