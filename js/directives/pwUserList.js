/*                _     _     _     _   _                   
  _ ____      __ | |   (_)___| |_  | | | |___  ___ _ __ ___ 
 | '_ \ \ /\ / / | |   | / __| __| | | | / __|/ _ \ '__/ __|
 | |_) \ V  V /  | |___| \__ \ |_  | |_| \__ \  __/ |  \__ \
 | .__/ \_/\_/   |_____|_|___/\__|  \___/|___/\___|_|  |___/
 |_|                                                        
///////////////////////// LIST USERS DIRECTIVE ////////////////////////*/

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
			userListId:'@userListId',
			userPostId:'=userPostId'
		},
		link: function( $scope, element, attrs ){

			$scope.userMax = parseInt($scope.userMax);

			
			// OBSERVE Attribute
			attrs.$observe('userMax', function(value) {
				//if( _.isUndefined( $scope.userMax ) )
					//$scope.userMax = 0;
			});
			
		}
	};
}]);

postworld.controller( 'pwUserListCtrl',
	[ '$scope', '$window', '$timeout', 'pwData', '$log',
	function( $scope, $window, $timeout, $pwData, $log ) {

	//$scope.$parent.attend_event_users = {'test':true}; 

	// Set local post ID
	if(  _.isUndefined($scope.userPostId) &&    // If the post ID isn't set by an attribute
		!_.isUndefined($scope.$parent.post) )   // And if a parent post object exists
		$scope.postId = $scope.$parent.post.ID; // Get the ID from the parent post object
	else
		$scope.postId = 0; // Otherwise set it to '0'


	// If strings provided for variables, convert to objects
	if( _.isString($scope.userIds) )
		$scope.userIds = angular.fromJson($scope.userIds);
	if( _.isString($scope.userFields) )
		$scope.userFields = angular.fromJson($scope.userFields);
	

	////////// EVENT LISTENER //////////
	// Takes broadcast / emit events from other scopes
	// To, for instance, add or Remove a user from the List
	$scope.$on('pwUserList', function( event, args ) { 
		/* //alert( JSON.stringify( args ) );
			args = {
				userListId: $scope.relationsAction,
		    	action: action,
		    	userId: $scope.userId(),
		    	postId: $scope.eventId()
	    	};
    	*/

		// Check if the action is directed at this post
		if( !_.isUndefined( args.postId ) && 	 	 // If the Post ID is defined in the action
			!_.isUndefined( $scope.userPostId ) && 	 // And the Post ID is defined in the directive
			args.postId != $scope.userPostId )	 	 // And if the defined ID is different from the current ID
			return true; // Go no farther

		// Convert args into the coorposonding types (integers)
		if( !_.isUndefined( args.postId ) )
			args.postId = parseInt(args.postId);
		if( !_.isUndefined( args.userId ) )
			args.userId = parseInt(args.userId);

		// If an action is specified, run the action feeding in the provided args
		if( !_.isUndefined( args.action ) )
			$scope[args.action]( args );

	});



	$scope.addUser = function( args ){

		// IS LIST ID
		if( !$scope.isListId(args) ) return false;

		///// ADD USER : Add a user to a list /////
		$log.debug('pwUserList // Add User : ', args );

		// ADD user to the user ID model
		if( $scope.isUserIds( args ) ){
			$scope.userIds = _.union( [args.userId], $scope.userIds );
			$scope.userIds = _.unique( $scope.userIds );
		}

		// GET USER DATA
		// Get the data for the newly added user
		// Convert string to array
		var userFields = ( _.isString($scope.userFields) ) ?
			angular.fromJson($scope.userFields) :
			$scope.userFields;

		// Setup Parameters
		var params = {
			user_id: args.userId,
			fields: userFields
		};
		$pwData.get_userdata( params ).then(
			// Success
			function(response) {    
				$log.debug('pwUserList // getUserData RESPONSE : ', response );
				
				// Add to the Model of users
				var newUserData = [ response.data ];
				if( !_.isUndefined( $scope.userModel ) ){
					$scope.userModel = _.union( newUserData, $scope.userModel );
				}
			},
			// Failure
			function(response) {
				return false;
			}
		);

	}

	$scope.removeUser = function( args ) {

		// IS LIST ID
		if( !$scope.isListId(args) ) return false;

		// LOG
		$log.debug('pwUserList // Remove User : ', args );

		// REMOVE user from the user data model array if it is defined
		// This is the model which holds all the user data specifics, display_name, etc
		if( !_.isUndefined( $scope.userModel ) ){
			// Get user data model - walk it and find the specified user ID, remove it
			var newUserData = [];
			angular.forEach( $scope.userModel, function(value){
				if( value.ID != args.userId )
					newUserData.push(value);
			});
			$scope.userModel = newUserData;
		}

		// REMOVE user from the user ID model
		if( !_.isUndefined( $scope.userIds ) && 	// If there is a defined array of user IDs
			_.isArray( $scope.userIds ) && 			// And the supplied object is an array
			!_.isUndefined(args.userId)  ){	  		// And the action passes a user ID
			$scope.userIds = _.without( $scope.userIds, args.userId );
		}

	}


	$scope.getUserData = function( userId ){

		var userFields = $scope.userFields;
		var args = {
			user_id:userId,
			fields:userFields
		};
		$pwData.get_userdata( args ).then(
			// Success
			function(response) {    
				return response.data;
				//alert( JSON.stringify( response ) );
			},
			// Failure
			function(response) {
				return false;
			}
		);


	};


	$scope.getUserDatas = function(){
		
		// Define User IDs
		if( _.isUndefined($scope.userIds) )
			$scope.userIds = [];
		var userIds = $scope.userIds;
		
		// Define User Fields
		var userFields = $scope.userFields;

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


	$scope.isListId = function( args ){
		// If a List ID is defined and it's different from current List ID

		// CHECK for requested list ID
		if( !_.isUndefined( args.userListId ) &&	// If a user list ID is defined in the args
			!_.isUndefined( $scope.userListId  ) &&	// And a user list ID is defined in the scope attributes
			args.userListId != $scope.userListId )	// And they are not equal
			return false;
		else
			return true; 	// Return true by default
	}

	$scope.isUserIds = function( args ){
		if( !_.isUndefined( $scope.userIds ) && 	// If there is a defined array of user IDs
			_.isArray( $scope.userIds ) && 			// And the supplied object is an array
			!_.isUndefined(args.userId)  )	  		// And the action passes a user ID
			return true;
		else
			return false; 	// Return false by default;
		
	}

	


}]);





