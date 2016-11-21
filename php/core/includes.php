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
/**
 * @todo Structure this in a modular way, where it's not spaghetti & codeballs
 */

function postworld_includes( $vars ){

	$site_version = pw_site_version();
	$in_footer = pw_config('includes.js.in_footer');

	// Add hook for admin <head></head>
	add_action('admin_print_scripts', 'pwGlobals_print', 8 );
	add_action('admin_print_scripts', 'pwBootstrapPostworldAdmin_print', 20 );
	
	// Add hook for front-end <head></head>
	add_action('wp_head', 'pwGlobals_print', 8 );

	global $pw;

	// Default Angular Version
	if( empty( $angular_version ) )
		$angular_version = 'angular-1.4.8';

	// Add injectors from Site Globals
	$config_inject = pw_config('inject');
	$pw['inject'] = ( !empty($config_inject) ) ?
		$config_inject : array();

	// Override with injectors from $vars
	$pw['inject'] = ( isset( $vars['inject'] ) ) ?
		$vars['inject'] : $pw['inject'];

	// Add Additional Angular Modules
	$pw['angular_modules'] = apply_filters( 'pw_angular_modules', $pw['angular_modules'] );

	if( is_admin() ){
		$pw['angular_modules'][] = 'colorpicker.module';
	}

	// Add Angular Modules to the Postworld Inject array
	$pw['inject'] = array_merge( $pw['inject'], $pw['angular_modules'] );

	// Add Glyphicons for Admin
	if( is_admin() ){
		array_push( $pw['inject'],
			'glyphicons-halflings',
			'angular-bootstrap-colorpicker'
			);
	}

	// Ensure Underscore is included
	wp_enqueue_script('underscore');

	//////////////////////// INJECTIONS //////////////////////

	// + MASONRY
	if( in_array( 'masonry.js', $pw['inject'] ) ){
		if( pw_mode() === 'deploy' ){

			// DEPRECIATED METHOD
			
			/*
			wp_enqueue_script(
				'Masonry-JS',
				POSTWORLD_URI.'/deploy/package-masonry.min.js',
				array('underscore'),
				$pw['info']['version'],
				$in_footer);
			*/
			
			
			pw_register_script( array(
				'group' => POSTWORLD_APP,
				'handle' => 'package-masonry',
				'file' => POSTWORLD_DIR . '/deploy/package-masonry.min.js',
				'version' => $pw['info']['version'],
				'priority' => 50,
				));

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

	// Queues up all the selected iconsets
	pw_load_iconsets();

	/**
	 * @todo : OMIT THIS & TEST, LEGACY CODE
	 */
	// All Dynamic Paths and Wordpress PHP data that needs to be added to JS files
	$jsVars = array(	'ajaxurl' 		=> admin_url( 'admin-ajax.php' ),
						'pluginurl' 	=> WP_PLUGIN_URL,
						'user_id'		=> get_current_user_id(),
						'is_admin'		=> is_admin(),
					);

	//////////---------- POSTWORLD INCLUDES ----------//////////
	///// DEPLOY FILE INCLUDES /////
	if ( pw_mode() == 'deploy' ){
	
		global $angularDep;
		$angularDep = array(
			'underscore',
			'jquery',
			//'Postworld-Deploy',
			);

		wp_enqueue_script('underscore');

		if( isset( $vars['js_deploy'] ) && !is_admin() ){
			// CUSTOM DEPLOY JS
			wp_enqueue_script( "Deploy-JS", $vars['js_deploy'], array(), $pw['info']['version'] );
		}
		else{
			// POSTWORLD

			// DEPRECIATED METHOD
			/*
			wp_register_script(
				"Postworld-Deploy",
				POSTWORLD_URI.'/deploy/postworld.min.js',
				array('underscore'),
				$pw['info']['version'],
				$in_footer );
			wp_localize_script( 'Postworld-Deploy', 'jsVars', $jsVars);
			wp_enqueue_script(  'Postworld-Deploy' );
			*/
			
			pw_register_script( array(
				'group' => POSTWORLD_APP,
				'handle' => 'postworld-core',
				'file' => POSTWORLD_DIR . '/deploy/postworld.min.js',
				'version' => $pw['info']['version'],
				'in_footer' => $in_footer,
				'priority' => 100,
				));
			
		}

	}
	///// DEVELOPMENT FILE INCLUDES /////
	else if ( pw_mode() == 'dev' ){
		
		// Build Angular Dependancies
		global $angularDep;
		$angularDep = array(
			'jquery',
			'underscore',
			'DeepMerge',
			'AngularJS',
			'AngularJS-Resource',
			'AngularJS-Route',
			'AngularJS-Sanitize',
			);

		///// JAVASCRIPT LIBRARIES /////

		// UNDERSCORE JS
		wp_enqueue_script('underscore');
		wp_enqueue_script('jquery');

		$include_scripts = array(

			'core' => array(
				// Core Libraries
				'DeepMerge' => POSTWORLD_URI.'/lib/deepmerge/deepmerge.js',
				'PHP.JS' => POSTWORLD_URI.'/lib/php.js/php.js',
				'AngularJS' => POSTWORLD_URI.'/lib/'.$angular_version.'/angular.min.js',

				// Angular Services
				'AngularJS-Resource' => POSTWORLD_URI.'/lib/'.$angular_version.'/angular-resource.min.js',
				'AngularJS-Route' => POSTWORLD_URI.'/lib/'.$angular_version.'/angular-route.min.js',
				'AngularJS-Sanitize' => POSTWORLD_URI.'/lib/'.$angular_version.'/angular-sanitize.min.js',
				'AngularJS-Touch' => POSTWORLD_URI.'/lib/'.$angular_version.'/angular-touch.min.js',
				'AngularJS-Aria' => POSTWORLD_URI.'/lib/'.$angular_version.'/angular-aria.min.js',
				'AngularJS-Animate' => POSTWORLD_URI.'/lib/'.$angular_version.'/angular-animate.min.js'
				),

			'angular_thirdparty' => array(
				'AngularJS-UI-Bootstrap' => POSTWORLD_URI.'/lib/angular-ui-bootstrap/ui-bootstrap-tpls-0.14.3.min.js',
				'angularJS-nInfiniteScroll' => POSTWORLD_URI.'/lib/ng-infinite-scroll/ng-infinite-scroll-1.2.js',
				'angularJS-Parallax' => POSTWORLD_URI.'/lib/angular-parallax/angular-parallax.js',
				'angularJS-Elastic' => POSTWORLD_URI.'/lib/angular-elastic/angular-elastic.js',
				'angularJS-Masonry' => POSTWORLD_URI.'/lib/angular-masonry/angular-masonry.js',
				'angularJS-ChecklistModel' => POSTWORLD_URI.'/lib/checklist-model/checklist-model.js'
				),

			'postworld_core' => array(
				POSTWORLD_APP => POSTWORLD_URI.'/js/app.js',

				// Components
				'pw-FeedItem-JS' => POSTWORLD_URI.'/js/components/feedItem.js',
				'pw-TreeView-JS' => POSTWORLD_URI.'/js/components/treeview.js',
				'pw-LoadComments-JS' => POSTWORLD_URI.'/js/components/loadComments.js',
				'pw-inputSearch-JS' => POSTWORLD_URI.'/js/components/inputSearch.js',
				'pw-LiveFeed-JS' => POSTWORLD_URI.'/js/components/liveFeed.js',
				'pw-MediaEmbed-JS' => POSTWORLD_URI.'/js/components/mediaEmbed.js',
				'pw-Users-JS' => POSTWORLD_URI.'/js/components/pwUsers.js',
				'pw-Modal-JS' => POSTWORLD_URI.'/js/components/pwModal.js',
				'pw-Comments-JS' => POSTWORLD_URI.'/js/components/pwComments.js',
				'pw-Embedly-JS' => POSTWORLD_URI.'/js/components/pwEmbedly.js',
				'pw-Input-JS' => POSTWORLD_URI.'/js/components/pwInput.js',
				'pw-InfiniteGallery-JS' => POSTWORLD_URI.'/js/components/pwGallery.js',
				'pw-geocode-JS' => POSTWORLD_URI.'/js/components/pwGeocode.js',
				'pw-timezone-JS' => POSTWORLD_URI.'/js/components/pwTimezone.js',
				'pw-UI-JS' => POSTWORLD_URI.'/js/components/pwUi.js',
				'pw-colors-JS' => POSTWORLD_URI.'/js/components/pwColors.js',
				'pw-filterFeed-JS' => POSTWORLD_URI.'/js/components/editFeed.js',

				// Controllers
				'pw-Controllers-JS' => POSTWORLD_URI.'/js/controllers/pwControllers.js',
				'pw-controlMenus-JS' => POSTWORLD_URI.'/js/controllers/controlMenus.js',
				'pw-editPost-JS' => POSTWORLD_URI.'/js/controllers/editPost.js',
				'pw-autoComplete-JS' => POSTWORLD_URI.'/js/controllers/autoComplete.js',
				'pw-Widgets-JS' => POSTWORLD_URI.'/js/controllers/pwWidgets.js',

				// Filters
				'pw-Filters-JS' => POSTWORLD_URI.'/js/filters/pwFilters.js',

				// Services
				'pw-pwData-JS' => POSTWORLD_URI.'/js/services/pwData.js',
				'pw-Services-JS' => POSTWORLD_URI.'/js/services/pwServices.js',
				'pw-Iconsets-JS' => POSTWORLD_URI.'/js/services/pwIconsets.js',
				'pw-pwCommentsService-JS' => POSTWORLD_URI.'/js/services/pwCommentsService.js',

				// Directives
				'pw-Directives-JS' => POSTWORLD_URI.'/js/directives/pwDirectives.js',
				'pw-Directives-ListUsers' => POSTWORLD_URI.'/js/directives/pwUserList.js',
				'pw-Directives-pwQuery' => POSTWORLD_URI.'/js/directives/pwQuery.js',
				'pw-Directives-pwGetPost' => POSTWORLD_URI.'/js/directives/pwGetPost.js',
				'pw-Directives-pwUsers' => POSTWORLD_URI.'/js/directives/pwUsers.js',
				'pw-Directives-pwImage' => POSTWORLD_URI.'/js/directives/pwImage.js',
				'pw-Directives-Background' => POSTWORLD_URI.'/js/directives/pwBackground.js',
				'pw-Directives-pwMenu' => POSTWORLD_URI.'/js/directives/pwMenu.js',
				'pw-Directives-pwWindow' => POSTWORLD_URI.'/js/directives/pwWindow.js',
				'pw-Directives-pwDevices' => POSTWORLD_URI.'/js/directives/pwDevices.js',
				'pw-Directives-pwEvents' => POSTWORLD_URI.'/js/directives/pwEvents.js',
				'pw-WpDirectives-Media-Library-JS' => POSTWORLD_URI.'/js/directives/wpMediaLibrary.js',

				// Modules
				'pw-Modules-Compile' => POSTWORLD_URI.'/js/modules/pwCompile.js',

				// Wizard
				'pw-Wizard' => POSTWORLD_URI.'/js/components/pwWizard.js',

				),
			);

		/**
		 * ENQUEUE SCRIPTS
		 */
		foreach( $include_scripts['core'] as $script_key => $script_location ){
			wp_enqueue_script( $script_key, $script_location, false, $site_version, $in_footer );
		}
		foreach( $include_scripts['angular_thirdparty'] as $script_key => $script_location ){
			wp_enqueue_script( $script_key, $script_location, $angularDep, $site_version, $in_footer );
		}
		foreach( $include_scripts['postworld_core'] as $script_key => $script_location ){
			wp_enqueue_script( $script_key, $script_location, $angularDep, $site_version, $in_footer );
		}

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
			
			/*
			pw_register_script( array(
				'group' => POSTWORLD_APP,
				'handle' => 'package-angular-fullcalendar',
				'file' => POSTWORLD_DIR . '/deploy/package-angular-fullcalendar.min.js',
				'version' => $pw['info']['version'],
				'priority' => 250,
				));
			*/

		} else{
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
				POSTWORLD_URI.'/lib/ui-calendar/src/calendar.js' );
		}

	}

	// + ANGULAR MOMENT
	// @todo : Make function to register PW packages
	if( in_array( 'angularMoment', $pw['inject'] ) ||
		in_array( 'timer', $pw['inject'] ) ){

		if( pw_mode() === 'deploy' ){

			
			/*wp_enqueue_script(
				'Postworld-Package-Angular-Moment',
				POSTWORLD_URI.'/deploy/package-angular-moment.min.js',
				array(),
				$pw['info']['version'],
				$in_footer
				);*/
				

			
			pw_register_script( array(
				'group' => POSTWORLD_APP,
				'handle' => 'package-angular-moment',
				'file' => POSTWORLD_DIR . '/deploy/package-angular-moment.min.js',
				'version' => $pw['info']['version'],
				'priority' => 250,
				));


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

			// jsTimezoneDetect : Used to detect the client's current timezone
			// Since at the time of adding this wasn't supported by Moment.js
			// CHECK UPDATES : https://github.com/moment/moment-timezone/pull/220

			// MOMENT-TIMEZONE DATA.JS
			wp_enqueue_script( 'jsTimezoneDetect-JS',
				POSTWORLD_URI.'/lib/jsTimezoneDetect/jstz.min.js', $angularDep);

			// ANGULAR-TIMER
			// Used for doing countdowns
			// https://github.com/siddii/angular-timer
			wp_enqueue_script( 'humanize-duration-JS',
				POSTWORLD_URI.'/lib/HumanizeDuration.js/humanize-duration.js', $angularDep);
			wp_enqueue_script( 'angularTimer-JS',
				POSTWORLD_URI.'/lib/angular-timer/angular-timer.js', $angularDep);

		}

	}

	// + TOUCH PACKAGE
	if( in_array( 'package-touch', $pw['inject'] ) ){
		if( pw_mode() === 'deploy' ){

			/*
			wp_enqueue_script(
				'Postworld-Package-Touch',
				POSTWORLD_URI.'/deploy/package-touch.min.js',
				array(),
				$pw['info']['version'],
				$in_footer );
			*/
			
			pw_register_script( array(
				'group' => POSTWORLD_APP,
				'handle' => 'package-touch',
				'file' => POSTWORLD_DIR . '/deploy/package-touch.min.js',
				'version' => $pw['info']['version'],
				'priority' => 250,
				));
				

		}
		else{
			// FAST CLICK
			wp_enqueue_script( 'Fastclick.JS',
				POSTWORLD_URI.'/lib/fastclick.js/fastclick.js');	
		}

		add_action( 'wp_footer', 'pw_print_scripts_package_touch' );

	}

	// ANGULAR BOOTSTRAP COLORPICKER
	if( in_array( 'angular-bootstrap-colorpicker', $pw['inject'] ) ){

		wp_enqueue_script(
			'angular-bootstrap-colorpicker-js',
			POSTWORLD_URI.'/lib/angular-bootstrap-colorpicker/js/bootstrap-colorpicker-module.min.js',
			array( POSTWORLD_APP ),
			$pw['info']['version'],
			$in_footer );

		wp_enqueue_style(
			'angular-bootstrap-colorpicker',
			POSTWORLD_URI.'/lib/angular-bootstrap-colorpicker/css/colorpicker.min.css' );
	
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

	/**
	 * Enqueue the Postworld group of scripts
	 */
	pw_enqueue_script( array(
		'group' => POSTWORLD_APP,
		'in_footer' => $in_footer,
		));

}

function pw_include_admin_scripts(){
	global $angularDep;
	global $pw;
	$site_version = pw_site_version();
	$in_footer = pw_config('includes.js.in_footer');
	
	if( pw_mode() === 'deploy' ){
		wp_enqueue_script(
			'Postworld-Admin',
			POSTWORLD_URI.'/deploy/postworld-admin.min.js',
			array( POSTWORLD_APP ),
			$site_version,
			$in_footer );
	}
	else{
	
		$admin_scripts = array(
			'admin_core' => array(
				// CONTROLLERS : ADMIN
				'Postworld-Admin-Layouts' => POSTWORLD_URI.'/admin/js/controllers-admin/layouts.js',
				'Postworld-Admin-Styles' => POSTWORLD_URI.'/admin/js/controllers-admin/styles.js',
				'Postworld-Admin-Sidebars' => POSTWORLD_URI.'/admin/js/controllers-admin/sidebars.js',
				'Postworld-Admin-Feeds' => POSTWORLD_URI.'/admin/js/controllers-admin/feeds.js',
				'Postworld-Admin-Backgrounds' => POSTWORLD_URI.'/admin/js/controllers-admin/backgrounds.js',
				'Postworld-Admin-Iconsets' => POSTWORLD_URI.'/admin/js/controllers-admin/iconsets.js',
				'Postworld-Admin-Shortcodes' => POSTWORLD_URI.'/admin/js/controllers-admin/shortcodes.js',
				'Postworld-Admin-Database' => POSTWORLD_URI.'/admin/js/controllers-admin/database.js',
				// DIRECTIVES : ADMIN
				'Postworld-Admin' => POSTWORLD_URI.'/admin/js/directives-admin/pwAdmin.js',
				'Postworld-Save-Options' => POSTWORLD_URI.'/admin/js/directives-admin/pwSaveOption.js',
				// ANGULAR : JQUERY SLIDER
				'angularJS-jQuery-Slider' => POSTWORLD_URI.'/lib/angular-jquery-slider/slider.js',
				),
			);


		foreach( $admin_scripts['admin_core'] as $script_key => $script_location ){
			wp_enqueue_script( $script_key, $script_location, false, $site_version, $in_footer );
		}
		
	}

	///// JQUERY /////
	// Required for Slider on Backgrounds
	if( pw_module_enabled('backgrounds') &&
		strpos( $_SERVER['QUERY_STRING'], 'backgrounds' ) ){
		//wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-slider' );
		wp_enqueue_script( 'jquery-ui-widget' );
		wp_enqueue_script( 'jquery-ui-mouse' );
	}

}

///// WINDOW JAVASCRIPT DATA INJECTION /////
// Inject Current User Data into Window
function pwGlobals_print() {
	global $pw;
	?><script type="text/javascript">/* <![CDATA[ */
		pw = {};
		pw.angularModules = <?php echo json_encode( $pw['angular_modules'] ) ?>;
		pw.info = <?php echo json_encode( $pw['info'] ); ?>;
		pw.view = <?php echo json_encode( pw_current_view() ); ?>;
		pw.query = <?php echo json_encode( _get( $pw, 'query' ) ); ?>;
		pw.nonce = <?php echo json_encode( $pw['nonce'] ); ?>;
		pw.background = <?php echo json_encode( pw_current_background() ); ?>;
		pw.posts = <?php echo json_encode( apply_filters( PW_POSTS, array() ) ); ?>;
		pw.user = <?php echo json_encode( pw_current_user() ); ?>;
		pw.users = <?php echo json_encode( apply_filters( PW_USERS, array() ) ); ?>;
		pw.device = <?php echo json_encode( pw_device_meta() ); ?>;
		pw.feeds = {};
		pw.widgets = {};
	/* ]]> */</script><?php
}

function pwBootstrapPostworldAdmin_print() {
	// Bootstraps the postworldAdmin module to the document in select instances
	$screen = get_current_screen();

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
	//if( !$do_boostrap  )
	//	return false;

	if( is_admin() ):
		// Add missing action attributes to form elements
		pw_add_forms_action_attribute();

		// Make all buttons type="button" if type not defined
		pw_add_buttons_default_type();

		/**
		 * Bootstrap the app
		 * Must come after adding action attributes
		 *
		 * Two different scopes are to avoid potential
		 * Collisions with other Javascript plugins operating
		 * On the post screen outside of the #poststuff context.
		 * @example WPBakery's VisualComposer
		 */
		$screen = get_current_screen();
		if( $screen->base == 'post' ):

			?><script>
				// Bootstrap Postworld AngularJS App to #poststuff
				jQuery( document ).ready(function() {
					angular.bootstrap('#poststuff', ['postworldAdmin']);
				});
			</script><?php

		else :

			?><script>
				// Bootstrap Postworld AngularJS App
				jQuery( document ).ready(function() {
					angular.bootstrap(document, ['postworldAdmin']);
				});
			</script><?php

		endif;
		
	endif;
}

/**
 * AngularJS prevents the submission of forms
 * without an action attribute. In the Admin
 * this is bad because some forms don't have one.
 * Here we have to force an action attribute for every
 * form element which doesn't have one.
 *
 * @link https://docs.angularjs.org/api/ng/directive/form
 */
function pw_add_forms_action_attribute(){
	?>
	<script>

		// Force an action attribute for every form element which doesn't have one.
		function pw_add_forms_action_attr(){
			jQuery('form').attr('action', function(){
				if( typeof jQuery(this).attr('action') === 'undefined' )
					jQuery(this).attr('action', '<?php echo $_SERVER["PHP_SELF"]; ?>');
			});
		}
		// Initialize
		//angular.element(document).ready(function() {
		jQuery( document ).ready(function() {
			pw_add_forms_action_attr();
		});
	</script>
	<?php
}

/**
 * Buttons by default submit the form they're in
 * Unless they have the type="button"
 */
function pw_add_buttons_default_type(){
	?>
	<script>
		// Force an action attribute for every form element which doesn't have one.
		jQuery( document ).ready(function() {
			jQuery('button').attr('type', function(){
				if( typeof jQuery(this).attr('type') === 'undefined' )
					jQuery(this).attr('type', 'button');
			});
		});
	</script>
	<?php
}

function pwSiteGlobals_include(){
	global $pw;

	///// DYNAMICALLY GENERATED JAVASCRIPT /////
	// This method is only used for site-wide globals
	// Not for user-specific globals.

	$config = pw_config();

	$text_direction = (is_rtl()) ? 'rtl' : 'ltr' ;

	$config['theme'] = pw_theme();

	$config['site'] = array( 
		'name' => get_bloginfo('name'),
		'description' => get_bloginfo('description'),
		'wpurl' => site_url(),
		'url' => home_url(),
		'version' => get_bloginfo('version'),
		'text_direction' => $text_direction,
		'language' => get_bloginfo('language'),
		'description' => get_bloginfo('description'),
	);

	///// PATHS /////
	$config['paths'] = array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'plugins_url' => WP_PLUGIN_URL,
		'plugins_dir' => WP_PLUGIN_DIR,
		'theme_dir'	=>	get_template_directory(),
		'home_url' => home_url(),
		'wp_url' => site_url(),
		'stylesheet_directory' => get_stylesheet_directory_uri(),
		'template_url' => get_template_directory_uri(),
		'postworld_url' => POSTWORLD_URI,
		'postworld_dir' => POSTWORLD_PATH,
		);

	///// POST TYPES /////
	$config["post_types"] = pw_get_post_types();

	///// TAXONOMIES /////
	$config["taxonomies"] = pw_get_taxonomies( array(),'objects');

	///// FIELD MODEL /////
	$config["fields"] = pw_field_models();

	///// REST NAMESPACE /////
	if( function_exists('pw_rest_namespace') )
		$config["rest_api"] = array(
			'namespace' => pw_rest_namespace(),
		);

	///// PRINT JAVASCRIPT /////
	// SITE GLOBALS
	$pwJs  = "";
	$pwJs .= "pw.config = ";
	$pwJs .= json_encode( $config );
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

	// OPTIONS META
	$pwJs .= "\n\n";
	$pwJs .= "pw.optionsMeta = ";
	$pwJs .= json_encode( pw_get_options_meta() );
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
	$globals_path = '/deploy/postworld-config.js';
	$pwJsFile = POSTWORLD_PATH . $globals_path;
	$file = fopen( $pwJsFile ,"w" );
	fwrite($file,"$pwJs");
	fclose($file);
	chmod($pwJsFile, 0755);

	/**
	 * @todo Include the theme version.
	 */

	if( pw_dev_mode() )
		wp_enqueue_script(
			'pw-Config-JS',
			POSTWORLD_URI . $globals_path,
			array( POSTWORLD_APP ),
			hash( 'sha256', $pwJs ),
			pw_config('includes.js.in_footer') );
	else
		pw_register_script( array(
			'group' => POSTWORLD_APP,
			'handle' => 'postworld-config',
			'file' => POSTWORLD_DIR . $globals_path,
			'version' => $pw['info']['version'],
			'in_footer' => pw_config('includes.js.in_footer'),
			'priority' => 200,
			));
	

	
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

/**
 * ADD GLOBALS
 * Hook into the 'init' action, which runs on both frontend and backend.
 */
add_action( 'init', 'pwGlobals_parse', 10, 2 );
function pwGlobals_parse(){
	global $pw;

	///// NONCE /////
	$pw['nonce'] = wp_create_nonce( 'postworld_ajax' );

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

	///// PW MODULES /////
	$pw['info']['modules'] = pw_enabled_modules();

	//// THEME VERSION /////
	$pw['info']['site_version'] = pw_site_version();

	return;

}

/**
 * ADD FRONTEND GLOBALS
 * These are added only for requests that are on typical front-end
 * requests, such as posts, archives and pages.
 * Hook into the 'wp' action, which only runs on frontend requests.
 */
add_action( 'wp', 'pwGlobals_frontend_parse', 10 );
function pwGlobals_frontend_parse( $vars ){

	/**
	 * Return early if it's not a real request
	 * @todo Investigate why this is running multiple times
	 * @note Hidden as it breaks various functionality, including pw_view_content()
	 */
	/*
	if( empty( $vars->query_vars ) &&
		empty( $vars->query_string ) &&
		empty( $vars->request ) )
		return;
	*/

	/////////// USER / PAGE SPECIFIC GLOBALS //////////
	global $pw;
	global $wp_query;

	///// CURRENT VIEW /////
	// VIEW
	$pw["view"] = pw_current_view();

	// QUERY
	$pw['query'] = pw_view_query( $pw["view"] );

	// LAYOUT
	$pw['layout'] = pw_get_current_layout();

	///// SAVED SETTINGS /////
	// SIDEBARS
	$pw['sidebars'] = pw_get_option( array( 'option_name' => PW_OPTIONS_SIDEBARS ) );

	// SOCIAL
	$pw['social'] = pw_get_option( array( 'option_name' => PW_OPTIONS_SOCIAL ) );

	// LAYOUTS
	$pw['layouts'] = pw_get_option( array( 'option_name' => PW_OPTIONS_LAYOUTS ) );

	return;
}


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
	$file_path = "/deploy/postworld-admin-config.js";
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
/**
 * Echos the link elements for fonts passed in
 * Or added via the 'pw_include_google_fonts' filter
 *
 * @param Array $fonts An array of arrays of fonts.
 */
function pw_include_google_fonts( $fonts = array() ){
	/**
	 * @example
	 *		$fonts = array(
	 *			array(
	 *				'name'	=>	'Roboto', // Optional
	 *				'code'	=>	'Roboto:100,300,700,100italic,300italic,400',
	 *			)
	 *		);
	 */
	// Get the fonts to include from a filter
	$fonts = apply_filters( 'pw_include_google_fonts', $fonts );
	// Iterate through each font and echo the include script
	if( is_array( $fonts ) )
		foreach( $fonts as $font ){
			echo "\n<link href='//fonts.googleapis.com/css?family=".$font['code']."' rel='stylesheet' type='text/css'>";
		}

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
		wp_enqueue_style( 'bootstrap-less', POSTWORLD_PATH . '/lib/bootstrap/less/bootstrap.less' );
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

function pw_print_scripts_package_touch(){
	?><script>
		// Init FastClick :: Postworld : Package-Touch
		window.addEventListener('load', function () {
		  FastClick.attach(document.body);
		}, false);
	</script><?php
}
