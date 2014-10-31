'use strict';

postworld.directive('feedItem', [ '_', function( $_ ) {
	return {
		restrict: 'A',
		replace: true,
		controller: 'pwFeedItemCtrl',
		template: '<div ng-include="itemTemplateUrl"></div>',

		link : function( $scope, element, attrs ){

			// Add classes to the parent feed item set by the block
			var blockClasses = $_.get( $scope, 'post.block.classes' );
			if( blockClasses != false && _.isString( blockClasses ) ){
				element.parent().addClass( blockClasses );
			}

		},

	};
}]);

postworld.controller('pwFeedItemCtrl',
	function($scope, $location, $log, pwData, $attrs) {
		
		var type = 'post';
		if ( $scope.post.post_type ) type = $scope.post.post_type;

		if (type == '_pw_block') {
			$scope.itemTemplateUrl = pwData.pw_get_template( { subdir:'blocks', view: $scope.post.template } );				
		}
		else 
			$scope.itemTemplateUrl = pwData.pw_get_template( { subdir:'posts', post_type: type, view: $scope.$parent.feed_item_view_type } );

		//$log.debug('template:', $scope.itemTemplateUrl);

		// Decodes Special characters in URIs
		$scope.decodeURI = function(URI) {
			URI = URI.replace("&amp;","&");
			return decodeURIComponent( URI );
		};

		// TODO set templateURL?		  
		// Template Update Event
		$scope.$on("FEED_TEMPLATE_UPDATE", function(event, feed_item_view_type){
			
			if ( $scope.post.post_type != '_pw_block' ) {
				var type = $scope.post.post_type;
				$scope.itemTemplateUrl = pwData.pw_get_template( { subdir:'posts', post_type: type, view: feed_item_view_type } );					
			} 
		   });		  		      	
	}
);



/* ____      _     ___     ___               
  / ___|_ __(_) __| \ \   / (_) _____      __
 | |  _| '__| |/ _` |\ \ / /| |/ _ \ \ /\ / /
 | |_| | |  | | (_| | \ V / | |  __/\ V  V / 
  \____|_|  |_|\__,_|  \_/  |_|\___| \_/\_/  

////////// GRID FEED CONTROLLER //////////*/


postworld.directive( 'pwGrid', [ function($scope){
	return {
		restrict: 'AE',
		controller: 'pwGridCtrl',
		link: function( $scope, element, attrs ){
			//$scope.gridItem = {};

			// OBSERVE Attribute
			//attrs.$observe('gridFixedHeight', function(value) {
			  //$scope.$parent.gridFeed.gridFixedHeight = value;
			//});
		}
	};
}]);

postworld.controller('pwGridCtrl',
	[ "$scope", "$window", "_", "$log", "pwImages",
	function($scope, $window,  $_, $log, $pwImages ) {

	////////// GRIDS //////////
	$scope.getImageSize = function( prefix, imageTags ){
		return prefix + $scope.selectImageTag( imageTags ).name;
	}

	$scope.selectImageTag = function( imageTags, tagMapping ){
		return $pwImages.selectImageTag( imageTags, tagMapping );
	}

	$scope.setGridClass = function( imageTags ){
		if( !_.isEmpty( imageTags ) ){
			var selectedTag = $scope.selectImageTag( imageTags );
			var gridClass = selectedTag.name;
		}
		else{
			var gridClass = 'no-image';
		}
		return gridClass;
	};

	$scope.setGridStyle = function( multiplier, imageTags, tagMapping, values ){
		if( _.isUndefined(values) )
			values = [ 'width', 'height' ];
		if( _.isUndefined(multiplier) )
			multiplier = 300;
		if( !_.isEmpty( imageTags ) ){
			var selectedTag = $pwImages.selectImageTag( imageTags, tagMapping );
			var width = selectedTag['width'] * multiplier;
			var height = selectedTag['height'] * multiplier;
		}
		else{
			var width = multiplier;
			var height = multiplier;
		}
		//$log.debug( 'setGridStyle : $pwImages.selectImageTag  : ', selectedTag );
		
		if( $_.inArray( 'width', values ) && $_.inArray( 'width', values ) )
			var style = { width: width + "px", height: height + "px" };
		else if( $_.inArray( 'height', values ) )
			var style = { height: height + "px" };
		else if( $_.inArray( 'width', values ) )
			var style = { width: width + "px" };

		//$log.debug( "setGridStyle: multiplier: " + multiplier + " // imageTags: " + imageTags + " // style: " + JSON.stringify(style) + " // tagMappings: ", tagMapping );
		return style;
	};


}]);



/*
   ____      _     _   ___ _                 
  / ___|_ __(_) __| | |_ _| |_ ___ _ __ ___  
 | |  _| '__| |/ _` |  | || __/ _ \ '_ ` _ \ 
 | |_| | |  | | (_| |  | || ||  __/ | | | | |
  \____|_|  |_|\__,_| |___|\__\___|_| |_| |_|

/////////// GRID ITEM CONTROLLER ///////////*/
// For use inside a feed template
// Largely a localized alias for the functions in pwGrid

postworld.directive( 'pwGridItem', [ function($scope){
	return {
		restrict: 'AE',
		controller: 'pwGridItemCtrl',
		link: function( $scope, element, attrs ){
			// OBSERVE Attribute
			//attrs.$observe('gridFixedHeight', function(value) {
			  //$scope.$parent.gridFeed.gridFixedHeight = value;
			//});
		}
	};
}]);

postworld.controller('pwGridItemCtrl',
	[ "$scope", "$window", "_", "$log", "pwImages",
	function($scope, $window,  $_, $log, $pwImages ) {

	////////// ALIAS FUNCTIONS //////////

	var parentFunctionExists = function( functionName ){
		return !_.isUndefined( $scope.$parent[ functionName ]() );
	}

	$scope.getImageSize = function( prefix, imageTags ){
		if ( parentFunctionExists( "getImageSize" ) ) 
			return $scope.$parent.getImageSize( prefix, imageTags );
	}

	$scope.selectImageTag = function( imageTags, tagMapping ){
		return $pwImages.selectImageTag( imageTags, tagMapping );
	};

	$scope.setGridClass = function( imageTags ){
		if ( parentFunctionExists( "setGridClass" ) )
			return $scope.$parent.setGridClass( imageTags );
	};

	$scope.setGridStyle = function( imageTags, tagMapping ){
		if ( parentFunctionExists( "setGridStyle" ) )
			return $scope.$parent.setGridStyle( imageTags, tagMapping );
	};

	$scope.tester = function(){
		return "test";
	}

	$scope.gridImageStyle = function( prefix, tags ){
		if( _.isEmpty( tags ) || _.isUndefined( tags ) )
			return false;

		var imageUrl = $scope.post.image.sizes[ $scope.getImageSize(prefix, tags) ].url;
		return { "background-image":"url( " + imageUrl + " )" };
	}


}]);

