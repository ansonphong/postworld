jQuery( document ).ready(function() {
	jQuery('head').append('<style type="text/css">.pw-cloak{opacity:1 !important;}</style>');
});

postworldAdmin.directive( 'pwAdmin', function(){
	return {
		restrict: 'A',
		controller: 'pwAdminCtrl',
		link: function( $scope, element, attrs ){}
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


postworldAdmin.directive( 'pwAdminOptions', function($pw){
	return {
		restrict: 'A',
		controller: 'pwAdminCtrl',
		link: function( $scope, element, attrs ){
			$scope['options'] = $pw.optionsMeta;
		}
	};
});


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


postworldAdmin.directive('pwAdminMetaboxEvent',
	function () {
	return {
		restrict: 'A',
		link: function( $scope, element, attrs ){
			$scope.removeTimeZone = function(){
				delete $scope.post.post_meta[ $scope.eventKey ].timezone;
			}
		}
	};
});


postworldAdmin.directive('pwAdminModules',
	function ( $_ ) {
	return {
		restrict: 'A',
		link: function( $scope, element, attrs ){
			
			$scope.modulesInit = function(){
				$_.arrayFromObjectWatch( $scope, 'pwModules', 'selectedModules' );
			}
			$scope.modulesInit();

			// Every time the selected modules changes
			$scope.$watch( 'selectedModules', function(val){
				// Force required modules to be enabled
				angular.forEach( $scope.requiredModules, function( moduleName ){
					$scope.selectedModules[ moduleName ] = true;
				});
			});

			$scope.isRequired = function( value ){
				return $_.isInArray( value, $scope.requiredModules );
			}

		}
	};
});




postworldAdmin.directive('pwAdminGalleryOptions', function( $_ ){
	return {
		restrict: 'A',
		link: function( $scope, element, attrs ){

			$scope.getSelectedOption = function( objectKey ){
				 // Return the option where the slug equals the selected value
				 return _.findWhere( $scope.galleryOptions, { key: objectKey } );
			};

			$scope.showGalleryView = function( view ){
				switch( view ){
					case 'showImmersion':
						var showFor = $_.get( $scope.showOptions, 'immersion.show_for' ),
							galleryTemplate = $_.get( $scope, $scope.galleryModel + '.template' );
						if( $_.isInArray( galleryTemplate, showFor ) )
							return true;
						break;
				}
				return false;
			}

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

