

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

	$scope.uiBoolean = function( val ){
		// If the value is truthy and not empty, return true
		var bool = ( Boolean( val ) && !_.isEmpty( val ) ) ? true : false;
		return bool; 
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


}]);