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
		scope : {},
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

postworld.directive('loadPost', function() {
	return {
		restrict: 'A',
		replace: true,
		template: '<div ng-include="templateUrl" class="post"></div>',
		controller: 'pwLoadPostController',
		scope : {
		}
	};
});

postworld.controller('pwFeedController',
	[ '$scope', '$location', '$log', '$attrs', '$timeout', 'pwData', '$route', '_',
	function( $scope, $location, $log, $attrs, $timeout, pwData, $route, $_ ) {
		
		
		// Initialize
		$scope.busy = false; 			// Avoids running simultaneous service calls to get posts. True: Service is Running to get Posts, False: Service is Idle    	
		$scope.firstRun = true; 		// True until pwLiveFeed runs once. False for al subsequent pwScrollFeed
		$scope.args = {};
		$scope.args.feed_query = {};
		$scope.feed_query = {};
		$scope.scrollMessage = "";
		$scope.posts = [];
		$scope.message = "";   
		// $scope.turl = "http://localhost/pdev/wp-content/plugins/postworld/templates/panels/live_feed_4.html"; 	
		
		// LIVE FEED
		if ($attrs.liveFeed)    { 
			$scope.directive = 		'liveFeed';
			$scope.feed	= 			$attrs.liveFeed;
			$scope.args.feed_id = 	$attrs.liveFeed; // This Scope variable will propagate to all directives inside Live Feed
		}

		// LOAD FEED
		else  if ($attrs.loadFeed)   {
			$scope.directive = 		'loadFeed';
			$scope.feed	= 			$attrs.loadFeed;
			$scope.args.feed_id = 	$attrs.loadFeed; // This Scope variable will propagate to all directives inside Live Feed
		};    	
						
		// NO FEED
		if (!$scope.feed) {
			$log.debug('no valid Feed ID provided in Feed Settings',$scope);
			return;
		}
		
		// Set Title
		$scope.title = ( pwData.feed_settings[$scope.feed].title ) ?
			pwData.feed_settings[$scope.feed].title : '';

		// Set View
		$scope.args.view = ( pwData.feed_settings[$scope.feed].view ) ?
			pwData.feed_settings[$scope.feed].view : {};

		var view =  (pwData.feed_settings[$scope.feed].view.current) ?
			pwData.feed_settings[$scope.feed].view.current :
			'list';

		$scope.feed_item_view_type = view; // pwData.pw_get_template('posts','post',view);
		//$log.debug('pwFeedController Set Initial Feed Item View Type', $scope.feed_item_view_type);
		
	   	// Get Feed Template from feed_settings if it exists, otherwise get it from default path
	   	if (pwData.feed_settings[$scope.feed].feed_template) {
			var template = pwData.feed_settings[$scope.feed].feed_template;			   	
			$scope.templateUrl = pwData.pw_get_template( { subdir: 'feeds', view: template } );
			//$log.debug('LiveFeed() Set Initial Feed Template to ',$scope.feed, template, $scope.templateUrl);
	   	}
	   	else {
			if ($scope.directive=='liveFeed')
				$scope.templateUrl = jsVars.pluginurl+'/postworld/templates/directives/liveFeed.html';
			else if ($scope.directive=='loadFeed')
				$scope.templateUrl = jsVars.pluginurl+'/postworld/templates/directives/loadFeed.html';
			// just use default template provided in directive settings, no action required
			return;			   	
	   	}

		
		$scope.$on("CHANGE_FEED_TEMPLATE", function(event, view){
		   $log.debug('pwFeedController: Event Received:CHANGE_FEED_TEMPLATE',view);
			$scope.feed_item_view_type = view; // pwData.pw_get_template('posts','post',view); 
		   // Broadcast to all children
			$scope.$broadcast("FEED_TEMPLATE_UPDATE", $scope.feed_item_view_type);
		   });
		

		

		$scope.injectAds = function() {
			// if ads settings exist, then inject ads, otherwise, just return.
			if (pwData.feed_settings[$scope.feed].blocks) {
				// Initialize Ad Blocks
				$scope.adBlocks = 0;
				var len = pwData.feed_data[$scope.feed].posts.length;
				var offset = 0;
				if (pwData.feed_settings[$scope.feed].blocks.offset) offset = pwData.feed_settings[$scope.feed].blocks.offset;
				var increment = 10;
				if (pwData.feed_settings[$scope.feed].blocks.increment) increment = pwData.feed_settings[$scope.feed].blocks.increment;
				var max_blocks = 5;
				if (pwData.feed_settings[$scope.feed].blocks.max_blocks) max_blocks = pwData.feed_settings[$scope.feed].blocks.max_blocks;
				var adTemplate = "ad-block";
				if (pwData.feed_settings[$scope.feed].blocks.template) adTemplate = pwData.feed_settings[$scope.feed].blocks.template;
				$scope.adNextID = offset;
				// loop on scope.items starting from offset, with increments = increment, and break when max_blocks is reached
				for (var i=offset;	i<len+$scope.adBlocks; 	i=i+increment)	{ 
					console.log('Next Ad on ',$scope.adNextID);
					if (i>=len) break;
					if ($scope.adBlocks>=max_blocks) break;
					var item = {
						'post_type'	: 'ad',
						'template'	: adTemplate,
					};
					//console.log('item',item);
					// inject here
					$scope.posts.splice(i+$scope.adBlocks,0,item);
					
					$scope.adBlocks++;
					$scope.adNextID += increment+1; // 1 is the new ad added					
				}				
			}
		};
		
		$scope.injectNewAd = function() {
			// check if ads enabled
			if (pwData.feed_settings[$scope.feed].blocks) {
				var max_blocks = 5;
				if (pwData.feed_settings[$scope.feed].blocks.max_blocks) max_blocks = pwData.feed_settings[$scope.feed].blocks.max_blocks;
				var adTemplate = "ad-block";
				if (pwData.feed_settings[$scope.feed].blocks.template) adTemplate = pwData.feed_settings[$scope.feed].blocks.template;
				var increment = 10;
				if (pwData.feed_settings[$scope.feed].blocks.increment) increment = pwData.feed_settings[$scope.feed].blocks.increment;
				// Check if max_blocks reached, return.
				if ($scope.adBlocks>=max_blocks) return;				
				var len = $scope.posts.length;
				// did we reach new id? insert
				if ($scope.adNextID==len) {
					var item = {
						'post_type'	: 'ad',
						'template'	: adTemplate,
					};
					// inject here
					$scope.posts.push(item);					
					$scope.adBlocks++;
					$scope.adNextID += increment+1; // 1 is the new ad added					
				}
				// else return
			}
		};
		

		$scope.resetFeedData = function () {
			// Reset Feed Data
			pwData.feed_data[$scope.feed] = {};
			if (pwData.feed_settings[$scope.feed].feed_outline) {
				pwData.feed_data[$scope.feed].feed_outline = pwData.feed_settings[$scope.feed].feed_outline;
				// truncate based on max_posts
				var max = pwData.feed_settings[$scope.feed].max_posts;
				if (max <pwData.feed_settings[$scope.feed].feed_outline.length) {					
					pwData.feed_data[$scope.feed].feed_outline = pwData.feed_data[$scope.feed].feed_outline.splice(0,max);
				}
					 
				pwData.feed_data[$scope.feed].count_feed_outline = pwData.feed_settings[$scope.feed].feed_outline.length;														
			};						
			pwData.feed_data[$scope.feed].loaded = 0;						
			pwData.feed_data[$scope.feed].count_loaded = 0;						
			pwData.feed_data[$scope.feed].posts = [];
			// var argsValue = JSON.parse(JSON.stringify($scope.args));			
			// $scope.posts = pwData.feed_data[$scope.feed].posts;
			$scope.posts = JSON.parse(JSON.stringify(pwData.feed_data[$scope.feed].posts));
			// $scope.injectAds();
		};
		
		$scope.fillFeedData = function(response) {
			// Reset Feed Data
			pwData.feed_data[$scope.feed] = {};
			if ($scope.directive=="loadFeed") {
				if (pwData.feed_settings[$scope.feed].offset)  {
					// truncate feed outline in case of existing offset for load-feed only
					var offset = pwData.feed_settings[$scope.feed].offset;
					var len = response.data.feed_outline.length;
					response.data.feed_outline = response.data.feed_outline.splice(offset,len);
					// truncate response posts in case of existing offset for load-feed only															
					response.data.post_data = response.data.post_data.splice(offset,len); 
					$log.debug('FEED DATA : ' + pwData.feed_settings[$scope.feed].feed_id, response.data );
					//response.data.post_data = response.data.post_data.splice(offset,len); 
					// PHONG : replace post with post_data 					
				}
			}
			// Insert Response in Feed Data
			pwData.feed_data[$scope.feed].feed_outline = response.data.feed_outline;

			// truncate based on max_posts
			var max = pwData.feed_settings[$scope.feed].max_posts;
			if (max <=response.data.feed_outline.length) {					
				pwData.feed_data[$scope.feed].feed_outline = pwData.feed_data[$scope.feed].feed_outline.splice(0,max);
			}
			pwData.feed_data[$scope.feed].posts = response.data.post_data;						
			pwData.feed_data[$scope.feed].loaded = response.data.post_data.length;	

			// Count Length of loaded and feed_outline
			pwData.feed_data[$scope.feed].count_loaded = response.data.post_data.length;						
			pwData.feed_data[$scope.feed].count_feed_outline = pwData.feed_data[$scope.feed].feed_outline.length;

			// Set Feed load Status
			if (pwData.feed_data[$scope.feed].count_loaded >= pwData.feed_data[$scope.feed].count_feed_outline) {
				pwData.feed_data[$scope.feed].status = 'all_loaded';													
				$scope.scrollMessage = "No more posts to load.";						
			} else {							
				pwData.feed_data[$scope.feed].status = 'loaded';						
				$scope.scrollMessage = "Scroll down to load more.";						
			}   			
		};
		
		$scope.getNext = function() {
			$scope.message = "";   			
			// If already getting results, do not run again.
			if ($scope.busy) {
				$log.debug('pwFeedController.getNext: We\'re Busy, wait!');
				return;
			}
			$scope.busy = true;
			// if running for the first time
			if ( $scope.firstRun ) {
				$scope.firstRun = false;
				if 		($scope.directive == 'liveFeed')	$scope.pwLiveFeed();
				else if ($scope.directive == 'loadFeed')	$scope.pwLoadFeed();
			}
			else {
				// Run Search
				$scope.pwScrollFeed();				
			}
		};
		
		// Searching from Filter Feed Directives will trigger this function, which in turn restarts the Feed Loading Process
		$scope.pwRestart = function() {
			// TODO Can we break an existing Ajax Call? We cannot do that, but we can use an identifier for the request and ignore previous requests to the current id.
			// This scenario might not happen since we're not allowing more than one feed request at a time, this might be a limitation, but it makes the data consistent.
			// Set feed_settings equal to new feed_query
			// pwData.feed_settings[$scope.feed].query_args = 
			$scope.convertFeedQuery2QueryString($scope.args.feed_query);						
			$scope.firstRun = true;			
			this.getNext();
		};

		$scope.pwLiveFeed = function() {
								
			if (!$scope.args.feed_query)
				$scope.args.feed_query = {};

			// identify the feed_settings feed_id
			
			$scope.posts = {};
			// TODO set Nonce from UI
			pwData.setNonce(78);
			// get Query String Parameters,
			var qsArgs = $scope.getQueryStringArgs();			
			// We need to work with a clone of the args value
			var argsValue = JSON.parse(JSON.stringify($scope.args));
			var qsArgsValue = JSON.parse(JSON.stringify(qsArgs));
			pwData.pw_live_feed(argsValue,qsArgsValue).then(
				// Success
				function(response) {

					// Prevent Flicker when Template Loading
					$timeout( function(){
						$scope.busy = false;
					}, 100 );

					// $log.debug('pwFeedController.pwLiveFeed',$scope.args.feed_query.order_by,$scope.args.feed_query.order);						
					if (response.status === undefined) {
						console.log('response format is not recognized');
						return;
					}
					if (response.status==200) {
						// Check if data exists
						if (!(response.data instanceof Array) ) {

							// Insert Response in Feed Data					
							$scope.fillFeedData( response );
							$scope.addFeedMeta();
							$scope.posts = pwData.feed_data[$scope.feed].posts;

							$scope.injectAds();
							

						} else {
							$scope.message = "No Data Returned";
							$log.debug('pwFeedController.pw_live_feed No Data Received');						
						}
						//$scope.busy = false;							
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
					$log.error('pwFeedController.pw_live_feed Failure',response);
					// TODO Show User Friendly Message
				}
			);
			// change url params after getting finalFeedQuery						
			// $scope.convertFeedQuery2QueryString(pwData.feed_settings[$scope.feed].finalFeedQuery);						
		  };

		$scope.addFeedMeta = function( vars ){
			// Add Mechanism for scrollFeed, so it stores the value of the last feed_order, so it doesn't have to re-iterate over the whole array
			
			//{ mode: 'scrollFeed', postsLoaded: postsLoaded, newItems: newItems.length }

			// Set the mode of the Meta Data
			if( !$_.objExists( vars, 'mode' ) ){
				vars = {};
				vars.mode = "newFeed";
			}

			// Localize the posts
			var posts = pwData.feed_data[$scope.feed].posts;
			
			var feed_order = 0;
			var load_order = 0;

			var newPosts = [];

			angular.forEach( posts, function( post ){
				var newPost = post;

				// Add new variables to post object
				if( vars.mode == "newFeed" ){
					newPost = $_.setObj( newPost, 'feed.feed_order', feed_order );
					newPost = $_.setObj( newPost, 'feed.load_order', feed_order );
				}
				else if( vars.mode == "scrollFeed" && feed_order >= vars.postsLoaded ){
					newPost = $_.setObj( newPost, 'feed.feed_order', feed_order );
				}

				newPosts.push( newPost );
				feed_order ++;
				
			});

			// Re-set the centralized posts object
			//$log.debug( "NEW POSTS: ", newPosts );
			pwData.feed_data[$scope.feed].posts = newPosts;

		};


		$scope.pwLoadFeed = function() {
			if (!$scope.args.feed_query)	$scope.args.feed_query = {};
			// identify the feed_settings feed_id
			
			$scope.posts = {};
			// TODO set Nonce from UI
			pwData.setNonce(78);
			var args = {};
			args.feed_id = $scope.feed;
			// if id is defined in feed_id of the settings array, then use it
			if (pwData.feed_settings[$scope.feed].feed_id) args.feed_id = pwData.feed_settings[$scope.feed].feed_id; 
			args.preload = pwData.feed_settings[$scope.feed].preload;
			// If that feed already has an outline, then do not load feed, just go get new posts(scroll) and ignore
			if (pwData.feed_settings[$scope.feed].feed_outline) {
				$scope.resetFeedData();				
				// Set loaded = 0, 
				// pwData.feed_settings[$scope.feed].loaded = 0;
				// Run Scroll Feed
				$scope.pwScrollFeed();
				return;
			}
			pwData.pw_load_feed(args).then(
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
							$scope.posts = pwData.feed_data[$scope.feed].posts;

							$scope.injectAds();
							
						} else {
							$scope.message = "No Data Returned";
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
					$log.error('pwFeedController.pw_live_feed Failure',response);
					// TODO Show User Friendly Message
				}
			);
		  };
		$scope.pwScrollFeed = function() {
			// Check if all Loaded, then return and do nothing
			if (pwData.feed_data[$scope.feed].status == 'all_loaded') {
				//$log.debug('pwFeedController.pwScrollFeed ALL LOADED - NO MORE POSTS');				
				$scope.busy = false;
				return;
			};		
			// TODO do we need to set the loading status? or just use the busy flag?
			pwData.feed_data[$scope.feed].status = 'loading';
			
			
			//$log.debug('pwFeedController.pwScrollFeed For',$scope.feed);
			// TODO set Nonce from UI
			pwData.setNonce(78);
			// console.log('Params=',$scope.args);
			pwData.pw_get_posts($scope.args).then(
				// Success
				function(response) {
					
					if (response.status === undefined) {
						$log.debug('Feed response format is not recognized.');
						return;
					}

					if( response.status == 200) {

						var newItems = response.data;
						var load_order = 0;
						for (var i = 0; i < newItems.length; i++) {
							// $log.debug('Looping :',i,newItems[i].ID);

							// Add feed.load_order Variable
							newItems[i] = $_.setObj( newItems[i], 'feed.load_order', load_order );
							load_order ++;

							// Push to central posts array
							pwData.feed_data[$scope.feed].posts.push( newItems[i] );							
							$scope.injectNewAd();

						}

						// Add Feed Meta for only the new posts
						var postsLoaded = parseInt( pwData.feed_data[$scope.feed].loaded );
						$scope.addFeedMeta( { mode: 'scrollFeed', postsLoaded: postsLoaded, newItems: newItems.length } );
						$scope.posts = pwData.feed_data[$scope.feed].posts;

						// Update the number of posts loaded
						pwData.feed_data[$scope.feed].loaded += newItems.length;

						// Count Length of loaded, update scroll message
						pwData.feed_data[$scope.feed].count_loaded = pwData.feed_data[$scope.feed].posts.length;
						if (pwData.feed_data[$scope.feed].count_loaded >= pwData.feed_data[$scope.feed].count_feed_outline) {
							pwData.feed_data[$scope.feed].status = 'all_loaded';	
							$scope.scrollMessage = "No more posts to load!";																									
						} else {
							pwData.feed_data[$scope.feed].status = 'loaded';						
							$scope.scrollMessage = "Scroll down to load more";						
						}

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


				// Definitions
		/*
		$scope.convertQueryString2FeedQuery= function (params) {
			$log.info('Feed Query Override by Query String',params);
			for(var key in params){
				// The value is obj[key]
				$scope.args.feed_query[key] = params[key];
			}			
		};
		*/
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
				//$scope.args.feed_query[key] = params[key];
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

}]);

postworld.controller('pwLoadPostController',
	function pwLoadPostController($scope, $location, $log, $attrs, $timeout, $sce, $sanitize, pwData) {
		// Initialize
		$scope.postSettings = window['load_post'];
		if (!$scope.postSettings) throw {message:'pwLoadPostController: no post settings defined'};
		$scope.postArgs = $scope.postSettings[$attrs.loadPost];
		if (!$scope.postSettings[$attrs.loadPost]) throw {message:'pwLoadPostController: no post settings for '+$attrs.loadPost+'defined'};
		$scope.args = {};
		$scope.args.post_id = $scope.postArgs.post_id;    	     	    	    	  	
		$scope.args.fields = $scope.postArgs.fields;    	     	    	    	  	
						
		$scope.pwLoadPost = function() {
			
			pwData.pw_get_post($scope.args).then(
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
						// Set Default Feed Template and Default Feed Item Template
						//pwData.templates.promise.then(function(value) {
							   if ($scope.post) {
									var template = $scope.postArgs.view;
									var post_type = 'post';
									if ($scope.post.post_type) post_type = $scope.post.post_type;			   		
									$scope.templateUrl = pwData.pw_get_template( { subdir: 'posts', post_type: post_type, view: template } );
									//$log.debug('pwLoadPostController Set Post Template to ', post_type, $scope.templateUrl);
							   }
							   else {
									$scope.templateUrl = jsVars.pluginurl+'/postworld/templates/posts/post-full.html';
							   }
								return;
						//});
						return response.data;						
					} else {
						// handle error
						// throw {message:"error: "+response.status+"- "+response.message};
						// console.log('error',response.status,response.message);
						// TODO should we set busy to false when error is returned?
					}
					// return response.posts;
				},
				// Failure
				function(response) {
					// $log.error('pwFeedController.pw_live_feed Failure',response);
					// TODO Show User Friendly Message
				}
			);
		  };
		  $scope.pwLoadPost();
	}
);

postworld.controller('pwTestController',
	function pwTestController($scope, $location, $log, $attrs, $timeout, pwData) {
		// Initialize
						
		$scope.pwTestPost = function() {

			var post = {
				'post_title'   	: 'Hello Title',
				'post_content' 	: 'Test content.',
				'post_status'  	: 'publish',
				'post_author'  	: 1,
				'post_category'	: [1,2,4],
				'post_class'	: 'standard',
				'link_format' 	: 'post',
				'link_url'		: 'http://www.com',
				'external_image': 'http://www.com/image.jpg',				
			};
			pwData.pw_save_post( post ).then(
				// Success
				function(response) {    
					//alert( "RESPONSE : " + response.data );
					$log.debug('pwData.pw_save_post : saved post id: ', response.data);                    

				},
				// Failure
				function(response) {
					//alert('error');

				}
			);
			
		  };
		  $scope.pwTestPost();
	}
);