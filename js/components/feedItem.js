'use strict';

postworld.directive('feedItem', function() {
    return {
        restrict: 'A',
        replace: true,
        controller: 'pwFeedItemController',
        // template: '<div ng-include src="\'http://localhost/pdev/wp-content/plugins/postworld/templates/posts/post-list.html\'"></div>',
        template: '<div ng-include src="itemTemplateUrl"></div>',        
        scope: {
        	// this identifies the panel id, hence the panel template
        	feedItem : '=',
        	post : "=",	// Get from ng-repeat
        	//feedId : '=', // Get from Parent Scope of Live Feed
        	feedSettings : '=', // Get from Parent Scope of Live Feed
        	}
    };
});

postworld.controller('pwFeedItemController',
    function pwFeedItemController($scope, $location, $log, pwData, $attrs) {
    	
		var type = 'post';
		if ($scope.post.post_type) type = $scope.post.post_type;
		if (type == "ad") {
			$scope.itemTemplateUrl = pwData.pw_get_template( { subdir:'panels', view: $scope.post.template } );				
		}
		else 
			$scope.itemTemplateUrl = pwData.pw_get_template( { subdir:'posts', post_type: type, view: $scope.$parent.feed_item_view_type } );
    		//$log.debug('pwFeedItemController New Template=',$scope.templateUrl,$scope.$parent.feed_item_view_type, type);    	
	    	
		
        // Decodes Special characters in URIs
        $scope.decodeURI = function(URI) {
            URI = URI.replace("&amp;","&");
            return decodeURIComponent( URI );
        };

        // TODO set templateURL?		  
		// Template Update Event
		$scope.$on("FEED_TEMPLATE_UPDATE", function(event, feed_item_view_type){
			
			if ( $scope.post.post_type != "ad" ) {
				var type = $scope.post.post_type;
				$scope.itemTemplateUrl = pwData.pw_get_template( { subdir:'posts', post_type: type, view: feed_item_view_type } );					
			} 
		
		   // $log.debug('pwFeedItemController: Event Received FEED_TEMPLATE_UPDATE',feedTemplateUrl);
		   // $scope.templateUrl = feed_item_view_type;
		   
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

    var gridSettings = ( $_.objExists( $scope, 'feedSettings.view.settings.grid' ) ) ?
        $scope.feedSettings.view.settings.grid : 
        {};

    // $scope.post.image
    //$log.debug( "LOAD GRID : " + $scope.post.post_title, gridSettings );

    var gridWidth = gridSettings['width'];
    var gridHeight = gridSettings['height'];

    $scope.getImageSize = function( prefix, imageTags ){
        return prefix + $scope.selectImageTag( imageTags ).name;
    }

    //var imageTags = $scope.post.image['tags'];
    //var imageStats = $scope.post.image['stats'];

    $scope.selectImageTag = function( imageTags, tagMapping ){
    	return $pwImages.selectImageTag( imageTags, tagMapping );
    }

    $scope.setGridClass = function( imageTags ){
    	var selectedTag = $scope.selectImageTag( imageTags );
    	return selectedTag.name;
    };

    $scope.setGridStyle = function( multiplier, imageTags, tagMapping ){
        if( _.isUndefined(multiplier) )
            multiplier = 300;
    	var selectedTag = $pwImages.selectImageTag( imageTags, tagMapping );
        //$log.debug( 'setGridStyle : $pwImages.selectImageTag  : ', tagMapping );
        var width = selectedTag['width'] * multiplier;
        var height = selectedTag['height'] * multiplier;
        var style = { width: width + "px", height: height + "px" };
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
    	return !_.isUndefined( $scope.$parent.$parent[ functionName ]() );
    }

    $scope.getImageSize = function( prefix, imageTags ){
    	if ( parentFunctionExists( "getImageSize" ) ) 
        	return $scope.$parent.$parent.getImageSize( prefix, imageTags );
    }

    $scope.selectImageTag = function( imageTags, tagMapping ){
    	return $pwImages.selectImageTag( imageTags, tagMapping );
    };

    $scope.setGridClass = function( imageTags ){
    	if ( parentFunctionExists( "setGridClass" ) )
    		return $scope.$parent.$parent.setGridClass( imageTags );
    };

    $scope.setGridStyle = function( imageTags, tagMapping ){
    	if ( parentFunctionExists( "setGridStyle" ) )
    		return $scope.$parent.$parent.setGridStyle( imageTags, tagMapping );
    };

    $scope.tester = function(){
        return "test";
    }


}]);

