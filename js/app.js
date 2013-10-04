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

/*
 * Getting Organized (Michel):
 * 
 * Whole Components
 ******************
 * Create Simple Search Panel
 * Create Advanced Search Panel
 * Create Post Directive
 * Use Get Templates for Panels and Posts
 * Create Post Views Directive
 * Do we need Directives for non-post types?
 *   
 * TODO List
 * *********
 * 
 * 
 * Adding Feed Item Directive, Update Feed Search with Post Templates Switch
 * 
 * 
 * Refactoring Needed
 * ******************
 * Use App Constants
 * 
 * Issues
 * ******
 * 
 * Enhancements
 * *************
 *  Submitting on Enter of any input field related to the loadPanel Directive - http://stackoverflow.com/questions/15417125/submit-form-on-pressing-enter-with-angularjs
 * 
 * Testing
 * *******
 * Test Multiple Panels in same page
 * Test Multiple Feeds in same page
 * 
 * UI Enhancements
 * ***************
 * Add Animation
 * Add Scrollbar like Facebook
 * 
 * 
 * 
 */
