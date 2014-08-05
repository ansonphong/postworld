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

postworld.factory('pwData', [ '$resource', '$q', '$log', '$window', '$pw',
	function ( $resource, $q, $log, $window, $pw ) {	  
	// Used for Wordpress Security http://codex.wordpress.org/Glossary#Nonce
	var nonce = 0;
	// Check feed_settigns to confirm we have valid settings
	var validSettings = true;
	// Set feed_settings and feed_data in pwData Singleton
	var feed_settings = $window['feed_settings'];
	// TODO check mandatory fields
	if (feed_settings == null) {
		validSettings = false;
		$log.error('Service: pwData Method:Constructor  no valid feed_settings defined');
	}
	
	var feeds = {};
	
	// $log.debug('pwData() Registering feed_settings', feed_settings);
	
	var	getTemplate = function( pwData, meta ) { // (pwData,subdir,post_type,view)
		// (this,subdir,post_type,view) -> ( this, meta )
		var template;

		// Localize Meta
		var subdir = meta.subdir;
		var post_type = meta.post_type;
		var view = meta.view;

		//$log.debug('getTemplate : META : ',meta);

		switch (subdir) {
			case 'posts':
				if (post_type) {
					template = $pw.templates.posts[post_type][view];						
				} else {
					template = $pw.templates.posts['post'][view];						
				}
				break;
			default:
				template = $pw.templates[subdir][view];
				break;
		}
		// $log.debug('Service: pwData Method:getTemplate template=',template);
		return template;			
	};
	
	
	// for Ajax Calls
    var resource = $resource( $pw.paths.ajax_url, {action:'wp_action'}, 
				{ wp_ajax: { method: 'POST', isArray: false, },	}
			);
	
    return {
    	feed_settings: feed_settings,
    	feeds: feeds,

    	templates: $pw.templates, 
    	

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
			// $log.debug('pwData.wp_ajax', fname, 'args: ',args);
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
		pw_query: function( args ){
			$log.debug('pwData.pw_query',args);
			var params = {'args':args};
			return this.wp_ajax('pw_query',params);
		},
		pw_live_feed: function(args,qsArgs) {
			// args: arguments received from Panel. fargs: is the final args sent along the ajax call.
			// fargs will be filled initially with data from feed settings, 
			// fargs will be filled next from data in the query string			
			var fargs = this.convertFeedSettings(args.feed_id,args); // will read settings and put them in fargs
			fargs = this.mergeQueryString(fargs,qsArgs); // will read args and override fargs
			fargs = this.removeEmptyArgs(fargs);
			// Get Query Arguments and save them in feed settings
			var feedSettings = feed_settings[args.feed_id];
			feedSettings.finalFeedQuery = fargs.feed_query;
			var params = {'args':fargs};
			return this.wp_ajax('pw_live_feed',params);
		},
		pw_scroll_feed: function(args) {
			$log.debug('pwData.pw_scroll_feed',args);
			var params = {args:args};
			return this.wp_ajax('pw_scroll_feed',params);
		},
		o_embed: function(url,args) {
			$log.debug('pwData.o_embed',args);
			var params = { url:url, args:args};
			return this.wp_ajax('o_embed',params);
		},
		pw_get_posts: function(args) {
			var feedSettings = feed_settings[args.feed_id];
			var feed = feeds[args.feed_id];

			// If already all loaded, then return
			if (feed.status == 'all_loaded')  {
				$log.debug('pwData.pw_get_posts ALL LOADED');
				// TODO should we return or set promise.?
				 //var results = {'status':200,'data':[]};
				 var response = $q.defer();
				 response.promise.resolve(1);				
				return response.promise;
			};
			// else, get posts and recalculate
			
			// Set Post IDs - get ids from outline, [Loaded Length+1 to Loaded Length+Increment]
			// Slice Outline Array
			var idBegin = feed.loaded;
			var idEnd = idBegin+feedSettings.load_increment;
			// TODO Check if load_increment exists
			// Only when feed_outline exists and this is the first run, load from preload value, not from auto increment value
			if (feed.loaded==0) {
				if (feedSettings.preload)
					idEnd = idBegin+feedSettings.preload;
					// TODO, use constant here
				else idEnd = idBegin+10;
			}
			var postIDs = feed.feed_outline.slice(idBegin,idEnd);
			var fields;
			if (feedSettings.query_args) {
				if (feedSettings.query_args.fields != null) {
					fields = feedSettings.query_args.fields;
				}				
			}
			// $log.debug('pwData.pw_get_posts range:',idBegin, idEnd);
			// Set Fields
			var params = { feed_id:args.feed_id, post_ids:postIDs, fields:fields};
			$log.debug('pwData.pw_get_posts',params);
			return this.wp_ajax('pw_get_posts',params);
		},
		pw_get_templates: function(templates_object) {
			// TODO Optimize by running it once and caching it
			$log.debug('pwData.pw_get_templates',templates_object);
			var params = { templates_object:templates_object};			
			return this.wp_ajax('pw_get_templates',params);
		},
		pw_register_feed: function(args) {
			$log.debug('pwData.pw_register_feed',args);
			var params = {args:args};
			return this.wp_ajax('pw_register_feed',params);
		},
		pw_load_feed: function(args) {
			$log.debug('pwData.pw_load_feed',args);
			var params = {args:args};
			return this.wp_ajax('pw_load_feed',params);
		},
		pw_get_post: function(args) {
			$log.debug('pwData.pw_get_post',args);
			//var params = {args:args};
			return this.wp_ajax('pw_get_post',args);
		},
		pw_get_template: function ( meta ) { // ( subdir, post_type, view)
			// if templates object already exists, then get value, if not, then retrieve it first

			// Setup Meta (lineage)
			/*
			var meta = {
				subdir: subdir,
				post_type: post_type,
				view: view,
			};
			*/

			///// Set Defaults /////
			// Subdirectory
			if( _.isUndefined(meta.subdir) )
				return false;
			// Post Type
			if( _.isUndefined(meta.post_type) )
				meta.post_type = '';
			// View
			if( _.isUndefined(meta.view) )
				return false;

			var template = getTemplate( this, meta ) + "?ver=" + $pw['version'] ; // ( this, subdir, post_type, name )
		    
		    // If on HTTPS / SSL, get on the same protocol
		    if( $pw.view['protocol'] == 'https' )
		    	template = template.replace('http://', 'https://');

		    return template;


		}, // END OF pw_get_template
		convertFeedSettings: function (feedID,args1) {
			var fargs = {};
			fargs.feed_query = {};
			//if(!args.feed_query) args.feed_query = {};
			// TODO use constants from app settings
			
			// Get Feed_Settings Parameters
			var feed = feed_settings[feedID];
  			$log.info('Feed Query Override by Feed Settings',feedID, feed.query_args);
			// Query Args will fill in the feed_query first, then any other parameter in the feed will override it, then any user parameter will override all
			if (feed.query_args != null) fargs.feed_query = feed.query_args;  
			if (feed.preload != null) fargs.preload = feed.preload; else fargs.preload = 10;  
			if (feed.offset	!= null) fargs.offset = feed.offset; else fargs.offset = 0;  
			if (feed.max_posts != null) fargs.feed_query.posts_per_page = feed.max_posts; else fargs.feed_query.posts_per_page = 1000;
			 
			if (feed.order_by != null) {
				// if + sort Ascending
				if (feed.order_by.charAt(0)=='+') fargs.feed_query.order = 'ASC';
				// if - sort Descending				
				else  if (feed.order_by.charAt(0)=='-') fargs.feed_query.order = 'DESC';
				else fargs.feed_query.order = 'ASC';
				// If + or - then remove the first character
				if ((feed.order_by.charAt(0)=='+') || (feed.order_by.charAt(0)=='-')) {
					fargs.feed_query.order_by = feed.order_by.slice(1);
				}
			}	// else the default whatever it is, is used
			if (feed.offset != null) fargs.feed_query.offset = feed.offset; // else the default is zero 
			fargs.feed_id = feedID;
			return fargs;			
		},
		
  		mergeQueryString: function (fargs,args) {
  			$log.info('Feed Query Override by Query String',args);
  			for(var key in args){
			    // $scope.args.feed_query[key] = params[key];
			    fargs.feed_query[key] = args[key];
			}			
			return fargs;
  		},		
		mergeFeedQuery: function (fargs,args) {
			if (args.feed_query) {
	  			$log.info('Feed Query Override by Search feedQuery',args.feed_query);
				for (var prop in args.feed_query) {
				    fargs.feed_query[prop] = args.feed_query[prop];
				    //$log.debug("args.feed_query",prop,args.feed_query[prop],fargs.feed_query[prop]);
				}
			}
			return fargs;
		},
  		removeEmptyArgs: function (args) {
  			$log.info('Feed Query Remove Empty Args',args);
  			for(var key in args.feed_query){
  				if ((args.feed_query[key]=="null") && (key!= "s")){
  					delete args.feed_query[key];
  					continue;
  				}  				
			    if ((args.feed_query[key]!==0) && (args.feed_query[key]!==false)) {
			    	if (args.feed_query[key] == "") {
			    		delete args.feed_query[key];
			    	}
			    }  			    			    
			}			
			return args;
  		},		
		pw_get_post_types: function(args) {
			//$log.debug('pwData.pw_load_feed',args);
			var params = {args:args};
			return this.wp_ajax('pw_get_post_types', params);
		},
		ajax_oembed_get: function(args) {
			$log.debug('pwData.ajax_oembed_get',args);
			var params = {args:args};
			return this.wp_ajax('ajax_oembed_get', params);
		},
		pw_save_post: function(args) {
			$log.debug('pwData.pw_save_post',args);
			var params = {args:args};
			return this.wp_ajax('pw_save_post', params);
		},
		pw_trash_post: function(args) {
			$log.debug('pwData.pw_trash_post',args);
			var params = {args:args};
			return this.wp_ajax('pw_trash_post', params);
		},
		pw_get_post_edit: function(args) {
			$log.debug('pwData.pw_get_post_edit',args);
			var params = {args:args};
			return this.wp_ajax('pw_get_post_edit',params);
		},
		taxonomies_outline_mixed: function(args) {
			$log.debug('pwData.taxonomies_outline_mixed',args);
			var params = {args:args};
			return this.wp_ajax('taxonomies_outline_mixed',params);
		},
		user_query_autocomplete: function(args) {
			$log.debug('pwData.user_query_autocomplete',args);
			var params = {args:args};
			return this.wp_ajax('user_query_autocomplete',params);
		},
		tags_autocomplete: function(args) {
			$log.debug('pwData.tags_autocomplete',args);
			var params = {args:args};
			return this.wp_ajax('tags_autocomplete',params);
		},
		set_post_relationship: function(args) {
			$log.debug('pwData.set_post_relationship',args);
			var params = {args:args};
			return this.wp_ajax('set_post_relationship',params);
		},
		set_post_points: function(args) {
			$log.debug('pwData.set_post_points',args);
			var params = {args:args};
			return this.wp_ajax('set_post_points',params);
		},
		set_comment_points: function(args) {
			$log.debug('pwData.set_comment_points',args);
			var params = {args:args};
			return this.wp_ajax('set_comment_points',params);
		},
		pw_set_avatar: function(args) {
			$log.debug('pwData.pw_set_avatar',args);
			var params = {args:args};
			return this.wp_ajax('pw_set_avatar',params);
		},
		pw_get_avatar: function(args) {
			$log.debug('pwData.pw_get_avatar',args);
			var params = {args:args};
			return this.wp_ajax('pw_get_avatar',params);
		},
		wp_user_query: function(args) {
			$log.debug('pwData.wp_user_query',args);
			var params = {args:args};
			return this.wp_ajax('wp_user_query',params);
		},
		pw_insert_user: function(args) {
			$log.debug('pwData.pw_insert_user',args);
			var params = {args:args};
			return this.wp_ajax('pw_insert_user',params);
		},
		send_activation_link: function(args) {
			$log.debug('pwData.send_activation_link',args);
			var params = {args:args};
			return this.wp_ajax('send_activation_link',params);
		},
		pw_activate_user: function(args) {
			$log.debug('pwData.pw_activate_user',args);
			var params = {args:args};
			return this.wp_ajax('pw_activate_user',params);
		},
		reset_password_email: function(args) {
			$log.debug('pwData.reset_password_email',args);
			var params = {args:args};
			return this.wp_ajax('reset_password_email',params);
		},
		reset_password_submit: function(args) {
			$log.debug('pwData.reset_password_submit',args);
			var params = {args:args};
			return this.wp_ajax('reset_password_submit',params);
		},
		post_share_report: function(args) {
			$log.debug('pwData.post_share_report',args);
			var params = {args:args};
			return this.wp_ajax('post_share_report',params);
		},
		user_share_report_outgoing: function(args) {
			$log.debug('pwData.user_share_report_outgoing',args);
			var params = {args:args};
			return this.wp_ajax('user_share_report_outgoing',params);
		},
		set_post_image: function(args) {
			$log.debug('pwData.set_post_image',args);
			var params = {args:args};
			return this.wp_ajax('set_post_image',params);
		},
		get_userdatas: function(args) {
			$log.debug('pwData.get_userdatas',args);
			var params = {args:args};
			return this.wp_ajax('pw_get_userdatas',params);
		},
		get_userdata: function(args) {
			$log.debug('pwData.get_userdata',args);
			var params = {args:args};
			return this.wp_ajax('pw_get_userdata',params);
		},
		get_wizard_status: function(args) {
			$log.debug('pwData.get_wizard_status',args);
			var params = {args:args};
			return this.wp_ajax('pw_get_wizard_status',params);
		},
		set_wizard_status: function(args) {
			$log.debug('pwData.set_wizard_status',args);
			var params = {args:args};
			return this.wp_ajax('pw_set_wizard_status',params);
		},
		get_image: function(args) {
			$log.debug('pwData.get_image',args);
			var params = {args:args};
			return this.wp_ajax('pw_get_image',params);
		},
		update_option: function(args) {
			$log.debug('pwData.set_option',args);
			var params = {args:args};
			return this.wp_ajax('pw_update_option',params);
		},
		set_option_obj: function(args) {
			$log.debug('pwData.set_option_obj',args);
			var params = {args:args};
			return this.wp_ajax('pw_set_option_obj',params);
		},
		get_option_obj: function(args) {
			$log.debug('pwData.get_option_obj',args);
			var params = {args:args};
			return this.wp_ajax('pw_get_option_obj',params);
		},
		get_menus: function(args) {
			$log.debug('pwData.get_menus',args);
			var params = {args:args};
			return this.wp_ajax('pw_get_menus',params);
		},


   }; // END OF pwData return value
}]);
