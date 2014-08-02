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

	// Add Google Maps to include before AngularJS app
	if( in_array( 'google-maps', $pwInject ) ){
		//array_push( $angularDep, 'google-maps' );
	}

	// Add LESS Support
	if( in_array( 'wp-less', $pwInject ) ){
		require_once( WP_PLUGIN_DIR.'/postworld/lib/wp-less/wp-less.php' );
	}
	
	// Add Font Awesome 3
	if( in_array( 'font-awesome-3', $pwInject ) ){
		wp_enqueue_style( 'font-awesome-3',
			WP_PLUGIN_URL.'/postworld/lib/font-awesome-3/css/font-awesome.min.css' );
	}

	// Add ICON X
	if( in_array( 'icon-x', $pwInject ) ){
		wp_enqueue_style( 'icon-x',
			WP_PLUGIN_URL.'/postworld/lib/icon-x/icon-x.css' );
	}

	// Add GLYPHICONS
	if( in_array( 'glyphicons-halflings', $pwInject ) ){
		wp_enqueue_style( 'glyphicons-halflings',
			WP_PLUGIN_URL.'/postworld/lib/glyphicons/glyphicons-halflings.css' );
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
	///// DEPLOY FILE INCLUDES /////
	if ( $mode == 'deploy' ){
	
		// ANGULAR
		//wp_enqueue_script( 'AngularJS',
		//	WP_PLUGIN_URL.'/postworld/lib/'.$angular_version.'/angular.min.js');

		// POSTWORLD
		wp_register_script( "Postworld-Deploy", WP_PLUGIN_URL.'/postworld/deploy/postworld.min.js', array(), $postworld_version );
		wp_localize_script( 'Postworld-Deploy', 'jsVars', $jsVars);
		wp_enqueue_script(  'Postworld-Deploy' );

		// ADD GOOGLE MAPS
		if( in_array('google-maps', $pwInject) ){
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


		// MASONRY
		wp_enqueue_script( 'Masonry-JS',
			WP_PLUGIN_URL.'/postworld/lib/masonry/masonry.pkgd.min.js');		

		wp_enqueue_script( 'ImagesLoaded-JS',
			WP_PLUGIN_URL.'/postworld/lib/masonry/imagesloaded.pkgd.min.js');
		
		// HISTORY.JS
		//wp_enqueue_script( 'History-JS',
		//	WP_PLUGIN_URL.'/postworld/lib/history.js/native.history.js');	

		///// THIRD PARTY LIBRARIES /////

		// ADD GOOGLE MAPS
		if( in_array('google-maps', $pwInject) ){
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
		wp_enqueue_script( 'AngularJS-Strap-Dimensions',
			WP_PLUGIN_URL.'/postworld/lib/angular-strap/angular-strap-dimensions.js', $angularDep );

		wp_enqueue_script( 'AngularJS-Strap-Tooltip',
			WP_PLUGIN_URL.'/postworld/lib/angular-strap/angular-strap-tooltip.js', $angularDep );

		wp_enqueue_script( 'AngularJS-Strap-Popover',
			WP_PLUGIN_URL.'/postworld/lib/angular-strap/angular-strap-popover.js', $angularDep );


		//wp_enqueue_script( 'AngularJS-Strap',
		//	plugins_url().'/postworld/lib/angular-strap-2.0.0-rc.2/angular-strap.js', $angularDep );

		//wp_enqueue_script( 'AngularJS-Strap-Templates',
		//	plugins_url().'/postworld/lib/angular-strap-2.0.0-rc.2/angular-strap.tpl.js', $angularDep );


		// ANGULAR : INFINITE SCROLL
		//wp_enqueue_script( 'angularJS-nInfiniteScroll', plugins_url().'/postworld/lib/ng-infinite-scroll/ng-infinite-scroll.js', $angularDep );
		
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

		// ANGULAR : MASONRY
		wp_enqueue_script( 'angularJS-Masonry',
			plugins_url().'/postworld/lib/angular-masonry/angular-masonry.js', $angularDep );


		/////// POSTWORLD APP /////	
		wp_enqueue_script( 	'pw-app-JS',
			WP_PLUGIN_URL.'/postworld/js/app.js', $angularDep );


		///// CREATE.JS /////
		//if( in_array('create.js', $pwInject) ){	
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

		wp_enqueue_script( 'pw-Input-JS',
			WP_PLUGIN_URL.'/postworld/js/components/pwInput.js', $angularDep );

		wp_enqueue_script( 'pw-InfiniteGallery-JS',
			WP_PLUGIN_URL.'/postworld/js/components/pwInfiniteGallery.js', $angularDep );


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

		wp_enqueue_script( 'pw-Directives-pwMenu',
			WP_PLUGIN_URL.'/postworld/js/directives/pwMenu.js', $angularDep );

		wp_enqueue_script( 'pw-Directives-pwWindow',
			WP_PLUGIN_URL.'/postworld/js/directives/pwWindow.js', $angularDep );

		// MODULES
		wp_enqueue_script( 'pw-Modules-Compile',
			WP_PLUGIN_URL.'/postworld/js/modules/pwCompile.js', $angularDep );

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

	$pwJsFile = WP_PLUGIN_DIR.'/postworld/deploy/pwSiteGlobals.js';
	$file = fopen( $pwJsFile ,"w" );
	fwrite($file,"$pwJs");
	fclose($file);
	chmod($pwJsFile, 0755);

	global $angularDep;
	wp_enqueue_script( 'pw-SiteGlobals-JS',
		WP_PLUGIN_URL.'/postworld/deploy/pwSiteGlobals.js', array(), hash( 'md5', 4 ) );
	
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
	} else
		$displayed_user_id = $GLOBALS['post']->post_author;

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