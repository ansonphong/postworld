postworld.directive( 'pwUi', [ '$log', function( $log ){
	return{
		controller: 'pwUiCtrl',
		link: function( $scope, element, attrs ){
			$scope.uiViews = {};
			// OBSERVE : UI Views
			attrs.$observe('uiViews', function(value) {
				if( !_.isEmpty( value ) )
					$scope.uiViews = $scope.$eval( value );
			});
		},
	}
}]);

postworld.controller( 'pwUiCtrl',
	[ '$scope', '$timeout', '_', '$log',
	function( $scope, $timeout, $_, $log ){

	////////// UI ELEMENT : DISPLAY //////////

	$scope.uiToggleElementDisplay = function( element ){
		element = angular.element( element );
		if( element.css('display') == 'none' )
			element.css('display', 'block');
		else
			element.css('display', 'none');
	}

	$scope.uiToggleView = function( viewId ){
		// If the view is registered
		if( $_.objExists( $scope, 'uiViews.'+viewId ) )
			// Invert the value
			$scope.uiViews[viewId] = !$scope.uiViews[viewId];
		// If the view is not registered, start by toggling on
		else
			$scope.uiViews[viewId] = true;
	}

	$scope.uiShowView = function( viewId ){
		// If the view is registered
		return $_.getObj( $scope, 'uiViews.'+viewId );
	}

	$scope.uiFocusElement = function( element ){
		element = angular.element( element );
		// Timeout incase the specified element is hidden
		$timeout( function(){
			element.focus();
		}, 0 );
	}

	$scope.uiSetClass = function( viewId, className ){
		// Set default class name
		if( _.isUndefined(className) )
			className = 'active';
		// Return className if view is true
		if( $scope.uiShowView( viewId ) )
			return className;
		else
			return '';
	}

	$scope.uiBoolClass = function( val, className, bool ){
		// For use with ng-class
		// Returns the className if val is truthy
		
		// Set default value for bool
		if( bool == null )
			bool = true;

		// Get boolean from value
		var valBool = $scope.uiBool(val);

		// If bool value is the same as the boolean of val
		if( valBool == bool )
			// Return the given class
			return className;

	}

	$scope.uiBool = function( val ){
		// If the value is truthy and not empty, return true
		var bool = ( Boolean( val ) && !_.isEmpty( val ) ) ? true : false;
		return bool; 
	}

	$scope.uiBoolean = function( val ){
		// DEPRECIATED as of Version 1.7.2
		return $scope.uiBool( val );
	}


	////////// UI ELEMENT : STYLING //////////

	$scope.backgroundImage = function( imageUrl, properties ){
		// Set the Image URL
		//var imageUrl = $scope.post.image[imageHandle].url;
		var style = { 'background-image': "url(" + imageUrl + ")" };

		// Add additional properties
		if( !_.isUndefined( properties ) ){
			angular.forEach( properties, function(value, key){
				style[key] = value;
			});
		}
		return style;
	}

	$scope.uiToggleElementClass = function( className, $event ){
		// $event must be passed in as the second parameter from the DOM
		//$log.debug( "EVENT : ", $event.currentTarget  );
		angular.element( $event.currentTarget ).toggleClass( className );
	}


}]);




/*
DEPRECIATED - USE `pwUi` DIRECTIVE
///// TOGGLE AN ELEMENT'S DISPLAY ON CLICK /////
postworld.directive('pwClickToggleDisplay', function( $log ) {
		return {
			restrict: 'A',
			link: function (scope, element, attrs) {
				element.bind('click', function (event) {
					if( !_.isNull( attrs.pwClickToggleDisplay ) ){
						var aElement = angular.element( attrs.pwClickToggleDisplay );
						var display = aElement.css('display');
						if( display == 'block' )
							aElement.css('display', 'none');
						else if( display == 'none' )
							aElement.css('display', 'block');
					}
					$log.debug( "display:" + display );
				});
			}
		};
	});
*/
/*
///// TOGGLE AN ELEMENT'S CLASS ON CLICK /////
postworld.directive('pwClickToggleClass', function() {
		return {
			restrict: 'A',
			link: function (scope, element, attrs) {
				element.bind('click', function (event) {
					if( !_.isNull( attrs.pwClickToggleClass ) ){
						var aElement = angular.element( attrs.pwClickToggleClass );
						aElement.toggleClass( attrs.toggleClass );
					}
				});
			}
		};
	});
*/