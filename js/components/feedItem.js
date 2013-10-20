'use strict';

postworld.directive('feedItem', function() {
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

postworld.controller('pwFeedItemController',
    function pwFeedItemController($scope, $location, $log, pwData, $attrs) {
    	$scope.templateUrl = $scope.$parent.feed_item_template;
    	$log.info('pwFeedItemController New Template=',$scope.templateUrl);    	
		
        // Decodes Special characters in URIs
        $scope.decodeURI = function(URI) {
            URI = URI.replace("&amp;","&");
            return decodeURIComponent( URI );
         }

        // TODO set templateURL?		  
		// Template Update Event
		$scope.$on("FEED_TEMPLATE_UPDATE", function(event, feedTemplateUrl){
		   // $log.info('pwFeedItemController: Event Received FEED_TEMPLATE_UPDATE',feedTemplateUrl);
		   $scope.templateUrl = feedTemplateUrl;
		   });		  		      	
    }
);
