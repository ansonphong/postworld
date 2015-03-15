///// GALLERY VIEWER /////
postworld.directive( 'pwGalleryViewer',
	[ 'pwData', '$pw', '$log', '_', '$document', '$timeout',
	function( $pwData, $pw, $log, $_, $document, $timeout ){
	return {
		restrict: 'AE',
		link: function( $scope, element, attrs ){

			/*
				Additonal attributes:
					gallery-posts="post.gallery.posts"
						- Scope expression resulting in the gallery posts
					gallery-keybind="true/false"
						- Enables previous/next keystrokes to switch image
			*/

			$scope.gallery = {
				index:0,
				count:0,
				posts:[],
			};	

			///// WATCH : INCOMING GALLERY POSTS /////
			$scope.$watch(
				function(){
					if( attrs.galleryPosts === null )
						return [];
					var galleryPosts = $scope.$eval( attrs.galleryPosts );
					return galleryPosts;
				},
				function( galleryPosts, oldGalleryPosts ){
					$log.debug( 'pwGalleryViewer : oldGalleryPosts', oldGalleryPosts );
					if( !$_.objEquality( galleryPosts, oldGalleryPosts ) || _.isEmpty( $scope.gallery.posts ) ){ // && _.isArray(galleryPosts)
						$scope.gallery.posts = galleryPosts;
					}
				}
			,1);

			///// WATCH : GALLERY /////
			$scope.$watch( 'gallery', function( gallery, oldGallery ){
				if( $_.get( gallery, 'index' ) < 0 )
					$scope.gallery.index = 0;

				// If posts is an array, get the count
				if( _.isArray( gallery.posts ) ){
					var count = gallery.posts.length;
					$log.debug( 'gallery.posts.count : CHANGE : ' + count, gallery.posts );
					$scope.gallery.count = count;
				}
				
				// If posts switched, reset index to 0
				if( !$_.objEquality( gallery.posts, oldGallery.posts ) )
					$scope.gallery.index = 0;

			},1);

			///// WATCH : GALLERY POSTS /////
			$scope.$watch( 'gallery.posts', function(posts, oldPosts){
				$log.debug( 'gallery.posts : CHANGE : ', posts );
			},1);

			$scope.galleryLoaded = function(){
				return ( $scope.gallery.posts.length > 0 );
			}

			$scope.nextImage = function(){
				var gallery = $scope.gallery;
				if( gallery.index < gallery.count - 1 )
					$scope.gallery.index ++;
				else
					$scope.gallery.index = 0;
			}

			$scope.previousImage = function(){
				var gallery = $scope.gallery;
				if( gallery.index <= 0 )
					$scope.gallery.index = gallery.count-1;
				else
					$scope.gallery.index --;
			}

			$scope.gotoIndex = function(index){
				$scope.gallery.index = index;
			}

			$scope.getImageIndex = function(imagePost){
				var imagePostId = $_.get( imagePost, 'ID');
				var index = _.findIndex( $scope.gallery.posts, function( post ){ return post.ID === imagePostId } );
				return index;

				/*

				var imagePostId = $_.get( imagePost, 'ID');
				var posts = $scope.gallery.posts;

				$log.debug( 'gallery : getImageIndex : PRE-IF : POSTS : ', posts);
				if( !_.isEmpty(posts) && imagePostId !== false ){

					$log.debug( 'gallery : getImageIndex : PRE-FOR : posts.length ', posts.length );

					for( var i = 0; i < posts.length; i++ ){
						$log.debug( 'gallery : getImageIndex : POST ID : ', posts[i].ID );
						if( posts[i].ID == imagePostId ){
							$log.debug( 'gallery : getImageIndex : ', i );
							return i;
						}
					}
				}

				$log.debug( 'gallery : getImageIndex : ERROR : ', -1 );
				return -1;

				/*
				$log.debug( 'gallery : getImageIndex : ' + index, imagePost.ID );
				$log.debug( 'gallery : getImageIndex : POSTS : ' + index, $scope.gallery.posts );
				return index;
				*/
			}

			$scope.gotoImage = function(imagePost){
				$scope.gallery.index = $scope.getImageIndex(imagePost);
			}

			$scope.imageIsSelected = function(imagePost){
				if( $scope.getImageIndex(imagePost) == $scope.gallery.index )
					return 'selected';
			}

			$scope.thumbnailImageBg = function( imagePost, imageSize ){
				var url = $_.get( imagePost, 'image.sizes.' + imageSize + '.url' );
				return {
					'background-image': 'url('+ url +')',
				}
			}

			$scope.selectedImage = function(){
				var posts = $_.get( $scope, 'gallery.posts' );
				if( _.isArray( posts ) )
					return posts[ $scope.gallery.index ];
				else
					return {};
			}

			$scope.keyDown = function( e ){
				var keyCode = parseInt( e.keyCode );
				switch( keyCode ){
					// Right Key
					case 39:
						$log.debug( "keyDown: nextImage" );
						$scope.nextImage();
						break;
					// Left Key
					case 37:
						$log.debug( "keyDown: previousImage" );
						$scope.previousImage();
						break;
				}
				$scope.$apply();
			}

			// Enable key bindings
			attrs.$observe( 'galleryKeybind', function(val){
				if( val === null )
					return false;
				var bool = $_.stringToBool( val );
				if( bool ){
					$document.keydown( function( e ){
					$scope.keyDown( e );
				});
				}
			});

			$scope.trackingPosts = function( imagePost ){
				// Use in ng-repeat to avoid carry-over artifacts in modal viewer
				// ng-repeat="imagePost in gallery.posts track by trackingPosts(imagePost)"
				return imagePost.ID;
			}


		}
	};
}]);


postworld.directive( 'pwInfiniteGallery', [ function( $scope ){
	return {
		restrict: 'AE',
		controller: 'pwInfiniteGalleryCtrl',
		link: function( $scope, element, attrs ){

			// Gallery Preload
			attrs.$observe('galleryPreload', function( value ) {
				if( !_.isUndefined( value ) )
					$scope.galleryPreload = parseInt(value);
				else
					$scope.galleryPreload = 3; // Default Preload
			});

		}
	};
}]);


postworld.controller( 'pwInfiniteGalleryCtrl',
	[ '$scope', '$log', '_', '$pw', 'pwData', 'pwPosts',
	function( $scope, $log, $_, $pw, $pwData, $pwPosts ){

	$scope.infiniteGallery = {
		posts:[],		// All the posts from the post gallery object
		displayed:[],	// All the posts which are actually displayed
	};

	///// WATCH : WHEN THE POST CHANGES /////
	$scope.$watchCollection( '[ post.ID, post.gallery ]', function(){

		$log.debug( "::::: POST CHANGED :::::" );

		// IF POST HAS GALLERY
		if( !_.isEmpty( $_.getObj( $scope, 'post.gallery.posts' ) ) ){

			// Establish the local posts object as the gallery
			$scope.infiniteGallery.posts = $scope.post.gallery.posts;
			
			// Clear the displayed posts, in case switching from another gallery
			$scope.infiniteGallery['displayed'] = [];

			// Preload Posts
			if( $scope.galleryDisplayedCount() == 0 ){
				$scope.galleryGetNext( $scope.galleryPreload );
			}

			///// INSERT FEED /////
			// Generate feed instance name
			var galleryInstance = 'gallery-' + $scope.post.ID;
			// Set instance name into the scope so it's accessible in the DOM
			$scope.infiniteGallery.instance = galleryInstance;
			// Insert the feed into the pwData.feeds
			$pwPosts.insertFeed( galleryInstance, { posts: $scope.infiniteGallery.posts } );
			// Log in Console
			$log.debug( "INSERTED FEED : " + galleryInstance, $scope.infiniteGallery.posts );


		// IF POST HAS NO GALLERY
		} else{
			$scope.infiniteGallery['posts'] = [];
			$scope.infiniteGallery['displayed'] = [];
		}
	}, 1 );

	$scope.galleryPostCount = function(){
		return ( $_.objExists( $scope, 'infiniteGallery.posts.length' ) ) ?
			$scope.infiniteGallery.posts.length : 0;
	}

	$scope.galleryDisplayedCount = function(){
		return $scope.infiniteGallery.displayed.length;
	}

	$scope.galleryPosts = function(){
		return ( $_.objExists( $scope, 'infiniteGallery.posts.length' ) ) ?
			$scope.infiniteGallery.posts : []; 
	}

	$scope.galleryDisplayedPosts = function(){
		return $scope.infiniteGallery.displayed;
	}

	$scope.addDisplayedPosts = function( posts ){
		// Append posts to the displayed posts
		angular.forEach( posts, function( post ){
			$scope.infiniteGallery.displayed.push( post );
		} );
	};

	$scope.galleryGetNext = function( getPostsCount ){
		// Get next image(s) in infinite scroll

		// Cast Get Posts Count as integer
		getPostsCount = parseInt( getPostsCount );

		// Get the number of total posts
		var galleryPostCount = $scope.galleryPostCount();

		// Get the number of posts already displayed
		var galleryDisplayedCount = $scope.galleryDisplayedCount();

		// Get the Start Index of the slice
		var postsStartIndex = galleryDisplayedCount;

		// Get the End Index of the slice
		var postsEndIndex = postsStartIndex + getPostsCount;

		// Cap the end index at the number of actual items in the array
		if( postsEndIndex >=  galleryPostCount )
			postsEndIndex = galleryPostCount;

		// Slice the posts from the set range
		var addPosts = $scope.galleryPosts().slice( postsStartIndex, postsEndIndex );
		
		// Add them to the array of displayed posts
		$scope.addDisplayedPosts( addPosts );

		// Console
		/*
		$log.debug(
			"pwInfiniteGallery.galleryGetNext : " +
			"getPostsCount: " + getPostsCount + " // " + 
			"galleryPostCount: " + galleryPostCount + " // " + 
			"galleryDisplayedCount: " + galleryDisplayedCount + " // " + 
			"postsStartIndex: " + postsStartIndex + " // " + 
			"postsEndIndex: " + postsEndIndex + " // " + 
			"addPosts: ", addPosts
			);
		*/
	};

}]);



///// INFINITE HORIZONTAL SCROLL /////
/* BASED ON : ng-infinite-scroll - v1.0.0 - 2013-05-13 */

postworld.directive('infiniteXScroll', [
	'$rootScope', '$window', '$timeout', '$log', function($rootScope, $window, $timeout, $log) {
		return {
			link: function(scope, elem, attrs) {
				var checkWhenEnabled, container, handler, scrollDistance, scrollEnabled;
				$window = angular.element($window);
				scrollDistance = 0;
				if (attrs.scrollDistance != null) {
					scope.$watch(attrs.scrollDistance, function(value) {
						return scrollDistance = parseInt(value, 10);
					});
				}
				scrollEnabled = true;
				checkWhenEnabled = false;
				if (attrs.scrollDisabled != null) {
					scope.$watch(attrs.scrollDisabled, function(value) {
						scrollEnabled = !value;
						if (scrollEnabled && checkWhenEnabled) {
							checkWhenEnabled = false;
							return handler();
						}
					});
				}
				container = $window;
				/*
				if (attrs.infiniteScrollContainer != null) {
					scope.$watch(attrs.infiniteScrollContainer, function(value) {
						value = angular.element(value);
						if (value != null) {
							return container = value;
						} else {
							throw new Exception("invalid infinite-scroll-container attribute.");
						}
					});
				}
				*/
				if (attrs.scrollParent != null) {
					container = elem.parent();
					scope.$watch(attrs.scrollParent, function() {
						return container = elem.parent();
					});
				}
				handler = function() {
					var containerBottom, elementBottom, remaining, shouldScroll;
					/*
					///// REMNANTS FROM Y-SCROLL /////
					if (container[0] === $window[0]) {
						containerBottom = container.height() + container.scrollTop();
						elementBottom = elem.offset().top + elem.height();
					} else {
					
					$log.debug(
							'container.scrollTop(): ' + container.scrollTop() + ' / ' +
							'container.scrollLeft(): ' + container.scrollLeft() + ' / ' +
							'container.innerHeight(): ' + container.innerHeight() + ' / ' +
							'container.innerWidth(): ' + container.innerWidth() + ' / ' +
							'container[0].scrollWidth: ' + container[0].scrollWidth + ' / ' +
							'scrollDistance: ' + scrollDistance + ' / ' +
							'remaining: ' + remaining
							//, container
						);
						containerBottom = container.height();
						elementBottom = elem.offset().top - container.offset().top + elem.height();
					}
					*/
					//remaining = elementBottom - containerBottom;
					// Vertical Scrolling
					//shouldScroll =  remaining <= container.height() * scrollDistance;
					// Horizontal Scrolling
					remaining = container[0].scrollWidth - (container.scrollLeft() + container.innerWidth() + scrollDistance);
					shouldScroll =  0 >= remaining;

					/*
						////////// DEV //////////
						$log.debug(
							'container.scrollTop(): ' + container.scrollTop() + ' / ' +
							'container.scrollLeft(): ' + container.scrollLeft() + ' / ' +
							'container.innerHeight(): ' + container.innerHeight() + ' / ' +
							'container.innerWidth(): ' + container.innerWidth() + ' / ' +
							'container[0].scrollWidth: ' + container[0].scrollWidth + ' / ' +
							'scrollDistance: ' + scrollDistance + ' / ' +
							'remaining: ' + remaining
							//, container
						);

						$log.debug(
							'elementBottom: ' + elementBottom + ' / ' +
							'containerBottom: ' + containerBottom
						);

						$log.debug(
							'shouldScroll: ' + shouldScroll + ' / ' +
							'remaining: ' + remaining + ' / ' +
							'container.height(): ' + container.height() + ' / ' +
							'scrollDistance: ' + scrollDistance
						);
					*/

					if (shouldScroll && scrollEnabled) {
						//$log.debug("X-SCROLL : CALL SCROLL ACTION");
						if ($rootScope.$$phase) {
							return scope.$eval(attrs.scrollAction);
						} else {
							return scope.$apply(attrs.scrollAction);
						}
					} else if (shouldScroll) {
						return checkWhenEnabled = true;
					}

				};
				container.on('scroll', handler);
				scope.$on('$destroy', function() {
					return container.off('scroll', handler);
				});
				return $timeout((function() {
					if (attrs.infiniteScrollImmediateCheck) {
						if (scope.$eval(attrs.infiniteScrollImmediateCheck)) {
							return handler();
						}
					} else {
						return handler();
					}
				}), 0);
			}
		};
	}
]);




///// INFINITE VERTICAL SCROLL /////
/* BASED ON : ng-infinite-scroll - v1.0.0 - 2013-05-13 */

postworld.directive('infiniteYScroll', [
	'$rootScope', '$window', '$timeout', '$log', '_', function( $rootScope, $window, $timeout, $log, $_ ) {
		return {
			link: function(scope, elem, attrs) {

				var checkWhenEnabled, container, handler, scrollDistance, scrollEnabled;
				$window = angular.element($window);
				scrollDistance = 0;
				if (attrs.scrollDistance != null) {
					scope.$watch(attrs.scrollDistance, function(value) {
						return scrollDistance = parseInt(value, 10);
					});
				}
				scrollEnabled = true;
				checkWhenEnabled = false;
				if (attrs.scrollDisabled != null) {
					scope.$watch(attrs.scrollDisabled, function(value) {
						scrollEnabled = !value;
						if (scrollEnabled && checkWhenEnabled) {
							checkWhenEnabled = false;
							return handler();
						}
					});
				}

				container = $window;

				if ( attrs.scrollContainer != null && typeof attrs.scrollContainer !== 'undefined' ) {
					var value = String( attrs.scrollContainer );
					container = angular.element( attrs.scrollContainer );
					//$log.debug( "<<<<< attrs.scrollContainer : element >>>>> ", container );
					/*
					// Throwing an Error Somehow
					scope.$watch(attrs.scrollContainer, function(value) {
						value = angular.element( String( attrs.scrollContainer ) );
						if ( value != null ) {
							return container = value;
						} else {
							throw new Exception("invalid infinite-scroll-container attribute.");
						}
					});
					*/
				}
				
				if (attrs.scrollParent != null) {
					container = elem.parent();
					scope.$watch(attrs.scrollParent, function() {
						return container = elem.parent();
					});
				}

				//$log.debug( "SCROLL CONTAINER", container );

				handler = function() {
					var containerBottom, elementBottom, remaining, shouldScroll;
					
					if (container[0] === $window[0]) {
						containerBottom = container.height() + container.scrollTop();
						elementBottom = elem.offset().top + elem.height();
					} else {
						containerBottom = container.height();
						elementBottom = elem.offset().top - container.offset().top + elem.height();
					}
					remaining = elementBottom - containerBottom;
					shouldScroll = ( remaining <= scrollDistance );
					
					/*
					////////// DEV //////////
					$log.debug(
						'container.scrollTop(): ' + container.scrollTop() + ' / ' +
						'container.scrollLeft(): ' + container.scrollLeft() + ' / ' +
						'container.innerHeight(): ' + container.innerHeight() + ' / ' +
						'container.innerWidth(): ' + container.innerWidth() + ' / ' +
						'container[0].scrollHeight: ' + container[0].scrollHeight + ' / ' +
						'scrollDistance: ' + scrollDistance + ' / ' +
						'remaining: ' + remaining
						, container
					);
					$log.debug("SCROLLING");
					$log.debug(
						'elementBottom: ' + elementBottom + ' / ' +
						'containerBottom: ' + containerBottom + ' / ' + 
						'remaining : ' + remaining
					);
					
					$log.debug(
						'shouldScroll: ' + shouldScroll + ' / ' +
						'remaining: ' + remaining + ' / ' +
						'container.height(): ' + container.height() + ' / ' +
						'scrollDistance: ' + scrollDistance
					);
					*/
					
					if (shouldScroll && scrollEnabled) {
						//$log.debug("Y-SCROLL : CALL SCROLL ACTION");
						if ($rootScope.$$phase) {
							return scope.$eval(attrs.scrollAction);
						} else {
							return scope.$apply(attrs.scrollAction);
						}
					} else if (shouldScroll) {
						return checkWhenEnabled = true;
					}
				};

				container.on('scroll', handler);
				//$log.debug( 'infinite-y-scroll : container >>>>> ', container );			

				scope.$on('$destroy', function() {
					return container.off('scroll', handler);
				});
				return $timeout((function() {
					if (attrs.scrollImmediateCheck) {
						if (scope.$eval(attrs.scrollImmediateCheck)) {
							return handler();
						}
					} else {
						return handler();
					}
				}), 0);
			}
		};
	}
]);



postworld.directive('infiniteScroll', [
	'$rootScope', '$window', '$timeout', '$log', '_', function($rootScope, $window, $timeout, $log, $_) {
		return {
			link: function(scope, elem, attrs) {
				var checkWhenEnabled, container, handler, scrollDistance, scrollEnabled;
				$window = angular.element($window);
				scrollDistance = 0;
				if (attrs.infiniteScrollDistance != null) {
					scope.$watch(attrs.infiniteScrollDistance, function(value) {
						return scrollDistance = parseInt(value, 10);
					});
				}
				scrollEnabled = true;
				checkWhenEnabled = false;
				if (attrs.infiniteScrollDisabled != null) {
					scope.$watch(attrs.infiniteScrollDisabled, function(value) {
						scrollEnabled = !value;
						if (scrollEnabled && checkWhenEnabled) {
							checkWhenEnabled = false;
							return handler();
						}
					});
				}
				container = $window;
				if (attrs.infiniteScrollContainer != null) {
					scope.$watch(attrs.infiniteScrollContainer, function(value) {
						value = angular.element(value);
						if (value != null) {
							return container = value;
						} else {
							throw new Exception("invalid infinite-scroll-container attribute.");
						}
					});
				}
				if (attrs.infiniteScrollParent != null) {
					container = elem.parent();
					scope.$watch(attrs.infiniteScrollParent, function() {
						return container = elem.parent();
					});
				}
				handler = function() {
					//$log.debug('infinite-scroll handler');
					var containerBottom, elementBottom, remaining, shouldScroll;
					if (container[0] === $window[0]) {
						containerBottom = container.height() + container.scrollTop();
						elementBottom = elem.offset().top + elem.height();
					} else {
						containerBottom = container.height();
						elementBottom = elem.offset().top - container.offset().top + elem.height();
					}
					remaining = elementBottom - containerBottom;
					shouldScroll = remaining <= container.height() * scrollDistance;
					
					//console.log('elementBottom,containerBottom',elementBottom,containerBottom);
					//console.log('scrolling shouldScroll,remaining, container.height(),scrollDistance',shouldScroll,remaining, container.height(),scrollDistance);
					


					if (shouldScroll && scrollEnabled) {
						if ($rootScope.$$phase) {
							return scope.$eval(attrs.infiniteScroll);
						} else {
							return scope.$apply(attrs.infiniteScroll);
						}
					} else if (shouldScroll) {
						return checkWhenEnabled = true;
					}

				};
				container.on('scroll', handler );
				scope.$on('$destroy', function() {
					return container.off('scroll', handler);
				});
				return $timeout((function() {
					if (attrs.infiniteScrollImmediateCheck) {
						if (scope.$eval(attrs.infiniteScrollImmediateCheck)) {
							return handler();
						}
					} else {
						return handler();
					}
				}), 0);
			}
		};
	}
]);




