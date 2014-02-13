/*                _     _     _     _   _                   
  _ ____      __ | |   (_)___| |_  | | | |___  ___ _ __ ___ 
 | '_ \ \ /\ / / | |   | / __| __| | | | / __|/ _ \ '__/ __|
 | |_) \ V  V /  | |___| \__ \ |_  | |_| \__ \  __/ |  \__ \
 | .__/ \_/\_/   |_____|_|___/\__|  \___/|___/\___|_|  |___/
 |_|                                                        
///////////////////////// POST MEDIA LIBRARY DIRECTIVE ////////////////////////*/

postworld.directive( 'pwUserList', [ function($scope){
	return {
		restrict: 'AE',
		controller: 'pwUserListCtrl',
		scope: {
			//userQuery:'@userQuery', // INOP
			userFields:'@userFields',
			userMax:'@userMax',
			userIds:'=userIds',
			userModel:'=userModel',
		},
		link: function( $scope, element, attrs ){

			$scope.userMax = parseInt($scope.userMax);

			/*
			// OBSERVE Attribute
			attrs.$observe('var', function(value) {
			});
			*/
		}
	};
}]);

postworld.controller( 'pwUserListCtrl',
	[ '$scope', '$window', '$timeout', 'pwData',
	function( $scope, $window, $timeout, $pwData ) {

	//$scope.$parent.attend_event_users = {'test':true}; 

	// EVENT LISTENER : 
	// Takes broadcast / emit events from other scopes
	// To, for instance, add or Remove a user from the List
	$scope.$on('pwUserList', function( event, args ) { 
		/*
			args = {
				userListId: $scope.relationsAction,
		    	action: action,
		    	userId: $scope.userId(),
		    	postId: $scope.eventId()
	    	};
    	*/
		//alert( JSON.stringify( args ) );
	
		if( args.action == 'addUser' ){
			alert( "add user : " + args.userId );

			// Get userdata for that new user being added
			// Prepend the data to the users array, so they (the new user) appears at the top

		}

		if( args.action == 'removeUser' ){
			alert( "remove user : " + args.userId );

			// Get list ID model - remove ID
			// Get user data model - walk it and find the specified user ID, remove it

		}


	});


	$scope.getUserDatas = function(){
		
		var userIds = $scope.userIds;
		var userFields = $scope.userFields;

		// If strings provided for variables, convert to objects
		if( typeof userIds == 'string' )
			userIds = angular.fromJson(userIds);
		if( typeof userFields == 'string' ){
			userFields = angular.fromJson(userFields);
		}

		// If users exceeds user max
		if( !_.isUndefined( $scope.userMax ) &&
			userIds.length > $scope.userMax )
			userIds = userIds.slice( 0, $scope.userMax );

		// Setup Args for PHP AJAX call
		var args = {
			user_ids: userIds,
			fields: userFields,
		};

		$pwData.get_userdatas( args ).then(
			// Success
			function(response) {    
				$scope.userModel = response.data;
				//alert( JSON.stringify( response ) );
			},
			// Failure
			function(response) {
			}
		);

	};
	$scope.getUserDatas();

}]);





