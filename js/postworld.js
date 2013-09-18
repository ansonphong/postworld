
///// INITIALIZE /////
var feed_init = {};
var feed_data = {};

///// THE POSTWORLD MODULE /////
var postworld = angular.module('postworld', []);

///// POSTWORLD META CONTROLLER /////
postworld.controller('postworld', function($scope) {
	$scope.templates = templates;
});

////////// TEST FEED DIRECTIVE //////////
// Takes the element with test-feed="feed_id" on it and turns it into a feed
postworld.directive( 'testFeed', ['$compile', function($compile, $scope){

	return { 
		restrict: 'A',
		scope : function(){
			// Scope functions here
		},
		link : function ($scope, elem, attrs){

			///// SETUP /////
			// Get the ID of the feed from the given attribute
			feed_id = attrs.testFeed;
			$scope.feed_id = feed_id;

			// Get the JSON Object associated with the feed_id
			feed_element = angular.element(elem);

			///// TRANSFER SETTINGS /////
			// Transfer ['feed_init'] Object settings into ['feed_data'] Object
			global_feed_object = window['feed_data'][feed_id];
			init_feed_object = window['feed_init'][feed_id];

			angular.forEach(init_feed_object, function(value, key){
				global_feed_object[key] = init_feed_object[key];
			});

			///// ADD CLASSES /////
			// Add feed classes
			feed_element.addClass('feed live_feed');

			// Add class of the current view
			feed_element.addClass( global_feed_object['view']['current'] );
			
			// Add ID attribute classes
			feed_element.attr('ID',feed_id);

			///// ADD CONTROL PANEL /////
			if ( global_feed_object.panel ) {
				panel_id = global_feed_object.panel;
				panel_src = "templates.panels." + panel_id ;
				panel_html = "<div class=\"panel\" ng-include src=\" " + panel_src + "\"></div>";
			}
			else{
				panel = "";
			}

			///// ADD FEED /////
			// Define Variables
			feed_controller = "postworld_feed";
			repeat_pattern = "post in feed_data.posts | orderBy:feed_data.order_by";
			template_source = "get_template(post.post_type)";
			
			// Build DOM Structure
			feed_html = "<div ng-controller=\" " + feed_controller + " \">" + panel_html + "<div class=\"post\" ng-repeat=\" " + repeat_pattern + " \"><div ng-include src=\" " + template_source + " \"></div></div></div>";
			feed_element = angular.element( feed_html );
			elem.append( feed_element );
			
			// Compile DOM into Angular Scope
			$compile( feed_element )( $scope );

		}
	}
}]);


////////// POSTWORLD FEED CONTROLLER //////////
postworld.controller('postworld_feed', function($scope) {

    // Pull feed_data[feed_id] Object for current feed into $scope
    $scope.feed_data = feed_data[ $scope.feed_id ];

    ///// SET VIEW TEMPLATE /////
    $scope.get_template = function (post_type) {
        return $scope.templates.posts[post_type][$scope.feed_data.view.current];
    }

});


////////// UTILITIES //////////

// Disable Template Cache for Development
postworld.run(function($rootScope, $templateCache) {
   $rootScope.$on('$viewContentLoaded', function() {
      $templateCache.removeAll();
   });
});





