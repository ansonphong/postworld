'use strict';
/*_        ___     _            _       
 \ \      / (_) __| | __ _  ___| |_ ___ 
  \ \ /\ / /| |/ _` |/ _` |/ _ \ __/ __|
   \ V  V / | | (_| | (_| |  __/ |_\__ \
	\_/\_/  |_|\__,_|\__, |\___|\__|___/
					 |___/              
//////////////// WIDGETS ////////////////*/

/**
 * Post Share Report
 * Populates the scope with a post share report
 * for the current contextual post.
 * @class pwPostShareReport
 * @return {object} Populates $scope.postShareReport
 */
postworld.directive('pwPostShareReport',
	['$window','$timeout','pwData', '$pw', '$log', '_',
	function( $window, $timeout, $pwData, $pw, $log, $_ ) {
	return {
		scope:{
			postShareReport:"=pwPostShareReport",
			shareReportLoading:"=",
			shareReportPostId:"="
		},
		link: function( $scope, element, attrs ){

			$scope.postShareReport = {};
			
			$scope.$watch( 'shareReportPostId', function( postId ){

				if( postId == null )
					postId = $_.get( $pw, 'view.post.ID' );

				if( !postId )
					return false;

				$scope.shareReportLoading = true;

				$pwData.postShareReport( {post_id:postId} ).then(
					function(response) {    
						$scope.postShareReport = response.data;
						$scope.shareReportLoading = false;
					},
					function(response) {
						$scope.shareReportLoading = false;
					}
				);

			});

		}

	}

}]);


/**
 * User Share Report : Outgoing / Incoming
 * Populates the scope with an outgoing user share report
 * showing posts which the given user has shared.
 *
 * @method pwPostShareReport
 * @return {object} Populates $scope.postShareReport
 */
postworld.directive('pwUserShareReport',
	['$window','$timeout','pwData', '_', '$pw', '$log',
	function($window, $timeout, $pwData, $_, $pw, $log ) {
		return {
			scope:{
				pwUserShareReport:"=", // Array of strings deliniating which reports to get ['outgoing','incoming']
				shareReportUserId:"=",
				shareReportOutgoing:"=",
				shareReportOutgoingLoading:"="
			},
			link: function( $scope, element, attrs ){

				$scope.shareReportOutgoing = {};

				$scope.$watch( 'shareReportUserId', function( userId ){

					if( userId === null || _.isUndefined( userId ) )
						userId = $_.get( $pw, 'view.displayed_user.user_id');

					if( !userId )
						return false;

					var args = { "user_id" : userId };

					if( $_.inArray( 'outgoing', $scope.pwUserShareReport ) ){
						$scope.shareReportOutgoingLoading = true;
						$pwData.userShareReportOutgoing( args ).then(
							// Success
							function(response) {    
								$scope.shareReportOutgoing = response.data;
								$scope.shareReportOutgoingLoading = false;
							},
							// Failure
							function(response) {
								$scope.shareReportOutgoingLoading = false;
							}
						);
					}


				});

			}

		}


}]);
