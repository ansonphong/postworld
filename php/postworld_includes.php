<?php

// Define Angular Dependancies
global $angularDep;
$angularDep = array('jquery','UnderscoreJS','DeepMerge','AngularJS','AngularJS-Resource','AngularJS-Route', 'AngularJS-Sanitize', 'UnderscoreJS');

function postworld_includes( $args ){

	extract( $args );

	// Default Angular Version
	if( empty( $angular_version ) )
		$angular_version = 'angular-1.2.9';

	// Default Dependencies
	if( empty($dep) ){
		$dep = array();
	}

	// Build Angular Dependancies
	global $angularDep;
	
	// Add Google Maps to include before AngularJS app
	if( in_array( 'google-maps', $dep ) ){
		//array_push( $angularDep, 'google-maps' );
	}

	// All Dynamic Paths and Wordpress PHP data that needs to be added to JS files
	$jsVars = array(	'ajaxurl' => admin_url( 'admin-ajax.php' ),
						'pluginurl' => WP_PLUGIN_URL,
						'user_id'		=> get_current_user_id(),
						'is_admin'		=> is_admin(),
					);

	//////////---------- LIBRARY INCLUDES ----------//////////

	//BOOTSTRAP CSS
	// Removed - move be added in theme
	//wp_enqueue_style( "bootstrap-CSS", WP_PLUGIN_URL.'/postworld/lib/bootstrap/bootstrap.min.css' );
	//wp_enqueue_style( "Angular-Strap-Animation", WP_PLUGIN_URL.'/postworld/lib/angular-strap-2.0.0-rc.2/css/angular-motion.min.css' );

	/* JQuery is added for nInfiniteScroll Directive, if directive is not used, then remove it */
	//wp_deregister_script('jquery');
	//wp_register_script('jquery', "http" . ($_SERVER['SERVER_PORT'] == 443 ? "s" : "") . "://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js", false, null);
	wp_enqueue_script('jquery','');

	//////////---------- POSTWORLD INCLUDES ----------//////////
	///// DELPLOY FILE INCLUDES /////
	if ( $mode == 'deploy' ){
	
		// ANGULAR
		//wp_enqueue_script( 'AngularJS',
		//	WP_PLUGIN_URL.'/postworld/lib/'.$angular_version.'/angular.min.js');

		// POSTWORLD
		wp_register_script( "Postworld-Deploy", WP_PLUGIN_URL.'/postworld/deploy/postworld.min.js' );
		wp_localize_script( 'Postworld-Deploy', 'jsVars', $jsVars);
		wp_enqueue_script(  'Postworld-Deploy' );

		// ADD GOOGLE MAPS
		if( in_array('google-maps', $dep) ){
			// GOOGLE MAPS
			wp_enqueue_script( 'Google-Maps-API',
				'//maps.googleapis.com/maps/api/js?sensor=false' );
			// ANGULAR UI : GOOGLE MAPS
			wp_enqueue_script( 'AngularJS-Google-Maps',
				plugins_url().'/postworld/lib/angular-google-maps/angular-google-maps.min.js', array('Postworld-Deploy') );
		}



	}
	///// DEVELOPMENT FILE INCLUDES /////
	else if ( $mode == 'dev' ){
		
		///// JAVASCRIPT LIBRARIES /////

		// UNDERSCORE JS
		wp_enqueue_script( 'UnderscoreJS',
			WP_PLUGIN_URL.'/postworld/lib/underscore/underscore.min.js');

		// DEEP MERGE
		wp_enqueue_script( 'DeepMerge',
			WP_PLUGIN_URL.'/postworld/lib/deepmerge/deepmerge.js');

		
		// MOMENT.JS
		wp_enqueue_script( 'Moment-JS',
			WP_PLUGIN_URL.'/postworld/lib/moment.js/moment.min.js');
		// MOMENT-TIMEZONE.JS
		wp_enqueue_script( 'Moment-Timezone-JS',
			WP_PLUGIN_URL.'/postworld/lib/moment.js/moment-timezone.min.js');
		// MOMENT-TIMEZONE DATA.JS
		wp_enqueue_script( 'Moment-Timezone-Data-JS',
			WP_PLUGIN_URL.'/postworld/lib/moment.js/moment-timezone-data.js');
		

		///// THIRD PARTY LIBRARIES /////

		// ADD GOOGLE MAPS
		if( in_array('google-maps', $dep) ){
			// GOOGLE MAPS
			wp_enqueue_script( 'Google-Maps-API',
				'//maps.googleapis.com/maps/api/js?sensor=false' );
			// ANGULAR UI : GOOGLE MAPS
			wp_enqueue_script( 'AngularJS-Google-Maps',
				plugins_url().'/postworld/lib/angular-google-maps/angular-google-maps.min.js', $angularDep );
		}

		// CREATE.JS
		// Development Only ( Not in Grunt File / Deploy Version )
		wp_enqueue_script( 'CreateJS-Easel',
			WP_PLUGIN_URL.'/postworld/lib/create.js/easeljs-0.7.0.min.js');
		wp_enqueue_script( 'CreateJS-Tween',
			WP_PLUGIN_URL.'/postworld/lib/create.js/tweenjs-0.5.0.min.js');
		wp_enqueue_script( 'CreateJS-MovieClip',
			WP_PLUGIN_URL.'/postworld/lib/create.js/movieclip-0.7.0.min.js');
		
		
		///// ANGULAR VERSION CONTROL /////

		// ANGULAR
		wp_enqueue_script( 'AngularJS',
			WP_PLUGIN_URL.'/postworld/lib/'.$angular_version.'/angular.min.js');

		// ANGULAR SERVICES
		wp_enqueue_script( 'AngularJS-Resource',
			WP_PLUGIN_URL.'/postworld/lib/'.$angular_version.'/angular-resource.min.js');

		wp_enqueue_script( 'AngularJS-Route',
			WP_PLUGIN_URL.'/postworld/lib/'.$angular_version.'/angular-route.min.js');

		wp_enqueue_script( 'AngularJS-Sanitize',
			WP_PLUGIN_URL.'/postworld/lib/'.$angular_version.'/angular-sanitize.min.js');

		//wp_enqueue_script( 'AngularJS-Animate',
		//	WP_PLUGIN_URL.'/postworld/lib/'.$angular_version.'/angular-animate.min.js');


		///// ANGULAR THIRD PARTY MODULES /////
		
		// ANGULAR UI UTILITIES
		// Development only
		//wp_enqueue_script( 'AngularJS-UI-Utils',
		//	WP_PLUGIN_URL.'/postworld/lib/angular-ui-utils/angular-ui-utils.min.js');
		
		//BOOTSTRAP JS
		wp_enqueue_script( "bootstrap-JS",
			WP_PLUGIN_URL.'/postworld/lib/bootstrap/bootstrap.min.js' );

		// ANGULAR UI : BOOTSTRAP
		//wp_enqueue_script( 'AngularJS-UI-Bootstrap',
		//	plugins_url().'/postworld/lib/angular/ui-bootstrap-tpls-0.6.0.min.js' );
		wp_enqueue_script( 'AngularJS-UI-Bootstrap',
			plugins_url().'/postworld/lib/angular-ui-bootstrap/ui-bootstrap-tpls-0.11.0.min.js' );

		// ANGULAR STRAP : BOOTSTRAP
		//wp_enqueue_script( 'AngularJS-Strap',
		//	WP_PLUGIN_URL.'/postworld/lib/angular-strap/angular-strap.js', $angularDep );

		//wp_enqueue_script( 'AngularJS-Strap',
		//	plugins_url().'/postworld/lib/angular-strap-2.0.0-rc.2/angular-strap.js', $angularDep );

		//wp_enqueue_script( 'AngularJS-Strap-Templates',
		//	plugins_url().'/postworld/lib/angular-strap-2.0.0-rc.2/angular-strap.tpl.js', $angularDep );


		// ANGULAR : INFINITE SCROLL
		wp_enqueue_script( 'angularJS-nInfiniteScroll', plugins_url().'/postworld/lib/ng-infinite-scroll/ng-infinite-scroll.js', $angularDep );
		
		// ANGULAR : TIMER
		wp_enqueue_script( 'AngularJS-Timer',
			plugins_url().'/postworld/lib/angular-timer/angular-timer.js', $angularDep );

		// ANGULAR : TIMER
		wp_enqueue_script( 'AngularJS-Moment',
			plugins_url().'/postworld/lib/angular-moment/angular-moment.min.js', $angularDep );

		// ANGULAR : PARALLAX
		wp_enqueue_script( 'angularJS-Parallax',
			plugins_url().'/postworld/lib/angular-parallax/angular-parallax.js', $angularDep );

		// ANGULAR : ELASTIC
		wp_enqueue_script( 'angularJS-Elastic',
			plugins_url().'/postworld/lib/angular-elastic/angular-elastic.js', $angularDep );


		/////// POSTWORLD APP /////	
		wp_enqueue_script( 	'pw-app-JS',
			WP_PLUGIN_URL.'/postworld/js/app.js', $angularDep );


		///// CREATE.JS /////
		//if( in_array('create.js', $dep) ){	
		// LOCAL COMPONENT
		wp_enqueue_script( 'Postworld-FlashCanvas',
			WP_PLUGIN_URL.'/postworld/js/components/flashCanvas.js', $angularDep);
		//}


		// COMPONENTS
		wp_enqueue_script( 'pw-FeedItem-JS',
			WP_PLUGIN_URL.'/postworld/js/components/feedItem.js', $angularDep );

		wp_enqueue_script( 'pw-TreeView-JS',
			WP_PLUGIN_URL.'/postworld/js/components/treeview.js', $angularDep );

		wp_enqueue_script( 'pw-LoadComments-JS',
			WP_PLUGIN_URL.'/postworld/js/components/loadComments.js', $angularDep );
		
		wp_enqueue_script( 'pw-inputSearch-JS',
			WP_PLUGIN_URL.'/postworld/js/components/inputSearch.js', $angularDep );

		wp_enqueue_script( 'pw-LiveFeed-JS',
			WP_PLUGIN_URL.'/postworld/js/components/liveFeed.js', $angularDep );

		wp_enqueue_script( 'pw-MediaEmbed-JS',
			WP_PLUGIN_URL.'/postworld/js/components/mediaEmbed.js', $angularDep );

		wp_enqueue_script( 'pw-Users-JS',
			WP_PLUGIN_URL.'/postworld/js/components/pwUsers.js', $angularDep );

		wp_enqueue_script( 'pw-Modal-JS',
			WP_PLUGIN_URL.'/postworld/js/components/pwModal.js', $angularDep );

		wp_enqueue_script( 'pw-Embedly-JS',
			WP_PLUGIN_URL.'/postworld/js/components/pwEmbedly.js', $angularDep );


		// CONTROLLERS
		wp_enqueue_script( 'pw-Controllers-JS',
			WP_PLUGIN_URL.'/postworld/js/controllers/pwControllers.js', $angularDep );

		wp_enqueue_script( 'pw-controlMenus-JS',
			WP_PLUGIN_URL.'/postworld/js/controllers/controlMenus.js', $angularDep );

		wp_enqueue_script( 'pw-editPost-JS',
			WP_PLUGIN_URL.'/postworld/js/controllers/editPost.js', $angularDep );

		wp_enqueue_script( 'pw-autoComplete-JS',
			WP_PLUGIN_URL.'/postworld/js/controllers/autoComplete.js', $angularDep );

		wp_enqueue_script( 'pw-Widgets-JS',
			WP_PLUGIN_URL.'/postworld/js/controllers/pwWidgets.js', $angularDep );

		// FILTERS
		wp_enqueue_script( 	'pw-Filters-JS',
			WP_PLUGIN_URL.'/postworld/js/filters/pwFilters.js', $angularDep );

		wp_enqueue_script( 'pw-filterFeed-JS',
			WP_PLUGIN_URL.'/postworld/js/filters/filterFeed.js', $angularDep );

		// SERVICES
		wp_enqueue_script( 'pw-pwData-JS',
			WP_PLUGIN_URL.'/postworld/js/services/pwData.js', $angularDep );

		wp_enqueue_script( 'pw-Services-JS',
			WP_PLUGIN_URL.'/postworld/js/services/pwServices.js', $angularDep );

		wp_enqueue_script( 'pw-pwCommentsService-JS',
			WP_PLUGIN_URL.'/postworld/js/services/pwCommentsService.js', $angularDep );

		wp_localize_script( 'pw-pwCommentsService-JS', 'jsVars', $jsVars);

		// DIRECTIVES
		wp_enqueue_script( 'pw-Directives-JS',
			WP_PLUGIN_URL.'/postworld/js/directives/pwDirectives.js', $angularDep );

		wp_enqueue_script( 'pw-Directives-ListUsers',
			WP_PLUGIN_URL.'/postworld/js/directives/pwUserList.js', $angularDep );

		wp_enqueue_script( 'pw-Directives-pwQuery',
			WP_PLUGIN_URL.'/postworld/js/directives/pwQuery.js', $angularDep );

		wp_enqueue_script( 'pw-Directives-pwGetPost',
			WP_PLUGIN_URL.'/postworld/js/directives/pwGetPost.js', $angularDep );

		wp_enqueue_script( 'pw-Directives-pwImage',
			WP_PLUGIN_URL.'/postworld/js/directives/pwImage.js', $angularDep );

		wp_enqueue_script( 'pw-Directives-pwWindow',
			WP_PLUGIN_URL.'/postworld/js/directives/pwWindow.js', $angularDep );

		// WIZARD
		wp_enqueue_script( 'pw-Wizard',
			plugins_url().'/postworld/js/components/pwWizard.js', $angularDep );

		// WORDPRESS DIRECTIVES
		wp_enqueue_script( 'pw-WpDirectives-Media-Library-JS',
			WP_PLUGIN_URL.'/postworld/js/directives/wpMediaLibrary.js', $angularDep );

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

	$pwSiteGlobals['wordpress'] = array( 
		'ajax_url' => admin_url('admin-ajax.php'),
		'stylesheet_directory_uri' => get_stylesheet_directory_uri(),
		'template_directory_uri' => get_template_directory_uri(),
		'plugins_dir' => WP_PLUGIN_DIR,
		'plugins_url' => WP_PLUGIN_URL,
	);

	$pwGlobalsJs  = "";
	$pwGlobalsJs .= "var pwSiteGlobals = ";
	$pwGlobalsJs .= json_encode( $pwSiteGlobals );
	$pwGlobalsJs .= ";";

	// ENCODE TEMPLATES
	$pwGlobalsJs .= "\n\n";
	$pwGlobalsJs .= "var pwTemplates = ";
	$pwGlobalsJs .= json_encode( pw_get_templates() );
	$pwGlobalsJs .= ";";	

	// ENCODE SITE LANGUAGE
	global $pwSiteLanguage;	
	$pwGlobalsJs .= "\n\n";
	$pwGlobalsJs .= "var pwSiteLanguage = ";
	$pwGlobalsJs .= json_encode( $pwSiteLanguage );
	$pwGlobalsJs .= ";";

	$pwGlobalsJsFile = WP_PLUGIN_DIR.'/postworld/deploy/pwSiteGlobals.js';
	$file = fopen( $pwGlobalsJsFile ,"w" );
	fwrite($file,"$pwGlobalsJs");
	fclose($file);
	chmod($pwGlobalsJsFile, 0755);

	global $angularDep;
	wp_enqueue_script( 'pw-SiteGlobals-JS',
		WP_PLUGIN_URL.'/postworld/deploy/pwSiteGlobals.js' );
	
}



///// PARSE pwGlobals /////
function pwGlobals_parse(){

	global $pw_globals;
	$pw_globals = array();
	$pw_globals['current_view'] = array();


	/////////// USER / PAGE SPECIFIC GLOBALS //////////

	///// POST /////
	if( !empty($GLOBALS['post']->ID) ){
		//$pw_globals["current_view"]["type"] = "post";
		$pw_globals["current_view"]["type"] = $GLOBALS['post']->post_type;
		$pw_globals["current_view"]["post"] = array(
			"post_id" => $GLOBALS['post']->ID,
			"post_name" => $GLOBALS['post']->post_name,
			"post_title" => $GLOBALS['post']->post_title,
			"post_status" => $GLOBALS['post']->post_status
			);
	}

	///// CURRENT USER /////
	$user_id = get_current_user_id();
	if( $user_id != 0 ){
		$userdata = wp_get_current_user();
		unset($userdata->data->user_pass);
		$userdata = (array) $userdata;
		$userdata["postworld"] = array();
		$userdata["postworld"]["vote_power"] = get_user_vote_power( $user_id );
		$userdata["is_admin"] = is_admin();

		// SUPPORT FOR WPMU MEMBERSHIP
		if( function_exists('current_user_is_member') ){
			$userdata["membership"] = array();
			$userdata["membership"]["is_member"] = current_user_is_member();
		}
	} else
		$userdata = 0;
	$pw_globals["current_user"] = $userdata;

	///// DISPLAYED USER /////
	// Support for Buddypress Globals
	if ( function_exists('bp_displayed_user_id') ){
		$displayed_user_id = bp_displayed_user_id();
	} else
		$displayed_user_id = $GLOBALS['post']->post_author;

	if ( isset($displayed_user_id) )
		$displayed_userdata = get_userdata( $displayed_user_id );

	$pw_globals['displayed_user'] = array(
		"user_id" => $displayed_user_id,
		"display_name" => $displayed_userdata->display_name,
		"first_name" => $displayed_userdata->first_name,	
		);



	/////////// SITE WIDE GLOBALS //////////

	///// SITE INFO /////
	$pw_globals["site_info"] = array(
		"name" => get_bloginfo( 'name' ),
		"description" => get_bloginfo( 'description' ),
		);

	///// POST TYPES /////
	$pw_globals["post_types"] = pw_get_post_types();

	///// PATHS /////
	$pw_globals["paths"] = array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'plugin_url' => WP_PLUGIN_URL,
		'plugin_dir' => WP_PLUGIN_DIR,
		"theme_dir"	=>	get_stylesheet_directory(),
		"home_url" => get_bloginfo( 'url' ),
		"wp_url" => get_bloginfo( 'wpurl' ),
		"stylesheet_directory" => get_bloginfo( 'stylesheet_directory' ),

		"template_url" => get_bloginfo( 'template_url' ),
		"postworld_url" => WP_PLUGIN_URL . '/postworld',
		"postworld_dir" => WP_PLUGIN_DIR . '/postworld',
		);

	///// LANGUAGE /////
	$pw_globals['language'] = $pw_settings['language'];


	///// RETURN /////
	return $pw_globals;
}


// Parse Globals After all Plugins Loaded
function parse_postworld_globals(){
 	// Init Globals
	global $pw_globals;
	$pw_globals = pwGlobals_parse();
}
add_action( 'plugins_loaded', 'parse_postworld_globals', 10, 2 );




?>