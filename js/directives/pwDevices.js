/**
 * @ngdoc directive
 * @name postworld.directive:pwDeviceClass
 * @description
 * Adds classes to the element relating to the current detected device.
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
			if( device.is_desktop )
				addClass( 'desktop' );
			if( device.is_mobile )
				addClass( 'mobile' );
			if( device.is_tablet )
				addClass( 'tablet' );

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