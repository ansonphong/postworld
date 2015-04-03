'use strict';
/*_        ___     _            _       
 \ \      / (_) __| | __ _  ___| |_ ___ 
  \ \ /\ / /| |/ _` |/ _` |/ _ \ __/ __|
   \ V  V / | | (_| | (_| |  __/ |_\__ \
	\_/\_/  |_|\__,_|\__, |\___|\__|___/
					 |___/              
//////////////// WIDGETS ////////////////*/

/**
* ///// POST SHARE REPORT /////
* Populates the scope with a post share report
* for the current contextual post.
*
* @method pwPostShareReport
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
			$scope.shareReportLoading = true;
			
			var postId = ( $scope.shareReportPostId == null ) ?
				$_.get( $pw, 'view.post.ID' ) :
				$scope.shareReportPostId;

			if( !postId )
				return false;

			$pwData.postShareReport( {post_id:postId} ).then(
				function(response) {    
					$scope.postShareReport = response.data;
					$scope.shareReportLoading = false;
				},
				function(response) {
					$scope.shareReportLoading = false;
				}
			);
			
		}

	}

}]);


///// POST SHARE REPORT /////
postworld.controller('userShareReportOutgoing',
	['$scope','$window','$timeout','pwData', '_', '$pw',
	function($scope, $window, $timeout, $pwData, $_, $pw ) {

	$scope.postShareReport = {};

	var userId = $_.get( $pw, 'view.displayed_user.user_id');

	if( !userId )
		return false;

	var args = { "user_id" : userId };

	$pwData.userShareReportOutgoing( args ).then(
		// Success
		function(response) {    
			$scope.shareReportMetaOutgoing = response.data;
			$scope.status = "done";
		},
		// Failure
		function(response) {
			//alert('Error loading report.');
		}
	);


}]);
