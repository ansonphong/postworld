/*               ____      _   ____           _   
  _ ____      __/ ___| ___| |_|  _ \ ___  ___| |_ 
 | '_ \ \ /\ / / |  _ / _ \ __| |_) / _ \/ __| __|
 | |_) \ V  V /| |_| |  __/ |_|  __/ (_) \__ \ |_ 
 | .__/ \_/\_/  \____|\___|\__|_|   \___/|___/\__|
 |_|                                              
//////// ---- PW GET POST DIRECTIVE ------ //////*/

postworld.directive( 'pwGetPost', [ function($scope){
	return {
		restrict: 'AE',
		controller: 'pwGetPostCtrl',
		scope:{
			postId:"=pwGetPost",
			postFields:"=postFields",
			postModel:"=postModel",
			postStatusModel:"=postStatusModel",
		},
		link: function( $scope, element, attrs ){
			// OBSERVE Attribute
			//attrs.$observe('postsModel', function(value) {
			//	alert(value);
			//});
		}
	};
}]);

postworld.controller('pwGetPostCtrl',
	['$scope', '$window', '$timeout', '_', 'pwData',
	function($scope, $window, $timeout, $_, $pwData) {

	// Create model if it doesn't exist
	if( _.isUndefined( $scope.postModel ) )
		$scope.postModel = [];

	// Create query model if it doesn't exist
	//if( _.isUndefined( $scope.queryStatusModel ) )
	$scope.postStatusModel = 'loading';

	$scope.pwGetPost = function(){
		if( _.isUndefined( $scope.postId ) ){
			return false;
		}

		if( _.isUndefined( $scope.postFields ) ){
			$scope.postFields = "preview";
		}

		var vars = {
			post_id: $scope.postId,
			fields: $scope.postFields,
		};

		$pwData.getPost( vars ).then(
			// Success
			function(response) {
				$scope.postModel = response.data;
				$scope.postStatusModel = "done";
			},
			// Failure
			function(response) {
				$scope.postModel = [{post_title:"Posts not loaded.", ID:"0"}];
				$scope.postStatusModel = "done";
			}
		);
	}

	// Action to Update Posts
	$scope.$on('postUpdated', function(post) { 
        $scope.pwGetPost();
    });

	// Watch values and re-get post if they change
	$scope.$watch('[postId, postFields]', function(value) {
		if( !_.isUndefined($scope.postId) )
			$scope.pwGetPost();

		//alert( $scope.postId );
	},1);
	
}]);
