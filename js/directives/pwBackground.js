/*              ____             _                                   _ 
  _ ____      _| __ )  __ _  ___| | ____ _ _ __ ___  _   _ _ __   __| |
 | '_ \ \ /\ / /  _ \ / _` |/ __| |/ / _` | '__/ _ \| | | | '_ \ / _` |
 | |_) \ V  V /| |_) | (_| | (__|   < (_| | | | (_) | |_| | | | | (_| |
 | .__/ \_/\_/ |____/ \__,_|\___|_|\_\__, |_|  \___/ \__,_|_| |_|\__,_|
 |_|                                 |___/                             
 ///////////////////////// LOAD BACKGROUND DIRECTIVE ////////////////////////*/
 postworld.directive( 'pwBackground',
 	[ '$window', '$timeout', 'pwData', '$log', '_',
 	function( $window, $timeout, $pwData, $log, $_ ){
	return {
		restrict: 'AE',
		scope: {
			pwBackground:'=',
		},
		link: function( $scope, element, attrs ){

			$scope.$watch('pwBackground', function( val ){
				$scope.updateBackground( val );
			}, 1 );

			$scope.updateBackground = function(){
				// Apply the background styles
				$scope.applyBackgroundStyles();
				// Check if there is an image ID
				var imageId = $scope.getImageId();
				if( imageId ){
					// And there is no image post
					if( !$scope.getImagePost() ){
						// Populate it
						$scope.populateImagePost( imageId );
						return;
					}
				}
			}

			$scope.applyBackgroundStyles = function(){
				var styles = $scope.parseBackgroundStyles();
				element.css( styles );
				$log.debug( "pwBackground › applyBackgroundStyles", styles );
			}

			$scope.getImageId = function(){
				return $_.get( $scope.pwBackground, 'image.id' );;
			}

			$scope.getImagePost = function(){
				// Get the image ID
				var imageId = $scope.getImageId();
				// If no image ID, return here
				if( !imageId )
					return false;
				// Setup imagePost variable
				var imagePost = false;
				// If there is an image ID, check if that post exists in the $pwData.posts[id]
				if( imageId ){
					// Check if the post exists
					imagePost = $_.get( $pwData, 'posts.'+imageId );
				}
				return imagePost;
			}

			$scope.getImageUrl = function(){
				// TODO : Select the approriate size depending on screen size
				// Check if there is an image post
				var imagePost = $scope.getImagePost();
				if( !imagePost )
					return false;
				// Get the image url
				return $_.get( imagePost, 'image.sizes.full.url' );
			}

			$scope.populateImagePost = function( imageId ){
				var get_post_vars = {
					post_id: imageId,
					fields: ['ID', 'image(all)'],
				};
				$pwData.pw_get_post(get_post_vars).then(
					function(response){
						$pwData.posts[imageId] = response.data;
						$log.debug( "pwBackground › populateImagePost › $pwData.posts ", $pwData.posts );
						$scope.updateBackground();
					},
					function(response){}
				);
			}

			$scope.parseBackgroundStyles = function(){
				// Define styles
				var styles = angular.fromJson( angular.toJson( $_.get( $scope.pwBackground, 'style' ) ) ); 
				if( !styles )
					styles = {};

				
				// Delete empty values
				angular.forEach( styles, function( value, key ){
					// Opacity 
					if( key == 'opacity' )
						styles[key] = value/100; 
					// Size
					if( key == 'background-size' )
						if( is_numeric( value ) )
							styles[key] = value + '%'; 
				});
				
				// Get image URL
				var backgroundImageUrl = $scope.getImageUrl(); 
				if( backgroundImageUrl )
					styles['background-image'] = 'url("'+ backgroundImageUrl +'")';
				else
					styles['background-image'] = 'none';

				return styles;
			}

		}
	};
}]);
/*
postworld.controller( 'pwBackgroundCtrl',
	[ '$scope', '$window', '$timeout', 'pwData', '$log', '_',
	function( $scope, $window, $timeout, $pwData, $log, $_ ) {
}]);*/