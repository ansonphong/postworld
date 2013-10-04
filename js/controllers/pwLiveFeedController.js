/**
 * Created by Michel on 9/22/13.
 */
'use strict';

pwApp.controller('pwLiveFeedController',
    function pwLiveFeedController($scope, $location, $log, $attrs, pwData) {
    	//$scope.args.year = '2007';
    	//$scope.args.monthnum= '1';
    	
    	// should this code be in the feedItem directive?
    	$scope.templateView = 'list';
    	// TODO use get template function
    	$scope.templateUrl = jsVars.pluginurl+'/postworld/templates/posts/post-'+$scope.templateView+'.html';
    	
    	
    	
		$scope.args = {};
		$scope.feed_query = {};
    	$scope.items = [];
    	$scope.args.feed_id = $attrs.liveFeed;
    	
    	// busy means that the service is now running to get data for this controller
    	$scope.busy = false;
    	
    	// firstRun is true until we run pw_live_feed once, then it turns to false;
    	$scope.firstRun = true;
    	
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
			$log.info('Controller: pwLiveFeedController Method:pwLiveFeed invoked');	    	
			// easy shortcut, for readability in the directive html
			$scope.args.feed_query = $scope.feed_query;
	    	// identify the feed_settings feed_id
			
			$log.info('Controller: pwLiveFeedController: Method:pwLiveFeed: feed_id=',$scope.args.feed_id);
			$log.info('Controller: pwLiveFeedController: Method:pwLiveFeed: Scope=',$scope);
			//return;
			// TODO set Nonce from UI
			pwData.setNonce(78);
        	pwData.pw_live_feed($scope.args).then(
				// Success
				function(response) {	
					$log.info('Controller: pwLiveFeedController Method:pw_live_feed ServiceReturned');
					// TODO should we set busy to false when error is returned?
					$scope.busy = false;
					if (response.status === undefined) {
						console.log('response format is not recognized');
						return;
					}
					if (response.status==200) {
						$log.info('Controller: pwLiveFeedController Method:pw_live_feed Success with data:',response.data);
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
					$log.error('Controller: pwLiveFeedController Method:pw_live_feed Failure with response:',response);
					// console.log('failure',response);
					// TODO how to handle error?
				}
			);
			// $scope.args = {};
			// $scope.feed_query = {};
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
					$log.info('Controller: pwLiveFeedController Method:pwScrollFeed ServiceReturned');
					// TODO should we set busy to false when error is returned?
					$scope.busy = false;
					if (response.status === undefined) {
						console.log('response format is not recognized');
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
					$log.error('Controller: pwLiveFeedController Method:pwScrollFeed Failure with response:',response);
					// console.log('failure',response);
					// TODO how to handle error?
				}
			);
			// TODO we need to find a way to remove this workaround of resetting the args
			// $scope.args = {};
			// $scope.feed_query = {};
		  };
    }
);

