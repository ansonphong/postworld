'use strict';

pwApp.directive('feedItem', function() {
    return {
        restrict: 'A',
        replace: true,
        controller: 'pwFeedItemController',
        scope: {
        	// this identifies the panel id, hence the panel template
        	feedItem	: '=',
        	post : "=",	// Get from ng-repeat
        	feedId	: '=', // Get from Parent Scope of Live Feed
        	}
    };
});

pwApp.controller('pwFeedItemController',
    function pwFeedItemController($scope, $location, $log, pwData, $attrs) {
    	
		// Get Initial Template from Live Feed Directive on Startup
		pwData.templates.promise.then(function(value) {
				var FeedID = $scope.feedId;
				var view = 'list';	// TODO get from Constant values
				if (!$scope.feedId) {
					$log.info('no valid Feed ID provided in Feed Settings');
					return;
				}
			   // Get Default View Name - TODO if the default view changes, then we need to get it from live feed directive instead
			   if (pwData.feed_settings[FeedID].view.current)
			   		view = pwData.feed_settings[FeedID].view.current;
		    	$scope.templateUrl = pwData.pw_get_template('posts','post',view);
				// $log.info('Directive:FeedItem Controller:pwFeedItemController Set Initial Feed Template to ',view, $scope.templateUrl);
		});
		  
		// Template Update Event
		$scope.$on("FEED_TEMPLATE_UPDATE", function(event, feedTemplateUrl){
		   $log.info('Directive:feedItem Controller: pwFeedItemController: ON:FEED_TEMPLATE_UPDATE - EMIT Received: ',feedTemplateUrl);
		   $scope.templateUrl = feedTemplateUrl;
		   });		  		      	
    }
);
