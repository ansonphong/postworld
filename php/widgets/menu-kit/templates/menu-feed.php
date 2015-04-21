<?php

//$feed_outline = pw_get_menu_posts( $OPTIONS['menu_feed_id'], 'ids' );
$posts = pw_get_menu_posts( $OPTIONS['menu_feed_id'], 'preview' );

$live_feed_vars = array(
	'feed' => array(
		//'feed_outline'	=>	$feed_outline,
		//'preload'		=>	count($feed_outline),	// Preload all posts
		'posts'			=>	$posts,
		//'fields'		=>	'preview',
		'view' => array(
			'current'	=>	$OPTIONS['menu_feed_view'],
			),
		),
	);
echo pw_feed( $live_feed_vars );
?>