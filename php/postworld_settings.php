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
		'edit_post'	=>	array(
			'post_title'	=>	"Titles limited to 10-100 characters.",
			'thumbnail'		=>	"Images should be at least 480 pixels wide and less than 1MB.",
			'link_url'		=>	"Add URL here to embed video, audio, or webpage link at top of page. System recognizes links from YouTube, Vimeo, and Soundcloud, among other services.",
			'post_status'	=>	"Save as Draft to preview and revise. Save as Published to share with others.",
			
			'post_types'	=>	array(
				'feature'	=>	array(
					'overview'	=>	"Feature articles appear on the Reality Sandwich home page, as well as on Section and Topic pages. All Feature articles are reviewed by editors, which may take several days. Authors retain all rights to their material. For more about posting on Reality Sandwich, read the FAQ.",
					'post_status'	=>	"Save as Draft to preview and revise. Save as Pending when finished & to be reviewed by an RS editor.",
					),
				'blog'	=>	array(
					'overview'	=>	"Blog posts by Authors appear on the Reality Sandwich home page when published. Contributors (registered users) blogs appear in the Community area. Editors may select posts from the Community area to publish on the Home Page. Authors retain all rights to their material. For more about posting on Reality Sandwich, read the FAQ."
					),
				'event'	=>	array(
					'overview'	=>	"Event posts by Authors appear on the Reality Sandwich home page, as well as on the Community Calendar. Events posted by Contributors (registered users) appear in the Community area. Editors may select events from the Community area to present on the Home Page and on Section pages. Authors retain all rights to their material. For more about posting on Reality Sandwich, read the FAQ."
					),
				'link'	=>	array(
					'overview'	=>	"Paste a webpage link into the Link URL field and it will automatically create a post for you. Videos from YouTube and Vimeo, and audio from Soundcloud, is automatically embedded. Links posted by Authors appear on the Reality Sandwich home page. Links posted by Contributors (registered users) appear in the Community area. Editors may select events from the Community area to present on the Home Page and on Section pages. For more about posting on Reality Sandwich, read How To Post."
					),
				),

			'taxonomy'	=>	array(
				'topic'	=>	"Choosing a topic and sub-topic is required."
				),
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