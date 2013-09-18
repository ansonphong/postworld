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
postworld.directive( 'testFeed', ['$compile', function($compile, $scope){

	return { 
		restrict: 'A',
		scope : function(){
			//scope.feed_id = 'search_results'; // NOT WORKING
			//parentFunc: '&'
			//return feed_id = "search_results";
			feed_id: '=feed_id'
		},
		link : function ($scope, elem, attrs){

			///// SETUP /////
			// Get the ID of the feed from the given attribute
			feed_id = attrs.testFeed;
			$scope.feed_id = feed_id;

			// Get the JSON Object associated with the feed_id
			feed_object = window[feed_id];
			feed_element = angular.element(elem);


			///// TRANSFER SETTINGS /////
			// Transfer Local Feed Data into global feed_data Object
			global_feed_object = window['feed_data'][feed_id];
			local_feed_object = window[feed_id];

			angular.forEach(local_feed_object, function(value, key){
				global_feed_object[key] = local_feed_object[key];
			});

			/*
			// Order By
			global_feed_object['order_by'] = local_feed_object['order_by'];
			// View
			global_feed_object['view'] = local_feed_object['view'];
			// Panel
			global_feed_object['panel'] = local_feed_object['panel'];
			*/

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
			feed_controller = "postworld_feed_dev";
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



///// POSTWORLD FEED CONTROLLER /////
postworld.controller('postworld_feed_dev', function($scope) {

	// Define Posts
    // Pull 'feed_data' Object for current feed into $scope
    $scope.feed_data = feed_data[ $scope.feed_id ];

    // Feed Template
    $scope.get_template = function (post_type) {
        return $scope.templates.posts[post_type][$scope.feed_data.view.current];
    }

});





