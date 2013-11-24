<?php

global $pw_settings;
$pw_settings = array(

	'language'	=>	array(
		'points'	=> array(
			'post'	=> array(
				'name'	=>					'Post Karma',
				),
			'comment'	=> array(
				'name'	=>					'Comment Karma',
				),
			'share'	=> array(
				'name'	=>					'Share Karma',
				'action'	=>				'Share',
				// Link
				'link_name'	=> 				'Share Link',
				'link_description'	=> 		'Use this link for email & social media, & receive 1 Share Karma point for each person who follows the link.',
				// Outgoing
				'outgoing_name' => 			'Outgoing Shares',
				'outgoing_description' => 	'Posts that you have shared',
				// Incoming
				'incoming_name'	=>			'Incoming Shares',
				'incoming_description'	=>	'Your posts that have been shared',
				// Descriptions
				'recent_description'	=>	'Most recent share',
				'total_description'		=>	'Total Share Karma',
				),
			),
		'community'	=>	array(
			'preface'	=>	'Be a Contributor to Reality Sandwich by posting a blog, link or event. All posts appear here in the Community section. Some posts may be selected by an editor to appear on the Home Page or Section pages.',
			),
		),

	'avatar'			=> array(
		'default'	=>	'/images/avatars/avatar-ajones-A.png'
		),

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
		'Default'	 		=> array(
			'vote_points'	=> 1,
			),
		),

	'points' => array(
		'post_types'		=> array('post','feature','blog','link','event'),
		'cache_interval'	=> 'fifteen_minutes',
		'cron_logs'			=> 0,
		),

	'rank' => array(
		'post_types'		=> array('post','feature','blog','link','event'),
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

	'formats'	=>	array(
		'post'	=>	array(
			'standard'	=>	array(),
			),
		),

	'views'		=>	array(
		'enable'	=>	"",
		'grid'		=>	"",
		),

	'buddypress'	=>	array(
		'avatar'	=>	array(
			'size'	=>	array(
				'full'		=>	'480',
				'thumb'		=>	'120',
				'original_max_width' => '640'
				),
			),
		),
	'post_views'	=> array('list', 'detail', 'grid', 'full', ),
	//'panel_ids' => array('feed_top','feed_search','feed_header'),
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


/*
///// BUDDYPRESS OPTIONS /////
$bp_avatar_full_size = $pw_settings['buddypress']['avatar']['size']['full'];
$bp_avatar_thumb_size = $pw_settings['buddypress']['avatar']['size']['thumb'];
$bp_avatar_original_max_width = $pw_settings['buddypress']['avatar']['size']['original_max_width'];

define( 'BP_AVATAR_ORIGINAL_MAX_WIDTH', $bp_avatar_original_max_width );
define( 'BP_AVATAR_THUMB_WIDTH', $bp_avatar_thumb_size ); //change this with your desired thumb width
define( 'BP_AVATAR_THUMB_HEIGHT', $bp_avatar_thumb_size ); //change this with your desired thumb height
define( 'BP_AVATAR_FULL_WIDTH', $bp_avatar_full_size ); //change this with your desired full size,weel I changed it to 260 <img src="http://buddydev.com/wp-includes/images/smilies/icon_smile.gif" alt=":)" class="wp-smiley"> 
define( 'BP_AVATAR_FULL_HEIGHT', $bp_avatar_full_size ); //change this to default height for full avatar
//define ( 'BP_AVATAR_DEFAULT', $img_url );
//define ( 'BP_AVATAR_DEFAULT_THUMB', $img_url );
*/


?>