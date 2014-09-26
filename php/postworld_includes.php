<?php

// Define Angular Dependancies
global $angularDep;
$angularDep = array('jquery','UnderscoreJS','DeepMerge','AngularJS','AngularJS-Resource','AngularJS-Route', 'AngularJS-Sanitize', 'UnderscoreJS');

function postworld_includes( $args ){

	extract( $args );

	global $postworld_version;
	global $pwSiteGlobals;

	// Default Angular Version
	if( empty( $angular_version ) )
		$angular_version = 'angular-1.3.0-beta.13';

	// Injections
	global $pwInject;

	// Add injectors from Site Globals
	$pwInject = ( isset( $pwSiteGlobals['inject'] ) ) ?
		$pwSiteGlobals['inject'] : array();

	// Override with injectors from $args
	$pwInject = ( isset( $args['inject'] ) ) ?
		$args['inject'] : $pwInject;

	// Build Angular Dependancies
	global $angularDep;
	
	//////////////////////// INJECTIONS //////////////////////

	/* JQuery is added for nInfiniteScroll Directive, if directive is not used, then remove it */
	//wp_deregister_script('jquery');
	//wp_register_script('jquery', "http" . ($_SERVER['SERVER_PORT'] == 443 ? "s" : "") . "://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js", false, null);
	wp_enqueue_script('jquery','');

	// Add MASONRY
	if( in_array( 'masonry.js', $pwInject ) ){
		// MASONRY
		wp_enqueue_script( 'Masonry-JS',
			POSTWORLD_URI.'/lib/masonry/masonry.pkgd.min.js');		
		wp_enqueue_script( 'ImagesLoaded-JS',
			POSTWORLD_URI.'/lib/masonry/imagesloaded.pkgd.min.js');
	}

	// Add Google Maps to include before AngularJS app
	if( in_array( 'google-maps', $pwInject ) ){
		//array_push( $angularDep, 'google-maps' );
	}

	// Add LESS Support
	if( in_array( 'wp-less', $pwInject ) ){
		require_once( POSTWORLD_PATH.'/lib/wp-less/wp-less.php' );
	}
	
	// Add Font Awesome 3
	if( in_array( 'font-awesome-3', $pwInject ) ){
		wp_enqueue_style( 'font-awesome-3',
			"//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" );
			// Todo : parse from LESS
			//POSTWORLD_URI.'/lib/font-awesome-3/css/font-awesome.min.css' );
	}

	// Add ICOMOON
	if( in_array( 'icomoon', $pwInject ) ){
		wp_enqueue_style( 'icomoon',
			POSTWORLD_URI.'/lib/icomoon/style.css' );
	}

	// Add ICON X
	if( in_array( 'icon-x', $pwInject ) ){
		wp_enqueue_style( 'icon-x',
			POSTWORLD_URI.'/lib/icon-x/icon-x.css' );
	}

	// Add GLYPHICONS
	if( in_array( 'glyphicons-halflings', $pwInject ) ){
		wp_enqueue_style( 'glyphicons-halflings',
			POSTWORLD_URI.'/lib/glyphicons/glyphicons-halflings.css' );
	}

	// All Dynamic Paths and Wordpress PHP data that needs to be added to JS files
	$jsVars = array(	'ajaxurl' 		=> admin_url( 'admin-ajax.php' ),
						'pluginurl' 	=> WP_PLUGIN_URL,
						'user_id'		=> get_current_user_id(),
						'is_admin'		=> is_admin(),
					);

	//////////---------- LIBRARY INCLUDES ----------//////////

	//BOOTSTRAP CSS
	// Removed - move be added in theme
	//wp_enqueue_style( "bootstrap-CSS", POSTWORLD_URI.'/lib/bootstrap/bootstrap.min.css' );
	//wp_enqueue_style( "Angular-Strap-Animation", POSTWORLD_URI.'/lib/angular-strap-2.0.0-rc.2/css/angular-motion.min.css' );



	//////////---------- POSTWORLD INCLUDES ----------//////////
	///// DEPLOY FILE INCLUDES /////
	if ( $mode == 'deploy' ){
	
		// ANGULAR
		//wp_enqueue_script( 'AngularJS',
		//	POSTWORLD_URI.'/lib/'.$angular_version.'/angular.min.js');

		// POSTWORLD
		wp_register_script( "Postworld-Deploy", POSTWORLD_URI.'/deploy/postworld.min.js', array(), $postworld_version );
		wp_localize_script( 'Postworld-Deploy', 'jsVars', $jsVars);
		wp_enqueue_script(  'Postworld-Deploy' );


	}
	///// DEVELOPMENT FILE INCLUDES /////
	else if ( $mode == 'dev' ){
		
		///// JAVASCRIPT LIBRARIES /////

		// UNDERSCORE JS
		wp_enqueue_script( 'UnderscoreJS',
			POSTWORLD_URI.'/lib/underscore/underscore.min.js');

		// DEEP MERGE
		wp_enqueue_script( 'DeepMerge',
			POSTWORLD_URI.'/lib/deepmerge/deepmerge.js');

		/*
		// MOMENT.JS
		wp_enqueue_script( 'Moment-JS',
			POSTWORLD_URI.'/lib/moment.js/moment.min.js');
		// MOMENT-TIMEZONE.JS
		wp_enqueue_script( 'Moment-Timezone-JS',
			POSTWORLD_URI.'/lib/moment.js/moment-timezone.min.js');
		// MOMENT-TIMEZONE DATA.JS
		wp_enqueue_script( 'Moment-Timezone-Data-JS',
			POSTWORLD_URI.'/lib/moment.js/moment-timezone-data.js');
		*/

		// HISTORY.JS
		//wp_enqueue_script( 'History-JS',
		//	POSTWORLD_URI.'/lib/history.js/native.history.js');	

		///// THIRD PARTY LIBRARIES /////

		
		// CREATE.JS
		// Development Only ( Not in Grunt File / Deploy Version )
		wp_enqueue_script( 'CreateJS-Easel',
			POSTWORLD_URI.'/lib/create.js/easeljs-0.7.0.min.js');
		wp_enqueue_script( 'CreateJS-Tween',
			POSTWORLD_URI.'/lib/create.js/tweenjs-0.5.0.min.js');
		wp_enqueue_script( 'CreateJS-MovieClip',
			POSTWORLD_URI.'/lib/create.js/movieclip-0.7.0.min.js');
		
		
		///// ANGULAR VERSION CONTROL /////

		// ANGULAR
		wp_enqueue_script( 'AngularJS',
			POSTWORLD_URI.'/lib/'.$angular_version.'/angular.min.js');

		// ANGULAR SERVICES
		wp_enqueue_script( 'AngularJS-Resource',
			POSTWORLD_URI.'/lib/'.$angular_version.'/angular-resource.min.js');

		wp_enqueue_script( 'AngularJS-Route',
			POSTWORLD_URI.'/lib/'.$angular_version.'/angular-route.min.js');

		wp_enqueue_script( 'AngularJS-Sanitize',
			POSTWORLD_URI.'/lib/'.$angular_version.'/angular-sanitize.min.js');

		//wp_enqueue_script( 'AngularJS-Animate',
		//	POSTWORLD_URI.'/lib/'.$angular_version.'/angular-animate.min.js');


		///// ANGULAR THIRD PARTY MODULES /////
		
		// ANGULAR UI UTILITIES
		// Development only
		//wp_enqueue_script( 'AngularJS-UI-Utils',
		//	POSTWORLD_URI.'/lib/angular-ui-utils/angular-ui-utils.min.js');
		
		//BOOTSTRAP JS
		wp_enqueue_script( "bootstrap-JS",
			POSTWORLD_URI.'/lib/bootstrap/bootstrap.min.js' );

		// ANGULAR UI : BOOTSTRAP
		//wp_enqueue_script( 'AngularJS-UI-Bootstrap',
		//	plugins_url().'/postworld/lib/angular/ui-bootstrap-tpls-0.6.0.min.js' );
		wp_enqueue_script( 'AngularJS-UI-Bootstrap',
			POSTWORLD_URI.'/lib/angular-ui-bootstrap/ui-bootstrap-tpls-0.11.0.min.js' );

		// ANGULAR STRAP : BOOTSTRAP
		wp_enqueue_script( 'AngularJS-Strap-Dimensions',
			POSTWORLD_URI.'/lib/angular-strap/angular-strap-dimensions.js', $angularDep );

		wp_enqueue_script( 'AngularJS-Strap-Tooltip',
			POSTWORLD_URI.'/lib/angular-strap/angular-strap-tooltip.js', $angularDep );

		wp_enqueue_script( 'AngularJS-Strap-Popover',
			POSTWORLD_URI.'/lib/angular-strap/angular-strap-popover.js', $angularDep );


		//wp_enqueue_script( 'AngularJS-Strap',
		//	plugins_url().'/postworld/lib/angular-strap-2.0.0-rc.2/angular-strap.js', $angularDep );

		//wp_enqueue_script( 'AngularJS-Strap-Templates',
		//	plugins_url().'/postworld/lib/angular-strap-2.0.0-rc.2/angular-strap.tpl.js', $angularDep );


		// ANGULAR : INFINITE SCROLL
		//wp_enqueue_script( 'angularJS-nInfiniteScroll', plugins_url().'/postworld/lib/ng-infinite-scroll/ng-infinite-scroll.js', $angularDep );
		
		// ANGULAR : TIMER
		wp_enqueue_script( 'AngularJS-Timer',
			POSTWORLD_URI.'/lib/angular-timer/angular-timer.js', $angularDep );

		/*
		// ANGULAR : TIMER
		wp_enqueue_script( 'AngularJS-Moment',
			plugins_url().'/postworld/lib/angular-moment/angular-moment.min.js', $angularDep );
		*/

		// ANGULAR : PARALLAX
		wp_enqueue_script( 'angularJS-Parallax',
			POSTWORLD_URI.'/lib/angular-parallax/angular-parallax.js', $angularDep );

		// ANGULAR : ELASTIC
		wp_enqueue_script( 'angularJS-Elastic',
			POSTWORLD_URI.'/lib/angular-elastic/angular-elastic.js', $angularDep );

		// ANGULAR : MASONRY
		wp_enqueue_script( 'angularJS-Masonry',
			POSTWORLD_URI.'/lib/angular-masonry/angular-masonry.js', $angularDep );


		/////// POSTWORLD APP /////	
		wp_enqueue_script( 	'pw-app-JS',
			POSTWORLD_URI.'/js/app.js', $angularDep );


		///// CREATE.JS /////
		//if( in_array('create.js', $pwInject) ){	
		// LOCAL COMPONENT
		wp_enqueue_script( 'Postworld-FlashCanvas',
			POSTWORLD_URI.'/js/components/flashCanvas.js', $angularDep);
		//}


		// COMPONENTS
		wp_enqueue_script( 'pw-FeedItem-JS',
			POSTWORLD_URI.'/js/components/feedItem.js', $angularDep );

		wp_enqueue_script( 'pw-TreeView-JS',
			POSTWORLD_URI.'/js/components/treeview.js', $angularDep );

		//wp_enqueue_script( 'pw-Ya-TreeView-JS',
		//	POSTWORLD_URI.'/js/components/ya-treeview.js', $angularDep );

		wp_enqueue_script( 'pw-LoadComments-JS',
			POSTWORLD_URI.'/js/components/loadComments.js', $angularDep );
		
		wp_enqueue_script( 'pw-inputSearch-JS',
			POSTWORLD_URI.'/js/components/inputSearch.js', $angularDep );

		wp_enqueue_script( 'pw-LiveFeed-JS',
			POSTWORLD_URI.'/js/components/liveFeed.js', $angularDep );

		wp_enqueue_script( 'pw-MediaEmbed-JS',
			POSTWORLD_URI.'/js/components/mediaEmbed.js', $angularDep );

		wp_enqueue_script( 'pw-Users-JS',
			POSTWORLD_URI.'/js/components/pwUsers.js', $angularDep );

		wp_enqueue_script( 'pw-Modal-JS',
			POSTWORLD_URI.'/js/components/pwModal.js', $angularDep );

		wp_enqueue_script( 'pw-Embedly-JS',
			POSTWORLD_URI.'/js/components/pwEmbedly.js', $angularDep );

		wp_enqueue_script( 'pw-Input-JS',
			POSTWORLD_URI.'/js/components/pwInput.js', $angularDep );

		wp_enqueue_script( 'pw-InfiniteGallery-JS',
			POSTWORLD_URI.'/js/components/pwInfiniteGallery.js', $angularDep );

		wp_enqueue_script( 'pw-geocode-JS',
			POSTWORLD_URI.'/js/components/pwGeocode.js', $angularDep );

		wp_enqueue_script( 'pw-UI-JS',
			POSTWORLD_URI.'/js/components/pwUi.js', $angularDep );


		// CONTROLLERS
		wp_enqueue_script( 'pw-Controllers-JS',
			POSTWORLD_URI.'/js/controllers/pwControllers.js', $angularDep );

		wp_enqueue_script( 'pw-controlMenus-JS',
			POSTWORLD_URI.'/js/controllers/controlMenus.js', $angularDep );

		wp_enqueue_script( 'pw-editPost-JS',
			POSTWORLD_URI.'/js/controllers/editPost.js', $angularDep );

		wp_enqueue_script( 'pw-autoComplete-JS',
			POSTWORLD_URI.'/js/controllers/autoComplete.js', $angularDep );

		wp_enqueue_script( 'pw-Widgets-JS',
			POSTWORLD_URI.'/js/controllers/pwWidgets.js', $angularDep );

		// FILTERS
		wp_enqueue_script( 	'pw-Filters-JS',
			POSTWORLD_URI.'/js/filters/pwFilters.js', $angularDep );

		wp_enqueue_script( 'pw-filterFeed-JS',
			POSTWORLD_URI.'/js/filters/filterFeed.js', $angularDep );

		// SERVICES
		wp_enqueue_script( 'pw-pwData-JS',
			POSTWORLD_URI.'/js/services/pwData.js', $angularDep );

		wp_enqueue_script( 'pw-Services-JS',
			POSTWORLD_URI.'/js/services/pwServices.js', $angularDep );

		wp_enqueue_script( 'pw-pwCommentsService-JS',
			POSTWORLD_URI.'/js/services/pwCommentsService.js', $angularDep );

		wp_localize_script( 'pw-pwCommentsService-JS', 'jsVars', $jsVars);

		// DIRECTIVES
		wp_enqueue_script( 'pw-Directives-JS',
			POSTWORLD_URI.'/js/directives/pwDirectives.js', $angularDep );

		wp_enqueue_script( 'pw-Directives-ListUsers',
			POSTWORLD_URI.'/js/directives/pwUserList.js', $angularDep );

		wp_enqueue_script( 'pw-Directives-pwQuery',
			POSTWORLD_URI.'/js/directives/pwQuery.js', $angularDep );

		wp_enqueue_script( 'pw-Directives-pwGetPost',
			POSTWORLD_URI.'/js/directives/pwGetPost.js', $angularDep );

		wp_enqueue_script( 'pw-Directives-pwImage',
			POSTWORLD_URI.'/js/directives/pwImage.js', $angularDep );

		wp_enqueue_script( 'pw-Directives-pwMenu',
			POSTWORLD_URI.'/js/directives/pwMenu.js', $angularDep );

		wp_enqueue_script( 'pw-Directives-pwWindow',
			POSTWORLD_URI.'/js/directives/pwWindow.js', $angularDep );

		// MODULES
		wp_enqueue_script( 'pw-Modules-Compile',
			POSTWORLD_URI.'/js/modules/pwCompile.js', $angularDep );

		// WIZARD
		wp_enqueue_script( 'pw-Wizard',
			POSTWORLD_URI.'/js/components/pwWizard.js', $angularDep );

		// WORDPRESS DIRECTIVES
		wp_enqueue_script( 'pw-WpDirectives-Media-Library-JS',
			POSTWORLD_URI.'/js/directives/wpMediaLibrary.js', $angularDep );

	}

	// ADD GOOGLE MAPS
	if( in_array('google-maps', $pwInject) ){
		// GOOGLE MAPS
		wp_enqueue_script( 'Google-Maps-API',
			'//maps.googleapis.com/maps/api/js?sensor=false' );
		// ANGULAR UI : GOOGLE MAPS
		wp_enqueue_script( 'AngularJS-Google-Maps',
			POSTWORLD_URI.'/lib/angular-google-maps/angular-google-maps.min.js' );
	}

	///// INCLUDE SITE WIDE JAVASCRIPT GLOBALS /////
	// Dynamically generate javascript file
	// After all Plugins and Theme Loaded
	add_action( 'init', 'pwSiteGlobals_include');
	
	///// WINDOW JAVASCRIPT DATA INJECTION /////
	// Inject Current User Data into Window
	function pwGlobals() {
	?>
		<script type="text/javascript">/* <![CDATA[ */
			var pwGlobals = <?php echo json_encode( pwGlobals_parse() ); ?>;
		/* ]]> */</script>

	<?php
	}
	// Add hook for admin <head></head>
	add_action('admin_head', 'pwGlobals');
	// Add hook for front-end <head></head>
	add_action('wp_head', 'pwGlobals');

}



///// PARSE pwSiteGlobals /////

function pwSiteGlobals_include(){

	///// DYNAMICALLY GENERATED JAVASCRIPT /////
	// This method can only be used for site-wide globals
	// Not for user-specific globals

	// ENCODE SITE GLOBALS
	global $pwSiteGlobals;

	$pwSiteGlobals['site'] = array( 
		'name' => get_bloginfo('name'),
		'description' => get_bloginfo('description'),
		'wpurl' => get_bloginfo('wpurl'),
		'url' => get_bloginfo('url'),
		'version' => get_bloginfo('version'),
		'text_direction' => get_bloginfo('text_direction'),
		'language' => get_bloginfo('language'),
		'description' => get_bloginfo('description'),
	);

	// Depreciated //
	$pwSiteGlobals['wordpress'] = array( 
		'ajax_url' => admin_url('admin-ajax.php'),
		'stylesheet_directory_uri' => get_stylesheet_directory_uri(),
		'template_directory_uri' => get_template_directory_uri(),
		'plugins_dir' => WP_PLUGIN_DIR,
		'plugins_url' => WP_PLUGIN_URL,
		'pingback_url' => get_bloginfo('pingback_url'),
	);

	///// PATHS /////
	$pwSiteGlobals["paths"] = array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'plugins_url' => WP_PLUGIN_URL,
		'plugins_dir' => WP_PLUGIN_DIR,
		"theme_dir"	=>	get_stylesheet_directory(),
		"home_url" => get_bloginfo( 'url' ),
		"wp_url" => get_bloginfo( 'wpurl' ),
		"stylesheet_directory" => get_bloginfo( 'stylesheet_directory' ),

		"template_url" => get_bloginfo( 'template_url' ),
		"postworld_url" => WP_PLUGIN_URL . '/postworld',
		"postworld_dir" => WP_PLUGIN_DIR . '/postworld',
		);

	$pwJs  = "";
	$pwJs .= "var pwSiteGlobals = ";
	$pwJs .= json_encode( $pwSiteGlobals );
	$pwJs .= ";";

	// ENCODE TEMPLATES
	$pwJs .= "\n\n";
	$pwJs .= "var pwTemplates = ";
	$pwJs .= json_encode( pw_get_templates() );
	$pwJs .= ";";	

	// ENCODE SITE LANGUAGE
	global $pwSiteLanguage;	
	$pwJs .= "\n\n";
	$pwJs .= "var pwSiteLanguage = ";
	$pwJs .= json_encode( $pwSiteLanguage );
	$pwJs .= ";";

	$pwJsFile = POSTWORLD_PATH . '/deploy/pwSiteGlobals.js';
	$file = fopen( $pwJsFile ,"w" );
	fwrite($file,"$pwJs");
	fclose($file);
	chmod($pwJsFile, 0755);

	global $angularDep;
	wp_enqueue_script( 'pw-SiteGlobals-JS',
		POSTWORLD_URI.'/deploy/pwSiteGlobals.js', array(), hash( 'md5', 4 ) );
	
}



///// PARSE pwGlobals /////
function pwGlobals_parse(){
	/////////// USER / PAGE SPECIFIC GLOBALS //////////
	global $pw;
	global $wp_query;
	$pw = array();

	///// CURRENT VIEW /////
	$viewdata = array();

	// URL
	$protocol = (!empty($_SERVER['HTTPS'])) ?
		"https" : "http";
	$viewdata['url'] = $protocol."://".$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];
	$viewdata['protocol'] = $protocol;

	// GET TYPE
	// Determine the view type
	$view_type = "default";
	if( is_archive() && !is_date() )
		$view_type = 'term_archive';
	else if( is_archive() && is_date() && !is_year() )
		$view_type = 'date_archive';
	else if( is_archive() && is_date() && is_year() )
		$view_type = 'year_archive';
	else if( is_page() )
		$view_type = 'page';
	else if( is_page() )
		$view_type = 'page';
	else if( is_single() )
		$view_type = 'post';

	// SET TYPE
	$viewdata["type"] = $view_type;

	///// SET META BY TYPE /////
	
	switch( $view_type ){

		// POST OR PAGE
		case "page":
		case "post":
			$viewdata["post"] = $GLOBALS['post'];
			break;

		// TERM ARCHIVE
		case "term_archive":
			$current_term = get_queried_object();
			$viewdata["term"] = $current_term;
			$viewdata["term"]->term_link = get_term_link( $current_term );
			$viewdata["taxonomy"] = get_taxonomy( $current_term->taxonomy );
			break;

		// YEAR ARCHIVE
		case "year_archive":
			$viewdata["query"] = array(
				"year"	=>	pw_to_array( $wp_query )['query_vars']['year'] ,
				);
			break;
	}

	$pw['view'] = pw_to_array( $viewdata );

	///// CURRENT USER /////
	$user_id = get_current_user_id();
	if( $user_id != 0 ){
		$userdata = wp_get_current_user();
		unset($userdata->data->user_pass);
		$userdata = (array) $userdata;
		$userdata["postworld"] = array();
		$userdata["postworld"]["vote_power"] = get_user_vote_power( $user_id );
		$userdata["is_admin"] = is_admin();

		// Force the roles as a flat array
		if( isset( $userdata['roles'] ) &&
			pw_is_associative( $userdata['roles'] ) ){
			$userdata['roles'] = array_values( $userdata['roles'] );
		}

		// SUPPORT FOR WPMU MEMBERSHIP
		if( function_exists('current_user_is_member') ){
			$userdata["membership"] = array();
			$userdata["membership"]["is_member"] = current_user_is_member();
		}


	} else
		$userdata = 0;



	$pw["user"] = $userdata;

	///// DISPLAYED USER /////
	// Support for Buddypress Globals
	if ( function_exists('bp_displayed_user_id') ){
		$displayed_user_id = bp_displayed_user_id();
	} else{
		global $post;
		if( gettype( $post ) == 'object' )
			$displayed_user_id = $post->post_author;
	}

	if ( isset($displayed_user_id) )
		$displayed_userdata = get_userdata( $displayed_user_id );

	$pw['displayed_user'] = array(
		"user_id" => $displayed_user_id,
		"display_name" => $displayed_userdata->display_name,
		"first_name" => $displayed_userdata->first_name,	
		);

	///// SECURITY /////
	$pw["security"] = array();
	// Set the default security mode
	$pw["security"]["mode"] = "user";

	/////////// SITE WIDE GLOBALS //////////
	// TODO : MOVE THIS STUFF INTO pwSiteglobals

	///// SITE INFO /////
	$pw["site_info"] = array(
		"name" => get_bloginfo( 'name' ),
		"description" => get_bloginfo( 'description' ),
		);

	///// POST TYPES /////
	$pw["post_types"] = pw_get_post_types();

	///// PATHS /////
	// TODO : Remove - Already moved this to pwSiteglobals for JS
	$pw["paths"] = array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'plugins_url' => WP_PLUGIN_URL,
		'plugins_dir' => WP_PLUGIN_DIR,
		"theme_dir"	=>	get_stylesheet_directory(),
		"home_url" => get_bloginfo( 'url' ),
		"wp_url" => get_bloginfo( 'wpurl' ),
		"stylesheet_directory" => get_bloginfo( 'stylesheet_directory' ),

		"template_url" => get_bloginfo( 'template_url' ),
		"postworld_url" => POSTWORLD_URI,
		"postworld_dir" => POSTWORLD_PATH,
		);

	///// LANGUAGE /////
	$pw['language'] = $pw_settings['language'];

	///// INJECTIONS /////
	global $pwInject;
	$pw['inject'] = $pwInject;

	///// RETURN /////
	return $pw;
}


// Parse Globals After all Plugins Loaded
function parse_postworld_globals(){
 	// Init Globals
	global $pw;
	$pw = pwGlobals_parse();
}
add_action( 'plugins_loaded', 'parse_postworld_globals', 10, 2 );


function pw_injections(){
	global $pwInject;
	return $pwInject;
}

?>