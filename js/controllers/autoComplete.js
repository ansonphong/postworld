'use strict';
/*_   _                    _         _                                  _      _       
 | | | |___  ___ _ __     / \  _   _| |_ ___   ___ ___  _ __ ___  _ __ | | ___| |_ ___ 
 | | | / __|/ _ \ '__|   / _ \| | | | __/ _ \ / __/ _ \| '_ ` _ \| '_ \| |/ _ \ __/ _ \
 | |_| \__ \  __/ |     / ___ \ |_| | || (_) | (_| (_) | | | | | | |_) | |  __/ ||  __/
  \___/|___/\___|_|    /_/   \_\__,_|\__\___/ \___\___/|_| |_| |_| .__/|_|\___|\__\___|
																 |_|                   
/////////////// ------------ USER AUTOCOMPLETE CONTROLLER ------------ ///////////////*/

postworld.directive( 'pwUserAutocomplete', [ '$log', '_', function( $log, $_ ){
	return {
		controller: 'UserAutocompleteController',
		//link: function( $scope, element, attrs ){}
	}
}]);

postworld.controller( 'UserAutocompleteController',
	[ '$scope', 'pwData', '$log', '_',
	function( $scope, $pwData, $log, $_ ){

		$scope.queryList = function( searchTerm ) {
			$log.debug( searchTerm );
			var searchTerm = searchTerm + "*";            
			var query_args = {
				number:20,
				search: searchTerm,
			};
			
			if( _.isEmpty( searchTerm ) )
				return [];

			return $pwData.userQueryAutocomplete( query_args ).then(
				// Success
				function(response) {
					$log.debug( 'userAutocomplete.querylist : RESPONSE', response.data );    
					return response.data;
				},
				// Failure
				function(response) {
					throw { message:'Error: ' + JSON.stringify(response)};
				}
			);
		};

		$scope.selectUser = function( user ){
			$log.debug( "SELECT USER", user );
		}

		$scope.setUserValue = function( userValue, model ){
			$scope.$eval( model + '=' + JSON.stringify( userValue ) );
			$log.debug( "SELECT USER", user );
		}

		$scope.setValue = function( value, ngModel ){
			// Take the passed in value ( ie. $item ) and assign value to ngModel
			$log.debug( 'userAutocomplete : setValue() // model : ' + ngModel, value );
			$scope.$eval( ngModel + ' = ' + JSON.stringify( value ) );
		}

		/*
		///// TODO : REFACTOR /////
		$scope.username = '';
		if (($scope.$parent.feedQuery) && ($scope.$parent.feedQuery.author_name)) {
			$scope.username = $scope.$parent.feedQuery.author_name;
		};    
		// Watch on the value of username
		$scope.$watch( "username",
			function (){
				// When it changes, emit it's value to the parent controller
				if ($scope.username) $scope.$emit('updateUsername', $scope.username);
			}, 1 );
		// Catch broadcast of username change
		$scope.$on('updateUsername', function(event, data) { 
			if (data) $scope.username = data; 
			});
		*/


}]);


/*_____                    _         _                                  _      _       
 |_   _|_ _  __ _ ___     / \  _   _| |_ ___   ___ ___  _ __ ___  _ __ | | ___| |_ ___ 
   | |/ _` |/ _` / __|   / _ \| | | | __/ _ \ / __/ _ \| '_ ` _ \| '_ \| |/ _ \ __/ _ \
   | | (_| | (_| \__ \  / ___ \ |_| | || (_) | (_| (_) | | | | | | |_) | |  __/ ||  __/
   |_|\__,_|\__, |___/ /_/   \_\__,_|\__\___/ \___\___/|_| |_| |_| .__/|_|\___|\__\___|
			|___/                                                |_|                   
/////////////// ------------ TAGS AUTOCOMPLETE CONTROLLER ------------ ///////////////*/

postworld.directive('pwInputTags', [ '$filter', 'pwData',
	function( $filter, $pwData ){
	return {
		restrict: 'A',
		link: function ($scope, element, attrs) {

			$scope.tagsAutocompleteLoading = false;
			$scope.tagsInput = [];

			$scope.queryTags = function( viewValue ) {
				var args = {
					search: viewValue,
					taxonomy:"post_tag"
				}
				return $pwData.tags_autocomplete( args ).then(
					// Success
					function(response) {
						console.log(response.data);    
						return response.data;
					},
					// Failure
					function(response) {
						throw { message:'Error: ' + JSON.stringify(response)};
					}
				);
			};

			$scope.addTag = function( item ){
				// Cycle through the tagOptions Object
				$scope.tagsInput.push( item );
				//$scope.queryTag = "";
			}

			$scope.removeTag = function( tag ){
				// Define tags object
				var tags = $scope.tagsInput;
				// Find the original tag object with the same matching slug
				var removeTag = _.findWhere( tags, { slug: tag.slug } );
				// Remove it from the tags object
				$scope.tagsInput = _.without( tags, removeTag );
			}

			$scope.newTag = function( tag ){
				var newTag = {
					name: tag,
					slug: tag,
					};
				$scope.tagsInput.push(newTag);
			}

			// Watch on the object with input tags
			$scope.$watch( "tagsInput",
				function (){
					// When it changes, modify the tagsInput object
					var tagSlugs = [];
					angular.forEach( $scope.tagsInput, function( tag ){
						tagSlugs.push(tag.slug);
					});
					// Emit it's value to the parent controller
					$scope.$emit('updateTagsInput', tagSlugs);
				}, 1 );
			
			// Catch broadcast of load in tags
			$scope.$on('postTagsObject', function(event, data) { $scope.tagsInput = data; });

		}
	};
}])
	