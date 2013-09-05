
<?php

$pw_default_options = array(

	'points' => array(
		'post_types'		=> array('post'),
		'cache_interval'	=> 'fifteen_minutes',
		'cron_logs'			=> 0,
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
			)
		),

	'rank' => array(
		'post_types'		=> array('post'),
		'cache_interval'	=> 'fifteen_minutes',
		'cron_logs'			=> 0,
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