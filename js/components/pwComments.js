/**
 * @ngdoc directive
 * @name postworld.directive:pwComments
 * @description Adds the configured comment plugins to the current element.
 * @restrict A
 * @param {expression} commentsEnable An expression which resolves a boolean, if false will disable directive. 
 */
postworld.directive( 'pwComments',
	[ '$_', '$pw', '$pwData', 'pwTemplatePartials', '$log', '$timeout',
	function( $_, $pw, $pwData, $pwTemplatePartials, $log, $timeout ){

	return {
		restrict: 'AE',
		link: function( $scope, element, attrs ){
			// If no comments module is not enabled, end here
			if( !$pw.moduleEnabled('comments') )
				return false;
			
			// If no comments are enabled, end here
			if( !$_.get( $pw, 'comments.facebook.enable' ) &&
				!$_.get( $pw, 'comments.disqus.enable' ) )
				return false;
	
			// Setup comments Cache ID variable
			var commentsCacheId = false;

			// Watch for the post ID change, on post switch
			$scope.$watch( 'post.ID', function( val ){
				$log.debug( 'Post ID : ', val );
				// Update the current cache name
				commentsCacheId = 'comments_' + $scope.post.ID;
			} );

			var commentsEnabled = function(){
				if( _.isUndefined( attrs.commentsEnable ) )
					return true;
				else
					return Boolean( $scope.$eval(attrs.commentsEnable) );
			}

			var update = function(){
				// If Facebook comments are enabled, re-parse after update
				if( $_.get( $pw, 'comments.facebook.enable' ) ){
					$timeout( function(){
						// If Facebook has initialized
						if( typeof FB !== 'undefined' )
							// Refresh Facebook Comments
							FB.XFBML.parse();
					}, 100 );
				}

				if( commentsEnabled() )
					element.html( getComments() );
				else
					element.html( '' );
			}

			// Get the comments code
			var getComments = function(){
				// Return the comments template partial
				return $pwTemplatePartials.get({
						partial:'pw.comments',
						vars: {
							id: $scope.post.ID,
							title: $scope.post.post_title,
							url: $scope.post.post_permalink,
						},
						id: commentsCacheId,
					});
			}

			// Watch for changes in the comments code
			$scope.$watch(
				function(){
					return getComments();
				},
				// When the comments code changes
				function(val){
					update();
				},
				1
			);

		}
	};
}]);