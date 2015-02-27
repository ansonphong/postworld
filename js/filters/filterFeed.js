'use strict';

postworld.directive('filterFeed', function($log, pwData) {
    return {
        restrict: 'EA',
        replace: true,
        controller: 'pwFilterFeedController',
        template: '<div ng-include="templateUrl" class="filter-feed"></div>',
        // Must use an isolated scope, to allow for using multiple panel directives in the same page
        scope: {
        	feedId : '=',
        	//submit	:'&',
        }
    };
});

postworld.controller('pwFilterFeedController',
    function pwFilterFeedController($scope, $rootScope, $location, $log, pwData, $attrs, $window, pwPostOptions) {    	
		var firstTime = true;
		// Set Panel Template

		$scope.feedId = $attrs.feedId;
		var FeedID = $scope.feedId;
		var template = 'feed_top';	// TODO get from Constant values
		if (!$scope.feedId) {
			$log.debug('no valid Feed ID provided in Feed Settings');
			return;
		}

		$scope.feed = pwData.feeds[$scope.feedId];

		// DEFAULTS
		$scope.feed.query.author_name = "";

		// Taxonomy Object Model
		$scope.taxInput = pwPostOptions.taxInputModel();

		// Get Default View Name
		/*
		if (pwData.feeds[FeedID].panels[$attrs.filterFeed])
				template = pwData.feeds[FeedID].panels[$attrs.filterFeed];			   	
		*/
		$scope.templateUrl = pwData.pw_get_template( { subdir: 'panels', view: $attrs.filterFeed } );
		$log.debug( 'pwFilterFeedController : templateUrl : ', $scope.templateUrl );
		// $log.debug('pwFilterFeedController() Set Initial Panel Template',FeedID, template, $scope.templateUrl,pwData.feeds);

		// UPDATE AUTHOR NAME FROM AUTOCOMPLETE
		// Interacts with userAutocomplete() controller
		// Catches the recent value of the auto-complete
		$scope.$on('updateUsername', function( event, data ) { 
	        $scope.feed.query.author_name = data;
	    });

    	// TODO : check best location for this code, should we create a panel child?
		$scope.toggleOrder = function() {
			if ($scope.feed.query.order == 'ASC') {
				$scope.feed.query.order = 'DESC';
			} else $scope.feed.query.order = 'ASC';
		};	

		// MONTH : Convert Month value into Integer (for model to work properly)
		$scope.$watch('feed.query.monthnum', function(value) {
			if( typeof $scope.feed.query.monthnum !== 'undefined' )
				$scope.feed.query.monthnum = parseInt($scope.feed.query.monthnum);
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
			$scope.feed.query.tax_query = [];

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

					// Push to feed.query.tax_input
					$scope.feed.query.tax_query.push( termQueryObject );

				}
		    });
		    $scope.submit();

		}, 1); 

		$scope.submit = function(){
			$log.debug( "filterFeed.$broadcast : feed.reload : ", $scope.feedId );
			$rootScope.$broadcast( 'feed.reload', $scope.feedId );
		}

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
    function ($scope, $location, $log, pwData, $attrs) {
    	$scope.args= {};
    	$scope.args.write_cache = false;
    	$scope.args.feed_id = '';
		$scope.registerFeed = function() {
			$scope.args.feed_query = $scope.$parent.feed.query;
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
        controller: 'pwLoadPanelCtrl',
        //transclude: true,
        template: '<div ng-include="templateUrl" class="load-panel"></div>',
        scope:{
        	// Must use an isolated scope, to allow for using multiple panel directives in the same page
        	panelMeta:"=",
        	//panelMetaJson:"@",
        },
        link: function($scope, element, attributes){
        	$scope.panel_id = attributes.loadPanel;
        	$scope.panel_grp = 'panels';
        	if (attributes.panelGroup) $scope.panel_grp = attributes.panelGroup; // can be ignored in case of panels, since it is the default
        	if (attributes.postType) $scope.post_type = attributes.postType; // only needed in case of posts
        }
    };
});

postworld.controller('pwLoadPanelCtrl',
    function( $scope, $timeout, $log, pwData ) {

		// Wait for pwData to initialize
		$timeout( function(){
			// Load Template URL
			$scope.templateUrl = pwData.pw_get_template( { subdir: 'panels', view: $scope.panel_id } );
			//$log.debug('Load Panel :' + $scope.panel_id, $scope.templateUrl );
		},1 );


	}
);
