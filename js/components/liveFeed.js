'use strict';

pwApp.directive('liveFeed', function() {
    return {
        restrict: 'A',
        templateUrl: jsVars.pluginurl+'/postworld/templates/directives/liveFeed.html',
        replace: true,
        controller: 'pwLiveFeedController',
    };
});

pwApp.controller('pwLiveFeedController',
    function pwLiveFeedController($scope, $location, $log, $attrs, $timeout, pwData) {
    	// Initialize
    	$scope.busy = false; 				// Avoids running simultaneous service calls to get posts. True: Service is Running to get Posts, False: Service is Idle    	
    	$scope.firstRun = true; 			// True until pwLiveFeed runs once. False for al subsequent pwScrollFeed
		$scope.args = {};
		$scope.feed_query = {};
    	$scope.items = [];					// List of Post Items displayed in Scroller
    	$scope.args.feed_id = $attrs.liveFeed; // This Scope variable will propagate to all directives inside Live Feed
    	// Get Data from Feed_Settings
			   
    	// TODO move getting templates to app startup
    	pwData.pw_get_templates(null).then(function(value) {
		    // TODO should we create success/failure responses here?
		    // resolve pwData.templates
		    pwData.templates.resolve(value.data);
		    pwData.templatesFinal = value.data;
		    console.log('Directive:LiveFeed Controller: pwLiveFeedController: templates=',pwData.templatesFinal);
		  });		      	    	
    	
		//Handle Emitted Arguments from LoadPanel Children
		$scope.$on("UPDATE_PARENT", function(event, message){
		   $log.info('Controller: pwLiveFeedController: ON:UPDATE_PARENT - EMIT Received: ',message);
		   $scope.args.feed_query = message;
		   $log.info('Controller: pwLiveFeedController: ON:UPDATE_PARENT - EMIT Received: args=',$scope.args.feed_query);
		   });
		
		$scope.$on("EXEC_PARENT", function(event, message){
		   $log.info('Controller: pwLiveFeedController: ON:EXEC_PARENT - EMIT Received: args=',$scope.args.feed_query);
		   $scope.pwLiveFeed();
		   });
		   
		$scope.$on("CHANGE_FEED_TEMPLATE", function(event, feedTemplateUrl){
		   $log.info('Controller: pwLiveFeedController: ON:CHANGE_FEED_TEMPLATE - EMIT Received: ',feedTemplateUrl);
		   // Broadcast to all children
			$scope.$broadcast("FEED_TEMPLATE_UPDATE", feedTemplateUrl);		   		   
		   });
		   
   		$scope.getNext = function() {
			// If already getting results, do not run again.
			if ($scope.busy) {
				$log.info('Controller: pwLiveFeedController: Method:getNext: Scrolling trigger but the controller is already busy getting data');
				return;
				}
			$scope.busy = true;
			// if running for the first time, do this
			if ($scope.firstRun) {
				$log.info('Controller: pwLiveFeedController: Method:getNext: Running for the first time');
				$scope.firstRun = false;
				$scope.pwLiveFeed();
			}
			// otherwise, do this
			else {
				// Run Search
				$log.info('Controller: pwLiveFeedController: Method:getNext: Running for next time');
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
						pwData.feed_data[$attrs.liveFeed] = {};
						// Insert Response in Feed Data
						pwData.feed_data[$attrs.liveFeed].feed_outline = response.data.feed_outline;						
						pwData.feed_data[$attrs.liveFeed].posts = response.data.post_data;						
						pwData.feed_data[$attrs.liveFeed].loaded = response.data.loaded;						
						// Count Length of loaded and feed_outline
						pwData.feed_data[$attrs.liveFeed].count_loaded = response.data.post_data.length;						
						pwData.feed_data[$attrs.liveFeed].count_feed_outline = response.data.feed_outline.length;
						// Set Feed load Status
						if (pwData.feed_data[$attrs.liveFeed].count_loaded == pwData.feed_data[$attrs.liveFeed].count_feed_outline) {
							pwData.feed_data[$attrs.liveFeed].status = 'all_loaded';													
						} else
							pwData.feed_data[$attrs.liveFeed].status = 'loaded';						
						
						$log.info('Controller: pwLiveFeedController Method:pw_live_feed Success pwData.feed_data:',pwData.feed_data[$attrs.liveFeed]);
						
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
					$log.error('Controller: pwLiveFeedController Method:pw_live_feed Failure with response:',response);
					// TODO Show User Friendly Message
				}
			);
		  };
		$scope.pwScrollFeed = function() {
			$log.info('Controller: pwLiveFeedController Method:pwScrollFeed invoked');
			// Check if all Loaded, then return and do nothing
			if (pwData.feed_data[$attrs.liveFeed].status == 'all_loaded') {
				$log.info('Controller: pwLiveFeedController Method:pwScrollFeed ALL LOADED - NO MORE POSTS');				
				return;
			};		
			// TODO do we need to set the loading status? or just use the busy flag?
			pwData.feed_data[$attrs.liveFeed].status = 'loading';
			
			
			$log.info('Controller: pwLiveFeedController: Method:pwScrollFeed: feed_id=',$scope.args.feed_id);
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
						$log.info('Controller: pwLiveFeedController Method:pwScrollFeed Success with data:',response.data);
						// Add Results to controller items						
						//$scope.posts = response.data;
						var newItems = response.data;
						for (var i = 0; i < newItems.length; i++) {
							// $log.info('Looping :',i,newItems[i].ID);
							//$scope.items.push(newItems[i]);
							// TODO check why when adding an item here, it affects also $scope.items !
							pwData.feed_data[$scope.args.feed_id].posts.push(newItems[i]);
							pwData.feed_data[$scope.args.feed_id].loaded.push(newItems[i].ID);							
							// $log.info('$scope.items has',$scope.items.length,' items');							
						  }
						// Update feed data with newly loaded posts
						$log.info('Controller: pwLiveFeedController Method:pwScrollFeed Success feed_data:',pwData.feed_data[$scope.args.feed_id]);
						return response.data;
						
					} else {
						// handle error
						console.log('error',response.status,response.message);
						// TODO Show User Friendly Error Message
					}
				},
				// Failure
				function(response) {
					$log.error('Controller: pwLiveFeedController Method:pwScrollFeed Failure with response:',response);
					$scope.busy = false;
					// TODO Show User Friendly Error Message
				}
			);
		  };
    }
);