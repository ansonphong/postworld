'use strict';

postworld.directive('feedItem', function() {
    return {
        restrict: 'A',
        replace: true,
        controller: 'pwFeedItemController',
        // template: '<div ng-include src="\'http://localhost/pdev/wp-content/plugins/postworld/templates/posts/post-list.html\'"></div>',
        template: '<div ng-include src="templateUrlf"></div>',        
        scope: {
        	// this identifies the panel id, hence the panel template
        	feedItem	: '=',
        	post : "=",	// Get from ng-repeat
        	feedId	: '=', // Get from Parent Scope of Live Feed
        	// templateUrlf : '=',
        	}
    };
});

postworld.controller('pwFeedItemController',
    function pwFeedItemController($scope, $location, $log, pwData, $attrs) {
    	
		//pwData.templates.promise.then(function(value) {
			var type = 'post';
			if ($scope.post.post_type) type = $scope.post.post_type;
			if (type=="ad") {
				$scope.templateUrlf = pwData.pw_get_template( { subdir:'panels', view: $scope.post.template } );
				// console.log('ad here', $scope.post.template, $scope.templateUrl);				
			}
			else 
				$scope.templateUrlf = pwData.pw_get_template( { subdir:'posts', post_type: type, view: $scope.$parent.feed_item_view_type } );
	    		//$log.debug('pwFeedItemController New Template=',$scope.templateUrl,$scope.$parent.feed_item_view_type, type);    	
		//});    	    	
		
        // Decodes Special characters in URIs
        $scope.decodeURI = function(URI) {
            URI = URI.replace("&amp;","&");
            return decodeURIComponent( URI );
        };

        // TODO set templateURL?		  
		// Template Update Event
		$scope.$on("FEED_TEMPLATE_UPDATE", function(event, feed_item_view_type){
			//pwData.templates.promise.then(function(value) {
				if ($scope.post.post_type!="ad") {
					var type = $scope.post.post_type;
					$scope.templateUrlf = pwData.pw_get_template( { subdir:'posts', post_type: type, view: feed_item_view_type } );					
				} 
			//});
		   // $log.debug('pwFeedItemController: Event Received FEED_TEMPLATE_UPDATE',feedTemplateUrl);
		   // $scope.templateUrl = feed_item_view_type;
		   
		   });		  		      	
    }
);
