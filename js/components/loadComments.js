'use strict';
/*
 * Angular Tree Implementations
 * recursive templates https://github.com/eu81273/angular.treeview
 * recursive directive https://gist.github.com/furf/4331090
 * lazy loading http://blog.boxelderweb.com/2013/08/19/angularjs-a-lazily-loaded-recursive-tree-widget/
 * 
 * 
 * Tasks
 * Get Comments Service
 * Create Recursive Comment Structure
 * Create Comment Tempalte 
 * 	- with Maximimze/minimize
 *  - 
 * Edit Comment
 * Add Comment/Reply
 * Delete Comment
 * 
 * Sort Options
 * 
 * Show as Tree or Not
 * 
 * 
 * Future: Lazy Loading/Load More/Load on Scrolling, etc...
 * 
 */
postworld.directive('loadComments', function(pwCommentsService) {
    return {
        restrict: 'A',
        replace: true,
        controller: 'pwCommentsController',
        scope: {
        	}
    };
});

postworld.controller('pwCommentsController',
    function pwCommentsController($scope, $location, $log, pwCommentsService, $attrs) {
		$scope.feed	= $attrs.loadComments;    	
    	pwCommentsService.pw_get_comments($scope.feed);
    }
);
