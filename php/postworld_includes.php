<?

// Define Angular Dependancies
global $angularDep;
$angularDep = array('jquery','AngularJS','AngularJS-Resource','AngularJS-Route', 'AngularJS-Sanitize', 'UnderscoreJS');

function postworld_includes( $args ){

	extract( $args );

	// Default Angular Version
	if( empty( $angular_version ) )
		$angular_version = 'angular-1.2.1';

	// Default Dependencies
	if( empty($dep) ){
		$dep = array();
	}

	// Build Angular Dependancies
	global $angularDep;
	
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

	/* JQuery is added for nInfiniteScroll Directive, if directive is not used, then remove it */
	wp_deregister_script('jquery');
	wp_register_script('jquery', "http" . ($_SERVER['SERVER_PORT'] == 443 ? "s" : "") . "://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js", false, null);
	wp_enqueue_script('jquery','');

	//////////---------- POSTWORLD INCLUDES ----------//////////
	///// DELPLOY FILE INCLUDES /////
	if ( $mode == 'deploy' ){
	
		// POSTWORLD
		wp_register_script( "Postworld-Deploy", WP_PLUGIN_URL.'/postworld/deploy/postworld.min.js' );
		wp_localize_script( 'Postworld-Deploy', 'jsVars', $jsVars);
		wp_enqueue_script(  'Postworld-Deploy' ); //array('Postworld-Libraries') );

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
		
		// UNDERSCORE JS
		wp_enqueue_script( 'UnderscoreJS',
			WP_PLUGIN_URL.'/postworld/lib/underscore/underscore.min.js');

		// ADD GOOGLE MAPS
		if( in_array('google-maps', $dep) ){
			// GOOGLE MAPS
			wp_enqueue_script( 'Google-Maps-API',
				'//maps.googleapis.com/maps/api/js?sensor=false' );
			// ANGULAR UI : GOOGLE MAPS
			wp_enqueue_script( 'AngularJS-Google-Maps',
				plugins_url().'/postworld/lib/angular-google-maps/angular-google-maps.min.js', $angularDep );
		}

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

		// wp_enqueue_script( 'AngularJS-Animate', WP_PLUGIN_URL.'/postworld/lib/angular/angular-animate.min.js');

		///// ANGULAR THIRD PARTY MODULES /////
		
		// ANGULAR UI UTILITIES
		wp_enqueue_script( 'AngularJS-UI-Utils',
			WP_PLUGIN_URL.'/postworld/lib/angular-ui-utils/angular-ui-utils.min.js');
		
		//BOOTSTRAP JS
		wp_enqueue_script( "bootstrap-JS",
			WP_PLUGIN_URL.'/postworld/lib/bootstrap/bootstrap.min.js' );

		// ANGULAR UI : BOOTSTRAP
		//wp_enqueue_script( 'AngularJS-UI-Bootstrap',
		//	plugins_url().'/postworld/lib/angular/ui-bootstrap-tpls-0.6.0.min.js' );
		wp_enqueue_script( 'AngularJS-UI-Bootstrap',
			plugins_url().'/postworld/lib/angular-ui-bootstrap/ui-bootstrap-tpls-0.10.0.min.js' );

		// ANGULAR STRAP : BOOTSTRAP
		//wp_enqueue_script( 'AngularJS-Strap',
		//	WP_PLUGIN_URL.'/postworld/lib/angular-strap/angular-strap.js', $angularDep );

		wp_enqueue_script( 'AngularJS-Strap',
			plugins_url().'/postworld/lib/angular-strap-2.0.0-rc.2/angular-strap.js', $angularDep );

		wp_enqueue_script( 'AngularJS-Strap-Templates',
			plugins_url().'/postworld/lib/angular-strap-2.0.0-rc.2/angular-strap.tpl.js', $angularDep );


		// ANGULAR : TIMER
		wp_enqueue_script( 	'AngularJS-Timer',
			WP_PLUGIN_URL.'/postworld/lib/angular-timer/angular-timer.js', $angularDep );


		/////// POSTWORLD APP /////	
		wp_enqueue_script( 	'pw-app-JS',
			WP_PLUGIN_URL.'/postworld/js/app.js', $angularDep );

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

		// COMPONENTS
		wp_enqueue_script( 'angularJS-nInfiniteScroll', plugins_url().'/postworld/js/components/ng-infinite-scroll.js', $angularDep );

		// WORDPRESS DIRECTIVES
		wp_enqueue_script( 'pw-WpDirectives-Media-Library-JS',
			WP_PLUGIN_URL.'/postworld/js/wp-directives/wpMediaLibrary.js', $angularDep );

	}

	///// INCLUDE SITE WIDE JAVASCRIPT GLOBALS /////

	pwSiteGlobals_include();
	
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
		'ajax_url' => admin_url('admin-ajax.php')
	);

	$pwGlobalsJs  = "";
	$pwGlobalsJs .= "var pwSiteGlobals = ";
	$pwGlobalsJs .= json_encode( $pwSiteGlobals );
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
		$pw_globals["current_view"]["type"] = "post";
		$pw_globals["current_view"]["post"] = array(
			"post_id" => $GLOBALS['post']->ID
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
		$displayed_userdata = get_userdata($displayed_user_id);

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


?>