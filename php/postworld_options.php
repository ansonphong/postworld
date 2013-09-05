<?php

$pw_default_options = array(

	'roles' 			=> array(
		'Administrator' 	=> array(
			'vote_points'	=> 10,
			),
		'Editor' 			=> array(
			'vote_points'	=> 5,
			),
		'Author' 			=> array(
			'vote_points'	=> 2,
			),
		'Contributor' 		=> array(
			'vote_points'	=> 1,
			),
		),

	'points' => array(
		'post_types'		=> array('post'),
		'cache_interval'	=> 'fifteen_minutes',
		'cron_logs'			=> 0,
		),

	'rank' => array(
		'post_types'		=> array('post'),
		'cache_interval'	=> 'fifteen_minutes',
		'cron_logs'			=> 0,
		'equations'			=> array(
			'default'		=> array(
				'time_compression'	=>	0.5,
				'time_weight'		=>	1,
				'comments_weight'	=>	1,
				'points_weight'		=>	1,
				'fresh_period'		=>	1*$one_week,
				'fresh_multiplier'	=>	2,
				'archive_period'	=>	6*$one_month,
				'archive_multiplier'=>	0.2,
				'free_rank_score'	=>	100,
				'free_rank_period'	=>	3*$one_day,
				),
			),
		),

	'feeds' => array(
		'cache_feeds'		=> array(),
		'cache_interval'	=> 'fifteen_minutes',
		'cron_logs'			=> 0,
		),

	'cleanup' => array(
		'points'			=> array(
			'interval'		=> 'daily',
			),
		'cron_logs'			=> array(
			'interval'		=> 'weekly',
			),
		),

	);
    

?>