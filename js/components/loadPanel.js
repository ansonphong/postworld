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
				$log.info('Directive:LoadPanel Controller:pwLoadPanelController Set Initial Panel Template to ',template, $scope.templateUrl);
		});
				

    	// the below is not needed since we are using args.feed_query from parent directive live-feed into load-panel 
		/*
		$scope.UpdateArguments1= function() {
			$log.info('Directive:LoadPanel Controller:pwLoadPanelController UpdateArguments',$scope);
			//Emit Arguments to Parent [liveFeed Controller]
    		this.$emit("UPDATE_PARENT", $scope.feed_query);
    		this.$emit("EXEC_PARENT", $scope.feed_query);
		};
		$scope.submitForm= function() {
			$log.info('Directive:LoadPanel Controller:pwLoadPanelController Submit Form',$scope.feedQuery);
    		this.$emit("SUBMIT_FORM_PARENT");
		};
		*/

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
			$log.info('Directive:LoadPanel Controller:pwLoadPanelController ChangeTemplate:',view);
	    	var feedTemplateUrl = pwData.pw_get_template('posts','post',view);
    		this.$emit("CHANGE_FEED_TEMPLATE", feedTemplateUrl);		    	
		};		
    	
    }
);
