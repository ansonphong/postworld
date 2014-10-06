/*_    _       _           _         ____  _         _      
 (_)  / \   __| |_ __ ___ (_)_ __   / ___|| |_ _   _| | ___ 
 | | / _ \ / _` | '_ ` _ \| | '_ \  \___ \| __| | | | |/ _ \
 | |/ ___ \ (_| | | | | | | | | | |  ___) | |_| |_| | |  __/
 |_/_/   \_\__,_|_| |_| |_|_|_| |_| |____/ \__|\__, |_|\___|
                                               |___/        
////////////////////////////////////////////////////////////*/

infinite.directive( 'iAdminStyle', [ function(){
    return { 
        controller: 'iAdminStyleCtrl',
        link:function( scope, element, attrs ){
        	// Add Module Class
        	element.addClass('i-admin-style');
        }
    };
}]);

infinite.controller('iAdminStyleCtrl',
	[ '$scope', '$log', '$window', '$parse', 'iData', '_',
	function ( $scope, $log, $window, $parse, $iData, $_ ) {
	// Initialize Status
	$scope.status = "done";

	$log.debug( "CTRL" );

	$scope.resetDefaults = function(){
		$scope.settings = $scope.style_model_default;
		//alert('defaults');
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

		$log.debug( "property : ", property );

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
			angular.forEach( $scope.iStyles, function( typeValues, typeKey ){
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
	
	
}]);
