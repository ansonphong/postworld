'use strict';

var pwApp = angular.module('pwApp', ['ngResource','ngRoute'])
    .config(function ($routeProvider, $locationProvider) {
        $routeProvider.when('/search-pw/',
            {
                templateUrl: jsVars.pluginurl+'/postworld/templates/pages/pwList.html',				
                controller: 'pwSearchController'
            });
		// this will be also the default route, or when no route is selected
        $routeProvider.otherwise({redirectTo: '/search-pw/'});        
    });