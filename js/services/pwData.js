/**
 * Created by Michel on 9/22/13.
 * 	Development Note:-
 *	To make Ajax request work as a form post, we need to do 3 things:
 *	1- Use AngularJS version 1.2
 * 	2- change content type in the header:-  headers: {'Content-Type': 'application/x-www-form-urlencoded', 'charset':'UTF-8'}
 * 	3- Transform data to url encoded format using the following function http://victorblog.com/2012/12/20/make-angularjs-http-service-behave-like-jquery-ajax/
 *	http://stackoverflow.com/questions/11442632/how-can-i-make-angular-js-post-data-as-form-data-instead-of-a-request-payload
 *  Otherwise, if you don't want to go through the above hassle, you can just do the following on the server:- 
 * 	$args_text = file_get_contents("php://input");
 *	$args = json_decode($args_text);
 * */

pwApp.factory('pwData', function ($resource, $q, $log) {	  
	// Used for Wordpress Security http://codex.wordpress.org/Glossary#Nonce
	var nonce = 0;
	// Check feed_settigns to confirm we have valid settings
	var validSettings = true;
	// Set feed_settings and feed_data in pwData Singleton
	var feed_settings = window['feed_settings'];
	// TODO check mandatory fields
	if (feed_settings == null) {
		validSettings = false;
		$log.error('Service: pwData Method:Constructor  no valid feed_settings defined');
	}
	
	var feed_data = {};
	
	$log.info('pwData: Constructor: Registering feed_settings', feed_settings);
	$log.info('pwData: Constructor: Registering feed_data', feed_data);
	// for Ajax Calls
    var resource = $resource(jsVars.ajaxurl, {action:'wp_action'}, 
    							{	wp_ajax: { method: 'POST', isArray: false, },	}
							);
							
    return {
    	feed_settings: feed_settings,
    	feed_data: feed_data,
    	// Set Nonce Value for Wordpress Security
    	setNonce: function(val) {
    		nonce = val;
    	},
    	// Get Nonce Value
    	getNonce: function() {
    		return nonce;
    	},
    	// A simplified wrapper for doing easy AJAX calls to Wordpress PHP functions
		wp_ajax: function(fname, args) {
			$log.info('Service: pwData Method:wp_ajax Arguments: ','fname: ', fname, 'args: ',args);
            var deferred = $q.defer();
            // works only for non array returns
            resource.wp_ajax({action:fname},{args:args,nonce:this.getNonce()},
				function (data) {
                    deferred.resolve(data);
                },
                function (response) {
                    deferred.reject(response);
                });
            return deferred.promise;		
		},
		pw_live_feed: function(args) {
			// get additional params from feed_settings
			// ensure that feed_query exists
			if(!args.feed_query) args.feed_query = {};
			// shortcut
			var feed = feed_settings[args.feed_id];
			// TODO use constants
			// TODO Sanity check for values, max, min, positive, negative, etc...
			if (feed.preload != null) args.preload = feed.preload; else args.preload = 10;  
			// Set a hard Max for performance consideration - use constant
			if (feed.max_posts != null) args.feed_query.posts_per_page = feed.max_posts; else args.feed_query.posts_per_page = 1000;
			 
			// TODO check for +/- values for asc/desc
			if (feed.order_by != null) args.feed_query.orderby = feed.order_by;
			if (feed.offset != null) args.feed_query.offset = feed.offset;
			 
			// QUESTION: Which overrides which, order_by, offset, max_posts, or other query_args in the query args field?
			// TODO add query args [don't we already get them from UI? but we need to get them from feed_settings too]
			   
			var params = {args:args};
			$log.info('Service: pwData Method:pw_live_feed Arguments: ',args);
			return this.wp_ajax('pw_live_feed',params);
		},
		pw_scroll_feed: function(args) {
			$log.info('Service: pwData Method:pw_scroll_feed Arguments: ',args);
			var params = {args:args};
			return this.wp_ajax('pw_scroll_feed',params);
		},
		o_embed: function(url,args) {
			$log.info('Service: pwData Method:o_embed Arguments: ',args);
			var params = { url:url, args:args};
			return this.wp_ajax('o_embed',params);
		},
		pw_get_posts: function(args) {
			var feedSettings = feed_settings[args.feed_id];
			var feedData = feed_data[args.feed_id];
			// Set Post IDs - get ids from outline, [Loaded Length+1 to Loaded Length+Increment]
			// Slice Outline Array
			var idBegin = feedData.loaded.length;
			var idEnd = idBegin+feedSettings.load_increment;
			var postIDs = feedData.feed_outline.slice(idBegin,idEnd);
			var fields;
			// TODO check that query_args exists first
			if (feedSettings.query_args.fields != null) {
				fields = feedSettings.query_args.fields;
			}
			$log.info('Service: pwData Method:pw_get_posts BeginID, EndID: ',idBegin, idEnd);
			// Set Fields
			var params = { feed_id:args.feed_id, post_ids:postIDs, fields:fields};
			$log.info('Service: pwData Method:pw_get_posts Arguments: ',params);
			return this.wp_ajax('pw_get_posts',params);
		},
		pw_get_templates: function(templates_object) {
			$log.info('Service: pwData Method:pw_get_templates Arguments: ',templates_object);
			var params = { templates_object:templates_object};			
			return this.wp_ajax('pw_get_templates',params);
		},
    };
});