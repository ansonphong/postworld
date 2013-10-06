'use strict';

pwApp.directive('loadPanel', function($log, pwData) {
    return {
        restrict: 'A',
        replace: true,
        controller: 'pwLoadPanelController',
        // Must use an isolated scope, to allow for using multiple panel directives in the same page
        scope: {
        	feedId : '=',
        }
    };
});

pwApp.controller('pwLoadPanelController',
    function pwLoadPanelController($scope, $location, $log, pwData, $attrs) {    	
    	
		
		
		// TODO Get Default Values from Settings 
		$scope.feed_query = {};
		
		
		// Set Panel Template
		pwData.templates.promise.then(function(value) {
				var FeedID = $scope.feedId;
				var template = 'feed_top';	// TODO get from Constant values
				if (!$scope.feedId) {
					$log.info('no valid Feed ID provided in Feed Settings');
					return;
				}
			   // Get Default View Name
			   if (pwData.feed_settings[FeedID].panels[$attrs.loadPanel])
			   		template = pwData.feed_settings[FeedID].panels[$attrs.loadPanel];
		    	$scope.templateUrl = pwData.pw_get_template('panels','panel',template);
				$log.info('Directive:LoadPanel Controller:pwLoadPanelController Set Initial Panel Template to ',template, $scope.templateUrl);
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
		
		$scope.changeFeedTemplate = function(view) {
			$log.info('Directive:LoadPanel Controller:pwLoadPanelController ChangeTemplate:',view);
	    	var feedTemplateUrl = pwData.pw_get_template('posts','post',view);
    		this.$emit("CHANGE_FEED_TEMPLATE", feedTemplateUrl);		    	
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
