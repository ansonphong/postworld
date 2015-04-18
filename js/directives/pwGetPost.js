/*               ____      _   ____           _   
  _ ____      __/ ___| ___| |_|  _ \ ___  ___| |_ 
 | '_ \ \ /\ / / |  _ / _ \ __| |_) / _ \/ __| __|
 | |_) \ V  V /| |_| |  __/ |_|  __/ (_) \__ \ |_ 
 | .__/ \_/\_/  \____|\___|\__|_|   \___/|___/\__|
 |_|                                              
//////// ---- PW GET POST DIRECTIVE ------ //////*/

/**
 * @class pwGetPost
 * @classdesc Loads the contents of a requested post into scope.
 * @implements Directive
 * @param {number} pwGetPost The ID of the post.
 * @param {expression} postFields An array or string representing the post field model.
 * @param {expression} postModel Where to bind the post content to in the local scope.
 * @param {expression} postLoading Binds a boolean to the current status of the post data loading.
 */
postworld.directive( 'pwGetPost',
	[ '$window', '$timeout', '_', 'pwData',
	function( $window, $timeout, $_, $pwData ){
	return {
		restrict: 'AE',
		controller: 'pwGetPostCtrl',
		scope:{
			postId:"=pwGetPost",
			postFields:"=",
			postModel:"=",
			postLoading:"=",
		},
		link: function( $scope, element, attrs ){

			// Create model if it doesn't exist
			if( _.isUndefined( $scope.postModel ) )
				$scope.postModel = [];

			// Create query model if it doesn't exist
			//if( _.isUndefined( $scope.queryStatusModel ) )
			$scope.postLoading = true;

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
						$scope.postLoading = false;
					},
					// Failure
					function(response) {
						$scope.postModel = [{post_title:"Posts not loaded.", ID:"0"}];
						$scope.postLoading = false;
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
			},1);


		}
	};
}]);
