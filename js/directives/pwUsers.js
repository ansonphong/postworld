/*
  _   _                   
 | | | |___  ___ _ __ ___ 
 | | | / __|/ _ \ '__/ __|
 | |_| \__ \  __/ |  \__ \
  \___/|___/\___|_|  |___/
                          
*/
///// USERS /////
postworld.directive( 'pwUsers', [ '_', 'pwData', '$log', function( $_, $pwData, $log ){
	return {
		link: function( $scope, element, attrs  ){
			$scope.users = pwData.users;
			/*
			attrs.$observe( '', function( userId ){
			});
			attrs.$observe( 'pwUserId', function( userId ){
				if( !_.isEmpty( userId ) ){
					$scope.user = $pwData.users[ userId ];
				}
			});
			*/
		},
	};
}]);

///// USER /////
// Directive procudes methods to access the currently logged in user

postworld.directive( 'pwUser',
	[ '_', 'pwData', '$log', '$pw',
	function( $_, $pwData, $log, $pw ){

	return {
		scope:{
			pwUser:"=",
			//userFunctions:"="
		},
		link: function( $scope, element, attrs  ){

			// Set the user data
			var user = $_.get($pw,'user');
			if( !_.isEmpty(user) )
				$scope.pwUser = $pw.user;
			else
				$scope.pwUser = {};

			// Returns boolean if the user is logged in
			$scope.pwUser.isLoggedIn = function(){
				var userId = $_.get( $pw, 'user.data.ID' );
				return ( userId !== false ) ? true : false;
			}

		},
	};

}]);