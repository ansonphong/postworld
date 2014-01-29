'use strict';

/* ____                 _         _                                  _      _       
  / ___| ___  ___      / \  _   _| |_ ___   ___ ___  _ __ ___  _ __ | | ___| |_ ___ 
 | |  _ / _ \/ _ \    / _ \| | | | __/ _ \ / __/ _ \| '_ ` _ \| '_ \| |/ _ \ __/ _ \
 | |_| |  __/ (_) |  / ___ \ |_| | || (_) | (_| (_) | | | | | | |_) | |  __/ ||  __/
  \____|\___|\___/  /_/   \_\__,_|\__\___/ \___\___/|_| |_| |_| .__/|_|\___|\__\___|
                                                              |_|                   
///////////// ------------ CITY AUTOCOMPLETE CONTROLLER ------------ /////////////*/

postworld.directive( 'pwGeoAutocomplete', [function(){
    return { 
        controller: 'pwGeoAutocompleteCtrl'
    };
}]);

postworld.controller('pwGeoAutocompleteCtrl', ['$scope','$http', '$window', function($scope, $http, $window) {

	$scope.locationObject = {};

	// Any function returning a promise object can be used to load values asynchronously
	$scope.getLocation = function(val) {
		return $http.get('http://maps.googleapis.com/maps/api/geocode/json', {
			params: {
				address: val,
				sensor: false
			}
		}).then(function(res){
			var addresses = [];
			angular.forEach(res.data.results, function(item){
				addresses.push(item);
			});
			return addresses;
		});
	};

	$scope.addGeocode = function(val) {
		// DO ADD LOCATION CALLBACK - DEFINED BY THEME / CONTEXT
		// OR... use $emit with location object - caught by parent local context specific controller
	
		//$scope.locationObject = val;
		$scope.$emit('pwAddGeocode', val);


	}

	// LISTENER FOR EMITTED LOCATION FROM LOAD POST
	// THEN INSERT INTO addLocation()

	// If 'post' object exists, insert it 
	// handle with callbacks, preset callbacks based on context :
	// ie. add as linear taxonomy terms input (ie. location taxonomy)


	/*
	$scope.addTag = function(){
		// Cycle through the tagOptions Object
		angular.forEach( $scope.tagOptions, function( tag ){
			if( tag.slug == $scope.queryTag ){
				$scope.tags_input_obj.push(tag);
				$scope.queryTag = "";
			}           
		});
	}
	*/


}]);




/*_   _                    _         _                                  _      _       
 | | | |___  ___ _ __     / \  _   _| |_ ___   ___ ___  _ __ ___  _ __ | | ___| |_ ___ 
 | | | / __|/ _ \ '__|   / _ \| | | | __/ _ \ / __/ _ \| '_ ` _ \| '_ \| |/ _ \ __/ _ \
 | |_| \__ \  __/ |     / ___ \ |_| | || (_) | (_| (_) | | | | | | |_) | |  __/ ||  __/
  \___/|___/\___|_|    /_/   \_\__,_|\__\___/ \___\___/|_| |_| |_| .__/|_|\___|\__\___|
																 |_|                   
////////// ------------ USER AUTOCOMPLETE CONTROLLER ------------ //////////*/
function userAutocomplete($scope, pwData) {
	$scope.username = undefined;
	if (($scope.$parent.feedQuery) && ($scope.$parent.feedQuery.author_name)) {
		$scope.username = $scope.$parent.feedQuery.author_name;
	};    
	$scope.queryList = function () {
		var searchTerm = $scope.username + "*";            
		var query_args = {
			number:20,
			fields:['user_nicename', 'display_name'],
			search: searchTerm,
		};
		pwData.user_query_autocomplete( query_args ).then(
			// Success
			function(response) {
				//console.log(response);    
				$scope.authors = response.data.results;
			},
			// Failure
			function(response) {
				throw { message:'Error: ' + JSON.stringify(response)};
			}
		);
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

}
/*
  _____                    _         _                                  _      _       
 |_   _|_ _  __ _ ___     / \  _   _| |_ ___   ___ ___  _ __ ___  _ __ | | ___| |_ ___ 
   | |/ _` |/ _` / __|   / _ \| | | | __/ _ \ / __/ _ \| '_ ` _ \| '_ \| |/ _ \ __/ _ \
   | | (_| | (_| \__ \  / ___ \ |_| | || (_) | (_| (_) | | | | | | |_) | |  __/ ||  __/
   |_|\__,_|\__, |___/ /_/   \_\__,_|\__\___/ \___\___/|_| |_| |_| .__/|_|\___|\__\___|
			|___/                                                |_|                   
////////// ------------ TAGS AUTOCOMPLETE CONTROLLER ------------ //////////*/
function tagsAutocomplete($scope, $filter, pwData) {

	$scope.tags_input = [];     // Array
	$scope.tags_input_obj = []; // Object

	$scope.queryTags = function () {
		var queryTag = $scope.queryTag;

		var args = {
			search: $scope.queryTag,
			taxonomy:"post_tag"
		}

		pwData.tags_autocomplete( args ).then(
			// Success
			function(response) {
				console.log(response.data);    
				$scope.tagOptions = response.data;
			},
			// Failure
			function(response) {
				throw { message:'Error: ' + JSON.stringify(response)};
			}
		);
	};

	$scope.addTag = function(){
		// Cycle through the tagOptions Object
		angular.forEach( $scope.tagOptions, function( tag ){
			if( tag.slug == $scope.queryTag ){
				$scope.tags_input_obj.push(tag);
				$scope.queryTag = "";
			}           
		});
	}

	$scope.removeTag = function(removeSlug){
		$scope.tags_input_obj = $filter('filter')($scope.tags_input_obj, function(item) {
			return !(item.slug == removeSlug);
		 });
	}

	$scope.newTag = function(){
		var newTag = {
			name: $scope.queryTag,
			slug: $scope.queryTag,
			};

		$scope.tags_input_obj.push(newTag);
		$scope.queryTag = "";
	}

	// Watch on the object with input tags
	$scope.$watch( "tags_input_obj",
		function (){
			// When it changes, modify the tags_input object
			$scope.tags_input = [];
			angular.forEach( $scope.tags_input_obj, function( tag ){
				$scope.tags_input.push(tag.slug);
			});

			// Emit it's value to the parent controller
			$scope.$emit('updateTagsInput', $scope.tags_input);
		}, 1 );
	
	// Catch broadcast of load in tags
	$scope.$on('postTagsObject', function(event, data) { $scope.tags_input_obj = data; });

}
