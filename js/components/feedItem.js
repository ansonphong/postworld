'use strict';

pwApp.directive('feedItem', function() {
    return {
        restrict: 'A',
        replace: true,  
        controller: 'pwFeedItemController',
        scope: {
        	// this identifies the panel id, hence the panel template
        	feedItem	: '=',
        	view	: '@',
        	post : "=",
        }        
    };
});

pwApp.controller('pwFeedItemController',
    function pwFeedItemController($scope, $location, $log, pwData, $attrs) {
    	// Load Template URL
		pwData.templates.promise.then(function(value) {
	    	$scope.templateUrl = pwData.pw_get_template('posts','post',$attrs.view);
		  });		      	
    }
);
