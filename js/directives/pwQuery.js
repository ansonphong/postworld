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
	['$scope', '$log', '$window', '$timeout', '_', 'pwData',
	function($scope, $log, $window, $timeout, $_, $pwData) {

	// Create query model if it doesn't exist
	if( _.isUndefined( $scope.queryResultsModel ) )
		$scope.queryResultsModel = [];

	// Create query model if it doesn't exist
	//if( _.isUndefined( $scope.queryStatusModel ) )
	$scope.queryStatusModel = 'loading';

	//$scope.movementStatus = "loading";

	$scope.pwQuery = function( queryVars ){
		$pwData.pwQuery( queryVars ).then(
			// Success
			function(response) {
				$scope.queryResultsModel = response.data.posts;
				$scope.queryStatusModel = "done";
			},
			// Failure
			function(response) {
				$scope.queryResultsModel = [{post_title:"Posts not loaded.", ID:"0"}];
				$scope.queryStatusModel = "done";
			}
		);
	}

	$scope.$watch('pwQueryVars', function(value) {
		if( !_.isUndefined($scope.pwQueryVars) )
			$scope.pwQuery( $scope.pwQueryVars );

		$log.debug( '$scope.pwQueryVars', $scope.pwQueryVars );

	});
	

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



/*              _____                       _____             _ 
  _ ____      _|_   _|__ _ __ _ __ ___  ___|  ___|__  ___  __| |
 | '_ \ \ /\ / / | |/ _ \ '__| '_ ` _ \/ __| |_ / _ \/ _ \/ _` |
 | |_) \ V  V /  | |  __/ |  | | | | | \__ \  _|  __/  __/ (_| |
 | .__/ \_/\_/   |_|\___|_|  |_| |_| |_|___/_|  \___|\___|\__,_|
 |_|                                                            
///////////// ----- PW TERMS FEED DIRECTIVE ------- //////////*/

postworld.directive( 'pwTermFeed', [ function($scope){
	return {
		restrict: 'A',
		controller: 'pwTermFeedCtrl',
		scope:{
			pwTermFeed:"=pwTermFeed",
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

postworld.controller('pwTermFeedCtrl',
	['$scope', '$log', '$window', '$timeout', '_', 'pwData',
	function($scope, $log, $window, $timeout, $_, $pwData) {

	// Create query model if it doesn't exist
	if( _.isUndefined( $scope.queryResultsModel ) )
		$scope.queryResultsModel = [];

	// Create query model if it doesn't exist
	//if( _.isUndefined( $scope.queryStatusModel ) )
	$scope.queryStatusModel = 'loading';

	$scope.getTermFeed = function( queryVars ){
		$pwData.getTermFeed( queryVars ).then(
			// Success
			function(response) {
				$log.debug( 'pwTermFeed : getTermFeed : RESPONSE :', response );
				$scope.queryResultsModel = response.data;
				$scope.queryStatusModel = "done";
			},
			// Failure
			function(response) {
				$scope.queryResultsModel = [{post_title:"Posts not loaded.", ID:"0"}];
				$scope.queryStatusModel = "done";
			}
		);
	}

	$scope.$watch('pwTermFeed', function(value) {
		if( !_.isUndefined($scope.pwTermFeed) )
			$scope.getTermFeed( $scope.pwTermFeed );

		$log.debug( '$scope.pwTermFeed', $scope.pwTermFeed );

	});


}]);