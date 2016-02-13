'use strict';

postworld.directive('editFeed',
	[ '$rootScope', '$log', '$pwData', '$_', '$pw', '$timeout', '$pwPosts',
	function( $rootScope, $log, $pwData, $_, $pw, $timeout, $pwPosts ) {
	return {
		restrict: 'EA',
		//require: 'ngModel',
		scope: {
			feedId : '@editFeed',
			feedKey:'@',
			feedReload:'@',
			ngModel:'=',
		},
		link: function( $scope, element, attrs ){

			var firstRun = true;

			///// WATCH : MODEL VALUE /////
			$scope.$watch( 'ngModel', function( val ){

				// If the feed doesn't exist, stop here
				if( !feedExists() )
					return false;

				// On first run
				if( firstRun ){
					// Set the model value to coorospond with the feed value
					updateModelValue();
					// Set first run false
					firstRun = false;
				}

				// Update the new value in the feed service
				updateFeedValue( val );

				// Refresh the feed
				if( getFeedReload() == 'change' )
					refreshFeed( val );

			} );

			///// WATCH : FEED RELOAD /////
			$scope.$watch( 'feedReload', function( val, oldVal ){
				switch( getFeedReload() ){
					/// ENTER ///
					case 'enter':
						element.bind("keydown keypress", function(event) {
						  	if(event.which === 13) {
								reloadFeed();
								event.preventDefault();
							}
						});
						break;
					/// CLICK ///
					case 'click':
						element.bind("mousedown", function(event) {
							$timeout(function(){
								reloadFeed()
							},0);
							//event.preventDefault();
						});
						break;
				}
			});

			var feedExists = function(){
				return $_.objExists( $pwData.feeds, $scope.feedId );
			}

			var getFeedReload = function(){
				// Return false if not defined
				if( _.isUndefined( $scope.feedReload ) ||
					_.isNull( $scope.feedReload ) )
					return 'change';
				else
					return $scope.feedReload;
			}

			var keyParts = function(){
				// If the feedKey is not a string, return here
				if( !_.isString( $scope.feedKey ) )
					return false;
				// Split the feed key on the dot
				return $scope.feedKey.split('.');
			}

			var updateModelValue = function(){
				// If no model value is defined, and the feed key is defined
				if( _.isUndefined( $scope.ngModel ) && !_.isUndefined( $scope.feedKey ) ){

					// Get the value of the feed key from the feed
					var value = $_.get( $pwData.feeds[$scope.feedId], $scope.feedKey );
					// If a value was found
					if( value )
						// Set the value in the model
						$scope.ngModel = value;
				}
			}

			var getPath = function(){
				return $scope.feedId + '.' + $scope.feedKey;
			}

			var prepareTaxQuery = function( terms ){
				// Prepares the taxonomy query from an array into a tax_query Object
				$log.debug( 'editFeed : prepareTaxQuery : INPUT', terms );

				if( _.isUndefined(terms) || _.isNull(terms) )
					return false;

				// Convert string values into arrays
				if( _.isString(terms) )
					terms = [terms];

				// Determine the tax query field
				var field = ( $_.isNumeric( terms[0] ) ) ? 'term_id' : 'slug';

				// Determine the taxonomy
				var taxonomy = keyParts()[2];

				var taxQueryUnit = {
					taxonomy: taxonomy,
					field: field,
					terms: terms	
				};

				$log.debug( 'editFeed : prepareTaxQuery : OUTPUT', taxQueryUnit );

				return taxQueryUnit;
			}

			var addTaxQueryUnit = function( taxQueryUnit, taxQuery ){

				/// ADDING TO NEW EMPTY QUERIES ///
				// If the unit is not empty, and the taxQuery is empty
				if( _.isEmpty( taxQuery ) ){
					// Set the tax query unit into it
					taxQuery[0] = taxQueryUnit;
					// Log
					$log.debug( 'editFeed : addTaxQueryUnit : ADD UNIT TO EMPTY QUERY : ', taxQueryUnit );
					// Return here
					return taxQuery;
				}

				/// REPLACING TAX QUERIES ///
				// Setup variable to determine if the tax query has been inserted
				var insertedTaxQuery = false;
				// Iterate through each of the existing taxQuery objects
				for( var i = 0; i < taxQuery.length; i++ ){
					// If the taxonomy is matching
					if( taxQueryUnit.taxonomy == taxQuery[i].taxonomy ){
						// Add the new query
						taxQuery[i] = taxQueryUnit;
						// And mark as having inserted
						insertedTaxQuery = true;
						// Log
						$log.debug( 'editFeed : addTaxQueryUnit : REPLACE QUERY : ', taxQueryUnit );
					}
				}
				if( insertedTaxQuery )
					return taxQuery;

				/// ADDING NEW TAX QUERIES ///
				// If no insertion has been made and if there are terms defined
				taxQuery.push(taxQueryUnit);
				// Log
				$log.debug( 'editFeed : addTaxQueryUnit : ADD NEW QUERY : ', taxQueryUnit );
				return taxQuery;

			}

			var removeTaxQueryUnit = function( removeTax, taxQuery ){
				// Log
				$log.debug( "editFeed : removeTaxQueryUnit : INPUT : " + removeTax, taxQuery );
				// Create new tax query array
				var newTaxQuery = [];
				// Iterate through each tax query object
				for( var i = 0; i < taxQuery.length; i++ ){
					// If it's not set to be removed
					if( taxQuery[i].taxonomy != removeTax )
						// Add it to the new array
						newTaxQuery.push( taxQuery[i] );
				}
				// Replace the taxQuery with the new one
				taxQuery = newTaxQuery;
				// Log
				$log.debug( "editFeed : removeTaxQueryUnit : OUTPUT : ", taxQuery );
				return taxQuery;
			}


			var setFeedTaxQuery = function( taxQueryUnit ){
				// Sets the tax query unit into the feed tax query

				$log.debug( "editFeed : setFeedTaxQuery : INPUT : ", taxQueryUnit );

				///// GET EXISTING TAX QUERY /////
				// Get the already existing tax query
				var taxQuery = $_.get( getFeed(), 'query.tax_query' );
				// If it doesn't exist, or it's empty, or it's not an array
				if( taxQuery == false || _.isEmpty(taxQuery) || !_.isArray( taxQuery ) )
					// Create it as an empty array
					taxQuery = [];
				
				///// SET : MODE /////
				// Establish the mode : ADD/REPLACE, REMOVE
				var mode;
				// If it's an empty or null value or not an object
				if( !_.isObject( taxQueryUnit ) )
					mode = 'remove';
				else
					// If there are no terms, set to remove
					mode = ( taxQueryUnit.terms.length == 0 ) ? 'remove' : 'add';
					
				///// SWITCH : MODE /////
				switch( mode ){
					case 'add':
						// Add unit to the tax query
						taxQuery = addTaxQueryUnit( taxQueryUnit, taxQuery );
						break;
					case 'remove':
						// Remove item from the tax query
						var removeTax = keyParts()[2];
						taxQuery = removeTaxQueryUnit( removeTax, taxQuery );
						break;
				}

				// Set the feed data
				$pwData.feeds[$scope.feedId].query.tax_query = taxQuery;

				$log.debug( "editFeed : setFeedTaxQuery : OUTPUT : ", $pwData.feeds[$scope.feedId].query.tax_query );

			}

			var getFeed = function(){
				// Gets the associated feed object
				return $_.get( $pwData.feeds, $scope.feedId );
			}

			var setFeedValue = function( val ){
				// Get the feed
				var feed = getFeed();
				// Log
				$log.debug( 'updateFeedValue : ' + $scope.feedId + ' : FEED : ', feed );
				// Set the feed data
				if( !_.isUndefined( $scope.feedKey ) )
					$pwData.feeds[$scope.feedId] = $_.set( feed, $scope.feedKey, val );

			}

			var updateFeedValue = function(val){
				///// HANDLE TAXONOMY QUERIES /////
				// If the primary key is query, and the secondary key is taxonomy
				// Handle differently
				if( keyParts()[0] == 'query' && keyParts()[1] == 'taxonomy' ){
					$log.debug( "editFeed : updateFeedValue : TAXONOMY QUERY : ", val );
					var taxQueryUnit = prepareTaxQuery( val );
					setFeedTaxQuery( taxQueryUnit );
					return;
				}
				///// HANDLE OTHER VALUES /////
				setFeedValue( val );
			}

			var refreshFeed = function(val){
				///// HANDLE FEED KEYS /////
				// Switch on primary key
				switch( keyParts()[0] ){
					case 'query':
						reloadFeed();
						break;
					case 'view':
						updateView(val);
						break;
				}
			}

			var reloadFeed = function(){
				// Reload the Feed
				$log.debug( "editFeed.$broadcast : feed.reload : ", $scope.feedId );
				$rootScope.$broadcast( 'feed.reload', $scope.feedId );
			}

			var updateView = function( val ){
				$pwPosts.setFeedView( $scope.feedId, val );
			}

		},
	};
}]);




postworld.controller('pwFilterFeedController',
	function pwFilterFeedController( $scope, $rootScope, $location, $log, $pwData, $attrs, $window, $pwPostOptions ) {    	
		var firstTime = true;
		// Set Panel Template


		// ADD FILTER-FEED TO INDIVIDUAL FIELDS,
		// 	filter-feed='search_results'
		//	

		//$scope.feedId = $attrs.feedId;
		var FeedID = $scope.feedId;
		var template = 'feed_top';	// TODO get from Constant values
		if (!$scope.feedId) {
			$log.debug('no valid Feed ID provided in Feed Settings');
			return;
		}

		//$scope.feed = $pwData.feeds[$scope.feedId];

		// DEFAULTS
		//$scope.feed.query.author_name = "";

		// Taxonomy Object Model
		$scope.taxInput = $pwPostOptions.taxInputModel();


		// Get Default View Name
		/*
		if( $pwData.feeds[FeedID].panels[$attrs.filterFeed])
				template = $pwData.feeds[FeedID].panels[$attrs.filterFeed];			   	
		*/

		//$scope.templateUrl = $pwData.pw_get_template( { subdir: 'feed-filters', view: $attrs.filterFeed } );
		//$log.debug( 'pwFilterFeedController : templateUrl : ', $scope.templateUrl );
		// $log.debug('pwFilterFeedController() Set Initial Panel Template',FeedID, template, $scope.templateUrl, $pwData.feeds);


		// UPDATE AUTHOR NAME FROM AUTOCOMPLETE
		// Interacts with userAutocomplete() controller
		// Catches the recent value of the auto-complete


		/*
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

		*/

	}
);

postworld.directive('registerFeed', function($log, $pwData) {
	return {
		restrict: 'A',
		replace: true,
		controller: 'pwRegisterFeedController',
	};
});

postworld.controller('pwRegisterFeedController',
	function ($scope, $location, $log, $pwData, $attrs) {
		$scope.args= {};
		$scope.args.write_cache = false;
		$scope.args.feed_id = '';
		$scope.registerFeed = function() {
			$scope.args.feed_query = $scope.$parent.feed.query;
			$log.debug('pwRegisterFeedController.pwRegisterFeed For',$scope.args);
			// TODO set Nonce from UI
			$pwData.setNonce(78);
			$pwData.pw_register_feed($scope.args).then(
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
