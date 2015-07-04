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
 * @name postworld.directive:pwSmartSrc
 * @description
 * Intelligently selects the right image from the image object
 * Based on the height and width of the element.
 *
 * @param Object pwImageSrc A Postworld post image object
 *
 * @example
 * 		<img pw-smart-src="post.image">
 *
 */
postworld.directive('pwSmartSrc',
	[ '$pw', '$log', '_', '$window', '$filter', 'pwImages', '$timeout',
	function( $pw, $log, $_, $window, $filter, $pwImages, $timeout ) {
	return {
		restrict:'A',
		scope:{
			pwSmartSrc:'='
		},
		link: function( $scope, element, attrs ) {

			// If an 'IMG' element, adjust the SRC
			// If other, adjust the 'background-image' style
			var init = function(){

				// Detect what type of element
				var elementTag = element[0].tagName; // 'IMG' / other
				var devicePixelRatio = $window.devicePixelRatio;
				var elementWidth = element[0].width;
				var elementHeight = element[0].height;
				

				// Here 
				var image = $pwImages.selectImageSize({
					width:0,
					minWidth: 0,
					maxWidth:0,
					height:0,
					minHeight:0,
					maxHeight:0
				});


				$log.debug( 'elementTag', elementTag );
				$log.debug( 'elementWidth', elementWidth );
				$log.debug( 'elementHeight', elementHeight );


			}

			// Timeout for DOM to initialize
			$timeout( function(){
				init();					
			}, 0 );
			
				
		}
	}
}]);






