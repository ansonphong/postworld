// THE POSTWORLD MODULE
var postworld = angular.module('postworld', []);

// POSTWORLD META CONTROLLER
postworld.controller('postworld', function($scope) {
	$scope.templates = templates;
});

// POSTWORLD FEED CONTROLLER
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