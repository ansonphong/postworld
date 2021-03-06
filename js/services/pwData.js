/**
 * @ngdoc service
 * @name postworld.$pwData
 * @requires $resource
 * @requires $q
 * @todo Rename to $pwData
 */
postworld.factory('$pwData', [ '$resource', '$http', '$q', '$log', '$window', '$pw', '$_',
	function ( $resource, $http, $q, $log, $window, $pw, $_ ) {	  
	// Used for Wordpress Security http://codex.wordpress.org/Glossary#Nonce
	var nonce = 0;
	// Check feed_settigns to confirm we have valid settings
	var validSettings = true;
	
	var	getTemplate = function( $pwData, meta ) {
		var template = false;

		// Localize Meta
		var subdir = meta.subdir;
		var post_type = meta.post_type;
		var view = meta.view;

		var deviceType = $pw.getDeviceType();

		switch( subdir ) {

			// Get a post template; includes the post_type and view
			case 'posts':
				// Set default post type
				if( !post_type )
					post_type = 'post';
				
				$log.debug( 'pwData : getTemplate : deviceTemplate : deviceType ',  deviceType);

				// If devices are defined
				if( deviceType !== false ){
					var deviceView = view + '-' + deviceType;
					template = $_.get( $pw.templates.posts, post_type + '.' + deviceView );
					$log.debug( 'pwData : getTemplate : deviceTemplate : template ',  template);
				}
				
				// Get template, second try	
				if( !template )
					template = $_.get( $pw.templates.posts, post_type + '.' + view ); // $pw.templates.posts[post_type][view];
				
				// Get template, third try
				if( !template ){
					post_type = 'post';
					template = $_.get( $pw.templates.posts, post_type + '.' + view );
				}
				break;

			// Get a standard template
			default:
				// Check for an override template with the device type appended
				if( deviceType !== false )
					template = $_.get( $pw.templates[ subdir ], view + '-' + deviceType  );
				// Get template
				if( template === false )
					template = $_.get( $pw.templates[ subdir ], view  ); // $pw.templates[subdir][view];
				break;
				
		}
		$log.debug('pwData : getTemplate', meta);
		$log.debug('pwData : getTemplate', template);

		return template;			
	};
	
	// for Ajax Calls
	var resource = $resource( $pw.paths.ajax_url, {action:'wp_action'}, 
				{ wpAjax: { method: 'POST', isArray: false, },	}
			);
	
	return {
		posts: $window.pw.posts,
		feeds: {},
		widgets: $window.pw.widgets,		
		templates: $pw.templates, 
		partials: $window.pw.partials,		// Used to store partials
		embeds: $window.pw.embeds,			// Used to store embed codes
		background: $window.pw.background,	// Used to represent the current background object
		users: $window.pw.users,	
		
		rest:{
			namespace: $pw.config.rest_api.namespace+'/v1',
		},

		restUrl: function( append ){
			return $pw.config.paths.wp_url+'/wp-json/'+this.rest.namespace+append;
		},

		// Set Nonce Value for Wordpress Security
		setNonce: function(val) {
			nonce = val;
		},
		// Get Nonce Value
		getNonce: function() {
			return 'n0nc3';// $pw.nonce;
		},

		/**
		* @ngdoc method
		* @name postworld.service#wpAjax
		* @methodOf postworld.$pwData
		* @description A simplified wrapper for doing easy AJAX calls to Wordpress PHP functions.
		* @param {string} fname The name of the server function.
		* @param {string|array|object} args Arguments for the function.
		* @returns {promise} Returns data from the server function called.
		*/
		wpAjax: function(fname, args) {
			// $log.debug('pwData.wpAjax', fname, 'args: ',args);
			var deferred = $q.defer();
			// works only for non array returns
			resource.wpAjax(
				{
					action: fname
				},
				{
					args: args,
					nonce: window.pw.nonce //this.getNonce(),
				},
				function (data) {
					deferred.resolve(data);
				},
				function (response) {
					deferred.reject(response);
				}
			);
			return deferred.promise;		
		},
		wp_ajax: function(fname, args) {
			///// DEPRECIATED /////
			return this.wpAjax;		
		},
		pwQuery: function( args ){
			$log.debug('pwData.pwQuery',args);
			var params = {'args':args};
			return this.wpAjax('pw_query',params);
		},
		saveOption: function( args ) {
			$log.debug('pwData.saveOption',args);
			var params = { args: args };
			return this.wpAjax('pw_save_option',params);
		},
		getLiveFeed: function(args,qsArgs) {
			$log.debug('pwData.getLiveFeed : INIT :',args);

			// args: arguments received from Panel. fArgs: is the final args sent along the ajax call.
			// feedArgs will be filled initially with data from feed settings, 
			// feedArgs will be filled next from data in the query string			
			var feedArgs = this.convertFeedSettings(args.feed_id,args); // will read settings and put them in feedArgs
			feedArgs = this.mergeQueryString(feedArgs,qsArgs); // will read args and override feedArgs
			feedArgs = this.removeEmptyArgs(feedArgs);
			// Get Query Arguments and save them in feed settings
			var feedSettings = this.feeds[args.feed_id];
			feedSettings.finalFeedQuery = feedArgs.query;

			$log.debug('pwData.getLiveFeed : FINAL :',feedArgs);

			var params = {'args':feedArgs};
			return this.wpAjax('pw_get_live_feed',params);

		},
		pw_scroll_feed: function(args) {
			$log.debug('pwData.pw_scroll_feed',args);
			var params = {args:args};
			return this.wpAjax('pw_scroll_feed',params);
		},
		o_embed: function(url,args) {
			$log.debug('pwData.o_embed',args);
			var params = { url:url, args:args};
			return this.wpAjax('o_embed',params);
		},

		findPost: function( properties ){
			return _.findWhere( this.posts, properties );
		},

		getPosts: function( params ){
			/*
			 params = {
				post_ids : 	[ array ],
				fields: 	'string' / [ array ],
				options: 	{ object }
			 } 
			*/
			$log.debug('pwData.getPosts : PARAMS',params);
			var url = this.restUrl('/posts'),
				config = {
				params:{
					ids: params.post_ids.join(),
					fields: params.fields
				}
			};
			return $http.get(url,config).then(
				function(response){
					$log.debug( 'pwData.getPosts : RESPONSE', response.data );
					return response;
				},
				function(response){
					$log.error('REST API Failed @ ' + url, params);
				});

		},
		getAttachment: function(vars) {
			$log.debug('pwData.getAttachment : VARS', vars);
			var url = this.restUrl('/post'),
				config = {
				params:{
					id: vars.attachment_id,
					fields: vars.fields
				}
			};
			return $http.get(url,config).then(
				function(response){
					$log.debug( 'pwData.getAttachment : RESPONSE', response.data );
					return response;
				},
				function(response){
					$log.error('REST API Failed @ ' + url, params);
				});
		},
		pw_get_templates: function(templates_object) {
			// TODO Optimize by running it once and caching it
			$log.debug('pwData.pw_get_templates',templates_object);
			var params = { templates_object:templates_object};			
			return this.wpAjax('pw_get_templates',params);
		},
		pw_register_feed: function(args) {
			$log.debug('pwData.pw_register_feed',args);
			var params = {args:args};
			return this.wpAjax('pw_register_feed',params);
		},
		pw_load_feed: function(args) {
			$log.debug('pwData.pw_load_feed',args);
			var params = {args:args};
			return this.wpAjax('pw_load_feed',params);
		},
		pw_get_post: function( vars ){
			// DEPRECIATED
			return this.getPost( vars );
		},
		getPost: function( vars ) {
			// If no ID is set
			if( _.isUndefined( vars.post_id ) ){
				$log.debug( 'pwData.getPost : No post ID specified.' );
				return false;
			}
			$log.debug('pwData.getPost',vars);
			//var params = {args:vars};
			return this.wpAjax('pw_get_post',vars);
		},

		/**
		* @ngdoc method
		* @name postworld.service#getTemplate
		* @methodOf postworld.$pwData
		* @description Retreives the URL of a requested template.
		* @param {object} meta
		*	Post type key is optional. 
		*	```{ subdir:'panels', post_type: 'post', view:'list' }```
		*
		* @returns {string} The URL of the requested template, or `false` if it doesn't exist.
		*/
		getTemplate: function( meta ){
			/**
			 * Set Defaults
			 */
			// Subdirectory
			if( _.isUndefined(meta.subdir) )
				return false;
			// Post Type
			if( _.isUndefined(meta.post_type) )
				meta.post_type = '';
			// View
			if( _.isUndefined(meta.view) )
				return false;

			// Get the template
			var template = getTemplate( this, meta );
			
			// If it exists, add the version number to the URL
			if( template )
				template = template + "?ver=" + $pw['info']['site_version'];
			else
				return false;

			// If on HTTPS / SSL, get on the same protocol
			if( $pw.view['protocol'] == 'https' )
				template = template.replace('http://', 'https://');

			//$log.debug( '$pwData.getTemplate()', template );
			return template;

		},
		
		///// DEPRECIATED /////
		pw_get_template: function ( meta ) {
			return this.getTemplate(meta);
		},

		convertFeedSettings: function ( feedID, args1 ) {
			var feedArgs = {};
			feedArgs.query = {};

			// Get feeds Parameters
			var feed = this.feeds[feedID];
			$log.info('Feed Query Override by Feed Settings',feedID, feed.query);
			// Query Args will fill in the query first, then any other parameter in the feed will override it, then any user parameter will override all
			if (feed.query != null)
				feedArgs.query = feed.query;  

			if (feed.preload != null)
				feedArgs.preload = feed.preload;
			else
				feedArgs.preload = 10;  

			if (feed.offset	!= null)
				feedArgs.offset = feed.offset;
			else
				feedArgs.offset = 0;  

			if (feed.related_posts	!= null)
				feedArgs.related_posts = feed.related_posts;  

			if (feed.order_by != null) {
				// if + sort Ascending
				if (feed.order_by.charAt(0)=='+') feedArgs.query.order = 'ASC';
				// if - sort Descending				
				else  if (feed.order_by.charAt(0)=='-') feedArgs.query.order = 'DESC';
				else feedArgs.query.order = 'ASC';
				// If + or - then remove the first character
				if ((feed.order_by.charAt(0)=='+') || (feed.order_by.charAt(0)=='-')) {
					feedArgs.query.order_by = feed.order_by.slice(1);
				}
			}	// else the default whatever it is, is used
			if (feed.offset != null) feedArgs.query.offset = feed.offset; // else the default is zero 
			feedArgs.feed_id = feedID;
			return feedArgs;			
		},
		
		mergeQueryString: function (feedArgs,args) {
			$log.info('Feed Query Override by Query String',args);
			for(var key in args){
				feedArgs.query[key] = args[key];
			}			
			return feedArgs;
		},		
		mergeFeedQuery: function (feedArgs,args) {
			if (args.query) {
				$log.info('Feed Query Override by Search feedQuery',args.query);
				for (var prop in args.query) {
					feedArgs.query[prop] = args.query[prop];
					//$log.debug("args.query",prop,args.query[prop],feedArgs.query[prop]);
				}
			}
			return feedArgs;
		},
		removeEmptyArgs: function (args) {
			$log.info('Feed Query Remove Empty Args',args);
			for(var key in args.query){
				if ((args.query[key]=="null") && (key!= "s")){
					delete args.query[key];
					continue;
				}  				
				if ((args.query[key]!==0) && (args.query[key]!==false)) {
					if (args.query[key] == "") {
						delete args.query[key];
					}
				}  			    			    
			}			
			return args;
		},
		pw_get_post_types: function(args) {
			//$log.debug('pwData.pw_load_feed',args);
			var params = {args:args};
			return this.wpAjax('pw_get_post_types', params);
		},
		ajax_oembed_get: function(args) {
			$log.debug('pwData.ajax_oembed_get',args);
			var params = {args:args};
			return this.wpAjax('ajax_oembed_get', params);
		},
		pw_save_post: function(args) {
			$log.debug('pwData.pw_save_post',args);
			var params = {args:args};
			return this.wpAjax('pw_save_post', params);
		},
		pw_trash_post: function(args) {
			$log.debug('pwData.pw_trash_post',args);
			var params = {args:args};
			return this.wpAjax('pw_trash_post', params);
		},
		getPostEdit: function(args) {
			$log.debug('pwData.getPostEdit',args);
			var params = {args:args};
			return this.wpAjax('pw_get_post_edit',params);
		},
		taxOutlineMixed: function(args) {
			$log.debug('pwData.taxOutlineMixed : REQUEST',args);
			var params = {args:args};
			return this.wpAjax('pw_tax_outline_mixed',params);
		},
		userQueryAutocomplete: function(args) {
			$log.debug('pwData.userQueryAutocomplete',args);
			var params = {args:args};
			return this.wpAjax('user_query_autocomplete',params);
		},
		tags_autocomplete: function(args) {
			$log.debug('pwData.tags_autocomplete',args);
			var params = {args:args};
			return this.wpAjax('tags_autocomplete',params);
		},
		setPostRelationship: function(args) {
			$log.debug('pwData.setPostRelationship',args);
			var params = {args:args};
			return this.wpAjax('pw_set_post_relationship',params);
		},
		setPostPoints: function(args) {
			$log.debug('pwData.setPostPoints',args);
			var params = {args:args};
			return this.wpAjax('pw_set_post_points',params);
		},
		
		setCommentPoints: function(args) {
			$log.debug('pwData.setCommentPoints',args);
			var params = {args:args};
			return this.wpAjax('pw_set_comment_points',params);
		},


		// TODO : DOCUMENT
		setAvatar: function( args ){
			$log.debug( 'pwData.setAvatar', args );
			var params = { args:args };
			return this.wpAjax( 'pw_set_avatar', params );
		},
		///// DEPRECIATED /////
		pw_set_avatar: function( args ) {
			return this.setAvatar( args );
		},

		pw_get_avatar: function(args) {
			$log.debug('pwData.pw_get_avatar',args);
			var params = {args:args};
			return this.wpAjax('pw_get_avatar',params);
		},

		getAvatars: function(args) {
			$log.debug('pwData.getAvatars',args);
			var params = {args:args};
			return this.wpAjax('pw_get_avatars',params);
		},
		
		wp_user_query: function(args) {
			$log.debug('pwData.wp_user_query',args);
			var params = {args:args};
			return this.wpAjax('wp_user_query',params);
		},
		pw_insert_user: function(args) {
			$log.debug('pwData.pw_insert_user',args);
			var params = {args:args};
			return this.wpAjax('pw_insert_user',params);
		},
		activationEmail: function(args) {
			$log.debug('pwData.pw_activation_email',args);
			var params = {args:args};
			return this.wpAjax('pw_activation_email',params);
		},
		pw_activate_user: function(args) {
			$log.debug('pwData.pw_activate_user',args);
			var params = {args:args};
			return this.wpAjax('pw_activate_user',params);
		},
		reset_password_email: function(args) {
			$log.debug('pwData.reset_password_email',args);
			var params = {args:args};
			return this.wpAjax('reset_password_email',params);
		},
		reset_password_submit: function(args) {
			$log.debug('pwData.reset_password_submit',args);
			var params = {args:args};
			return this.wpAjax('reset_password_submit',params);
		},
		postShareReport: function(args) {
			$log.debug('pwData.postShareReport',args);
			var params = {args:args};
			return this.wpAjax('pw_post_share_report',params);
		},
		userShareReportOutgoing: function(args) {
			$log.debug('pwData.userShareReportOutgoing',args);
			var params = {args:args};
			return this.wpAjax('pw_user_share_report_outgoing',params);
		},
		set_post_image: function(args) {
			$log.debug('pwData.set_post_image',args);
			var params = {args:args};
			return this.wpAjax('set_post_image',params);
		},
		get_userdatas: function(args) {
			$log.debug('pwData.get_userdatas',args);
			var params = {args:args};
			return this.wpAjax('pw_get_userdatas',params);
		},
		get_userdata: function(args) {
			$log.debug('pwData.get_userdata',args);
			var params = {args:args};
			return this.wpAjax('pw_get_userdata',params);
		},
		get_wizard_status: function(args) {
			$log.debug('pwData.get_wizard_status',args);
			var params = {args:args};
			return this.wpAjax('pw_get_wizard_status',params);
		},
		set_wizard_status: function(args) {
			$log.debug('pwData.set_wizard_status',args);
			var params = {args:args};
			return this.wpAjax('pw_set_wizard_status',params);
		},
		get_image: function(args) {
			$log.debug('pwData.get_image',args);
			var params = {args:args};
			return this.wpAjax('pw_get_image',params);
		},

		update_option: function(args) {
			$log.debug('pwData.set_option',args);
			var params = {args:args};
			return this.wpAjax('pw_update_option',params);
		},
		/*
		set_option_obj: function(args) {
			$log.debug('pwData.set_option_obj',args);
			var params = {args:args};
			return this.wpAjax('pw_set_option_obj',params);
		},
		*/
		/*
		get_option_obj: function(args) {
			$log.debug('pwData.get_option_obj',args);
			var params = {args:args};
			return this.wpAjax('pw_get_option_obj',params);
		},
		*/
		get_menus: function(args) {
			$log.debug('pwData.get_menus',args);
			var params = {args:args};
			return this.wpAjax('pw_get_menus',params);
		},
		getTemplatePartial: function(args) {
			$log.debug('pwData.getTemplatePartial',args);
			var params = {args:args};
			return this.wpAjax('pw_get_template_partial',params);
		},
		getTermFeed: function(args) {
			$log.debug('pwData.getTermFeed',args);
			var params = {args:args};
			return this.wpAjax('pw_get_term_feed',params);
		},

		///// FEEDS /////
		getFeedView: function( feedId ){
			// Get Current View
			var currentView = $_.get( this.feeds, feedId + '.view.current' );
			var defaultView = 'list';
			var view =  ( currentView ) ? currentView : defaultView;
			
			return view;
		},
		
		setFeedView: function( feedId, view ){

		},

		/**
		* @ngdoc method
		* @name postworld.service#setWpUsermeta
		* @methodOf postworld.$pwData
		* @description Sets user meta values in the WordPress database.
		* @param {object} args See example.
		*	
		* @returns {promise} Returns data from the server function called.
		* @example
		<pre>
		args = {
			user_id:1,
			meta_key:'userData',
			sub_key: 'key.subkey',
			value:'Hello'
		};
		$pwData.setWpUsermeta(args).then({
			function(response){
				// Success
			},
			function(response){
				// Failure
			}
		});
		</pre>
		*/
		setWpUsermeta: function(args) {
			/*
				args = {
					user_id		:	[integer], 	// optional
					sub_key		:	[string],	// optional
					value 		:	[mixed],	// required
					meta_key 	:	[string]	// optional
				}
			*/
			$log.debug('pwData.setWpUsermeta',args);
			var params = {args:args};
			return this.wpAjax('pw_set_wp_usermeta',params);
		},

		getFeed: function( feedId ){
			return $_.get( this, 'feeds.' + feedId );
    	},

		insertFeed: function( feedId, feed ){
			/* Inserts a feed into the $pwData.feeds service
			 * feedId = [ string ]
			 * feed = { posts:[], ... }
			 */
			 
			// Add/replace the feed_id key with the feedId
			feed.feed_id = feedId;

			
			//	feed.loaded = [];

			var hasFeedOutline = _.isArray( feed.feed_outline );
			if( !hasFeedOutline ){
				feed.feed_outline = [];
				feed.loaded = [];
			}

			///// ADD FEED OBJECT TO POSTS /////
			// If the feed has posts
			if( $_.objExists( feed, 'posts' ) ){
				// Create a new feed container
				var newPosts = [];
				// Interate through each post in the feed
				angular.forEach( feed.posts, function( post ){
					// And add the 'feed.id' value if it doesn't exist
					if( !$_.objExists( post, 'feed.id' ) )
						post = $_.setObj( post, 'feed.id', feedId );
					newPosts.push( post );
					
					// Add post ID to tracking feed outline
					if( !hasFeedOutline ){
						feed.feed_outline.push( post.ID );
						// Add post ID to tracking loaded array
						feed.loaded.push( post.ID );
					}
					

				});
				feed.posts = newPosts;
			}

			$log.debug( "$pwData.insertFeed : ID : " + feedId, feed );

			// Add it to the central $pwData service
			this.feeds[feedId] = feed;
			return true;
		},

		///// PROGRESS API /////
		getProgress: function( key ){
			$log.debug('pwData.getProgress',key);
			var params = { key:key };			
			return this.wpAjax('pw_get_progress',params);
		},
		endProgress: function( key ){
			$log.debug('pwData.endProgress',key);
			var params = { key:key };			
			return this.wpAjax('pw_end_progress',params);
		},

		

   }; // END OF $pwData return value
}]);
