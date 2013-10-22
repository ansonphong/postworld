'use strict';
/*
 * Angular Tree Implementations
 * recursive templates https://github.com/eu81273/angular.treeview
 * recursive directive https://gist.github.com/furf/4331090
 * lazy loading http://blog.boxelderweb.com/2013/08/19/angularjs-a-lazily-loaded-recursive-tree-widget/
 * 
 */
postworld.directive('loadComments', function() {
    return {
        restrict: 'A',
        replace: true,
        controller: 'pwCommentsController',
        scope: {
        	}
    };
});

postworld.controller('pwCommentsController',
    function pwCommentsController($scope, $location, $log, pwData, $attrs) {
    	console.log('hello comments');
    }
);
