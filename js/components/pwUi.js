

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
	[ '$scope', '$timeout', '_',
	function( $scope, $timeout, $_ ){

	$scope.toggleElementDisplay = function( element ){
		element = angular.element( element );
		if( element.css('display') == 'none' )
			element.css('display', 'block');
		else
			element.css('display', 'none');
	}

	$scope.toggleView = function( viewId ){
		// If the view is registered
		if( $_.objExists( $scope, 'uiViews.'+viewId ) )
			// Invert the value
			$scope.uiViews[viewId] = !$scope.uiViews[viewId];
	}

	$scope.showView = function( viewId ){
		// If the view is registered
		return $_.getObj( $scope, 'uiViews.'+viewId )
	}

	$scope.focusElement = function( element ){
		element = angular.element( element );
		// Timeout incase the specified element is hidden
		$timeout( function(){
			element.focus();
		}, 0 );
		
	}


}]);