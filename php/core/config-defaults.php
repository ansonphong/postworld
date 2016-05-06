<?php
/**
 * Set the default values for the Postworld Configuration.
 */
add_action( POSTWORLD_CONFIG, 'postworld_config_defaults', 1 );
function postworld_config_defaults(){

	extract( pw_time_units() );

	$GLOBALS[ POSTWORLD_CONFIG ]['modules'] = array(
		'required'	=>	array(),
		'supported'	=>	array(),
		'settings' => array(),
		);

	pw_config_required_modules(array(
		'site',
		));

	pw_config_supported_modules(array(
		'site',
		'layouts',
		'sidebars',
		'styles',
		'social',
		'feeds',
		'backgrounds',
		'iconsets',
		'taxonomy_meta',
		'shortcodes',
		'devices',
		'post_cache',
		'layout_cache',
		'colors',
		'widgets',
		'comments',
		'visual_composer',
		));

	$GLOBALS[ POSTWORLD_CONFIG ]['templates'] = array(
		'dir'	=>	array(
			'default'	=>	get_template_directory() . '/postworld/templates/' ,
			'override'	=>	get_stylesheet_directory() . '/views/',
			),
		'url'	=>	array(
			'default'	=>	get_template_directory_uri() . '/postworld/templates/',
			'override'	=>	get_stylesheet_directory_uri() . '/views/',
			),
		);

	$GLOBALS[ POSTWORLD_CONFIG ]['post_views'] = array(
		'supported' => array('list','modal','grid','full'),
		'options' => array(
			'feeds' => array('list','grid','full'),
			'related_posts' => array('list'),
			),
		'meta'	=>	array(
			'list' => array(
				'label' => 'List',
				),
			'detail' => array(
				'label' => 'Detail',
				),
			'grid' => array(
				'label' => 'Grid',
				),
			'full' => array(
				'label' => 'Single',
				),
			'modal' => array(
				'label' => 'Modal',
				)
			),
		);

	$GLOBALS[ POSTWORLD_CONFIG ]['user_meta'] = array(
			'pw_avatar'	=>	false,
			);

	$GLOBALS[ POSTWORLD_CONFIG ]['edit_post'] = array(
		'post'	=>	array(
			'url'	=>	'/post/',
			'new'	=>	array(
				'default'	=>	array(
					'post_type'		=>	'post',
					'post_status'	=>	'publish',
					'post_class'	=>	'contributor',
					'link_format'	=>	'standard',
					),
				),
			),
		);


	$GLOBALS[ POSTWORLD_CONFIG ]['post_options'] = array(
		'year'	=>	array( '2007', '2008', '2009', '2010', '2011', '2012', '2013', '2014', '2015', '2016' ),
		'taxonomies'	=> array( 'category', 'post_tag' ),
		'taxonomy_outline'	=>	array(
			'category' => array(
				'max_depth' => 2,
				'fields' => array(
					'term_id',
					'name',
					'slug'
					),
				//'filter' => 'label_group'
				),
			'post_tag' => array(
				'max_depth' => 1,
				'fields' => array(
					'term_id',
					'name',
					'slug'
					),
				),
			),

		'link_format'	=>	array(
			'standard'	=> 'Standard',
			'link'		=> 'Link',
			'video'		=> 'Video',
			'audio'		=> 'Audio',
			),

		'link_format_defaults'	=>	array(
			'none'	=>	'standard',
			'link'	=>	'link'
			),

		'link_format_meta'	=>	array(
				array(
					'name'		=>	'',
					'slug'		=>	'standard',
					'domains'	=>	array(),
					'icon'		=> 'pwi-circle-thick'
				),
				array(
					'name'		=>	'Link',
					'slug'		=>	'link',
					'domains'	=>	array(),
					'icon'		=>	'pwi-link'
				),
				array(
					'name'		=>	'Video',
					'slug'		=>	'video',
					'domains'	=>	array('youtube.com/','youtu.be/','vimeo.com/','hulu.com/','ted.com/','sapo.pt/','dailymotion.com','blip.tv/','ustream.tv/',),
					'icon'		=>	'pwi-play-fill'
				),
				array(
					'name'		=>	'Audio',
					'slug'		=>	'audio',
					'domains'	=>	array('soundcloud.com/','mixcloud.com/','official.fm/','shoudio.com/','rdio.com/'),
					'icon'		=>	'pwi-headphones'
				),
			),

		'post_class'	=>	array(
			// By Post Types
			'event-post' => array(
				'participant'	=> 'Participant',
				'organizer'		=> 'Organizer',
				),
			'announcement' => array(
				'movement'		=> 'On Movement',
				'events'		=> 'On Events & Movement',
				),
			),

		'post_status'	=>	array(
			'publish' => 'Published',
			'draft' => 'Draft',
			'pending' => 'Pending'
			),

		'role_post_type_status_access'	=>	array(
			'administrator' => array(
				'post'		=> array('publish','draft','pending'),
				),
			'editor' => array(
				'post'		=> array('publish','draft','pending'),
				),
			'author' => array(
				'post'		=> array('draft','pending'),
				),
			'contributor' => array(
				'post'		=> array('draft','pending'),
				),
			),

		);


	$GLOBALS[ POSTWORLD_CONFIG ]['rank'] = array(
		'post_types'		=> array('post'),
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
		);


	$GLOBALS[ POSTWORLD_CONFIG ]['points'] = array(
		'post_types'		=> array('post'),
		'cache_interval'	=> 'fifteen_minutes',
		'cron_logs'			=> 0,
		);


	$GLOBALS[ POSTWORLD_CONFIG ]['shares'] = array(
		'post_types'	=>	array('post'),
		'tracker'	=>	array(
			'ip_history'	=>	100,	// How many unique IP addresses before re-count
			),
		);

	$GLOBALS[ POSTWORLD_CONFIG ]['embedly'] = array(
		'key'	=>	'512f7d063fc1490d9bcc7504c764a6dd',
		);


	/**
	 * This is code which is currently not operational.
	 * @todo Impliment.
	 */
	$GLOBALS[ POSTWORLD_CONFIG ]['classes'] = array(
		'post_types'	=>	array(),
		'data'	=>	array(
			'author'	=>	array(
				'name'			=>	'Blog',
				'description'	=>	'Main Blog',
				'roles'			=>	array('Administrator', 'Editor', 'Author'),
				),
			'contributor'	=>	array(
				'name'			=>	'Contributer',
				'description'	=>	'Features for community members.',
				'roles'			=>	array('Contributor'),
				),
			'members'	=>	array(
				'name'			=>	'Members Only',
				'description'	=>	'Features for community members.',
				'roles'			=>	array('Contributor'),
				),
			),
		);


	$GLOBALS[ POSTWORLD_CONFIG ]['paths'] = array(
		'postworld' => array(
			'url'	=>	get_template_directory_uri().'/postworld',
			),
		);

	/**
	 * Controls configure what options appear to what user roles
	 * On the frontend post control menus.
	 */
	$GLOBALS[ POSTWORLD_CONFIG ]['controls'] = array(
		'post'	=>	array(
			'role_access'	=>	array(
				'administrator' => array(
					  'own' => array( 'quick-edit', 'wp-edit', 'trash' ), // 'pw-edit',
					'other' => array( 'quick-edit', 'wp-edit', 'trash' ),
					),
				'editor' => array(
					  'own' => array( 'quick-edit', 'wp-edit', 'trash' ),
					'other' => array( 'quick-edit','wp-edit', 'trash' ),
					),
				'author' => array(
					  'own' => array( 'quick-edit', 'wp-edit', 'trash' ),
					'other' => array(  ),
					),
				'contributor' => array(
					  'own' => array( 'quick-edit', 'wp-edit', 'trash' ),
					'other' => array(  ),
					),
				'guest' => array(
					  'own' => array(  ),
					'other' => array(  ),
					),
				),
			'menu_options'	=>	array(
				array(
					"name" => 	"Quick Edit",
					"icon" => 	"pwi-quick-edit",
					"action" => "quick-edit"
					),
				array(
					"name" => 	"PW Edit",
					"icon" => 	"pwi-edit-square",
					"action" => "pw-edit"
					),
				array(
					"name" => 	"Edit",
					"icon" => 	"pwi-edit",
					"action" => "wp-edit"
					),
				array(
					"name" => 	"Trash",
					"icon" => 	"pwi-trash-o",
					"action" => "trash"
					),
				),
			),
		'comment'	=>	array(
			'role_access'	=>	array(
				'administrator' => array(
					  'own' => array( 'edit', 'flag', 'trash' ),
					'other' => array( 'edit', 'flag', 'trash' ),
					),
				'editor' => array(
					  'own' => array( 'edit', 'flag', 'trash' ),
					'other' => array( 'edit', 'flag', 'trash' ),
					),
				'author' => array(
					  'own' => array( 'edit', 'trash' ),
					'other' => array( 'flag' ),
					),
				'contributor' => array(
					  'own' => array( 'edit', 'trash' ),
					'other' => array( 'flag' ),
					),
				'guest' => array(
					  'own' => array( ),
					'other' => array( ),
					),
				),
			),
		);


	$GLOBALS[ POSTWORLD_CONFIG ]['roles'] = array(
		'administrator' 	=> array(
			'vote_points'	=> 10,
			'post_class'	=> 'author',
			),
		'editor' 			=> array(
			'vote_points'	=> 5,
			'post_class'	=> 'author',
			),
		'organizer' 			=> array(
			'vote_points'	=> 2,
			'post_class'	=> 'author',
			),
		'contributor' 		=> array(
			'vote_points'	=> 1,
			'post_class'	=> 'contributor'
			),
		'default'	 		=> array(
			'vote_points'	=> 1,
			'post_class'	=> 'guest'
			),
		);

	$GLOBALS[ POSTWORLD_CONFIG ]['role'] = array(
		'default'	=>	'contributor',
		'map'	=>	array(
			'administrator'	=> array( 'administrator' ),
			'editor'		=> array( 'administrator', 'editor' ),
			'author'		=> array( 'author' ),
			'contributor'	=> array( 'contributor' ),
			),
		);

	pw_config_module('iconsets', array(
		'required'	=>	array(
			'postworld-icons',
			),
		));

	pw_config_module('colors', array(
		'process_images' => true,
		'max_size' => 640,
		'number' => 5,
		));

	pw_config_module('visual_composer', array(
		'disable_frontend' => true,
		'shortcodes' => array(
			'supported' => array(
				'pw-feed'
				),
			),
		));

}
