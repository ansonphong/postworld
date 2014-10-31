'use strict';

postworld.config(function($locationProvider){
	// $locationProvider.html5Mode(true).hashPrefix('!');
});

postworld.directive('liveFeed', function() {
	return {
		restrict: 'A',
		replace: true,
		controller: 'pwFeedController',
       	template: '<div ng-include="templateUrl" class="feed"></div>',
		scope : {
			//feedVars: '=',
		},
		link : function( $scope, element, attrs ){
			
		},
	};
});

postworld.directive('loadFeed', function() {
	return {
		restrict: 'A',
		replace: true,
       	controller: 'pwFeedController',
       	template: '<div ng-include="templateUrl" class="feed"></div>',
		scope : {}
	};
});


postworld.controller('pwFeedController',
	[ '$scope', '$location', '$log', '$attrs', '$timeout', 'pwData', '$route', '_',
	function( $scope, $location, $log, $attrs, $timeout, $pwData, $route, $_ ) {
		
		// Initialize
		$scope.busy = false; 			// Avoids running simultaneous service calls to get posts. True: Service is Running to get Posts, False: Service is Idle    	
		$scope.firstRun = true; 		// True until pwLiveFeed runs once. False for al subsequent pwScrollFeed
		$scope.scrollMessage = "";
		$scope.posts = [];

		// LIVE FEED
		if ($attrs.liveFeed)    { 
			$scope.directive = 		'liveFeed';
			$scope.feedId	= 		$attrs.liveFeed;
		}
		// LOAD FEED
		else  if ($attrs.loadFeed)   {
			$scope.directive = 		'loadFeed';
			$scope.feedId	= 			$attrs.loadFeed;
		};

		// FEED OBJECT
		$scope.feed = ( $_.objExists( $pwData.feeds, $scope.feedId ) ) ?
			$pwData.feeds[$scope.feedId] : {};
		
		// FEED ID
		$scope.feed.feed_id = 	$scope.feedId; // This Scope variable will propagate to all directives inside Live Feed

		// NO FEED
		if (!$scope.feedId) {
			$log.debug('no valid Feed ID provided in Feed Settings',$scope);
			return;
		}
		
		// FEED VARIABLES
		//$scope.feedOptions = $_.getObj( $scope.feed, 'options' )

		// SET VIEW
		$scope.feed.view = ( $pwData.feeds[$scope.feedId].view ) ?
			$pwData.feeds[$scope.feedId].view : {};

		var view =  ($pwData.feeds[$scope.feedId].view.current) ?
			$pwData.feeds[$scope.feedId].view.current :
			'list';

		$scope.feed_item_view_type = view;

	   	// Get Feed Template from feeds if it exists
	   	if ($pwData.feeds[$scope.feedId].feed_template) {
			var template = $pwData.feeds[$scope.feedId].feed_template;			   	
			$scope.templateUrl = $pwData.pw_get_template( { subdir: 'feeds', view: template } );
	   	}
	   	// Otherwise get it from default
	   	else {
	   		$scope.templateUrl = $pwData.pw_get_template( { subdir: 'feeds', view: 'feed-list' } );   	
	   	}

		$scope.$on("CHANGE_FEED_TEMPLATE", function(event, view){
		   $log.debug('pwFeedController: Event Received:CHANGE_FEED_TEMPLATE',view);
			$scope.feed_item_view_type = view; // $pwData.pw_get_template('posts','post',view); 
		   // Broadcast to all children
		   // TODO : Also broadcast the feed ID, so only the effected feed is updated
			$scope.$broadcast("FEED_TEMPLATE_UPDATE", $scope.feed_item_view_type);
		   });


		$scope.setDefault = function( exp, defaultVal ){
			var value = $scope.$eval( exp );
			if( _.isUndefined( value ) )
				return defaultVal;
			else
				return value;
		}

		$scope.resetFeedData = function () {
			// Reset Feed Data
			$pwData.feeds[$scope.feedId] = {};
			if ($pwData.feeds[$scope.feedId].feed_outline) {
				$pwData.feeds[$scope.feedId].feed_outline = $pwData.feeds[$scope.feedId].feed_outline;
			};											
			$pwData.feeds[$scope.feedId].posts = [];
			$pwData.feeds[$scope.feedId].loaded = [];

			// var argsValue = JSON.parse(JSON.stringify($scope.feed));			
			// $scope.posts = $pwData.feeds[$scope.feedId].posts;
			$scope.posts = JSON.parse(JSON.stringify($pwData.feeds[$scope.feedId].posts));
			// $scope.injectBlocks();
		};
		
		$scope.fillFeedData = function( response ) {
			// This function executes on the first run of a pwLiveFeed or pwLoadFeed
			// After hearing back from the server AJAX call
			// With the first payload of posts
			// For feeds preloaded with post data on load, this function is not called

			// Create Feed Object if it doesn't exist
			if( _.isUndefined( $pwData.feeds[$scope.feedId] ) )
				$pwData.feeds[$scope.feedId] = {};

			// If we're handling the loadFeed directive
			if ($scope.directive=="loadFeed") {
				if ($pwData.feeds[$scope.feedId].offset)  {
					// truncate feed outline in case of existing offset for load-feed only
					var offset = $pwData.feeds[$scope.feedId].offset;
					var len = response.data.feed_outline.length;
					response.data.feed_outline = response.data.feed_outline.splice(offset,len);
					// truncate response posts in case of existing offset for load-feed only															
					response.data.posts = response.data.posts.splice(offset,len); 
					//$log.debug('FEED DATA : ' + $pwData.feeds[$scope.feedId].feed_id, response.data );
					//response.data.posts = response.data.posts.splice(offset,len); 				
				}
			}

			/// INSERT FEED DATA RESPONSE ///
			$pwData.feeds[$scope.feedId].feed_outline = response.data.feed_outline;
			$pwData.feeds[$scope.feedId].posts = response.data.posts;
			$pwData.feeds[$scope.feedId].loaded = response.data.loaded;

			$scope.updateStatus();
						
		};
		
		$scope.getNext = function() {
			$scope.message = "";   			
			// If already getting results, do not run again.
			if ($scope.busy) {
				$log.debug('pwFeedController.getNext: We\'re Busy, wait!');
				return;
			}
			// if running for the first time
			if ( $scope.firstRun ) {
				$scope.firstRun = false;
				if 		($scope.directive == 'liveFeed')	$scope.pwLiveFeed();
				else if ($scope.directive == 'loadFeed')	$scope.pwLoadFeed();
			}
			else {
				// Run Search
				$scope.scrollFeed();				
			}
		};
		
		// Searching from Filter Feed Directives will trigger this function, which in turn restarts the Feed Loading Process
		$scope.pwRestart = function() {
			// TODO Can we break an existing Ajax Call? We cannot do that, but we can use an identifier for the request and ignore previous requests to the current id.
			// This scenario might not happen since we're not allowing more than one feed request at a time, this might be a limitation, but it makes the data consistent.
			// Set feeds equal to new 'query'
			// $pwData.feeds[$scope.feedId].query = 
			$scope.convertFeedQuery2QueryString($scope.feed.query);						
			$scope.firstRun = true;			
			this.getNext();
		};


		$scope.pwLiveFeed = function() {
			// TODO : Set Nonce Authentically
			$pwData.setNonce(78);
			$log.debug( "LIVE FEED (init) : ID : " + $scope.feed.feed_id, $scope.feed );

			///// GET FEED FROM PRELOADED DATA /////
			// If posts have already been pre-loaded
			if( _.isArray( $pwData.feeds[$scope.feedId].posts ) ){
				// Inject blocks into the feed
				$scope.injectBlocks();
				// Add feed meta data
				$scope.addFeedMeta();
				// Localize the posts
				$scope.posts = $pwData.feeds[$scope.feedId].posts;
				// Update the status
				$scope.updateStatus();
				// Toggle off busy
				$scope.busy = false;
				// Return here to avoid AJAX call
				return;
			}

			///// GET FEED BY AJAX /////
			// Toggle on busy
			$scope.busy = true;
			// Establish scope posts object
			$scope.posts = {};
			// Clone Args Value as 'feed'
			var feed = JSON.parse( JSON.stringify( $scope.feed ) );

			// Get Query String Parameters, if any are provided
			var qsArgs = $scope.getQueryStringArgs();			
			var qsArgsValue = JSON.parse( JSON.stringify( qsArgs ) );

			// Initiate the AJAX Call
			$pwData.pw_get_live_feed( feed, qsArgsValue ).then(
				// Success
				function(response) {

					$log.debug( "LIVE FEED (response) : ID : " + response.data.feed_id, response.data );

					// Prevent Flicker when Template Loading
					$timeout( function(){
						$scope.busy = false;
					}, 100 );

					// Handle Error					
					if ( response.status === undefined ) {
						$log.error('LIVE FEED : ID : ' + feed.feed_id + ' : response format is not recognized');
						return;
					}
					if ( response.status == 200 ) {
						// Check if data exists
						if ( _.isObject( response.data ) ) {
							// Insert Response in Feed Data					
							$scope.fillFeedData( response );
							$scope.injectBlocks();
							$scope.addFeedMeta();
							$scope.posts = $pwData.feeds[$scope.feedId].posts;
						} else {
							$log.debug('pwFeedController.pw_get_live_feed No Data Received');						
						}
						return response.data;
					} else {
						// handle error
						$log.debug( 'error', response.status, response.message );
						// TODO should we set busy to false when error is returned?
					}
					// return response.posts;
				},
				// Failure
				function(response) {
					$scope.busy = false;
					$log.error('pwFeedController.pw_get_live_feed Failure',response);
					// TODO Show User Friendly Message
				}
			);
			// change url params after getting finalFeedQuery						
			// $scope.convertFeedQuery2QueryString($pwData.feeds[$scope.feedId].finalFeedQuery);						
		  };

		$scope.addFeedMeta = function( vars ){
			// TODO : PERFORMANCE : Add Mechanism for scrollFeed, so it stores the value of the last index,
			// so it doesn't have to re-iterate over the whole array
			// vars = { mode: 'scrollFeed', postsLoaded: postsLoaded, newItems: newItems.length }

			// Set the mode of the Meta Data
			if( !$_.objExists( vars, 'mode' ) ){
				vars = {};
				vars.mode = "newFeed";
			}

			// Localize the posts
			var posts = $pwData.feeds[$scope.feedId].posts;
			
			var index = 0;
			var loadOrder = 0;

			var newPosts = [];

			// Iterate through each post
			angular.forEach( posts, function( post ){
				// Add the Feed ID
				post = $_.setObj( post, 'feed.id', $scope.feedId );

				// Add new variables to post object
				if( vars.mode == "newFeed" ){
					post = $_.setObj( post, 'feed.index', index );
					post = $_.setObj( post, 'feed.loadOrder', index );
				}
				else if( vars.mode == "scrollFeed" && index >= vars.postsLoaded ){
					post = $_.setObj( post, 'feed.index', index );
				}

				newPosts.push( post );
				index ++;
				
			});

			// Re-set the centralized posts object
			$pwData.feeds[$scope.feedId].posts = newPosts;

		};

		///// DEPRECIATED (DEV CACHE PROPERTIES OF LIVE FEED) /////
		$scope.pwLoadFeed = function() {
			$scope.busy = true;
			$scope.posts = {};

			// TODO set Nonce from UI
			$pwData.setNonce(78);
			var args = {};
			args.feed_id = $scope.feed;
			// if id is defined in feed_id of the settings array, then use it
			if ($pwData.feeds[$scope.feedId].feed_id) args.feed_id = $pwData.feeds[$scope.feedId].feed_id; 
			args.preload = $pwData.feeds[$scope.feedId].preload;
			// If that feed already has an outline, then do not load feed, just go get new posts(scroll) and ignore
			if ($pwData.feeds[$scope.feedId].feed_outline) {
				$scope.resetFeedData();				
				// Set loaded = 0, 
				// $pwData.feeds[$scope.feedId].loaded = 0;
				// Run Scroll Feed
				$scope.scrollFeed();
				return;
			}
			$pwData.pw_load_feed(args).then(
				// Success
				function(response) {
					$scope.busy = false;
					if (response.status === undefined) {
						console.log('response format is not recognized');
						return;
					}
					if (response.status==200) {
						//$log.debug('pwFeedController.pw_load_feed Success',response.data);						
						// Check if data exists
						if (!(response.data instanceof Array) ) {

							// Insert Response in Feed Data					
							$scope.fillFeedData( response );
							$scope.addFeedMeta();
							$scope.posts = $pwData.feeds[$scope.feedId].posts;

							$scope.injectBlocks();
							
						} else {
							$log.debug('pwFeedController.pw_load_feed No Data Received');						
						}
						$scope.busy = false;							
						return response.data;						
					} else {
						// handle error
						console.log('error',response.status,response.message);
						// TODO should we set busy to false when error is returned?
					}
					// return response.posts;
				},
				// Failure
				function(response) {
					$scope.busy = false;
					$log.error('pwFeedController.pw_get_live_feed Failure',response);
					// TODO Show User Friendly Message
				}
			);
		  };

		$scope.scrollFeed = function() {
			
			// Check if all Loaded, then return and do nothing
			if ($pwData.feeds[$scope.feedId].status == 'all_loaded') {
				$log.debug('pwFeedController.scrollFeed : ALL LOADED');				
				$scope.busy = false;
				return;
			};

			// TODO do we need to set the loading status? or just use the busy flag?
			$pwData.feeds[$scope.feedId].status = 'loading';
			
			//$log.debug( ">>> SCROLL FEED <<<", $scope.feed );

			if( $scope.busy )
				return;

			// TODO set Nonce from UI
			$pwData.setNonce(78);


			///// PREPARE GET POSTS /////

			var feed = $pwData.feeds[$scope.feedId];

			// If already all loaded, then return
			if (feed.status == 'all_loaded')  {
				$log.debug('pwData.pw_get_posts : ALL LOADED : ' + $scope.feedId );			
				return;
			};
	
			// Slice Outline Array
			var idBegin = feed.loaded.length;

			// Use Preload value if no posts loaded, otherwise use load_increment
			var idEnd = ( feed.loaded.length == 0 ) ?
				idBegin + feed.preload :
				idBegin + feed.load_increment;
			
			//$log.debug( 'pwData.feeds : ', feeds );
			$log.debug( 'pwFeedController.scrollFeed // idBegin : ' + idBegin + ' / idEnd : ' + idEnd );

			// Set the post IDs
			var postIDs = feed.feed_outline.slice( idBegin, idEnd );

			// Set the fields
			var fields = $_.getObj( feed, 'query.fields' );
			if( fields == false )
				fields = 'preview';

			// Set the options
			var options = $_.getObj( feed, 'options' );
			if( options == false )
				options = {};

			// Set the parameters
			var params = {
				post_ids : postIDs,
				fields : fields,
				options : options,
			};

			////////////////////////////
			$log.debug( "pw_get_posts : " , params );

			$scope.busy = true;
			$pwData.pw_get_posts( params ).then(
				// Success
				function(response) {
					if (response.status === undefined) {
						$log.debug('Feed response format is not recognized.');
						return;
					}
					if( response.status == 200) {

						var newItems = response.data;
						
						// Used to sequence the order of post fade-in transitions 
						var loadOrder = 0;
						var injectBlock = false;

						for ( var i = 0; i < newItems.length ; i++ ) {

							/// LOAD ORDER ///
							// Tells which order in the chunk of posts loaded each post is
							// To allow for sequencing transitions, such as offset fade-ins on scroll
							newItems[i] = $_.setObj( newItems[i], 'feed.loadOrder', loadOrder );
							loadOrder ++;

							$log.debug( ">>> INJECTED POST : loadOrder : " + newItems[i].feed.loadOrder + " : " + newItems[i].post_title );

							// Push to central posts array
							$pwData.feeds[$scope.feedId].posts.push( newItems[i] );

							/// INJECT BLOCKS ///					
							var blockVars = { singleRun: true, post:{ feed:{ loadOrder: loadOrder } } };
							// Used to know if a block has been injected
							// Will return true if a block was added
							injectBlock = $scope.injectBlock( blockVars );
							if( injectBlock == true ){
								$log.debug( ">>> INJECTED BLOCK : loadOrder : ", blockVars.post.feed.loadOrder );
								loadOrder ++;
							}

						}

						// Inject Blocks
						//$scope.injectBlocks();

						/// UPDATE : LOADED ///
						// Get the new 'loaded' array directly from which post IDs were requested
						// Since in some cases of returning gallery posts, they will differ from the actual post data
						var loaded = $pwData.feeds[$scope.feedId].loaded;
						loaded = loaded.concat( postIDs );
						$pwData.feeds[$scope.feedId].loaded = loaded;

						// Add Feed Meta for only the new posts
						var postsLoaded = parseInt( $pwData.feeds[$scope.feedId].posts.length - 1 );
						$scope.addFeedMeta( { mode: 'scrollFeed', postsLoaded: postsLoaded, newItems: newItems.length } );
						
						// Set the posts into the scope
						$scope.posts = $pwData.feeds[$scope.feedId].posts;

						

						// Update feed status
						$scope.updateStatus();
						$scope.busy = false;
										
					} else {
						$log.debug('FEED ERROR : ',response.status,response.message);
						$scope.busy = false;

					}
				},
				// Failure
				function(response) {
					$log.error('pwFeedController.pwScrollFeed Failure',response);
					$scope.busy = false;
					// TODO Show User Friendly Error Message
				}
			);
		};

		$scope.initBlocksWidgets = function( blocks ){
			// blocks = [ object ] // the blocks object
			// If widgets are defined in blocks, localize the widgets item

			// Get the Sidebar ID
			var sidebarId = $_.get( blocks, 'widgets.sidebar' );
			// If sidebar ID doesn't exist as a string, return here
			if( !_.isString( sidebarId ) )
				return blocks;

			// Get the Widgets
			var widgets = $_.get( $pwData.widgets, sidebarId );
			// If widgets doesn't exist as an array, return here
			if( !_.isArray( widgets ) )
				return blocks;

			// If there are less widgets than the max blocks value
			if( blocks.max > widgets.length )
				// Reduce the max to the number of widgets
				blocks.max = widgets.length;

			// Create scope object of widgets
			$scope.widgets = widgets;
			
			// Return
			return blocks;

		}


		$scope.initBlocks = true;
		$scope.injectBlocks = function() {
			// Run at the initiation of a feed
			// Injects blocks into the feed array
			//$log.debug( "FEED : ", $pwData.feeds[$scope.feedId] );

			///// INIT BLOCK INJECTION /////
			// If this is the first time injecting, establish the blocks object
			if( $scope.initBlocks ){
				$scope.initBlocks = false;

				// Get the blocks object
				var blocks = $_.get( $pwData.feeds, $scope.feedId +'.blocks' );

				// If blocks is defined as an object
				if( _.isObject( blocks ) ){

					// Define the default blocks settings
					var defaultBlocks = {
						offset: 3,				// How many posts in the feed before first block
						increment: 10,			// Place blocks every X number of posts
						max: 50,				// Maximum number of blocks to inject
						template: 'ad-block',	// Name of panel template id
						sidebar: false,			// Use a sidebar for blocks
						query: false,			// Use a query for the blocks
						classes: false,
					};

					// Replace the default settings with the settings from the feed
					blocks = array_replace_recursive( defaultBlocks, blocks );

					// Current number of blocks in the feed
					blocks._count = 0;

					// Establish where the next block will be added
					blocks._nextIndex = blocks.offset;

					// Initiate widgets
					blocks = $scope.initBlocksWidgets( blocks );

					// Set the object into the scope
					$scope.blocks = blocks;


				}
				// If no blocks object is defined, return here
				else
					return;

			}

			///// IF : NO BLOCKS DEFINED /////
			else if( !_.isObject( $scope.blocks ) )
				// Return here
				return;


			// Get the number of posts loaded
			//var postCount = $pwData.feeds[$scope.feedId].loaded.length;

			$scope.injectBlock();
	
		};


		
		$scope.injectBlock = function( vars ){

			// If no blocks configured, return here
			if( _.isEmpty( $scope.blocks ) )
				return;

			/// SET DEFAULTS ///
			if( _.isUndefined( vars ) )
				vars = {};
			if( _.isUndefined( vars.singleRun ) )
				vars.singleRun = false;
			if( _.isUndefined( vars.post ) )
				vars.post = {};

			// If the next block is within range of the current feed length
			if( $scope.blocks._nextIndex <= $pwData.feeds[$scope.feedId].posts.length &&
				// And the maximum number of blocks has not been reached
				$scope.blocks._count < $scope.blocks.max ){

				// Increase number of blocks
				$scope.blocks._count ++;

				// Define the block item as a post to inject into the feed 
				var post = {
					post_type	: '_pw_block',
					template	: $scope.blocks.template,
					block: {
						'index'			: $scope.blocks._count - 1,
						'feed_index'	: $scope.blocks._nextIndex,
						'classes'		: $scope.blocks['classes'],
					},
				};

				// If a post is provided, add the post data
				if( !_.isEmpty( vars.post ) )
					post = array_replace_recursive( post, vars.post );

				// Inject the block into the feed at the pre-calculated next index
				$pwData.feeds[$scope.feedId].posts.splice( $scope.blocks._nextIndex, 0, post );
				
				// Set when the next block will be added
				$scope.blocks._nextIndex += $scope.blocks.increment + 1; // + 1 to include the new block added					
		
				// LOOP : Run this function again
				if( !vars.singleRun )
					$scope.injectBlock();
				else
					return true;
			}

			return false;

		}

		$scope.updateStatus = function(){
			// Count Length of loaded, update scroll message
			if ($pwData.feeds[$scope.feedId].loaded.length >= $pwData.feeds[$scope.feedId].feed_outline.length ) {
				$pwData.feeds[$scope.feedId].status = 'all_loaded';	
				$scope.scrollMessage = "No more posts to load!";																									
			} else {
				$pwData.feeds[$scope.feedId].status = 'loaded';						
				$scope.scrollMessage = "Scroll down to load more";						
			}
		}


		$scope.convertFeedQuery2QueryString= function (params) {
			// $log.info('pwFeedController convertFeedQuery2QueryString', params);
			$log.info('Feed Query Override by Feed Query',params);			  			
			// Loop on all query variables
			var queryString = "";
			for(var key in params){
				// Remove Null Values
				if (params[key]==null){  					
					continue;
				}
				if (key=="tax_query") {
					var taxInput = escape(JSON.stringify(params[key]));
					queryString += key + "=" + taxInput + "&";
					continue;
				};
				// The value is obj[key]
				//$scope.feed.query[key] = params[key];
				// TODO objects like taxonomy?
				// TODO arrays?
				if ((params[key]!==0) && (params[key]!==false)) {
					if (params[key] == "") {
						continue;
					}
				}  
				queryString += key + "=" + escape(params[key]) + "&"; 
			}
			queryString = queryString.substring(0, queryString.length - 1);
			$log.debug('path is ',$location.path());
			// $location.search('page', pageNumber);
			var path = $location.path();
			$location.path(path).search(queryString);
			//$location.path().search(queryString);
			$log.info('abslute path = ',$location.absUrl(),queryString);			
			//$log.info('pwFeedController convertFeedQuery2QueryString', queryString);  			
		};

		$scope.getQueryStringArgs= function () {
			// TODO Should query string work with live feed only?
			if ($attrs.loadFeed) {
				return;
			}
			// Get Query String Parameters
			// TODO Check if location.search work on all browsers.
			var params = $location.search();
			if ((params) && (params.tax_query)) {    			
				params.tax_query = JSON.parse(params.tax_query); 
			}
			return params;
			//$scope.convertQueryString2FeedQuery(params);  			
		};
	


	$log.debug( 'feed : ', $scope.feed );


}]);




postworld.directive('loadPost', function() {
	return {
		restrict: 'A',
		replace: true,
		template: '<div ng-include="templateUrl" class="post"></div>',
		controller: 'pwLoadPostController',
		scope : {
		},
	};
});

postworld.controller('pwLoadPostController',
	function pwLoadPostController($scope, $location, $log, $attrs, $timeout, $sce, $sanitize, pwData) {
		// Initialize
		$scope.postSettings = window['load_post'];
		if (!$scope.postSettings) throw {message:'pwLoadPostController: no post settings defined'};
		$scope.postArgs = $scope.postSettings[$attrs.loadPost];
		if (!$scope.postSettings[$attrs.loadPost]) throw {message:'pwLoadPostController: no post settings for '+$attrs.loadPost+'defined'};
		$scope.feed = {};
		$scope.feed.post_id = $scope.postArgs.post_id;    	     	    	    	  	
		$scope.feed.fields = $scope.postArgs.fields;    	     	    	    	  	
						
		$scope.pwLoadPost = function() {
			
			pwData.pw_get_post($scope.feed).then(
				// Success
				function(response) {
					$scope.busy = false;
					if (response.status === undefined) {
						console.log('response format is not recognized');
						return;
					}
					if (response.status==200) {
						//$log.debug('pwPostLoadController.pw_load_post Success',response.data);						
						$scope.post = response.data;

						// Set Template URL
						if ($scope.post) {
							var template = $scope.postArgs.view;
							var post_type = 'post';
							if ($scope.post.post_type) post_type = $scope.post.post_type;			   		
							$scope.templateUrl = pwData.pw_get_template( { subdir: 'posts', post_type: post_type, view: template } );
						}
						else {
							$scope.templateUrl = jsVars.pluginurl+'/postworld/templates/posts/post-full.html';
						}
						return;
						//return response.data;	

					} else {
						// handle error
					}
				},
				// Failure
				function(response) {
					$log.error('pwFeedController.pw_get_live_feed Failure',response);
				}
			);
		  };
		  $scope.pwLoadPost();
	}

);
