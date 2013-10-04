'use strict';

pwApp.controller('pwLoadPanelController',
    function pwLoadPanelController($scope, $location, $log, pwData) {
    	// On Change trigger LiveFeed Controller to search again
		var order = true; // true is ascending and false is descending
		$scope.clsOrder = 'glyphicon-arrow-up';
		$scope.feed_query.order = 'ASC';
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
		
		$scope.getTemplate = function(view) {
	    	// should this code be in the feedItem directive?
	    	$scope.templateView = view;
	    	// TODO use get template function
	    	$scope.templateUrl = jsVars.pluginurl+'/postworld/templates/posts/post-'+$scope.templateView+'.html';
		};
    	
    }
);

