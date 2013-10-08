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
    	$scope.templateUrl = $scope.$parent.feed_item_template;
    	$log.info('pwFeedItemController New Template=',$scope.templateUrl);    	
		// TODO set templateURL?		  
		// Template Update Event
		$scope.$on("FEED_TEMPLATE_UPDATE", function(event, feedTemplateUrl){
		   // $log.info('pwFeedItemController: Event Received FEED_TEMPLATE_UPDATE',feedTemplateUrl);
		   $scope.templateUrl = feedTemplateUrl;
		   });		  		      	
    }
);
