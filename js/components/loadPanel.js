'use strict';

pwApp.directive('loadPanel', function($log, pwData) {
    return {
        restrict: 'EA',
        replace: true,
        controller: 'pwLoadPanelController',
        // Must use an isolated scope, to allow for using multiple panel directives in the same page
        scope: {
        	feedId : '=',
        	feedQuery : '=',
        	submit	:'&',
        }
    };
});

pwApp.controller('pwLoadPanelController',
    function pwLoadPanelController($scope, $location, $log, pwData, $attrs) {    	
		
		// Set Panel Template
		pwData.templates.promise.then(function(value) {
				var FeedID = $scope.feedId;
				var template = 'feed_top';	// TODO get from Constant values
				if (!$scope.feedId) {
					$log.info('no valid Feed ID provided in Feed Settings');
					return;
				}
				// Get Default Argument Values
				$scope.feedQuery = pwData.convertFeedSettings($scope.feedId).feed_query;
			   // Get Default View Name
			   if (pwData.feed_settings[FeedID].panels[$attrs.loadPanel])
			   		template = pwData.feed_settings[FeedID].panels[$attrs.loadPanel];			   	
		    	$scope.templateUrl = pwData.pw_get_template('panels','panel',template);
				$log.info('pwLoadPanelController() Set Initial Panel Template',FeedID, template, $scope.templateUrl,pwData.feed_settings);
		});				

    	// TODO check best location for that code, should we create a panel child?
		$scope.toggleOrder = function() {
			if ($scope.feedQuery.order == 'ASC') {
				$scope.feedQuery.order = 'DESC';
			} else $scope.feedQuery.order = 'ASC';
		};		
		$scope.$watch('feedQuery.order', function(value) {
 			if (value == 'DESC') {
				$scope.clsOrder ='glyphicon-arrow-down'; 				
 			} else  {
				$scope.clsOrder ='glyphicon-arrow-up';		
 			}
		}); 
		
		// Send request event to Live-Panel Directive [parent] to change the Feed Template		
		$scope.changeFeedTemplate = function(view) {
			$log.info('pwLoadPanelController.changeFeedTemplate ChangeTemplate',view);
    		this.$emit("CHANGE_FEED_TEMPLATE", view);		    	
		};		
    	
    }
);

pwApp.directive('registerPanel', function($log, pwData) {
    return {
        restrict: 'A',
        replace: true,
        controller: 'pwRegisterPanelController',
    };
});

pwApp.controller('pwRegisterPanelController',
    function pwRegisterPanelController($scope, $location, $log, pwData, $attrs) {
    	$scope.args= {};
    	$scope.args.write_cache = false;
    	$scope.args.feed_id = '';
		$scope.registerFeed = function() {
			$scope.args.feed_query = $scope.$parent.feedQuery;
			$log.info('pwRegisterFeedController.pwRegisterFeed For',$scope.args);
			// TODO set Nonce from UI
			pwData.setNonce(78);
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
						$log.info('pwRegisterFeedController.pwRegisterFeed Success',response.data);
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
					$log.error('pwRegisterFeedController.pwRegisterFeed Failure',response);
					// TODO Show User Friendly Error Message
				}
			);
		  };
		
    }
);