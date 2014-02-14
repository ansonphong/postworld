/*               ___                        
  _ ____      __/ _ \ _   _  ___ _ __ _   _ 
 | '_ \ \ /\ / / | | | | | |/ _ \ '__| | | |
 | |_) \ V  V /| |_| | |_| |  __/ |  | |_| |
 | .__/ \_/\_/  \__\_\\__,_|\___|_|   \__, |
 |_|                                  |___/ 

//////// ---- PW QUERY DIRECTIVE ------ //////*/

postworld.directive( 'pwQuery', [ function($scope){
	return {
		restrict: 'A',
		controller: 'pwQueryCtrl',
		scope:{
			pwQueryVars:"=pwQuery",
			queryResultsModel:"=queryResultsModel",
			queryStatusModel:"=queryStatusModel",
			queryId:"=queryId"
		},
		link: function( $scope, element, attrs ){
			// OBSERVE Attribute
			//attrs.$observe('postsModel', function(value) {
			//	alert(value);
			//});

		}
	};
}]);

postworld.controller('pwQueryCtrl',
	['$scope', '$window', '$timeout', 'ext', 'pwData',
	function($scope, $window, $timeout, $ext, $pwData) {

	// Create query model if it doesn't exist
	if( _.isUndefined( $scope.queryResultsModel ) )
		$scope.queryResultsModel = [];

	// Create query model if it doesn't exist
	if( _.isUndefined( $scope.queryStatusModel ) )
		$scope.queryStatusModel = 'loading';

	//$scope.movementStatus = "loading";

	$scope.pwQuery = function( queryVars ){
		$pwData.pw_query( queryVars ).then(
			// Success
			function(response) {
				$scope.queryResultsModel = response.data.posts;
				$scope.statusQueryModel = "done";
			},
			// Failure
			function(response) {
				$scope.queryResultsModel = [{post_title:"Posts not loaded.", ID:"0"}];
			}
		);
	}
	$scope.pwQuery( $scope.pwQueryVars );

	// LOAD SUCCESS
	$scope.$on('postUpdated', function(post) { 
		// Action to Update Posts
        $scope.pwQuery( $scope.pwQueryVars );
    });

	// TRASH SUCCESS
	$scope.$on('trashPost', function( post ) {
		// Define New Post Object with same post type as current post
		var newPost = ( !_.isUndefined($scope.$parent.post.post_type) ) ?
			{ post_type:$scope.$parent.post.post_type, post_title:'' } : {};

		// Clear Post Object
        $scope.$parent.newPost( newPost );

        // Emit Update
        $scope.$emit('postUpdated', post);
    });


}]);