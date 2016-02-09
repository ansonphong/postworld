'use strict';
/*              ____             _                                   _ 
  _ ____      _| __ )  __ _  ___| | ____ _ _ __ ___  _   _ _ __   __| |
 | '_ \ \ /\ / /  _ \ / _` |/ __| |/ / _` | '__/ _ \| | | | '_ \ / _` |
 | |_) \ V  V /| |_) | (_| | (__|   < (_| | | | (_) | |_| | | | | (_| |
 | .__/ \_/\_/ |____/ \__,_|\___|_|\_\__, |_|  \___/ \__,_|_| |_|\__,_|
 |_|                                 |___/                             
 ///////////////////////// LOAD BACKGROUND DIRECTIVE ////////////////////////*/

/*
 * Easily sets and modifies the background of the page 
 *
 * @class pwBackground
 * @param {String} pwBackground A Postworld background object ID
 */

postworld.directive( 'pwBackground',
 	[ '$window', '$timeout', '$pwData', 'pwPosts', '$log', '$_',
 	function( $window, $timeout, $pwData, $pwPosts, $log, $_ ){
	return {
		restrict: 'AE',
		//controller: 'pwBackgroundCtrl',
		scope: {
			pwBackground:'@',
		},
		link: function( $scope, element, attrs ){

			//////////// WATCH ////////////
			$scope.$watch('pwBackground', function( val ){
				$scope.updateBackground( val );
			}, 1 );

			$scope.getBackgroundObj = function(){
				if( $scope.pwBackground == 'primary' ){
					//$log.debug( ">>> PRIMARY BG <<< ", $pwData.background.primary );
					return $pwData.background.primary;
				}
				else if( $scope.pwBackground == 'secondary' )
					return $pwData.background.secondary;
				else
					return $scope.$eval( $scope.pwBackground );
			}

			//////////// UPDATE ////////////
			$scope.updateBackground = function(){

				// Get the background Object
				$scope.backgroundObj = $scope.getBackgroundObj();
			
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

				// Bind / Unbind parallax
				$scope.bindParallax();

			}

			$scope.bindParallax = function(){

				// Check for Parallax values
				var parallax = $_.get( $scope.backgroundObj, 'image.parallax' );
				var position = $_.get( $scope.backgroundObj, 'style.background-position' );
				// If parallax is present
				if( is_numeric( parallax ) && parallax != 0 && position == 'parallax' ){
					// Bind window scrolling to update the position
					angular.element($window).on("scroll", setPosition);
					// Init Parallax
					setPosition();
				}
				else{
					// Unbind window scrolling
					angular.element($window).off("scroll", setPosition);
					// Clear Parallax
					//element.css('background-position', 'initial');
				}
			}

			$scope.applyBackgroundStyles = function(){
				var styles = $scope.parseBackgroundStyles();
				element.css( styles );
				$log.debug( "backgroundObj › applyBackgroundStyles", styles );
			}

			$scope.getImageId = function(){
				return $_.get( $scope.backgroundObj, 'image.id' );;
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
				$log.debug( '$scope.getImagePost', imagePost );
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
				var vars = {
					post_id: imageId,
					fields: [ 'ID', 'post_type', 'image(full)', 'fields' ],
				};
				$pwData.getPost( vars ).then(
					function(response){
						$pwData.posts[imageId] = response.data;
						$log.debug( "backgroundObj › populateImagePost › $pwData.posts ", $pwData.posts );
						$scope.updateBackground();
					},
					function(response){}
				);
			}

			$scope.parseBackgroundStyles = function(){
				// Define styles
				var styles = angular.fromJson( angular.toJson( $_.get( $scope.backgroundObj, 'style' ) ) ); 
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
					// Position
					//if( key == 'background-position' )
					//	if( value == 'parallax' )
					//		delete styles[key]; 
				});
				
				// Get image URL
				var backgroundImageUrl = $scope.getImageUrl(); 
				if( backgroundImageUrl )
					styles['background-image'] = 'url("'+ backgroundImageUrl +'")';
				else
					styles['background-image'] = 'none';

				return styles;
			}

			
			//////////// PARALLAX ////////////
			// Cache the page Y offset
			$scope.pageYOffset = 0;
			var setPosition = function () {

				// Disable Parallax if it's not enabled
				if( $_.get( $scope.backgroundObj, 'style.background-position' ) != 'parallax' )
					return false;

				// Get the current Page Y Offset
				var pageYOffset = $window.pageYOffset;

				// If Offset is the same as the cached value, stop here
				if( pageYOffset == $scope.pageYOffset )
					return false;

				// Cache the Page Y Offset
				$scope.pageYOffset = pageYOffset;

				// Get parallax ratio
				var parallaxRatio = $_.get( $scope.backgroundObj, 'image.parallax' );
				if( !parallaxRatio || !is_numeric( parallaxRatio ) )
					return false;
				parallaxRatio = Number(parallaxRatio);

				var calcValY = ( element.prop('offsetTop') - pageYOffset ) * parallaxRatio;
				calcValY = parseInt(calcValY);
				// horizontal positioning
				element.css('background-position', "50% " + calcValY + "px");

			};

			// Set initial position - fixes webkit background render bug
			angular.element($window).bind('load', function(e) {
				setPosition();
				$scope.$apply();
			});

		}
	};
}]);
/*
postworld.controller( 'pwBackgroundCtrl',
	[ '$scope', '$window', '$timeout', '$pwData', '$log', '$_',
	function( $scope, $window, $timeout, $pwData, $log, $_ ) {
}]);*/