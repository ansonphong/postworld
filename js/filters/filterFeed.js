'use strict';

postworld.directive('filterFeed', function($log, pwData) {
    return {
        restrict: 'EA',
        replace: true,
        controller: 'pwFilterFeedController',
        // Must use an isolated scope, to allow for using multiple panel directives in the same page
        scope: {
        	feedId : '=',
        	feedQuery : '=',
        	submit	:'&',
        }
    };
});

postworld.controller('pwFilterFeedController',
    function pwFilterFeedController($scope, $location, $log, pwData, $attrs, $window, pwPostOptions) {    	
		var firstTime = true;
		// Set Panel Template
		pwData.templates.promise.then(function(value) {
				var FeedID = $scope.feedId;
				var template = 'feed_top';	// TODO get from Constant values
				if (!$scope.feedId) {
					$log.debug('no valid Feed ID provided in Feed Settings');
					return;
				}
				// Get Default Argument Values
				$scope.feedQuery = pwData.convertFeedSettings($scope.feedId).feed_query;
			   
			   	// DEFAULTS
				$scope.feedQuery.author_name = "";

				// Taxonomy Object Model
				$scope.taxInput = pwPostOptions.pwGetTaxInputModel();

			   // Get Default View Name
			   if (pwData.feed_settings[FeedID].panels[$attrs.filterFeed])
			   		template = pwData.feed_settings[FeedID].panels[$attrs.filterFeed];			   	
		    	$scope.templateUrl = pwData.pw_get_template('panels','panel',template);
				// $log.debug('pwFilterFeedController() Set Initial Panel Template',FeedID, template, $scope.templateUrl,pwData.feed_settings);
		});		

		// UPDATE AUTHOR NAME FROM AUTOCOMPLETE
		// Interacts with userAutocomplete() controller
		// Catches the recent value of the auto-complete
		$scope.$on('updateUsername', function( event, data ) { 
	        $scope.feedQuery.author_name = data;
	    });

		$scope.submit1 = function() {
			// debugger;
		};

    	// TODO : check best location for that code, should we create a panel child?
		$scope.toggleOrder = function() {
			if ($scope.feedQuery.order == 'ASC') {
				$scope.feedQuery.order = 'DESC';
			} else $scope.feedQuery.order = 'ASC';
		};	

		// MONTH : Convert Month value into Integer (for model to work properly)
		$scope.$watch('feedQuery.monthnum', function(value) {
			if( typeof $scope.feedQuery.monthnum !== 'undefined' )
				$scope.feedQuery.monthnum = parseInt($scope.feedQuery.monthnum);
		});

		$scope.$watch('feedQuery.order_by', function(value) {
		//	$log.debug('pwFilterFeedController.changeFeedTemplate order by changed',$scope.feedQuery.order_by);
		}); 

		// SET ORDER ICON
		$scope.$watch('feedQuery.order', function(value) {
			$log.debug('pwFilterFeedController.changeFeedTemplate order changed',$scope.feedQuery.order);
 			if (value == 'DESC') {
				$scope.clsOrder = $window.pwSiteGlobals.icons.order.descending; 				
 			} else  {
				$scope.clsOrder = $window.pwSiteGlobals.icons.order.ascending;	
 			}
		});

		///// TRANSLATE TAXONOMY MODEL INPUT FOR WP_QUERY /////
		// Format the Model Input into format accessible to WP_Query
		$scope.$watch('taxInput', function(value) {
			if (firstTime) {
				firstTime = false;
				return;
			}
			$log.info('taxInput',value);
			// Reset tax_query object
			$scope.feedQuery.tax_query = [];

			// For each taxonomy
			angular.forEach( $scope.taxInput, function( terms, taxonomy ){
				//$log.debug(terms);
				// If terms isn't empty
				if( terms.length > 0 && terms[0] != null ){
					// For each taxonomy with terms specified
					// produce a tax_query object for it
					// for : http://codex.wordpress.org/Class_Reference/WP_Query

					// Remove Null Terms
					angular.forEach( terms, function( term ){
						if( term == null ){
							var index = terms.indexOf(term);
							if (index > -1) {
							    terms.splice( index, 1);
							}
						}
					});

					// Define Taxonomy Term Query Object
					var termQueryObject = { 
						"taxonomy": taxonomy,
						"field": "slug",
						"terms":terms,
						//"operator":"AND"
					};

					// Push to feedQuery.tax_input
					$scope.feedQuery.tax_query.push( termQueryObject );

				}
		    });

		    $scope.submit();
		}, 1); 
		

		// Send request event to Live-Panel Directive [parent] to change the Feed Template		
		$scope.changeFeedTemplate = function(view) {
			//$log.debug('pwFilterFeedController.changeFeedTemplate ChangeTemplate',view);
    		this.$emit("CHANGE_FEED_TEMPLATE", view);		    	
		};	

    	
    }
);

postworld.directive('registerFeed', function($log, pwData) {
    return {
        restrict: 'A',
        replace: true,
        controller: 'pwRegisterFeedController',
    };
});

postworld.controller('pwRegisterFeedController',
    function pwRegisterPanelController($scope, $location, $log, pwData, $attrs) {
    	$scope.args= {};
    	$scope.args.write_cache = false;
    	$scope.args.feed_id = '';
		$scope.registerFeed = function() {
			$scope.args.feed_query = $scope.$parent.feedQuery;
			$log.debug('pwRegisterFeedController.pwRegisterFeed For',$scope.args);
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
						$log.debug('pwRegisterFeedController.pwRegisterFeed Success',response.data);
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



///// LOAD PANEL /////
postworld.directive('loadPanel', function() {
    return {
        restrict: 'EA',
        //replace: true,
        controller: 'pwLoadPanelController',
        //transclude: true,
        scope:{
        	// Must use an isolated scope, to allow for using multiple panel directives in the same page
        },
        link: function($scope, element, attributes){
        	$scope.panel_id = attributes.loadPanel;
        	$scope.panel_grp = 'panels'; // posts, comments, posts
        	$scope.post_type = '';  // post type only
        	if (attributes.panelGroup) $scope.panel_grp = attributes.panelGroup; // can be ignored in case of panels, since it is the default
        	if (attributes.postType) $scope.post_type = attributes.postType; // only needed in case of posts
        }
    };
});

postworld.controller('pwLoadPanelController',
    function pwLoadPanelController($scope, $timeout, $log, pwData ) {
    	// TEMP LOADING TEMPLATE
    	//$scope.templateUrl = jsVars.pluginurl+'/postworld/templates/panels/ajaxloader.html';
    	$scope.$on('pwTemplatesLoaded', function(event, data) {
	        //$scope.panel = {};	        	
	        $scope.templateUrl = pwData.pw_get_template($scope.panel_grp,$scope.post_type, $scope.panel_id);
		    $log.debug('setting loadpanel url',$scope.panel_grp,$scope.post_type, $scope.panel_id,$scope.templateUrl);
	    });

	}
);


