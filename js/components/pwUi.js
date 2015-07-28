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
	[ '$scope', '$timeout', '_', '$log', '$pw',
	function( $scope, $timeout, $_, $log, $pw ){

	$scope.uiLoggedIn = function(){
		return !_.isEmpty( $pw['user'] );
	}

	////////// UI ELEMENT : DISPLAY //////////

	$scope.uiToggleElementDisplay = function( element ){
		element = angular.element( element );
		if( element.css('display') == 'none' )
			element.css('display', 'block');
		else
			element.css('display', 'none');
	}

	// For use with ng-click
	$scope.uiToggleView = function( viewId ){
		// If the view is registered
		if( $_.objExists( $scope, 'uiViews.'+viewId ) )
			// Invert the value
			$scope.uiViews[viewId] = !$scope.uiViews[viewId];
		// If the view is not registered, start by toggling on
		else
			$scope.uiViews[viewId] = true;
	}

	// For use with ng-show / ng-hide
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

	// For use with ng-class
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
		var bool = ( Boolean( val ) ) ? true : false;
		// If it's an empty object
		if( _.isObject( val ) && _.isEmpty( val ) )
			return false;
		// If it's an empty string
		if( _.isString( val ) && _.isEmpty( val ) )
			return false;
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

	$scope.uiToggleElementClass = function( className, selector ){
		// If $event is passed as the second parameter
		if( !_.isUndefined( selector.currentTarget ) )
			// Toggle class on the current DOM item
			angular.element( selector.currentTarget ).toggleClass( className );
		else
			// Otherwise use the standard selector
			angular.element( selector ).toggleClass( className );
	}

	$scope.uiToggleValue = function( key, values ){
		$log.debug( "uiToggleValue : " + key );
		// Get the current key value
		var currentValue = $_.get( $scope, key );
		// Determine the new value
		var newValue = ( currentValue == values[0] ) ? values[1] : values[0];
		// Set the new value as a string
		$scope.$eval( key + ' = ' + JSON.stringify( newValue ) );
	}

	$scope.uiImageSrc = function( srcs ){
		
	}

	/**
	 * Checks to see if floated content in a container is wrapping,
	 * and if so, adds a class to the current element.
	 * For use with ng-class.
	 * 
	 * @param string containerSelector Selector for the container element.
	 * @param string itemsSelector Selector for elements within the container
	 * @param string className The class name to apply if it's wrapping.
	 */
	$scope.uiWrappingClass = function( containerSelector, itemsSelector, className ){
		if( className == null )
			className = 'is-wrapping';

		// Get the container client width
		var container = angular.element( containerSelector );
		var containerWidth = container[0].clientWidth;

		// Subtract element padding from width
		var styles = window.getComputedStyle( container[0] );
		var padding = 	parseFloat(styles.paddingLeft) +
		        		parseFloat(styles.paddingRight);
		containerWidth = containerWidth - padding;

		// Get the item elements
		var items = angular.element( containerSelector + " " + itemsSelector );
		// Add up the width of all the items
		var itemsWidth = 0;
		for( var i = 0; i<items.length; i++ ){
			itemsWidth += items[i].clientWidth;
		}

		//$log.debug( 'uiWrappingClass :: containerWidth : ', containerWidth );
		//$log.debug( 'uiWrappingClass :: itemsWidth : ', itemsWidth );

		if( containerWidth < itemsWidth )
			return className;
		else
			return '';

	}


}]);
