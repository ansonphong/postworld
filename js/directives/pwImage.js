/*                _                    _   ___                            
  _ ____      __ | |    ___   __ _  __| | |_ _|_ __ ___   __ _  __ _  ___ 
 | '_ \ \ /\ / / | |   / _ \ / _` |/ _` |  | || '_ ` _ \ / _` |/ _` |/ _ \
 | |_) \ V  V /  | |__| (_) | (_| | (_| |  | || | | | | | (_| | (_| |  __/
 | .__/ \_/\_/   |_____\___/ \__,_|\__,_| |___|_| |_| |_|\__,_|\__, |\___|
 |_|                                                           |___/      
 ///////////////////////// LOAD IMAGE DIRECTIVE ////////////////////////*/

 postworld.directive( 'pwImage', [ function($scope){
	return {
		restrict: 'AE',
		controller: 'pwImageCtrl',
		scope: {
			//userQuery:'@userQuery', // INOP
			imageId:'@imageId',
			imageModel:'=imageModel',
		},
		link: function( $scope, element, attrs ){
			$scope.imageId = parseInt($scope.imageId);

			// OBSERVE Attribute
			attrs.$observe('imageId', function(value) {
				$scope.getImage($scope.imageId);
			});
			
		}
	};
}]);


postworld.controller( 'pwImageCtrl',
	[ '$scope', '$window', '$timeout', 'pwData', '$log',
	function( $scope, $window, $timeout, $pwData, $log ) {

	$scope.getImage = function( imageId ){
		
		// If the value is empty
		if( _.isEmpty(imageId) ){
			$scope.imageModel = {};
			return false;
		}

		var args = {
			'image_id': imageId,
			//'return_fields': ['ID','image(all)'],
			//'return': 'image( large, 300, 300, true )' // id / all / ID of registeded image size / parameters of image - passed to pw_get_post 
		};

		$pwData.get_image( args ).then(
			// Success
			function(response) {    
				$scope.imageModel = response.data;
			},
			// Failure
			function(response) {
				//$scope.movements = [{post_title:"Movements not loaded.", ID:"0"}];
			}
		);

	};

}]);




/**
 * @ngdoc directive
 * @name postworld.directive:pwImageSrc
 * @description
 * Shows the correct image based on the current device pixel ratio.
 *
 * @param Object pwImageSrc An object
 *
 * @example
 * 		<img pw-image-src="{'1':'image.jpg','2':'image-hi-res.jpg'}">
 *
 */
postworld.directive('pwImageSrc',
	[ '$pw', '$log', '_', '$window', '$filter',
	function( $pw, $log, $_, $window, $filter ) {
	return {
		restrict:'A',
		scope:{
			pwImageSrc:'='
		},
		link: function( $scope, element, attrs ) {
			var srcs = $scope.pwImageSrc;// $scope.$eval( $scope.pwImageSrc );

			if( _.isEmpty(srcs) )
				return false;

			//$log.debug( 'pwImageSrc', srcs );
			//$log.debug( 'devicePixelRatio', $window.devicePixelRatio );

			/**
			 * Reconstruct the input sources
			 * As an array of objects.
			 */
			var orderSrcs = function( srcs ){
				// Placeholder array
				var srcsArr = [];
				// Construct array
				angular.forEach( srcs, function( value, key ){
					srcsArr.push({
						pixelRatio: parseFloat(key),
						src: value
					});
				});
				// Order the array by the pixel ratio
				srcsArr = $filter('orderBy')( srcsArr, 'pixelRatio', true );
				return srcsArr;
			}

			/**
			 * Using the current device's pixel ratio
			 * get the appropriate image source.
			 */
			var selectSrc = function( options ){
				// Placeholder for the chosen source
				var src = '';
				// The current device's pixel ratio
				var devicePixelRatio = $window.devicePixelRatio;				
				// Iterate through source options
				angular.forEach( options, function( option ){
					/**
					 * If the option's ratio is greater than
					 * Or equal to the current device pixel ratio
					 * Then go ahead and use that ratio.
					 */ 
					if( option.pixelRatio >= devicePixelRatio  )
						src = option.src;
				});
				/**
				 * If no source has been found because the device
				 * has a larger pixel ratio than what is available
				 * then select the highest available ratio.
				 */
				if( _.isEmpty( src ) )
					src = options[0].src;
				return src;
			}

			/**
			 * Set the actual element's src value.
			 */
			var setImageSrc = function(){
				var src = selectSrc( orderSrcs( srcs ) );
				src = $pw.injectVariables(src);
				element.attr( 'src', src );
			}

			setImageSrc();

			/**
			 * If the src-update attribute is present
			 * Watch for a change of device pixel ratio
			 * Such as in a zoom in, and update the source.
			 */
			/*
			if( !_.isUndefined( attrs.srcUpdate ) )
				angular.element($window).bind("resize", elemClasses);

				$scope.$watch(
					// ISSUE : This isn't parsing?
					function(){ return $window.devicePixelRatio; },
					function( val ){
						$log.debug( 'changing' );
						setImageSrc();
					}
				);
			else
				setImageSrc();
			*/
				
		}
	}
}]);


/**
 * @ngdoc directive
 * @name postworld.directive:pwSmartImage
 * @description
 * Intelligently selects the right image from the image object
 * Based on the height and width of the element.
 *
 * @param Object pwSmartImage A Postworld post image object
 * @param Object smartImageOverride An override to use instead if it's found
 * @param none smartImageDynamic If this attribute is present, update the image when the source or screen change. May cause performance issues if many images use this
 * @param string smartImagePriority Optional. Options: height|width Priority is given to the defined dimension when deciding on image size
 * @param integer smartImageMinWidth Optional. Number of pixels of minimum width of image to select.
 * @param integer smartImageMinHeight Optional. Number of pixels of minimum height of image to select.
 *
 * @example
 * 		<img pw-smart-image="post.image" smart-image-override="post.image.alt">
 *
 */
postworld.directive('pwSmartImage',
	[ '$pw', '$log', '_', '$window', '$filter', 'pwImages', '$timeout',
	function( $pw, $log, $_, $window, $filter, $pwImages, $timeout ) {
	return {
		restrict:'A',
		link: function( $scope, element, attrs ) { 

			var getImgUrl = function(){

				var devicePixelRatio = $window.devicePixelRatio;
				var elementWidth = element[0].offsetWidth * devicePixelRatio;
				var elementHeight = element[0].offsetHeight * devicePixelRatio;
				
				// Minimum Width
				if( !_.isUndefined( attrs.smartImageMinWidth ) )
					elementWidth = parseInt( attrs.smartImageMinWidth );
				
				// Minimum Height
				if( !_.isUndefined( attrs.smartImageMinHeight ) )
					elementHeight = parseInt( attrs.smartImageMinHeight );

				// Show debug
				if( !_.isUndefined( attrs.smartImageShowDebug ) ){
					$log.debug( 'pwSmartImage : element', element[0] );
					$log.debug( 'pwSmartImage : elementWidth', elementWidth );
					$log.debug( 'pwSmartImage : elementHeight', elementHeight );
				}
				
				// Get the image object from provided expression
				var imageObj = $scope.$eval( attrs.pwSmartImage );

				// If an override is provided
				if( !_.isUndefined( attrs.smartImageOverride ) ){
					var overrideImageObj = $scope.$eval( attrs.smartImageOverride );
					if( !_.isUndefined( overrideImageObj ) ){
						imageObj = overrideImageObj;
					}
				}

				// Select priority if provided
				var priority = !_.isUndefined( attrs.smartImagePriority ) ?
					attrs.smartImagePriority : false;
		
				// Get image sizes from the 'sizes' subobject
				var imageSizes = $_.get( imageObj, 'sizes' );

				// If it doesn't exist, assume we already have a sizes array
				if( !_.isObject(imageSizes) && _.isObject(imageObj) )
					imageSizes = imageObj;

				// Get the correctly sized image
				if( _.isObject( imageSizes ) ){
					var image = $pwImages.selectImageSize(
						imageSizes,
						{
							width: elementWidth,
							height: elementHeight,
							priority: priority
						}
					);

					return image.url;
				}
				else{
					return '';
				}

			}

			function setImgUrl( imgUrl ){
				// Detect what type of element
				var elementTag = element[0].tagName;

				if( imgUrl == null ){
					var imgUrl = getImgUrl();
					if( _.isEmpty( imgUrl ) )
						return false;
				}
				
				if( elementTag === 'IMG' )
					element.attr( 'src', imgUrl );
				else
					element.css( 'background-image', 'url('+imgUrl+')' );
			}

			// Timeout for DOM to initialize
			$timeout( function(){
				setImgUrl();		
			}, 0 );


			/**
			 * If the smartImageDynamic attribute is present
			 * Watch the element dimensions as well as the
			 * Image object itself for changes.
			 */
			if( !_.isUndefined( attrs.smartImageDynamic ) ){

				/**
				 * If the width or height of the element changes
				 * Re-evaluate which image is being used.
				 */
				$scope.$watch(
					function(){
						var elementWidth = element[0].offsetWidth;
						//$log.debug( 'elementWidth', elementWidth );
						var elementHeight = element[0].offsetHeight;
						//$log.debug( 'elementHeight', elementHeight );
						return {
							width: elementWidth,
							height: elementHeight
						};
					},
					function(val,oldVal){
						//$log.debug( 'pwsmartImage : DYNAMIC', val );
						//$log.debug( 'pwsmartImage : getImgUrl', getImgUrl() );

						// Don't re-evaluate if the height/width is 0
						if( val.width !== 0 && val.height !== 0 ){
							//$log.debug( 'val', val );
							// Timeout for DOM to initialize
							//setImgUrl();

							$timeout( function(){
								setImgUrl();
							}, 0 );

						}
					}, 1
				);

				/**
				 * Watch for image object changes
				 * Then re-set the image URL.
				 */
				$scope.$watch(
					function(){
						return $scope.$eval( attrs.pwSmartImage )
					},
					function(val){
						/**
						 * Clear image URL, and then timeout,
						 * Waiting 0ms for potential DOM changes to initialize.
						 */
						setImgUrl('');
						$timeout( function(){
							setImgUrl();		
						}, 0 );
					}
				);

			}
			
		}

	}

}]);



/**
 * @ngdoc directive
 * @name postworld.directive:pwParallax
 * @description
 * Parallax an image.
 *
 * @param {string} pwParallax (Optional) The method to compute the parallax. Options: parent  
 *
 */
postworld.directive('pwParallax',
	['$log','$timeout','$window', '_',function($log, $timeout, $window, $_){
	return{
		restrict:'A',
		scope:{
			parallaxEnable: "=",
		},
		link:function( $scope, element, attrs ){

			var	prevY = 0,
      			curY = 0,
      			depth = 1.2,
      			parentHeight = 0,
      			elementHeight = 0,
      			median = 0,
      			inView = 0,
      			inViewMedian = 0,
      			method = 'parent',
      			el = element[0];

      		var prefixed = {
				transform: $_.getSupportedProp(['transform', 'msTransform', 'webkitTransform', 'mozTransform', 'oTransform'])
			};

			var enable = function(){
				return ($scope.parallaxEnable !== false ) ? true : false;
			}

			var init = function(){
				if( !_.isEmpty(attrs.pwParallax) )
					method = attrs.pwParallax;

				if( !_.isUndefined(attrs.parallaxDepth) )
					depth = parseFloat(attrs.parallaxDepth);

				switch( method ){
					case 'parent':
						element.css('background-position', 'center');
						element.css('background-size', 'cover');
						element.css('position', 'absolute');
						element.css('width', '100%');
						element.css('translateZ', '0');
						element.parent().addClass('pw-parallax--parent');
						element.parent().css('position','relative');
						element.parent().css('overflow','hidden');

						el = element.parent()[0];

						break;
				}

			}

			var getParentHeight = function(){
				parentHeight = el.clientHeight;
				/**
				 * Set the element height based on the
				 * parent height, times the depth of the layer.
				 */
				elementHeight = parentHeight * depth;
				element.css('height', elementHeight+"px");

				/**
				 * Set the median point which is the point where
				 * the image is in the middle of the parent object.
				 */
				median = (elementHeight*-1) + (elementHeight-parentHeight)/2;

				return parentHeight;
			}

			var getInView = function(){
				/**
				 * inView : Generate decimal which is a value between 0-1
				 * 0 is value when the element is at the bottom of the viewport
				 * 1 is the value when the element is at the top of the viewport 
				 */
				var rect = el.getBoundingClientRect();
				inView = ( window.innerHeight - rect.top ) / (window.innerHeight+parentHeight);
				return inView;
			}

			var updateElementTransform = function(){

				/**
				 * inViewMedian : Generate decimal between (-1)-(1)
				 * 0 is when the object is in the center of the viewport
				 * -1 is when it's at the bottom of the viewport
				 * 1 is when it's at the top of the viewport
				 */
				inViewMedian = ( getInView() - 0.5 ) * 2;
				//$log.debug('parallax : inView', inView);

				pos = median + (inViewMedian * (elementHeight-parentHeight) );
				element[0].style[prefixed.transform] = 'translate3d(0px,' + pos + 'px,0px)';
			}

			var update = function(){
				requestAnimationFrame(update);

				if( !enable )
					return false;

				/**
				 * If the scroll position hasn't changed
				 * Return early.
				 */
				curY = window.scrollY || ((window.pageYOffset || document.body.scrollTop) - (document.body.clientTop || 0));
				if(prevY == curY)
					return false;
				else
					prevY = curY;

				/**
				 * If the parent container object is visible
				 * Update the element.
				 */
				if( $_.isInView( el ) )
					updateElementTransform();

			}

			/**
			 * As the DOM initializes, elements may change dimension
			 * So re-evaluate as long as this is happening.
			 */
			if( method === 'parent' )
				$scope.$watch(
					function(){
						return getParentHeight();
					},
					function(val){
						$log.debug('parallax : parent height', val);
						updateElementTransform();
				});

			/**
			 * Enable / disable parallax based on
			 * evaluation of parallax-enable attribute expression.
			 */
			$scope.$watch(
				function(){
					return enable()
				},
				function( enable ){
					if( enable )
						angular.element($window).bind("scroll", update);
					else
						angular.element($window).unbind("scroll", update);
			});
			
			// Initialize
			$timeout( init(), 0 );

			/**
			 * After document loads, update the element transformations.
			 */
			angular.element(document).ready(function () {
				updateElementTransform();
			});

		}
	}
}]);




