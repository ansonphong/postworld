

postworld.directive( 'pwUi', [ '$log', function( $log ){
	return{
		controller: 'pwUiCtrl',
		link: function( $scope, element, attrs ){

			// OBSERVE : UI Views
			attrs.$observe('uiViews', function(value) {
				$scope.uiViews = $scope.$eval( value );
			});
			
		},
	}
}]);

postworld.controller( 'pwUiCtrl',
	[ '$scope', '$timeout', '_', '$log',
	function( $scope, $timeout, $_, $log ){

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
		if( $scope.showView( viewId ) )
			return className;
		else
			return '';
	}


}]);