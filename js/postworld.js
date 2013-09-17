///// THE POSTWORLD MODULE /////
var postworld = angular.module('postworld', []);

///// POSTWORLD META CONTROLLER /////
postworld.controller('postworld', function($scope) {
	$scope.templates = templates;
});


///// POSTWORLD FEED CONTROLLER /////
postworld.controller('postworld_feed', function($scope) {

	// Define Posts
    $scope.posts = feed_data[$scope.feed].posts; 

    // Pull 'feed_data' Object for current feed into $scope
    $scope.feed_data = feed_data[$scope.feed];

    // Feed Template
    $scope.get_template = function (post_type) {
        return $scope.templates.posts[post_type][$scope.feed_data.view.current];
    }

});


// Disable Template Cache for Development
postworld.run(function($rootScope, $templateCache) {
   $rootScope.$on('$viewContentLoaded', function() {
      $templateCache.removeAll();
   });
});


////////// LIVE FEED DIRECTIVE //////////
// Takes the element with live-feed="feed_id" on it and turns it into a feed
postworld.directive( 'liveFeed', ['$compile', function($compile){

	return { 
		restrict: 'A',
		link : function (scope, elem, attrs, transclude){

			///// SETUP /////
			// Get the ID of the feed from the given attribute
			feed_id = attrs.liveFeed;

			// Get the JSON Object associated with the feed_id
			feed_object = window[feed_id];

			///// ADD CLASSES /////
			// Add feed classes
			angular.element(elem).addClass('feed live_feed');

			// Add class of the current view
			angular.element(elem).addClass( feed_object['view']['current'] );
			
			// Add ID attribute classes
			angular.element(elem).attr('ID',feed_id);


			///// ADD CONTROLLER /////
			angular.element(elem).attr('ng-controller','postworld_feed_dev');
			//anglar.bootstrap(elem);
			//$scope.apply();

			///// ADD PANEL /////
			// If panel is defined
			if ( feed_object.panel ) {
				panel_id = feed_object.panel;
				panel_html = "<div class=\"panel\" ng-include src=\"templates.panels." + panel_id + "\"></div>";
				var panel_element = angular.element( panel_html );
				elem.prepend( panel_element );
				$compile( panel_element )(scope);
			}

		}
	}
}]);



///// POSTWORLD FEED CONTROLLER /////
postworld.controller('postworld_feed_dev', function($scope) {

	// Define Posts
    // Pull 'feed_data' Object for current feed into $scope
    $scope.feed_data = feed_data['search_results'];


    // Feed Template
    $scope.get_template = function (post_type) {
        return $scope.templates.posts[post_type][$scope.feed_data.view.current];
    }

});





