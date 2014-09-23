<?php

///// GET_POST_DATA : MODELLING ///// 
// INPUT
$fields = array(
	'post_title', 'post_content', 'post_type', 'link_format', 'post_class',
	'image(original)', 'image(thumbnail)', 'image(topview,250,200,0)',
	'author(display_name,profile_url,posts_url,user_nicename)',
	'avatar(small,48)',

	'taxonomy(category)',	// <<< TODAY
	'taxonomy(topic)',		// <<< TODAY
	);

// OUTPUT
$post_data = array(
	'title'		=> 'Hello World!',
	'content'	=> 'This is the post body.',
	'type'		=> 'post',
	'format'	=> 'standard',
	'class'		=> 'editorial_blog',
	'permalink'	=>	'http://...',
	'taxonomy'	=>	array(
		'category'	=>	'...',
		'topic'		=>	'...',
		),

	'avatar'		=> array(
		'thumbnail'	=> array(
			'url'	=> '',
			'width'	=> '',
			'height'=> ''
			),
		),
	'author'		=> array(
		'username'		=>	'',
		'nicename'		=>	'',
		'twitter'		=>	'',
		'email'			=>	'',
		'first_name'	=>	'',
		'last_name'		=>	'',
		'full_name'		=>	'',
		'display_name'	=>	'',
		'nickname'		=>	'',
		'role'			=>	'',
		'posts_link'	=> 'the_author_posts_link()',
		'...'			=>	'',
		),
	'images'	=> array(
		'full'	=>	array(
			'url'	=>	'http://full.jpg',
			'width'	=>	960,
			'height'=>	540
			),
		'thumbnail'	=>	array(
			'url'	=>	'http://thumbnail.jpg',
			'width'	=>	150,
			'height'=>	150
			),
		'topview'	=>	array(
			'url'	=>	'http://topview.jpg',
			'width'	=>	250,
			'height'=>	200
			),
		),
	);



?>