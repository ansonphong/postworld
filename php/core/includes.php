<?php

/**
 * Defines and injects Postworld dependencies
 * into the HTML / Browser.
 *
 * To be run on the Action Hooks: 
 * wp_enqueue_scripts / admin_enqueue_scripts
 *
 * @since 0.1
 */
function postworld_includes( $args ){
	extract( $args );

	// Add hook for admin <head></head>
	add_action('admin_print_scripts', 'pwGlobals_print', 8 );
	add_action('admin_print_scripts', 'pwBootstrapPostworldAdmin_print', 20 );
	
	// Add hook for front-end <head></head>
	add_action('wp_head', 'pwGlobals_print', 8 );

	global $pw;
	global $pwSiteGlobals;

	// Default Angular Version
	if( empty( $angular_version ) )
		$angular_version = 'angular-1.3.0-beta.13';

	// Add injectors from Site Globals
	$pw['inject'] = ( isset( $pwSiteGlobals['inject'] ) ) ?
		$pwSiteGlobals['inject'] : array();

	// Override with injectors from $args
	$pw['inject'] = ( isset( $args['inject'] ) ) ?
		$args['inject'] : $pw['inject'];

	// Add Additional Angular Modules
	$pw['angularModules'] = apply_filters( 'pw_angular_modules', $pw['angularModules'] );

	// Add Angular Modules to the Postworld Inject array
	$pw['inject'] = array_merge( $pw['inject'], $pw['angularModules'] );

	// Add Glyphicons for Admin
	if( is_admin() ){
		array_push( $pw['inject'],
			'glyphicons-halflings'
			);
	}

	//////////////////////// INJECTIONS //////////////////////

	/* JQuery is added for nInfiniteScroll Directive, if directive is not used, then remove it */
	//wp_deregister_script('jquery');
	//wp_register_script('jquery', "http" . ($_SERVER['SERVER_PORT'] == 443 ? "s" : "") . "://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js", false, null);
	wp_enqueue_script('jquery','');

	// + MASONRY
	if( in_array( 'masonry.js', $pw['inject'] ) ){
		if( pw_mode() === 'deploy' ){
			wp_enqueue_script( 'Masonry-JS',
				POSTWORLD_URI.'/deploy/package-masonry.min.js');	
		}
		else{
			wp_enqueue_script( 'Masonry-JS',
				POSTWORLD_URI.'/lib/masonry/masonry.pkgd.min.js');		
			wp_enqueue_script( 'ImagesLoaded-JS',
				POSTWORLD_URI.'/lib/masonry/imagesloaded.pkgd.min.js');
		}
		
	}

	// + Google Maps to include before AngularJS app
	if( in_array( 'google-maps', $pw['inject'] ) ){
		//array_push( $angularDep, 'google-maps' );
	}

	// + LESS Support
	if( in_array( 'wp-less', $pw['inject'] ) ){
		require_once( POSTWORLD_PATH.'/lib/wp-less/wp-less.php' );
	}
	

	/*
	///// DEPRECIATED /////

	// + Font Awesome 3
	if( in_array( 'font-awesome-3', $pw['inject'] ) ){
		wp_enqueue_style( 'font-awesome-3',
			"//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" );
			// Todo : parse from LESS
			//POSTWORLD_URI.'/lib/font-awesome-3/css/font-awesome.min.css' );
	}

	// + ICOMOON
	if( in_array( 'icomoon', $pw['inject'] ) ){
		wp_enqueue_style( 'icomoon',
			POSTWORLD_URI.'/lib/icomoon/style.css' );
	}

	// + ICON X
	if( in_array( 'icon-x', $pw['inject'] ) ){
		wp_enqueue_style( 'icon-x',
			POSTWORLD_URI.'/lib/icon-x/icon-x.css' );
	}

	// + GLYPHICONS
	if( in_array( 'glyphicons-halflings', $pw['inject'] ) ){
		wp_enqueue_style( 'glyphicons-halflings',
			POSTWORLD_URI.'/lib/glyphicons/glyphicons-halflings.css' );
	}
	*/

	// Queues up all the selected iconsets
	pw_load_iconsets();

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


		// HISTORY.JS
		//wp_enqueue_script( 'History-JS',
		//	POSTWORLD_URI.'/lib/history.js/native.history.js');	

		///// THIRD PARTY LIBRARIES /////

		/*
		// CREATE.JS
		// Development Only ( Not in Grunt File / Deploy Version )
		wp_enqueue_script( 'CreateJS-Easel',
			POSTWORLD_URI.'/lib/create.js/easeljs-0.7.0.min.js');
		wp_enqueue_script( 'CreateJS-Tween',
			POSTWORLD_URI.'/lib/create.js/tweenjs-0.5.0.min.js');
		wp_enqueue_script( 'CreateJS-MovieClip',
			POSTWORLD_URI.'/lib/create.js/movieclip-0.7.0.min.js');
		*/
		
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
		// ANGULAR UI : BOOTSTRAP
		wp_enqueue_script( 'AngularJS-UI-Bootstrap',
			POSTWORLD_URI.'/lib/angular-ui-bootstrap/ui-bootstrap-tpls-0.12.0.min.js' );

		// ANGULAR : INFINITE SCROLL
		//wp_enqueue_script( 'angularJS-nInfiniteScroll', plugins_url().'/postworld/lib/ng-infinite-scroll/ng-infinite-scroll.js', $angularDep );
		
		// ANGULAR : TIMER
		wp_enqueue_script( 'AngularJS-Timer',
			POSTWORLD_URI.'/lib/angular-timer/angular-timer.js', $angularDep );

		// ANGULAR : PARALLAX
		wp_enqueue_script( 'angularJS-Parallax',
			POSTWORLD_URI.'/lib/angular-parallax/angular-parallax.js', $angularDep );

		// ANGULAR : ELASTIC
		wp_enqueue_script( 'angularJS-Elastic',
			POSTWORLD_URI.'/lib/angular-elastic/angular-elastic.js', $angularDep );

		// ANGULAR : MASONRY
		wp_enqueue_script( 'angularJS-Masonry',
			POSTWORLD_URI.'/lib/angular-masonry/angular-masonry.js', $angularDep );

		// ANGULAR : CHECKLIST MODEL
		wp_enqueue_script( 'angularJS-ChecklistModel',
			POSTWORLD_URI.'/lib/checklist-model/checklist-model.js', $angularDep );
		
		/////// POSTWORLD APP /////	
		// TODO : blob through the dirs and get all the js files, auto-include in foreach
		wp_enqueue_script( 	'pw-app-JS',
			POSTWORLD_URI.'/js/app.js', $angularDep );

		///// CREATE.JS /////
		//if( in_array('create.js', $pw['inject']) ){	
		// LOCAL COMPONENT
		//wp_enqueue_script( 'Postworld-FlashCanvas',
		//	POSTWORLD_URI.'/js/components/flashCanvas.js', $angularDep);
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
			POSTWORLD_URI.'/js/components/pwGallery.js', $angularDep );

		wp_enqueue_script( 'pw-geocode-JS',
			POSTWORLD_URI.'/js/components/pwGeocode.js', $angularDep );

		wp_enqueue_script( 'pw-timezone-JS',
			POSTWORLD_URI.'/js/components/pwTimezone.js', $angularDep );

		wp_enqueue_script( 'pw-UI-JS',
			POSTWORLD_URI.'/js/components/pwUi.js', $angularDep );

		wp_enqueue_script( 'pw-filterFeed-JS',
			POSTWORLD_URI.'/js/components/editFeed.js', $angularDep );
		

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
		
		
		// SERVICES
		wp_enqueue_script( 'pw-pwData-JS',
			POSTWORLD_URI.'/js/services/pwData.js', $angularDep );

		wp_enqueue_script( 'pw-Services-JS',
			POSTWORLD_URI.'/js/services/pwServices.js', $angularDep );

		wp_enqueue_script( 'pw-Iconsets-JS',
			POSTWORLD_URI.'/js/services/pwIconsets.js', $angularDep );

		wp_enqueue_script( 'pw-pwCommentsService-JS',
			POSTWORLD_URI.'/js/services/pwCommentsService.js', $angularDep );
		

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
		
		wp_enqueue_script( 'pw-Directives-pwDevices',
			POSTWORLD_URI.'/js/directives/pwDevices.js', $angularDep );
		
		// MODULES
		wp_enqueue_script( 'pw-Modules-Compile',
			POSTWORLD_URI.'/js/modules/pwCompile.js', $angularDep );
		
		// WIZARD
		wp_enqueue_script( 'pw-Wizard',
			POSTWORLD_URI.'/js/components/pwWizard.js', $angularDep );

		// WORDPRESS DIRECTIVES
		wp_enqueue_script( 'pw-WpDirectives-Media-Library-JS',
			POSTWORLD_URI.'/js/directives/wpMediaLibrary.js', $angularDep );
		

		// This is causing issues
		//wp_localize_script( 'pw-pwCommentsService-JS', 'jsVars', $jsVars);

	}

	// + GOOGLE MAPS
	if( in_array('google-maps', $pw['inject']) ){
		// GOOGLE MAPS
		wp_enqueue_script( 'Google-Maps-API',
			'//maps.googleapis.com/maps/api/js?sensor=false' );
		// ANGULAR UI : GOOGLE MAPS
		wp_enqueue_script( 'AngularJS-Google-Maps',
			POSTWORLD_URI.'/lib/angular-google-maps/angular-google-maps.min.js' );
	}

	// + CALENDAR
	if( in_array( 'ui.calendar', $pw['inject'] ) ){

		if( pw_mode() === 'deploy' ){
			wp_enqueue_script( 'Postworld-Package-Angular-FullCalendar',
				POSTWORLD_URI.'/deploy/package-angular-fullcalendar.min.js' );
		}
		else{
			// Full Calendar
			wp_enqueue_script( 'Full-Calendar-Moment-JS',
				POSTWORLD_URI.'/lib/fullcalendar-2.2.5/lib/moment.min.js' );

			wp_enqueue_script( 'Full-Calendar-JS',
				POSTWORLD_URI.'/lib/fullcalendar-2.2.5/fullcalendar.min.js' );

			//wp_enqueue_style( 'Full-Calendar-CSS',
			//	POSTWORLD_URI.'/lib/fullcalendar-2.2.5/fullcalendar.min.css' );		

			wp_enqueue_script( 'Full-Calendar-jQuery-UI-JS',
				POSTWORLD_URI.'/lib/fullcalendar-2.2.5/lib/jquery-ui.custom.min.js' );

			// Angular UI Calendar
			wp_enqueue_script( 'Angular-UI-Calendar-JS',
				POSTWORLD_URI.'/lib/ui-calendar-master/src/calendar.js' );
		}

	}

	// + ANGULAR MOMENT
	if( in_array( 'angularMoment', $pw['inject'] ) ){

		if( pw_mode() === 'deploy' ){
			wp_enqueue_script( 'Postworld-Package-Angular-Moment',
				POSTWORLD_URI.'/deploy/package-angular-moment.min.js' );
		}
		else{
			// MOMENT.JS
			wp_enqueue_script( 'Moment-JS',
				POSTWORLD_URI.'/lib/moment.js/moment.min.js', $angularDep);
			// ANGULAR - MOMENT
			wp_enqueue_script( 'AngularJS-Moment',
				POSTWORLD_URI.'/lib/angular-moment/angular-moment.min.js', $angularDep );
			// MOMENT-TIMEZONE.JS
			wp_enqueue_script( 'Moment-Timezone-JS',
				POSTWORLD_URI.'/lib/moment.js/moment-timezone.min.js', $angularDep);
			// MOMENT-TIMEZONE DATA.JS
			wp_enqueue_script( 'Moment-Timezone-Data-JS',
				POSTWORLD_URI.'/lib/moment.js/moment-timezone-data.js', $angularDep);
		}
		

	}

	// Include Admin Scripts if in Admin
	if( is_admin() ){
		// Include JS files
		pw_include_admin_scripts();
		// Include Styles
		wp_enqueue_style( 'pw-admin-styles', POSTWORLD_URI.'/admin/less/style.less' );
		// Localize Global Vars 
		pwAdminGlobals_include();
	}

	///// INCLUDE SITE WIDE JAVASCRIPT GLOBALS /////
	// Dynamically generate javascript file
	// After all Plugins and Theme Loaded
	//add_action( 'init', 'pwSiteGlobals_include');
	pwSiteGlobals_include();

}

function pw_include_admin_scripts(){
	global $angularDep;

	// APP
	//wp_enqueue_script('Infinite-App', get_infinite_directory_uri().'/js/app.js', $angularDep );

	// CONTROLLERS : ADMIN
	wp_enqueue_script('Postworld-Admin-Options', get_infinite_directory_uri().'/js/controllers-admin/options.js', $angularDep );
	wp_enqueue_script('Postworld-Admin-Layouts', get_infinite_directory_uri().'/js/controllers-admin/layouts.js', $angularDep );
	wp_enqueue_script('Postworld-Admin-Styles', get_infinite_directory_uri().'/js/controllers-admin/styles.js', $angularDep );
	wp_enqueue_script('Postworld-Admin-Sidebars', get_infinite_directory_uri().'/js/controllers-admin/sidebars.js', $angularDep );
	wp_enqueue_script('Postworld-Admin-Feeds', get_infinite_directory_uri().'/js/controllers-admin/feeds.js', $angularDep );
	//wp_enqueue_script('Postworld-Admin-Term-Feeds', get_infinite_directory_uri().'/js/controllers-admin/term-feeds.js', $angularDep );
	wp_enqueue_script('Postworld-Admin-Backgrounds', get_infinite_directory_uri().'/js/controllers-admin/backgrounds.js', $angularDep );
	wp_enqueue_script('Postworld-Admin-Iconsets', get_infinite_directory_uri().'/js/controllers-admin/iconsets.js', $angularDep );
	wp_enqueue_script('Postworld-Admin-Shortcodes', get_infinite_directory_uri().'/js/controllers-admin/shortcodes.js', $angularDep );
	wp_enqueue_script('Postworld-Admin-Database', get_infinite_directory_uri().'/js/controllers-admin/database.js', $angularDep );
	
	// DIRECTIVES : ADMIN
	wp_enqueue_script('Postworld-Admin', get_infinite_directory_uri().'/js/directives-admin/pwAdmin.js', $angularDep );
	wp_enqueue_script('Infinite-Save-Options', get_infinite_directory_uri().'/js/directives-admin/iSaveOption.js', $angularDep );
	wp_enqueue_script('Infinite-iData', get_infinite_directory_uri().'/js/services/iData.js', $angularDep );

	// DIRECTIVES
	wp_enqueue_script('Infinite-Directives', get_infinite_directory_uri().'/js/directives/iDirectives.js', $angularDep );
	
	/////// ANGULAR : JQUERY SLIDER /////
	wp_enqueue_script( 'angularJS-jQuery-Slider', POSTWORLD_URI.'/lib/angular-jquery-slider/slider.js', $angularDep );
	///// JQUERY /////
	// Required for Slider
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-slider' );
	wp_enqueue_script( 'jquery-ui-widget' );
	wp_enqueue_script( 'jquery-ui-mouse' );

	
}

///// WINDOW JAVASCRIPT DATA INJECTION /////
// Inject Current User Data into Window
function pwGlobals_print() {
	global $pw;
	?><script type="text/javascript">/* <![CDATA[ */
		pw = {};
		pw.angularModules = <?php echo json_encode( $pw['angularModules'] ) ?>;
		pw.info = <?php echo json_encode( $pw['info'] ); ?>;
		pw.view = <?php echo json_encode( pw_current_view() ); ?>;
		pw.query = <?php echo json_encode( $pw['query'] ); ?>;
		pw.background = <?php echo json_encode( pw_current_background() ); ?>;
		pw.posts = <?php echo json_encode( apply_filters( PW_POSTS, array() ) ); ?>;
		pw.user = <?php echo json_encode( pw_current_user() ); ?>;
		pw.users = <?php echo json_encode( apply_filters( PW_USERS, array() ) ); ?>;
		pw.device = <?php echo json_encode( pw_device_meta() ); ?>;
	/* ]]> */</script><?php
}

function pwBootstrapPostworldAdmin_print() {
	// Bootstraps the postworldAdmin module to the document in select instances
	$screen = get_current_screen();
	//pw_log( $screen );

	// Create filter here to add to array of pages it boostraps on
	$bootstrap = array(
		'base'				=>	array( 'post', 'edit', 'widgets', 'profile', 'user-edit', 'edit-tags' ),
		'base_substring'	=>	array( 'postworld' ),
		);

	// Filter for themes to modify
	$bootstrap = apply_filters( 'pw_admin_bootstrap_angular', $bootstrap );

	// Boolean whether or not to bootstrap on current page
	$do_boostrap = false;

	// If the current screen base is a bootstrap base
	if( in_array( $screen->base, $bootstrap['base'] ) )
		$do_boostrap = true;

	// Iterate through each of the base substrings
	if( $do_boostrap == false )
		foreach( $bootstrap['base_substring'] as $substring ){
			if( strpos( $screen->base, $substring) !== false )
				$do_boostrap = true;
		}

	// If nothing triggered bootstrapping
	if( !$do_boostrap  )
		return false;

	if( is_admin() ): ?>

		<script>
			///// BOOTSTRAP APP /////
			angular.element(document).ready(function() {
				
				/**
				 * AngularJS prevents the submission of forms
				 * without an action attribute. In the Admin
				 * this is bad because some forms don't have one.
				 * Here we have to force an action attribute for every
				 * form element which doesn't have one.
				 *
				 * @link https://docs.angularjs.org/api/ng/directive/form
				 */
				jQuery('form').attr('action', function(){
					if( typeof jQuery(this).attr('action') === 'undefined' )
						jQuery(this).attr('action', '<?php echo $_SERVER["PHP_SELF"]; ?>');
				});

				// Bootstrap the app
				// Must come after adding action attributes
				angular.bootstrap(document, ['postworldAdmin']);

			});
		</script>
	<?php endif;
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

	///// TAXONOMIES /////
	$pwSiteGlobals["taxonomies"] = pw_get_taxonomies( array(  ),'objects');

	///// FIELD MODEL /////
	$pwSiteGlobals["fields"] = pw_field_models();

	///// PRINT JAVASCRIPT /////
	// SITE GLOBALS
	$pwJs  = "";
	$pwJs .= "var pwSiteGlobals = ";
	$pwJs .= json_encode( $pwSiteGlobals );
	$pwJs .= ";";

	// MODULES
	$pwJs .= "\n\n";
	$pwJs .= "pw.modules = ";
	$pwJs .= json_encode( pw_modules_outline() );
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

	// ICON SETS
	$pwJs .= "\n\n";
	$pwJs .= "pw.iconsets = ";
	$pwJs .= json_encode( pw_get_iconsets() );
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
		POSTWORLD_URI . $globals_path, array(), hash( 'sha256', $pwJs ) );
	
}

function pw_current_user(){
	$user_id = get_current_user_id();
	if( $user_id != 0 ){
		$userdata = wp_get_current_user();
		unset($userdata->data->user_pass);
		$userdata = (array) $userdata;
		$userdata["postworld"] = array();
		$userdata["postworld"]["vote_power"] = pw_get_user_vote_power( $user_id );

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

	///// SECURITY /////
	$pw["security"] = array();
	// Set the default security mode
	$pw["security"]["mode"] = "user";

	///// LANGUAGE /////
	$pw['language'] = $pw_settings['language'];

	///// INJECTIONS /////
	//$pw['inject'] = $pw['inject'];

	///// URL QUERY VARS /////
	$pw['url_vars'] = $_GET;

	///// PW MODULES /////
	$modules = pw_get_option( array( 'option_name' => PW_OPTIONS_MODULES ) );
	$pw['info']['modules'] = $modules;
	
	//// THEME VERSION /////
	$pw['info']['theme_version'] = apply_filters( PW_THEME_VERSION, $pw['info']['version'] );

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
	//global $pw['inject'];
	global $pw;
	return $pw['inject'];
}

//////////// ADMIN GLOBALS ////////////
function pwAdminGlobals_include(){

	if( !is_admin() )
		return false;

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
		POSTWORLD_URI.$file_path, array(), hash( 'md5', $js ), true );

}

function pwAdminGlobals_parse(){
	global $pwAdminGlobals;

	/// TEMPLATES ///
	$pwAdminGlobals['templates'] = array();
	$pwAdminGlobals['templates']['php'] = pw_get_templates( array( 'ext' => 'php', 'path_type' => 'dir', 'output' => 'ids' ) );
	$pwAdminGlobals['templates']['html'] = pw_get_templates( array( 'ext' => 'html', 'path_type' => 'url', 'output' => 'ids' ) );

	/// SIDEBARS ///
	$pwAdminGlobals['sidebars'] = pw_get_option( array( 'option_name' => PW_OPTIONS_SIDEBARS ) );

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
	if( is_array( $fonts ) )
		foreach( $fonts as $font ){
			echo "\n<link href='http://fonts.googleapis.com/css?family=".$font['code']."' rel='stylesheet' type='text/css'>";
		}
	// Return the fonts, incase a function wants to see them
	return $fonts;
}

add_action( 'wp_enqueue_scripts', 'pw_include_bootstrap_styles', 11 );
function pw_include_bootstrap_styles(){
	global $pw;
	$inject = $pw['inject'];

	if( !in_array( 'bootstrap', $inject ) )
		return false;

	// If LESS is included in injectors
	if( in_array( 'wp-less', $inject ) ){
		// BOOSTRAP LESS
		wp_enqueue_style( 'bootstrap-less', get_infinite_directory_uri() . '/packages/bootstrap/less/bootstrap.less' );
    	return;
    }

    // If LESS not included
    wp_enqueue_style( 'bootstrap-cdn', '//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css' );
    
}

add_action( 'wp_head', 'pw_add_base' );
function pw_add_base(){
	global $pw;
	?>
	<base href="<?php echo $pw['view']['base_url'] ?>">
	<?php
}

?>