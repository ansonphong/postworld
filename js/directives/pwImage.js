/*                _                    _   ___                            
  _ ____      __ | |    ___   __ _  __| | |_ _|_ __ ___   __ _  __ _  ___ 
 | '_ \ \ /\ / / | |   / _ \ / _` |/ _` |  | || '_ ` _ \ / _` |/ _` |/ _ \
 | |_) \ V  V /  | |__| (_) | (_| | (_| |  | || | | | | | (_| | (_| |  __/
 | .__/ \_/\_/   |_____\___/ \__,_|\__,_| |___|_| |_| |_|\__,_|\__, |\___|
 |_|                                                           |___/      
 ///////////////////////// LOAD IMAGE DIRECTIVE ////////////////////////*/
'use strict';

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
 * @param integer smartImageTimeout Optional. Number of milliseconds to re-evaluate after.
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
			//$timeout( function(){
				setImgUrl();		
			//}, 0 );

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
							setImgUrl();

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
						//setImgUrl('');
						//$timeout( function(){
							setImgUrl();		
						//}, 0 );
					}
				);

			}

			else if( !_.isUndefined( attrs.smartImageTimeout ) ){
				$timeout( function(){
					setImgUrl();
				}, parseInt( attrs.smartImageTimeout ) );
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
 * @param {float|string} parallaxDepth (Optional) Define a depth of how far back the layer appears.
 * @param {string} parallaxMedian (Optional) Changes how the median is calculated. Options: normal | proportional
 * @param {expression} parallaxEnable (Optional) Toggles if parallax is enabled.
 * @param {none} parallaxInFeed (Optional) If attribute is provided, will update after feeds init.
 *
 */
postworld.directive('pwParallax',
	['$rootScope', '$log','$timeout','$window', '_', '$pw',
	function($rootScope, $log, $timeout, $window, $_, $pw){
	return{
		restrict:'A',
		scope:{
			parallaxEnable: "=",
		},
		link:function( $scope, element, attrs ){

			var	prevYScroll = 0,
      			yScroll = 0,
      			depth = 1.2,
      			inView = 0,
      			inViewMedian = 0,
      			method = 'parent',
      			medianType = 'normal',
      			el = element[0],
      			translateY = 0,
      			prevTranslateY = 0,
      			rect = {},
      			deviceType = $pw.getDeviceType(),
      			frozen = ( deviceType === 'mobile' || deviceType === 'tablet' );

      		var prefixed = {
				transform: $_.getSupportedProp(['transform', 'msTransform', 'webkitTransform', 'mozTransform', 'oTransform'])
			};

			var enable = function(){
				return ($scope.parallaxEnable !== false ) ? true : false;
			}

			/**
			 * Store dynamic values in local variables cache, 'c'.
			 */
			var c = {};
			var updateCache = function(){
				/**
				 * Set the median point which is the point where
				 * the image is in the middle of the parent object.
				 */
				var median;
				// Use median calculation for proportionally sized parent containers
				if( medianType === 'proportional' )
					median = (c.elementHeight*-1) + (c.elementHeight-c.parentHeight)/2;
				// Use regular median calculation
				else
					median = (c.elementHeight-c.parentHeight)/-2;

				/**
				 * Cache object.
				 */
				c = {
					elHeight: element.parent()[0].clientHeight,
					elTopOffset: element.parent().offset().top,
					windowHeight: window.innerHeight,
					parentHeight: el.clientHeight,
					elementHeight: el.clientHeight * depth,
					median: median,
				};

				/**
				 * Set CSS values which may have changed
				 * due to updated cache.
				 */
				element.css('height', c.elementHeight+"px");

				return c;
			}

			var init = function(){

				if( !_.isEmpty(attrs.pwParallax) )
					method = attrs.pwParallax;

				if( !_.isUndefined(attrs.parallaxDepth) ){
					var getDepth = parseFloat(attrs.parallaxDepth);
					if( !_.isNaN( getDepth ) )
						depth = getDepth;
				}

				if( !_.isUndefined(attrs.parallaxMedian) )
					medianType = String(attrs.parallaxMedian);

				switch( method ){
					case 'parent':
						element.css('position', 'absolute');
						element.css('left', '0');
						element.css('right', '0');
						element.css('width', '100%');
						element.css('translateZ', '0');
						element.addClass('pw-parallax');
						element.parent().addClass('pw-parallax--parent');
						element.parent().css('position','relative');
						element.parent().css('overflow','hidden');

						el = element.parent()[0];

						break;
				}

			}

			var updateElementTransform = function(){
				
				if( frozen ){
					inViewMedian = 0;
				}
				else{
					/**
					 * inView : Generate decimal which is a value between 0-1
					 * 0 is value when the element is at the bottom of the viewport
					 * 1 is the value when the element is at the top of the viewport 
					 */
					inView = ( window.innerHeight - (c.elTopOffset - yScroll) ) / (c.windowHeight+c.parentHeight);

					/**
					 * inViewMedian : Generate decimal between (-1)-(1)
					 * 0 is when the object is in the center of the viewport
					 * -1 is when it's at the bottom of the viewport
					 * 1 is when it's at the top of the viewport
					 */
					inViewMedian = ( inView - 0.5 ) * 2;
				}

				/**
				 *  Set the position of the element on the vertical axis.
				 */
				translateY = c.median + (inViewMedian * (c.elementHeight-c.parentHeight) );

				element[0].style[prefixed.transform] = 'translate3d(0px,' + Math.round(translateY) + 'px,0px)';
			}

			var update = function(){
				requestAnimationFrame(update);

				if( !enable || c.elementHeight === 0 )
					return false;

				/**
				 * If the scroll position hasn't changed
				 * Return early.
				 */
				yScroll = window.scrollY || ((window.pageYOffset || document.body.scrollTop) - (document.body.clientTop || 0));
				if(prevYScroll == yScroll)
					return false;
				else
					prevYScroll = yScroll;

				/**
				 * If the parent container object is visible
				 * Update the element.
				 * @note Commented out until more efficient function developed.
				 */
				//if( $_.isInView( el ) ){
					updateElementTransform();
				//}

			}

			/**
			 * As the DOM initializes, or elements move, cached values may change
			 * So update the parallax every time cached values are changing.
			 */
			$scope.$watch(
				function(){
					return updateCache();
				},
				function(val){
					updateElementTransform();
			}, 1 );


			/**
			 * Enable / disable parallax based on
			 * evaluation of parallax-enable attribute expression.
			 */
			if( !frozen ){
				$scope.$watch(
					function(){
						return enable()
					},
					function( enable ){
						if( enable && !frozen ){
							angular.element($window).bind("scroll", update);
							angular.element($window).bind("resize", update);
						}
						else{
							angular.element($window).unbind("scroll", update);
							angular.element($window).unbind("resize", update);
						}
				});
			}
			else
				$timeout( updateElementTransform(), 1 );
			
			// Initialize
			$timeout( init(), 0 );

			/**
			 * After document loads, update the element transformations.
			 */
			angular.element(document).ready(function(){
				updateElementTransform();
			});

			/**
			 * Update after feeds initialize
			 * If parallax-in-feed is defined.
			 */
			if( !_.isUndefined( attrs.parallaxInFeed ) )
				$scope.$on('pw.feedInit', function(vars){
					$timeout( function(){
						updateElementTransform();
					}, 200 );
				});

		}
	}
}]);


/**
 * @ngdoc directive
 * @name postworld.directive:pwHeight
 * @description
 * Sizes the height of an element based on preset methods.
 *
 * @param {string} pwHeight Methods by which to size height. Options: window-base, window-percent, pixels, proportion
 * @param {string|float} heightValue Value by which to size, based on method.
 * @param {none} heightDynamic (Optional) Whether or not to dynamically change.
 *
 * //IN DEV @param {number} heightSubtract - make entry for a fixed value to subtract from window height
 * //IN DEV @param {} - option to subtract the scroll distance, in the case of ...
 *
 */
postworld.directive('pwHeight',
	['$rootScope', '$log','$timeout','$window', '_', '$pw',
	function($rootScope, $log, $timeout, $window, $_, $pw){
	return{
		restrict:'A',
		link:function( $scope, element, attrs ){

			var c = {},
				fixedAncestor = false;

			var updateCache = function(){
				c = {
					offsetTop: element.offset().top, //element[0].offsetTop,
					windowHeight: $window.innerHeight,
					//htmlTopMargin: parseInt( $window.getComputedStyle( angular.element('html')[0] )['margin-top'] ),
				};
				$log.debug('pwHeight : updateCache', c);
				return c;
			}

			var isDynamic = function(){
				return !_.isUndefined( attrs.heightDynamic );
			}

			/**
			 * Window Base
			 */
			var initWindowBase = function(){
				fixedAncestor = $_.ancestorHasStyle( element, 'position', 'fixed' );
				$timeout( function(){
					updateWindowBase();
				}, 0 );
			}
			var updateWindowBase = function(){
				updateCache();
				// If any ancestors are fixed, subtract the window's y scroll value
				var scrollY = ( fixedAncestor ) ? $_.windowScrollY() : 0;
				// Subtract the element's top offset from the window's height
				var elemHeight = c.windowHeight - (c.offsetTop - scrollY);
				element[0].style['height'] = elemHeight + "px";
			}

			/**
			 * Window Percent
			 */
			var initWindowPercent = function(){
				/**
				 * Mobile browsers sometimes change viewport height
				 * When initially scrolling, which creates unexpected results.
				 * So for mobile devices, use a fixed pixel value
				 */
				if( $pw.getDeviceType() === 'mobile' )
					element[0].style['height'] = ($window.innerHeight * parseInt( attrs.heightValue ) / 100) + 'px';
				else			
					element[0].style['height'] = attrs.heightValue + "vh";
			}

			/**
			 * Pixels
			 */
			var initPixels = function(){
				element[0].style['height'] = attrs.heightValue + "px";
			}

			/**
			 * Proportion
			 */
			var initProportion = function(){
				if(isDynamic())
					$timeout( function(){
						updateProportion();
					}, 0 );
				else
					updateProportion();
				
			}
			var updateProportion = function(){
				var elementWidth = element[0].clientWidth;
				var prop = parseFloat(attrs.heightValue);
				var elementHeight = elementWidth/prop;
				element[0].style['height'] = elementHeight + "px";
			}

			var init = function(){
				// Initialize based on height method
				switch( attrs.pwHeight ){
					case 'window-base':
						$log.debug('pwHeight : init', attrs.pwHeight );
						initWindowBase();
						/**
						 * Change event listener type on mobile browers.
						 */
						if( $pw.device['is_mobile'] )
							window.addEventListener("orientationchange", updateWindowBase, false);
						else
							angular.element($window).bind("resize", updateWindowBase);
						break;

					case 'window-percent':
						initWindowPercent();
						break;

					case 'pixels':
						initPixels();
						break;

					case 'proportion':
						$log.debug('pwHeight : init : ' + attrs.pwHeight, attrs.heightValue );
						initProportion();
						angular.element($window).bind("resize", updateProportion);
						break;
				}
			}

			/**
			 * Initialize
			 * If dynamic, add timeout for other data to initialize first
			 */
			if(isDynamic())
				$timeout( function(){
					init();
				}, 0 );
			else
				init();
			

		}
	}
}]);
