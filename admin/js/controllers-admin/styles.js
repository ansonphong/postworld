/*
  ____  _         _      
 / ___|| |_ _   _| | ___ 
 \___ \| __| | | | |/ _ \
  ___) | |_| |_| | |  __/
 |____/ \__|\__, |_|\___|
            |___/        
/////////////////////////*/

postworldAdmin.directive( 'pwAdminStyle', [ function(){
    return { 
        controller: 'pwAdminStyleCtrl',
        link:function( scope, element, attrs ){
        	// Add Module Class
        	element.addClass('pw-admin-style');
        }
    };
}]);

postworldAdmin.directive('pwAdminStyleInput', function( $pwData, $log){
	return {
		restrict: 'A',
		scope: {
			inputObj:'=pwAdminStyleInput',
			inputModel:'=',
			options:'=inputOptions'
		},
		template: '<div ng-include="itemTemplateUrl"></div>',
		link: function( $scope, element, attrs ) {
			
			//$log.debug( 'pwAdminStyleInput : inputObj', $scope.inputObj );
			//$log.debug( 'pwAdminStyleInput : inputModel', $scope.inputModel );

			var templateUrl = $pwData.getTemplate({
				subdir:'admin',
				view:'style-input-' + $scope.inputObj.input
			});
			$scope.itemTemplateUrl = templateUrl;


			$scope.backgroundColor = function( color ){

				///// LESS VARIABLES /////
				// If it's a less variable
				if( _.isString(color) && color.charAt(0) == '@' ){
					// Trim whitespace
					var searchKey = color.trim();
					// Get all characters in the string 
					searchKey = color.substring(1);
					// Set empty search value
					var searchValue = '';
					// For performance optimization
					var keepGoing = true;
					// Iterate through TYPES
					if( keepGoing )
					angular.forEach( $scope.$parent.pwStyles, function( typeValues, typeKey ){
						// Iterate through SECTIONS
						if( keepGoing )
						angular.forEach( typeValues, function( sectionValues, sectionKey ){
							// Iterate through PROPERTIES
							if( keepGoing )
							angular.forEach( sectionValues, function( propertyValue, propertyKey ){
								// IF the property key matches
								if( propertyKey == searchKey ){
									searchValue = propertyValue;
									keepGoing = false;
								}
							});
						});
					});
					color = searchValue;
				}

				///// STYLE OBJECT /////
				var style = {
					background: color,
				};

				///// EMPTY /////
				if( _.isEmpty( color ) ){
					style.background = '#fff';
					style.border = "1px dashed #ccc";
				}

				return style;

			}




		}
	};
})

postworldAdmin.controller('pwAdminStyleCtrl',
	[ '$scope', '$log', '$window', '$parse', '$_', '$pw', 
	function ( $scope, $log, $window, $parse, $_, $pw ) {
	// Initialize Status
	$scope.status = "done";
	//$log.debug( "CTRL" );

	// Localize the core option set
	$scope.options = $pw.optionsMeta;

	$scope.resetStyleDefaults = function(){
		$scope.pwStyles = angular.fromJson( angular.toJson( $scope.pwStyleDefaults ) );
	};

	///// SHOW / HIDE BUTTON /////
	$scope.toggleShow = function ( key, meta ){

		// Initialize sub-object
		if( typeof $scope.showing[key] === 'undefined' )
			$scope.showing[key] = {};

		// Toggle Show / Hide
		( $scope.showing[key][meta] ) ? 
			$scope.showing[key][meta] = false :
			$scope.showing[key][meta] = true ;
	};

	$scope.showProperty = function( property, view ){

		//$log.debug( "property : ", property );

		switch( view ){
			case 'edit':
				if( !_.isUndefined( property.key ) )
					return true;
				break;
			case 'space':
				if( property == 'space' )
					return true;
				break;
			case 'line':
				if( property == 'line' )
					return true;
				break;


			case 'edit-color':
				if( $_.getObj( property, 'input' ) == 'color' )
					return true;
				break;

		}
		return false;
	}


	
	
	
}]);
