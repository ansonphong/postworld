/*_   _                   
 | | | |___  ___ _ __ ___ 
 | | | / __|/ _ \ '__/ __|
 | |_| \__ \  __/ |  \__ \
  \___/|___/\___|_|  |___/
*/

/**
 * @ngdoc directive
 * @name postworld.directive:pwUsers
 * @description 
 * Provides methods to access user data.
 *
 * @param {string} userId A user ID to retreive data for.
 * 		If no user is provided, all available users are provided. 
 * @param {expression} userModel Where to bind the user.
 * @param {string|boolean} userDynamic If the user ID is expected to change.
 */
postworld.directive( 'pwUsers',
	[ '_', 'pwData', '$log', '$timeout',
	function( $_, $pwData, $log, $timeout ){
	return {
		scope:{
			userId:'@',
			userModel:'=',
			userDynamic:'@',
			//userFields:'@' // Not implimented
		},
		link: function( $scope, element, attrs  ){

			/**
			 * Initializes the directive.
			 */
			var init = function(){
				/*
				 * If the userDynamic is true,
				 * watch the userId for changes, then populate.
				 */
				if( $_.stringToBool($scope.userDynamic) === true ){
					$scope.$watch( 'userId', function(val,oldVal){
						populate();
					});
				}
				else{
					populate();
				}
			}

			/**
			 * Populates the model with the user data.
			 *
			 * @todo Get userdata from server for undefined users
			 */
			var populate = function(){
				if( !_.isUndefined( $scope.userId ) )
					$scope.userModel = $_.get( $pwData.users, $scope.userId );
				else
					$scope.userModel = $pwData.users;
			}

			// Initialize
			init();
				
		},
	};
}]);

/**
 * @ngdoc directive
 * @name postworld.directive:pwCurrentUser
 * @description 
 * Provides methods to access the currently logged in user.
 *
 * @param {expression} pwCurrentUser An expression to bind the logged in user to.
 */
postworld.directive( 'pwCurrentUser',
	[ '_', 'pwData', '$log', '$pw',
	function( $_, $pwData, $log, $pw ){
	return {
		scope:{
			pwCurrentUser:"=",
		},
		link: function( $scope, element, attrs  ){

			// Set the user data
			var user = $_.get($pw,'user');
			if( !_.isEmpty(user) )
				$scope.pwCurrentUser = $pw.user;
			else
				$scope.pwCurrentUser = {};

			// Returns boolean if the user is logged in
			$scope.pwCurrentUser.isLoggedIn = function(){
				var userId = $_.get( $pw, 'user.data.ID' );
				return ( userId !== false ) ? true : false;
			}

		},
	};

}]);