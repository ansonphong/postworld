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
			*/
		},
	};
}]);


///// USER /////
postworld.directive( 'pwUser', [ '_', 'pwData', '$log', function( $_, $pwData, $log ){
	return {
		link: function( $scope, element, attrs  ){
			attrs.$observe( 'pwUser', function( userId ){
				if( !_.isEmpty( userId ) ){
					$scope.user = $pwData.users[ userId ];
				}
			});
		},
	};
}]);