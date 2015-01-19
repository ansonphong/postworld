<?php

// Define Angular Dependancies
function postworld_includes( $args ){

	extract( $args );

	global $pw;
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

	// Add Additional Angular Modules
	$pw['angularModules'] = apply_filters( 'pw_angular_modules', $pw['angularModules'] );

	// Add Angular Modules to the Postworld Inject array
	$pwInject = array_merge( $pwInject, $pw['angularModules'] );

	// Add Glyphicons for Admin
	if( is_admin() ){
		array_push( $pwInject,
			'glyphicons-halflings'
			);
	}

	//////////////////////// INJECTIONS //////////////////////

	/* JQuery is added for nInfiniteScroll Directive, if directive is not used, then remove it */
	//wp_deregister_script('jquery');
	//wp_register_script('jquery', "http" . ($_SERVER['SERVER_PORT'] == 443 ? "s" : "") . "://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js", false, null);
	wp_enqueue_script('jquery','');

	

	// + MASONRY
	if( in_array( 'masonry.js', $pwInject ) ){
		// MASONRY
		wp_enqueue_script( 'Masonry-JS',
			POSTWORLD_URI.'/lib/masonry/masonry.pkgd.min.js');		
		wp_enqueue_script( 'ImagesLoaded-JS',
			POSTWORLD_URI.'/lib/masonry/imagesloaded.pkgd.min.js');
	}

	// + Google Maps to include before AngularJS app
	if( in_array( 'google-maps', $pwInject ) ){
		//array_push( $angularDep, 'google-maps' );
	}

	// + LESS Support
	if( in_array( 'wp-less', $pwInject ) ){
		require_once( POSTWORLD_PATH.'/lib/wp-less/wp-less.php' );
	}
	
	// + Font Awesome 3
	if( in_array( 'font-awesome-3', $pwInject ) ){
		wp_enqueue_style( 'font-awesome-3',
			"//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" );
			// Todo : parse from LESS
			//POSTWORLD_URI.'/lib/font-awesome-3/css/font-awesome.min.css' );
	}

	// + ICOMOON
	if( in_array( 'icomoon', $pwInject ) ){
		wp_enqueue_style( 'icomoon',
			POSTWORLD_URI.'/lib/icomoon/style.css' );
	}

	// + ICON X
	if( in_array( 'icon-x', $pwInject ) ){
		wp_enqueue_style( 'icon-x',
			POSTWORLD_URI.'/lib/icon-x/icon-x.css' );
	}

	// + GLYPHICONS
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
	if ( pw_mode() == 'deploy' ){
	
		global $angularDep;
		$angularDep = array(
			'Postworld-Deploy',
			);

		// ANGULAR
		//wp_enqueue_script( 'AngularJS',
		//	POSTWORLD_URI.'/lib/'.$angular_version.'/angular.min.js');

		// POSTWORLD
		wp_register_script( "Postworld-Deploy", POSTWORLD_URI.'/deploy/postworld.min.js', array(), $pw['info']['version'] );
		wp_localize_script( 'Postworld-Deploy', 'jsVars', $jsVars);
		wp_enqueue_script(  'Postworld-Deploy' );

	}
	///// DEVELOPMENT FILE INCLUDES /////
	else if ( pw_mode() == 'dev' ){
		
		// Build Angular Dependancies
		global $angularDep;
		$angularDep = array(
			'jquery',
			'UnderscoreJS',
			'DeepMerge',
			'AngularJS',
			'AngularJS-Resource',
			'AngularJS-Route',
			'AngularJS-Sanitize',
			);

		///// JAVASCRIPT LIBRARIES /////

		// UNDERSCORE JS
		wp_enqueue_script( 'UnderscoreJS',
			POSTWORLD_URI.'/lib/underscore/underscore.min.js');

		// DEEP MERGE
		wp_enqueue_script( 'DeepMerge',
			POSTWORLD_URI.'/lib/deepmerge/deepmerge.js');

		// PHP.JS
		wp_enqueue_script( 'PHP.JS',
			POSTWORLD_URI.'/lib/php.js/php.js');

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

		wp_enqueue_script( 'AngularJS-Touch',
			POSTWORLD_URI.'/lib/'.$angular_version.'/angular-touch.min.js');

		wp_enqueue_script( 'AngularJS-Aria',
			POSTWORLD_URI.'/lib/'.$angular_version.'/angular-aria.min.js');

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
			POSTWORLD_URI.'/lib/angular-ui-bootstrap/ui-bootstrap-tpls-0.12.0.min.js' );

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
		// TODO : blob through the dirs and get all the js files, auto-include in foreach
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

		wp_enqueue_script( 'pw-Directives-pwUsers',
			POSTWORLD_URI.'/js/directives/pwUsers.js', $angularDep );

		wp_enqueue_script( 'pw-Directives-pwImage',
			POSTWORLD_URI.'/js/directives/pwImage.js', $angularDep );

		wp_enqueue_script( 'pw-Directives-Background',
			POSTWORLD_URI.'/js/directives/pwBackground.js', $angularDep );

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

	// + GOOGLE MAPS
	if( in_array('google-maps', $pwInject) ){
		// GOOGLE MAPS
		wp_enqueue_script( 'Google-Maps-API',
			'//maps.googleapis.com/maps/api/js?sensor=false' );
		// ANGULAR UI : GOOGLE MAPS
		wp_enqueue_script( 'AngularJS-Google-Maps',
			POSTWORLD_URI.'/lib/angular-google-maps/angular-google-maps.min.js' );
	}

	// + CALENDAR
	if( in_array( 'ui.calendar', $pwInject ) ){
		// Full Calendar
		wp_enqueue_script( 'Full-Calendar-Moment-JS',
			POSTWORLD_URI.'/lib/fullcalendar-2.2.5/lib/moment.min.js' );

		wp_enqueue_script( 'Full-Calendar-JS',
			POSTWORLD_URI.'/lib/fullcalendar-2.2.5/fullcalendar.min.js' );	
		wp_enqueue_style( 'Full-Calendar-CSS',
			POSTWORLD_URI.'/lib/fullcalendar-2.2.5/fullcalendar.min.css' );		

		wp_enqueue_script( 'Full-Calendar-jQuery-UI-JS',
			POSTWORLD_URI.'/lib/fullcalendar-2.2.5/lib/jquery-ui.custom.min.js' );

		// Angular UI Calendar
		wp_enqueue_script( 'Angular-UI-Calendar-JS',
			POSTWORLD_URI.'/lib/ui-calendar-master/src/calendar.js' );
	}

	///// INCLUDE SITE WIDE JAVASCRIPT GLOBALS /////
	// Dynamically generate javascript file
	// After all Plugins and Theme Loaded
	//add_action( 'init', 'pwSiteGlobals_include');
	pwSiteGlobals_include();

	// Add hook for admin <head></head>
	add_action('admin_head', 'pwGlobals_print');
	// Add hook for front-end <head></head>
	add_action('wp_head', 'pwGlobals_print');

}

///// WINDOW JAVASCRIPT DATA INJECTION /////
// Inject Current User Data into Window
function pwGlobals_print() {
	global $pw;
	?><script type="text/javascript">/* <![CDATA[ */
		pw.angularModules = pw.angularModules.concat( <?php echo json_encode( $pw['angularModules'] ); ?> );
		pw.info = <?php echo json_encode( $pw['info'] ); ?>;
		pw.view = <?php echo json_encode( pw_current_view() ); ?>;
		pw.query = <?php echo json_encode( $pw['query'] ); ?>;
		pw.user = <?php echo json_encode( pw_current_user() ); ?>;
		pw.background = <?php echo json_encode( pw_current_background() ); ?>;
		pw.posts = <?php echo json_encode( apply_filters( PW_POSTS, array() ) ); ?>;
		pw.users = <?php echo json_encode( apply_filters( PW_USERS, array() ) ); ?>;
	/* ]]> */</script><?php
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
		"postworld_url" => POSTWORLD_URI,
		"postworld_dir" => POSTWORLD_PATH,
		);

	///// POST TYPES /////
	$pwSiteGlobals["post_types"] = pw_get_post_types();

	$pwSiteGlobals["fields"] = pw_field_model();

	///// PRINT JAVASCRIPT /////
	// SITE GLOBALS
	$pwJs  = "";
	$pwJs .= "var pwSiteGlobals = ";
	$pwJs .= json_encode( $pwSiteGlobals );
	$pwJs .= ";";

	// TEMPLATES
	$pwJs .= "\n\n";
	$pwJs .= "pw.templates = ";
	$pwJs .= json_encode( pw_get_templates() );
	$pwJs .= ";";

	// OPTIONS
	$pwJs .= "\n\n";
	$pwJs .= "pw.options = ";
	$pwJs .= json_encode( apply_filters( PW_GLOBAL_OPTIONS, array() ) );
	$pwJs .= ";";

	// SITE LANGUAGE
	global $pwSiteLanguage;	
	$pwJs .= "\n\n";
	$pwJs .= "var pwSiteLanguage = ";
	$pwJs .= json_encode( $pwSiteLanguage );
	$pwJs .= ";";

	// WRITE THE FILE
	$globals_path = '/deploy/pwSiteGlobals.js';
	$pwJsFile = POSTWORLD_PATH . $globals_path;
	$file = fopen( $pwJsFile ,"w" );
	fwrite($file,"$pwJs");
	fclose($file);
	chmod($pwJsFile, 0755);

	// ENQUEUE SCRIPT
	wp_enqueue_script( 'pw-SiteGlobals-JS',
		POSTWORLD_URI . $globals_path, array(), hash( 'md5', 4 ) );
	
}


function pw_current_user(){
	$user_id = get_current_user_id();
	if( $user_id != 0 ){
		$userdata = wp_get_current_user();
		unset($userdata->data->user_pass);
		$userdata = (array) $userdata;
		$userdata["postworld"] = array();
		$userdata["postworld"]["vote_power"] = get_user_vote_power( $user_id );

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

	return $userdata;
}

///// PARSE pwGlobals /////
function pwGlobals_parse(){
	/////////// USER / PAGE SPECIFIC GLOBALS //////////
	global $pw;
	global $wp_query;

	///// CURRENT VIEW /////
	$viewdata = array();

	// TYPE
	$viewdata["type"] = pw_get_view_type();

	// VIEW
	$pw["view"] = pw_current_view();

	// QUERY
	$pw['query'] = pw_view_query( $pw["view"] );

	// LAYOUT
	$pw['layout'] = pw_get_current_layout();

	///// CURRENT USER /////
	$pw["user"] = pw_current_user();

	///// PATHS /////
	$pw["paths"] = array(
		'template_directory_uri'	=>	get_template_directory_uri(),
		'stylesheet_directory_uri' 	=> get_stylesheet_directory_uri(),
		);


	///// GLOBAL OPTIONS /////
	$pw["options"] = apply_filters( PW_GLOBAL_OPTIONS, array() );

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

	///// LANGUAGE /////
	$pw['language'] = $pw_settings['language'];

	///// INJECTIONS /////
	global $pwInject;
	$pw['inject'] = $pwInject;

	///// URL QUERY VARS /////
	$pw['url_vars'] = $_GET;

	///// PW MODULES /////
	$modules = pw_get_option( array( 'option_name' => PW_OPTIONS_MODULES ) );
	$pw['info']['modules'] = $modules;
	
	///// INFINITE /////
	// Merge the Infinite Globals into $pw
	// This is a temporary solution, as Infinite is being digested & refactored into Postworld
	$pw = array_replace_recursive( $pw, iGlobals() );

	///// RETURN /////
	return $pw;
}

// Parse Globals After all Plugins Loaded
function parse_postworld_globals(){
 	// Init Globals
	global $pw;
	$pw = array_replace_recursive( $pw, pwGlobals_parse() );	// pwGlobals_parse();
}
add_action( 'wp', 'parse_postworld_globals', 10, 2 );


function pw_injections(){
	global $pwInject;
	return $pwInject;
}

//////////// ADMIN GLOBALS ////////////
function pwAdminGlobals_include(){

	///// GENERATE JAVASCRIPT /////
	$pwAdminGlobals = pwAdminGlobals_parse();
	$js  = "";
	$js .= "pw.admin = ";
	$js .= json_encode( $pwAdminGlobals );
	$js .= ";";

	///// WRITE JAVASCRIPT FILE /////
	$file_path = "/deploy/pwAdminGlobals.js";
	$pwJsFile = POSTWORLD_PATH . $file_path;
	$file = fopen( $pwJsFile ,"w" );
	fwrite($file,"$js");
	fclose($file);
	chmod($pwJsFile, 0755);

	///// INCLUDE JAVASCRIPT FILE /////
	global $angularDep;
	wp_enqueue_script( 'pw-AdminGlobals-JS',
		POSTWORLD_URI.$file_path, array(), hash( 'md5', 4 ), true );

}

function pwAdminGlobals_parse(){
	global $pwAdminGlobals;

	/// TEMPLATES ///
	$pwAdminGlobals['templates'] = array();
	$pwAdminGlobals['templates']['php'] = pw_get_templates( array( 'ext' => 'php', 'path_type' => 'dir', 'output' => 'ids' ) );
	$pwAdminGlobals['templates']['html'] = pw_get_templates( array( 'ext' => 'html', 'path_type' => 'url', 'output' => 'ids' ) );

	/// SIDEBARS ///
	$pwAdminGlobals['sidebars'] = i_get_option( array( 'option_name' => PW_OPTIONS_SIDEBARS ) );

	/// MENUS ///
	$pwAdminGlobals['menus'] = pw_get_menus();

	return $pwAdminGlobals;
}


add_action( 'wp_head', 'pw_include_google_fonts' );
add_action( 'admin_head', 'pw_include_google_fonts' );
function pw_include_google_fonts( $fonts = array() ){
	// Includes the filtered fonts
	/*
		$fonts = array(
			array(
				'name'	=>	'Roboto',
				'code'	=>	'Roboto:100,300,700,100italic,300italic,400',
			)
		);
	*/
	// Get the fonts to include from a filter
	$fonts = apply_filters( 'pw_include_google_fonts', $fonts );
	// Iterate through each font and echo the include script
	foreach( $fonts as $font ){
		echo "\n<link href='http://fonts.googleapis.com/css?family=".$font['code']."' rel='stylesheet' type='text/css'>";
	}
	// Return the fonts, incase a function wants to see them
	return $fonts;
}


?>