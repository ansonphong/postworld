'use strict';

pwApp.directive('loadPanel', function($log, pwData) {
    return {
        restrict: 'A',
        replace: true,
        controller: 'pwLoadPanelController',
        // Must use an isolated scope, to allow for using multiple panel directives in the same page
        scope: {
        }
    };
});

pwApp.controller('pwLoadPanelController',
    function pwLoadPanelController($scope, $location, $log, pwData, $attrs) {
		$scope.feed_query = {};
    	// Load Template URL
		pwData.templates.promise.then(function(value) {
	    	$scope.templateUrl = pwData.pw_get_template('panels','panel',$attrs.loadPanel);
		  });

		$scope.UpdateArguments= function() {
			$log.info('Directive:LoadPanel Controller:pwLoadPanelController UpdateArguments',$scope);
			//Emit Arguments to Parent [liveFeed Controller]
    		this.$emit("UPDATE_PARENT", $scope.feed_query);
    		this.$emit("EXEC_PARENT", $scope.feed_query);
		};
		  
    	//if (!$scope.feed_query) $scope.feed_query = {};
    	// On Change trigger LiveFeed Controller to search again
		
    	// should this code be in the feedItem directive?
		var order = true; // true is ascending and false is descending
		$scope.clsOrder = 'glyphicon-arrow-up';
		$scope.feed_query.order = 'ASC';
		
		$scope.changeTemplate = function(template) {
		};		

		$scope.toggleOrder = function() {
			order = !order;
			if (order==true) {
				$scope.clsOrder ='glyphicon-arrow-up';
				$scope.feed_query.order = 'ASC';
			}
			else  {
				$scope.clsOrder ='glyphicon-arrow-down';
				$scope.feed_query.order = 'DESC';
			}
		};		
    	
    }
);
