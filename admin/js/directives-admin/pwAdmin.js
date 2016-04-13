postworldAdmin.directive( 'pwAdmin', function(){
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
});

postworldAdmin.controller( 'pwAdminCtrl',
	['$scope', '$window', '$timeout', '$log', '$_',
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


postworldAdmin.directive('pwAdminLinkUrl',
	function ($pwEditPostFilters, $pwPostOptions) {
	return {
		restrict: 'A',
		link: function( $scope, element, attrs ){
			// Get Link Format Meta
			$scope.link_format_meta = $pwPostOptions.linkFormatMeta();

			// LINK_URL WATCH : Watch for changes in link_url
			// Evaluate the link_format
			$scope.$watchCollection('[post.link_url, post.link_format]',
				function (){
					$scope.post.link_format = $pwEditPostFilters.evalPostFormat( $scope.post.link_url );
				});
		}
	};
});


postworldAdmin.directive('pwAdminPostParent',
	function ($pwData, $_, $log) {
	return {
		restrict: 'A',
		link: function( $scope, element, attrs ){

			$scope.getPosts = function( val ) {
				var query = $scope.query;
				query.s = val;

				return $pwData.pwQuery( query ).then(
					function( response ){
						$log.debug( "QUERY RESPONSE : ", response.data );
						return response.data;
					},
					function(){}
				);
			};

			$scope.addPostParent = function( item ){
				$log.debug( "PW METABOX : POST PARENT : addPostParent( $item ) : ", item );
				// Set the ID as the post parent
				$scope.ppPost['post_parent'] = item.ID;
				// Populate the parent post object
				$scope.parent_post = item;
			}

			$scope.removePostParent = function(){
				// Clear the post_parent field from the post
				$scope.ppPost['post_parent'] = 0;
				// Clear the post_parent object
				$scope.parent_post = false;
				//alert('remove');
			}


		}
	};
});

