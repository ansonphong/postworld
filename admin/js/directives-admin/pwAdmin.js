postworldAdmin.directive( 'pwAdmin', [ function($scope){
	return {
		restrict: 'A',
		controller: 'pwAdminCtrl',
		link: function( $scope, element, attrs ){
			/*
			// OBSERVE Attribute
			attrs.$observe('var', function(value) {
			});
			*/
		}
	};
}]);

postworldAdmin.controller( 'pwAdminCtrl',
	['$scope', '$window', '$timeout', '$log', '_',
	function($scope, $window, $timeout, $log, $_) {

	$scope.selectedItem = {};

	$scope.showView = function( viewId ){
		if( viewId == $scope.view )
			return true;
		return false;
	}

	$scope.duplicateItem = function( item, obj ){
		item = angular.fromJson( angular.toJson( item ) );
		item.name += " Copy";
		item.id += "_copy";
		$scope[ obj ].push( item );
		$scope.selectItem( item );
	}

	$scope.deleteItem = function( item, obj ){
		$scope[ obj ] = _.reject( $scope[ obj ], function( thisFeed ){ return thisFeed.id == item.id } );
		$scope.selectedItem = {};
		$scope.view = '';
	}

	$scope.selectItem = function( item ){
		if( _.isString( item ) ){
			$scope.view = item;
			$scope.selectedItem = {};
		}
		if( _.isObject( item ) ){
			$scope.view = 'editItem';
			$scope.selectedItem = item;
		}
	}

	$scope.menuClass = function( menuItem ){
		var selected = false;
		if( _.isString( menuItem ) )
			if( $scope.view == menuItem )
				selected = true;
		if( _.isObject( menuItem ) )
			if( $scope.selectedItem.id == menuItem.id )
				selected = true;
		if( selected )
			return 'selected';
		return;
	}

	$scope.enableInput = function( selector ){
		element = angular.element( selector );
		element.removeAttr('disabled');
	}

	$scope.focusInput = function( selector ){
		element = angular.element( selector );
		element.focus();
	}

	$scope.disableInput = function( selector ){
		element = angular.element( selector );
		element.attr('disabled', 'disabled');
	}

}]);