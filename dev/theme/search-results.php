<?php
/**
 * Template Name: Search Results
 *
 * This Template is a Sandbox for Post World Search Results - 
 * Using AngularJS Framework
 *
 */
 
 /* These JS Files can be kept separately during development, 
  * then upon release, put all together minified and in one file, to minimize the number of http requests
  use this http://wp.smashingmagazine.com/2011/10/18/how-to-use-ajax-in-wordpress/ to modify script inclusion
  */

  
/* TODO - Bootstrap style included for testing purposes, remove in final version, add ,array('twentytwelve-style') if you want to place after style.css */
wp_enqueue_style( 'Bootstrap', '//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css', array('twentytwelve-style') );

/* JQuery is added for nInfiniteScroll Directive, if directive is not used, then remove it */
wp_deregister_script('jquery');
wp_register_script('jquery', "http" . ($_SERVER['SERVER_PORT'] == 443 ? "s" : "") . "://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js", false, null);
wp_enqueue_script('jquery');

  
wp_enqueue_script( 'AngularJS', WP_PLUGIN_URL.'/postworld/lib/angular/angular.min.js');
wp_enqueue_script( 'AngularJS-Resource', WP_PLUGIN_URL.'/postworld/lib/angular/angular-resource.min.js');
wp_enqueue_script( 'AngularJS-Route', WP_PLUGIN_URL.'/postworld/lib/angular/angular-route.min.js');

// All Dynamic Paths and Wordpress PHP data that needs to be added to JS files
$jsVars = array( 'ajaxurl' => admin_url( 'admin-ajax.php' ),
				  'pluginurl' => WP_PLUGIN_URL,
				);

wp_register_script( "pw-app-JS", WP_PLUGIN_URL.'/postworld/js/app.js' );
wp_localize_script( 'pw-app-JS', 'jsVars', $jsVars);
wp_enqueue_script( 'pw-app-JS' );

wp_register_script( "pw-LiveFeed-JS", WP_PLUGIN_URL.'/postworld/js/components/liveFeed.js' );
wp_localize_script( 'pw-LiveFeed-JS', 'jsVars', $jsVars);
wp_enqueue_script( 'pw-LiveFeed-JS' );

wp_register_script( "pw-FilterFeed-JS", WP_PLUGIN_URL.'/postworld/js/components/filterFeed.js' );
wp_localize_script( 'pw-FilterFeed-JS', 'jsVars', $jsVars);
wp_enqueue_script( 'pw-FilterFeed-JS' );

wp_register_script( "pw-FeedItem-JS", WP_PLUGIN_URL.'/postworld/js/components/feedItem.js' );
wp_localize_script( 'pw-FeedItem-JS', 'jsVars', $jsVars);
wp_enqueue_script( 'pw-FeedItem-JS' );

wp_register_script( "pw-pwData-JS", WP_PLUGIN_URL.'/postworld/js/services/pwData.js' );
wp_localize_script( 'pw-pwData-JS', 'jsVars', $jsVars);
wp_enqueue_script( 'pw-pwData-JS' );

wp_enqueue_script( 'angularJS-nInfiniteScroll', plugins_url().'/postworld/js/components/ng-infinite-scroll.js' );

wp_register_script( "BootStrap-JS", '//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js' );
wp_enqueue_script( 'BootStrap-JS' );


get_header('postworld'); ?>
<div ng-app='pwApp' data-nonce="<?php echo wp_create_nonce("postworld_nonce");?>" >
	<ng-view></ng-view>
</div>

<?php get_footer('postworld'); ?>