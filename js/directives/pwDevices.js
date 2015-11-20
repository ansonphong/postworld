'use strict';
/**
 * @ngdoc directive
 * @name postworld.directive:pwDeviceClass
 * @description
 * Adds classes to the element relating to the current detected device.
 * Type of device is prefixed with 'device-', devices are 'desktop', 'tablet', 'mobile'
 * The Postworld 'Devices' module must be enabled for this to work.
 */
postworld.directive('pwDeviceClass', function( $pw, $log ) {
	return {
		link: function( $scope, element, attrs ) {

			// If Mobile Detect module not loaded
			if(!$pw.device)
				return false;

			var device = $pw.device;
			var classes = [];
			var prefix = 'device-';

			var addClass = function( string ){
				classes.push( prefix + string );
			}

			// Add supported classes
			if( device.is_desktop ){
				addClass( 'desktop' );
				addClass( 'input-pointer' );
			}
			if( device.is_mobile ){
				addClass( 'mobile' );
				addClass( 'input-touch' );
			}
			if( device.is_tablet ){
				addClass( 'tablet' );
				addClass( 'input-touch' );
			}

			// Convert classes array into a string
			var classesString = '';
			for( var i = 0; i<classes.length; i++ ){
				classesString += " " + classes[i] ;
				$log.debug( 'pwMobileDetectClass : FOR', classes[i]);
			}

			// Add classes
			element.addClass( classesString );

		}
	}
});


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
				
				// Debug Data
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
						// Don't re-evaluate if the height/width is 0
						if( val.width !== 0 && val.height !== 0 ){
							//$log.debug( 'val', val );
							setImgUrl();
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


