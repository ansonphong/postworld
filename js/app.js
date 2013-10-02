'use strict';

var feed_settings = [];

var pwApp = angular.module('pwApp', ['ngResource','ngRoute','infinite-scroll'])
    .config(function ($routeProvider, $locationProvider) {
        $routeProvider.when('/livefeed/',
            {
                templateUrl: jsVars.pluginurl+'/postworld/templates/pages/pwLiveFeedWidget.html',				
                // controller: 'pwSearchController'
            });
		// this will be also the default route, or when no route is selected
        $routeProvider.otherwise({redirectTo: '/livefeed/'});
    });
