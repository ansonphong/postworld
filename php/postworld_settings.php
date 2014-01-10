<?php

global $pw_settings;
$pw_settings = array(

	'points' => array(
		'post_types'		=> array('feature','blog','link','event','announcement'),
		'cache_interval'	=> 'fifteen_minutes',
		'cron_logs'			=> 0,
		),

	'rank' => array(
		'post_types'		=> array('feature','blog','link','event','announcement'),
		'cache_interval'	=> 'fifteen_minutes',
		'cron_logs'			=> 0,
		'equations'			=> array(
			'default'		=> array(
				'time_compression'	=>	0.5,
				'time_weight'		=>	1,
				'comments_weight'	=>	1,
				'points_weight'		=>	1,
				'fresh_period'		=>	1*$ONE_WEEK,
				'fresh_multiplier'	=>	2,
				'archive_period'	=>	6*$ONE_MONTH,
				'archive_multiplier'=>	0.2,
				'free_rank_points'	=>	100,
				'free_rank_period'	=>	3*$ONE_DAY,
				),
			'rsv2'		=> array(
				'time_compression'	=>	0.5,
				'time_weight'		=>	2,
				'comments_weight'	=>	0.05,
				'points_weight'		=>	1.5,
				'fresh_period'		=>	2*$ONE_WEEK,
				'fresh_multiplier'	=>	6,
				'archive_period'	=>	2*$ONE_MONTH,
				'archive_multiplier'=>	0.2,
				'free_rank_points'	=>	333,
				'free_rank_period'	=>	8*$ONE_MONTH,
				),
			),
		),

	'feeds' => array(
		'cache_feeds'		=> array(),
		'cache_interval'	=> 'fifteen_minutes',
		'cron_logs'			=> 0,
		),

	'views'	=> array(
		'post_types'	=>	array(),
		'stats_page'	=>	0,
		'description'	=>	"Views track which posts that you've already seen.",
		'tracker'	=>	array(
			'bottom'	=>	1000,	// How many pixels from the bottom
			'time'		=>	60,		// How many seconds after page load
			),
		'labels'		=>	array(
			'name'			=>	"Views",
			'singular_name'	=>	"View",
			'not_viewed'	=>	"View this",
			'has_viewed'	=>	"You have already viewed this",
			),
		),

	'shares'	=> array(
		'post_types'	=>	array(),
		'stats_page'	=>	0,
		'description'	=>	"Views track which posts that you've already seen.",
		'tracker'	=>	array(
			'ip_history'	=>	100,	// How many unique IP addresses before re-count
			),
		'labels'		=>	array(
			'name'			=>	"Views",
			'singular_name'	=>	"View",
			'not_viewed'	=>	"View this",
			'has_viewed'	=>	"You have already viewed this",
			),
		),

	'cleanup' => array(
		'points'			=> array(
			'interval'		=> 'daily',
			),
		'cron_logs'			=> array(
			'interval'		=> 'weekly',
			),
		),

	'classes'	=>	array(
		'post_types'	=>	array(),
		'data'	=>	array(
			'author'	=>	array(
				'name'			=>	"Blog",
				'description'	=>	"Main Blog",
				'roles'			=>	array('Administrator', 'Editor', 'Author'),
				),
			'contributor'	=>	array(
				'name'			=>	"Contributer",
				'description'	=>	"Features for community members.",
				'roles'			=>	array('Contributor'),
				),
			),
		),

	// DELETE THIS (AND TEST)
	'buddypress'	=>	array(
		'avatar'	=>	array(
			'size'	=>	array(
				'full'		=>	'480',
				'thumb'		=>	'120',
				'original_max_width' => '640'
				),
			),
		),
	// END DELETE THIS

	'post_views'	=> array('micro', 'compact', 'list', 'detail', 'grid', 'full' ),

	'template_paths' =>array(
	
		'default_posts_template_abs_path' => ABSPATH . "wp-content/plugins/postworld/templates/posts/" ,
		'override_posts_template_abs_path' => get_template_directory()."/postworld/templates/posts/",
		'default_panel_template_abs_path' => ABSPATH . "wp-content/plugins/postworld/templates/panels/" ,
		'override_panel_template_abs_path' => get_template_directory()."/postworld/templates/panels/",
		'default_comment_template_abs_path' => ABSPATH . "wp-content/plugins/postworld/templates/comments/" ,
		'override_comment_template_abs_path' => get_template_directory()."/postworld/templates/comments/",
		
		//urls
		'default_posts_template_url' => plugins_url()."/postworld/templates/posts/",
		'override_posts_template_url' => get_template_directory_uri()."/postworld/templates/posts/",			 
		'default_panel_template_url' => plugins_url()."/postworld/templates/panels/",
		'override_panel_template_url' => get_template_directory_uri()."/postworld/templates/panels/",
		'default_comment_template_url' => plugins_url()."/postworld/templates/comments/",
		'override_comment_template_url' => get_template_directory_uri()."/postworld/templates/comments/",
	),

	);


?>