'use strict';

pwApp.directive('liveFeed', function() {
    return {
        restrict: 'A',
        // DO not set url here and in nginclude at the same time, so many errors!
        // templateUrl: jsVars.pluginurl+'/postworld/templates/directives/liveFeed.html',
        replace: true,
        controller: 'pwLiveFeedController',
        scope : {
        	
        }
    };
});


pwApp.directive('loadFeed', function() {
    return {
        restrict: 'A',
        // DO not set url here and in nginclude at the same time, so many errors!
        // templateUrl: jsVars.pluginurl+'/postworld/templates/directives/loadFeed.html',
        replace: true,
        controller: 'pwLiveFeedController',
        scope : {
        	
        }
    };
});


pwApp.controller('pwLiveFeedController',
    function pwLiveFeedController($scope, $location, $log, $attrs, $timeout, pwData) {
    	// Initialize
    	$scope.busy = false; 				// Avoids running simultaneous service calls to get posts. True: Service is Running to get Posts, False: Service is Idle    	
    	$scope.firstRun = true; 			// True until pwLiveFeed runs once. False for al subsequent pwScrollFeed
		$scope.args = {};
		$scope.args.feed_query = {};
		$scope.feed_query = {};
    	$scope.items = [];					// List of Post Items displayed in Scroller
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
    	
    	// TODO move getting templates to app startup
    	pwData.pw_get_templates(null).then(function(value) {
		    // TODO should we create success/failure responses here?
		    // resolve pwData.templates
		    pwData.templates.resolve(value.data);
		    pwData.templatesFinal = value.data;
		    console.log('pwLiveFeedController templates=',pwData.templatesFinal);
		  });    	
    	    	
    	// Set Default Feed Template and Default Feed Item Template
		pwData.templates.promise.then(function(value) {
				if (!$scope.feed) {
					$log.info('no valid Feed ID provided in Feed Settings',$scope);
					return;
				}
				var view = 'list';	// TODO get from Constant values
				// Get Feed Item Template from Feed Settings by default
			   if (pwData.feed_settings[$scope.feed].view.current)
			   		view = pwData.feed_settings[$scope.feed].view.current;
		    	$scope.feed_item_template = pwData.pw_get_template('posts','post',view);
				$log.info('pwLiveFeedController Set Initial Feed Item Template to ',view, $scope.feed_item_template);
				
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
    	
    	// Broadcast Feed Template Value when it changes
		$scope.$watch('feed_item_template', function(newValue, oldValue) { 
			// $scope.feed_item_template = 
				// scope.counter = scope.counter + 1; 
			});			
			
		$scope.$on("CHANGE_FEED_TEMPLATE", function(event, view){
		   $log.info('pwLiveFeedController: Event Received:CHANGE_FEED_TEMPLATE',view);
	    	$scope.feed_item_template = pwData.pw_get_template('posts','post',view); 
		   // Broadcast to all children
			$scope.$broadcast("FEED_TEMPLATE_UPDATE", $scope.feed_item_template);
		   });
		   
   		$scope.getNext = function() {
			// If already getting results, do not run again.
			if ($scope.busy) {
				$log.info('pwLiveFeedController.getNext: We\'re Busy, Wait!');
				return;
				}
			$scope.busy = true;
			// if running for the first time, do this
			if ($scope.firstRun) {
				$log.info('pwLiveFeedController.getNext: Running for the first time',$scope.feed,$scope.directive);
				$scope.firstRun = false;
				if ($scope.directive=='liveFeed')	$scope.pwLiveFeed();
				else if ($scope.directive=='loadFeed')	$scope.pwLoadFeed();
			}
			// otherwise, do this
			else {
				// Run Search
				$log.info('pwLiveFeedController.getNext: Scrolling More');
				$scope.pwScrollFeed();				
			}
		};
		
		$scope.pwLiveFeed = function() {
			if (!$scope.args.feed_query)	$scope.args.feed_query = {};
	    	// identify the feed_settings feed_id
			
			$scope.items = {};
			// TODO set Nonce from UI
			pwData.setNonce(78);
        	pwData.pw_live_feed($scope.args).then(
				// Success
				function(response) {	
					$scope.busy = false;
					if (response.status === undefined) {
						console.log('response format is not recognized');
						return;
					}
					if (response.status==200) {
						// Reset Feed Data
						pwData.feed_data[$scope.feed] = {};
						
						// Insert Response in Feed Data
						pwData.feed_data[$scope.feed].feed_outline = response.data.feed_outline;						
						pwData.feed_data[$scope.feed].posts = response.data.post_data;						
						pwData.feed_data[$scope.feed].loaded = response.data.loaded;						
						// Count Length of loaded and feed_outline
						pwData.feed_data[$scope.feed].count_loaded = response.data.post_data.length;						
						pwData.feed_data[$scope.feed].count_feed_outline = response.data.feed_outline.length;
						// Set Feed load Status
						if (pwData.feed_data[$scope.feed].count_loaded == pwData.feed_data[$scope.feed].count_feed_outline) {
							pwData.feed_data[$scope.feed].status = 'all_loaded';													
						} else
							pwData.feed_data[$scope.feed].status = 'loaded';						
						
						$log.info('pwLiveFeedController.pw_live_feed Success',pwData.feed_data[$scope.feed]);
						
						// Clear Items from $scope 
						// $scope.posts = response.data.post_data;
						$scope.items = response.data.post_data;
						
						$scope.busy = false;							
						// TODO Do we need to return something?
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
					$log.error('pwLiveFeedController.pw_live_feed Failure',response);
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
			args.preload = pwData.feed_settings[$scope.feed].preload;
        	pwData.pw_load_feed(args).then(
				// Success
				function(response) {
					$scope.busy = false;
					if (response.status === undefined) {
						console.log('response format is not recognized');
						return;
					}
					if (response.status==200) {
						// Reset Feed Data
						pwData.feed_data[$scope.feed] = {};
						
						// Insert Response in Feed Data
						pwData.feed_data[$scope.feed].feed_outline = response.data.feed_outline;						
						pwData.feed_data[$scope.feed].posts = response.data.post_data;						
						pwData.feed_data[$scope.feed].loaded = response.data.post_data;						
						// Count Length of loaded and feed_outline
						pwData.feed_data[$scope.feed].count_loaded = response.data.post_data.length;						
						pwData.feed_data[$scope.feed].count_feed_outline = response.data.feed_outline.length;
						// Set Feed load Status
						if (pwData.feed_data[$scope.feed].count_loaded == pwData.feed_data[$scope.feed].count_feed_outline) {
							pwData.feed_data[$scope.feed].status = 'all_loaded';													
						} else
							pwData.feed_data[$scope.feed].status = 'loaded';						
						
						$log.info('pwLiveFeedController.pw_load_feed Success',pwData.feed_data[$scope.feed]);
						
						// Clear Items from $scope 
						// $scope.posts = response.data.post_data;
						$scope.items = response.data.post_data;
						
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
					$log.error('pwLiveFeedController.pw_live_feed Failure',response);
					// TODO Show User Friendly Message
				}
			);
		  };
		$scope.pwScrollFeed = function() {
			// Check if all Loaded, then return and do nothing
			if (pwData.feed_data[$scope.feed].status == 'all_loaded') {
				$log.info('pwLiveFeedController.pwScrollFeed ALL LOADED - NO MORE POSTS');				
				return;
			};		
			// TODO do we need to set the loading status? or just use the busy flag?
			pwData.feed_data[$scope.feed].status = 'loading';
			
			
			$log.info('pwLiveFeedController.pwScrollFeed For',$scope.feed,$scope);
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
						$log.info('pwLiveFeedController.pwScrollFeed Success',response.data);
						// Add Results to controller items						
						//$scope.posts = response.data;
						var newItems = response.data;
						for (var i = 0; i < newItems.length; i++) {
							// $log.info('Looping :',i,newItems[i].ID);
							//$scope.items.push(newItems[i]);
							// TODO check why when adding an item here, it affects also $scope.items !
							pwData.feed_data[$scope.feed].posts.push(newItems[i]);
							pwData.feed_data[$scope.feed].loaded.push(newItems[i].ID);							
							// $log.info('$scope.items has',$scope.items.length,' items');							
						  }
						// Update feed data with newly loaded posts
						$log.info('pwLiveFeedController.pwScrollFeed Success feed_data:',pwData.feed_data[$scope.feed]);
						return response.data;
						
					} else {
						// handle error
						console.log('error',response.status,response.message);
						// TODO Show User Friendly Error Message
					}
				},
				// Failure
				function(response) {
					$log.error('pwLiveFeedController.pwScrollFeed Failure',response);
					$scope.busy = false;
					// TODO Show User Friendly Error Message
				}
			);
		  };
		$scope.pwRegisterFeed = function() {
			$log.info('pwLiveFeedController.pwRegisterFeed For',$scope.feed);
			// TODO set Nonce from UI
			pwData.setNonce(78);
			$scope.args.write_cache = $scope.args.feed_query.pw_write_cache; 
			$scope.args.feed_id = $scope.args.feed_query.pw_register_id; 
        	pwData.pw_register_feed($scope.args).then(
				// Success
				function(response) {
					if (response.status === undefined) {
						console.log('response format is not recognized');
						$scope.message = "Error in Feed Registration";
						// TODO Show User Friendly Error Message
						return;
					}
					if (response.status==200) {
						$log.info('pwLiveFeedController.pwRegisterFeed Success',response.data);
						$scope.message = "Feed Registered Successfully";
						return response.data;						
					} else {
						// handle error
						console.log('error',response.status,response.message);
						$scope.message = "Error in Feed Registration"+response.message;
						// TODO Show User Friendly Error Message
					}
				},
				// Failure
				function(response) {
					$log.error('pwLiveFeedController.pwRegisterFeed Failure',response);
					// TODO Show User Friendly Error Message
				}
			);
		  };
    }
);