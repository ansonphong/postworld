'use strict';

postworld.config(function($locationProvider){
    // $locationProvider.html5Mode(true).hashPrefix('!');
});

postworld.directive('liveFeed', function() {
    return {
        restrict: 'A',
        // DO not set url here and in nginclude at the same time, so many errors!
        // templateUrl: jsVars.pluginurl+'/postworld/templates/directives/liveFeed.html',
        replace: true,
        controller: 'pwFeedController',
        scope : {
        	
        }
    };
});


postworld.directive('loadFeed', function() {
    return {
        restrict: 'A',
        // DO not set url here and in nginclude at the same time, so many errors!
        // templateUrl: jsVars.pluginurl+'/postworld/templates/directives/loadFeed.html',
        replace: true,
        controller: 'pwFeedController',
        scope : {
        	
        }
    };
});


postworld.directive('loadPost', function() {
    return {
        restrict: 'A',
        // DO not set url here and in nginclude at the same time, so many errors!
        // templateUrl: jsVars.pluginurl+'/postworld/templates/directives/loadFeed.html',
        replace: true,
        controller: 'pwLoadPostController',
        scope : {
        	
        }
    };
});


postworld.controller('pwFeedController',
    function pwFeedController($scope, $location, $log, $attrs, $timeout, pwData) {

    	// Definitions
  		$scope.convertQueryString2FeedQuery= function (params) {
  			for(var key in params){
			    // The value is obj[key]
			    $scope.args.feed_query[key] = params[key];
			}			
  		};

  		$scope.getQueryStringArgs= function () {
  			// TODO Should query string work with live feed only?
  			if ($attrs.laodFeed) {
  				return;
  			}
    		// Get Query String Parameters
    		// TODO Check if location.search work on all browsers.
    		var params = $location.search();
  			$scope.convertQueryString2FeedQuery(params);  			
  		};
    	
    	
    	// Initialize
    	$scope.busy = false; 				// Avoids running simultaneous service calls to get posts. True: Service is Running to get Posts, False: Service is Idle    	
    	$scope.firstRun = true; 			// True until pwLiveFeed runs once. False for al subsequent pwScrollFeed
		$scope.args = {};
		$scope.args.feed_query = {};
		$scope.feed_query = {};
		$scope.scrollMessage = "";
    	$scope.items = [];
    	$scope.message = "";    	
    	
    	// List of Post Items displayed in Scroller
    	// is this a live feed or a load feed?
    	if ($attrs.liveFeed)    { 
    		$scope.directive = 'liveFeed';
    		$scope.feed		= $attrs.liveFeed;
	    	$scope.args.feed_id = $attrs.liveFeed; // This Scope variable will propagate to all directives inside Live Feed
    	}
    	else  if ($attrs.loadFeed)   {
    		$scope.directive = 'loadFeed';
    		$scope.feed		= $attrs.loadFeed;
	    	$scope.args.feed_id = $attrs.loadFeed; // This Scope variable will propagate to all directives inside Live Feed
    	};    	
    	// Set feed ID here
    	    	    	  	
    	// Set Default Feed Template and Default Feed Item Template
		pwData.templates.promise.then(function(value) {
				if (!$scope.feed) {
					$log.info('no valid Feed ID provided in Feed Settings',$scope);
					return;
				}
				
		    	// Set Title
		    	if (pwData.feed_settings[$scope.feed].title) {
		    		$scope.title = pwData.feed_settings[$scope.feed].title;
		    	} else {
		    		$scope.title = '';
		    	}				
				var view = 'list';	// TODO get from Constant values
				// Get Feed Item Template from Feed Settings by default
			   if (pwData.feed_settings[$scope.feed].view.current)
			   		view = pwData.feed_settings[$scope.feed].view.current;
		    	$scope.feed_item_template = pwData.pw_get_template('posts','post',view);
				$log.info('pwFeedController Set Initial Feed Item Template to ',view, $scope.feed_item_template);
				
			   // Get Feed Template from feed_settings if it exists, otherwise get it from default path
			   if (pwData.feed_settings[$scope.feed].feed_template) {
			   		var template = pwData.feed_settings[$scope.feed].feed_template;			   	
			    	$scope.templateUrl = pwData.pw_get_template('panels','panel',template);
					$log.info('LiveFeed() Set Initial Feed Template to ',$scope.feed, template, $scope.templateUrl);
			   }
			   else {
			   		if ($scope.directive=='liveFeed')
			   			$scope.templateUrl = jsVars.pluginurl+'/postworld/templates/directives/liveFeed.html';
			   		else if ($scope.directive=='loadFeed')
			   			$scope.templateUrl = jsVars.pluginurl+'/postworld/templates/directives/loadFeed.html';
			   		// just use default template provided in directive settings, no action required
			   		return;			   	
			   }
				// $log.info('Directive:FeedItem Controller:pwFeedItemController Set Initial Feed Template to ',view, $scope.templateUrl);
		});
    	
		$scope.$on("CHANGE_FEED_TEMPLATE", function(event, view){
		   $log.info('pwFeedController: Event Received:CHANGE_FEED_TEMPLATE',view);
	    	$scope.feed_item_template = pwData.pw_get_template('posts','post',view); 
		   // Broadcast to all children
			$scope.$broadcast("FEED_TEMPLATE_UPDATE", $scope.feed_item_template);
		   });
		
		$scope.resetFeedData = function () {
			// Reset Feed Data
			pwData.feed_data[$scope.feed] = {};
			if (pwData.feed_settings[$scope.feed].feed_outline) {
				pwData.feed_data[$scope.feed].feed_outline = pwData.feed_settings[$scope.feed].feed_outline;
				pwData.feed_data[$scope.feed].count_feed_outline = pwData.feed_settings[$scope.feed].feed_outline.length;														
			};						
			pwData.feed_data[$scope.feed].loaded = 0;						
			pwData.feed_data[$scope.feed].count_loaded = 0;						
			pwData.feed_data[$scope.feed].posts = [];
			$scope.items = pwData.feed_data[$scope.feed].posts;
		},
		
   		$scope.fillFeedData = function(response) {
			// Reset Feed Data
			pwData.feed_data[$scope.feed] = {};
			// Insert Response in Feed Data
			pwData.feed_data[$scope.feed].feed_outline = response.data.feed_outline;						
			pwData.feed_data[$scope.feed].posts = response.data.post_data;						
			pwData.feed_data[$scope.feed].loaded = response.data.post_data.length;						
			// Count Length of loaded and feed_outline
			pwData.feed_data[$scope.feed].count_loaded = response.data.post_data.length;						
			pwData.feed_data[$scope.feed].count_feed_outline = response.data.feed_outline.length;
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
				$log.info('pwFeedController.getNext: We\'re Busy, wait!');
				return;
				}
			$scope.busy = true;
			// if running for the first time, do this
			if ($scope.firstRun) {
				$log.info('pwFeedController.getNext: Running for the first time',$scope.feed,$scope.directive);
				$scope.firstRun = false;
				if ($scope.directive=='liveFeed')	$scope.pwLiveFeed();
				else if ($scope.directive=='loadFeed')	$scope.pwLoadFeed();
			}
			// otherwise, do this
			else {
				// Run Search
				$log.info('pwFeedController.getNext: Scrolling More');
				$scope.pwScrollFeed();				
			}
		};
		
		// Searching from Filter Feed Directives will trigger this function, which in turn restarts the Feed Loading Process
		$scope.pwRestart = function() {
			// TODO Can we break an existing Ajax Call? We cannot do that, but we can use an identifier for the request and ignore previous requests to the current id.
			// This scenario might not happen since we're not allowing more than one feed request at a time, this might be a limitation, but it makes the data consistent.
			$scope.firstRun = true;
			this.getNext();
		};
		$scope.pwLiveFeed = function() {
			$scope.getQueryStringArgs();    				
			if (!$scope.args.feed_query)	$scope.args.feed_query = {};
    		console.log('$scope.args.feed_query2',$scope.args.feed_query);    	
	    	// identify the feed_settings feed_id
			
			$scope.items = {};
			// TODO set Nonce from UI
			pwData.setNonce(78);
			// We need to work with a clone of the args value
			var argsValue = JSON.parse(JSON.stringify($scope.args));
        	pwData.pw_live_feed(argsValue).then(
				// Success
				function(response) {	
					$scope.busy = false;
					// $log.info('pwFeedController.pwLiveFeed',$scope.args.feed_query.order_by,$scope.args.feed_query.order);						
					if (response.status === undefined) {
						console.log('response format is not recognized');
						return;
					}
					if (response.status==200) {
						// Check if data exists
						if (!(response.data instanceof Array) ) {
							// Insert Response in Feed Data						
							$log.info('pwFeedController.pw_live_feed Success',response.data);						
							$scope.fillFeedData(response);																			
							$scope.items = response.data.post_data;
						} else {
							$scope.message = "No Data Returned";
							$log.info('pwFeedController.pw_live_feed No Data Received');						
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
		$scope.pwLoadFeed = function() {
			if (!$scope.args.feed_query)	$scope.args.feed_query = {};
	    	// identify the feed_settings feed_id
			
			$scope.items = {};
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
						$log.info('pwFeedController.pw_load_feed Success',response.data);						
						// Check if data exists
						if (!(response.data instanceof Array) ) {
							// Insert Response in Feed Data						
							$scope.fillFeedData(response);																			
							$scope.items = response.data.post_data;
						} else {
							$scope.message = "No Data Returned";
							$log.info('pwFeedController.pw_load_feed No Data Received');						
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
				$log.info('pwFeedController.pwScrollFeed ALL LOADED - NO MORE POSTS');				
				$scope.busy = false;
				return;
			};		
			// TODO do we need to set the loading status? or just use the busy flag?
			pwData.feed_data[$scope.feed].status = 'loading';
			
			
			$log.info('pwFeedController.pwScrollFeed For',$scope.feed);
			// TODO set Nonce from UI
			pwData.setNonce(78);
			// console.log('Params=',$scope.args);
        	pwData.pw_get_posts($scope.args).then(
				// Success
				function(response) {
					$scope.busy = false;
					if (response.status === undefined) {
						console.log('response format is not recognized');
						// TODO Show User Friendly Error Message
						return;
					}
					if (response.status==200) {
						$log.info('pwFeedController.pwScrollFeed Success',response.data);
						// Add Results to controller items						
						//$scope.posts = response.data;
						
						var newItems = response.data;
						for (var i = 0; i < newItems.length; i++) {
							// $log.info('Looping :',i,newItems[i].ID);
							//$scope.items.push(newItems[i]);
							// TODO check why when adding an item here, it affects also $scope.items !
							pwData.feed_data[$scope.feed].posts.push(newItems[i]);							
							// $log.info('$scope.items has',$scope.items.length,' items');							
						 }
						pwData.feed_data[$scope.feed].loaded += newItems.length;
						// Count Length of loaded
						pwData.feed_data[$scope.feed].count_loaded = pwData.feed_data[$scope.feed].posts.length;
						if (pwData.feed_data[$scope.feed].count_loaded >= pwData.feed_data[$scope.feed].count_feed_outline) {
							pwData.feed_data[$scope.feed].status = 'all_loaded';	
							$scope.scrollMessage = "No more posts to load!";																									
						} else {							
							pwData.feed_data[$scope.feed].status = 'loaded';						
							$scope.scrollMessage = "Scroll down to load more";						
						}
						  
						// Update feed data with newly loaded posts
						$log.info('pwFeedController.pwScrollFeed Success feed_data:',pwData.feed_data[$scope.feed]);
						return response.data;						
					} else {
						// handle error
						console.log('error',response.status,response.message);
						// TODO Show User Friendly Error Message
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
    }
);

postworld.controller('pwLoadPostController',
    function pwLoadPostController($scope, $location, $log, $attrs, $timeout, pwData) {
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
						$log.info('pwPostLoadController.pw_load_post Success',response.data);						
						$scope.post = response.data;
						// Set Template URL
				    	// Set Default Feed Template and Default Feed Item Template
						pwData.templates.promise.then(function(value) {
							   if ($scope.post) {
							   		var template = $scope.postArgs.view;
							   		var post_type = 'post';
							   		if ($scope.post.post_type) post_type = $scope.post.post_type;			   		
							    	$scope.templateUrl = pwData.pw_get_template('posts',post_type,template);
									$log.info('pwLoadPostController Set Post Template to ', post_type, $scope.templateUrl);
							   }
							   else {
						   			$scope.templateUrl = jsVars.pluginurl+'/postworld/templates/posts/post-full.html';
							   }
						   		return;
						});
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

			var post_data = {
		       'post_title'    : 'hello content',
		       'post_content'  :'sdsfdsfds',
		       'post_status'   : 'publish',
		       'post_author'   : 1,
		       'post_category' : [8,39 ],
		       'post_class':'test',
		       'post_format' :'ggggggg',
		       'link_url':'sssssss',
		       'external_image' : 'fgdfgdfgdf',				
			};
            pwData.pw_save_post( post_data ).then(
                // Success
                function(response) {    
                    //alert( "RESPONSE : " + response.data );
                    $log.info('pwData.pw_save_post : saved post id: ', response.data);                    

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