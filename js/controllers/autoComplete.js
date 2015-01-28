'use strict';

/*_   _                    _         _                                  _      _       
 | | | |___  ___ _ __     / \  _   _| |_ ___   ___ ___  _ __ ___  _ __ | | ___| |_ ___ 
 | | | / __|/ _ \ '__|   / _ \| | | | __/ _ \ / __/ _ \| '_ ` _ \| '_ \| |/ _ \ __/ _ \
 | |_| \__ \  __/ |     / ___ \ |_| | || (_) | (_| (_) | | | | | | |_) | |  __/ ||  __/
  \___/|___/\___|_|    /_/   \_\__,_|\__\___/ \___\___/|_| |_| |_| .__/|_|\___|\__\___|
																 |_|                   
////////// ------------ USER AUTOCOMPLETE CONTROLLER ------------ //////////*/
postworld.controller( 'userAutocomplete',
	[ '$scope', 'pwData', '$log', '_',
	function( $scope, $pwData, $log, $_ ){

		$scope.username = '';
		$scope.authors = [];
		if (($scope.$parent.feedQuery) && ($scope.$parent.feedQuery.author_name)) {
			$scope.username = $scope.$parent.feedQuery.author_name;
		};    

		$scope.queryList = function(){
			// Clobber the Validation Function
			$_.clobber( 'autocompleteUser', 200, function(){
				$scope.queryListAjax();
			} );
		}

		$scope.queryListAjax = function( searchTerm ) {

			var searchTerm = $scope.username + "*";            
			var query_args = {
				number:20,
				search: searchTerm,
			};
			$pwData.userQueryAutocomplete( query_args ).then(
				// Success
				function(response) {
					$log.debug( 'userAutocomplete.querylist : RESPONSE', response.data );    
					$scope.authors = response.data;
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


}]);




/*
  _____                    _         _                                  _      _       
 |_   _|_ _  __ _ ___     / \  _   _| |_ ___   ___ ___  _ __ ___  _ __ | | ___| |_ ___ 
   | |/ _` |/ _` / __|   / _ \| | | | __/ _ \ / __/ _ \| '_ ` _ \| '_ \| |/ _ \ __/ _ \
   | | (_| | (_| \__ \  / ___ \ |_| | || (_) | (_| (_) | | | | | | |_) | |  __/ ||  __/
   |_|\__,_|\__, |___/ /_/   \_\__,_|\__\___/ \___\___/|_| |_| |_| .__/|_|\___|\__\___|
			|___/                                                |_|                   
////////// ------------ TAGS AUTOCOMPLETE CONTROLLER ------------ //////////*/
postworld.controller('tagsAutocomplete',
	[ '$scope', '$filter', 'pwData',
	function( $scope, $filter, pwData ){


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

	$scope.removeTag = function( tag ){
		// Define tags object
		var tags = $scope.tags_input_obj;

		// Find the original tag object with the same matching slug
		var removeTag = _.findWhere( tags, { slug: tag.slug } );

		// Remove it from the tags object
		$scope.tags_input_obj = _.without( tags, removeTag );

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



}]);

	