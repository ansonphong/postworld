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


postworld.directive( 'pwGridItem', [ function($scope){
    return {
        restrict: 'AE',
        controller: 'pwGridItemCtrl',
        link: function( $scope, element, attrs ){
            //$scope.gridItem = {};

            // OBSERVE Attribute
            //attrs.$observe('gridFixedHeight', function(value) {
              //$scope.$parent.gridFeed.gridFixedHeight = value;
            //});
        }
    };
}]);

postworld.controller('pwGridItemCtrl',
    [ "$scope", "$window", "_", "$log",
    function($scope, $window,  $_, $log ) {

    

    var gridSettings = ( $_.objExists( $scope, 'feedSettings.view.settings.grid' ) ) ?
        $scope.feedSettings.view.settings.grid : 
        {};

    // $scope.post.image
    $log.debug( "LOAD GRID : " + $scope.post.post_title, gridSettings );

    var gridWidth = gridSettings['width'];
    var gridHeight = gridSettings['height'];

    $scope.getImageSize = function(){
        return "full";
    }


    var imageTags = $scope.post.image['tags'];
    var imageStats = $scope.post.image['stats'];

    var tagMappings = [
    	{
    		name: 'square',
    		width: 1,
    		height: 1,
    	},
    	{
    		name: 'wide',
    		width: 1,
    		height: 1,
    	},
    	
    	{
    		name: 'x-wide',
    		width: 2,
    		height: 1,
    	},
    	/*
    	{
    		name: 'xx-wide',
    		width: 3,
    		height: 1,
    	},
    	*/
    	{
    		name: 'tall',
    		width: 1,
    		height: 1.5,
    	},
    	
    	{
    		name: 'x-tall',
    		width: 1,
    		height: 2,
    	},
    	/*
    	{
    		name: 'xx-tall',
    		width: 1,
    		height: 3,
    	},
    	*/
    ];

    $scope.selectImageTag = function(){
    	var selectedTag = {};
    	// Iterate through each image tag in the selected image
    	angular.forEach( imageTags, function( imageTag ){
    		// Iterate through each mapping option
    		angular.forEach( tagMappings, function( tagMapping ){
    			// Select the last match
    			if( tagMapping['name'] == imageTag )
    				selectedTag = tagMapping;
	    	});
    	});
    	// If none selected
    	if( selectedTag == {} )
    		return false;
    	// Return the selected tag
    	return selectedTag;
    }

    $scope.setGridStyle = function(){
    	var selectedTag = $scope.selectImageTag();
    	var multiplier = 300;
        var width = selectedTag['width'] * multiplier;
        var height = selectedTag['height'] * multiplier;
        return { width: width + "px", height: height + "px", };
    }

    $scope.setGridStyleB = function(){

        var width = gridHeight * imageStats.ratio;
        var height = gridHeight;
        return { width: width + "px", height: height + "px", };

    }

}]);

