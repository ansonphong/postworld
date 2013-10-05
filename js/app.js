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
 * Create Post Types in Search Panel Dynamic
 *   
 * TODO List
 * *********
 * Create Startup code that runs at app startup, and put getting templates into it
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
 * Make the Infinite Scroller reset scrolling up when refreshing
 * 
 * Enhancements
 * *************
 *  Submitting on Enter of any input field related to the loadPanel Directive - http://stackoverflow.com/questions/15417125/submit-form-on-pressing-enter-with-angularjs
 * Fix Bootstrap field alignment
 * Shouldnt we get all templates in pw_get_templates, and cache them to be used across the whole session? this will save many uneeded calls as long as we're on the same SPA session?
 * we need to add pw_get_template, to get a single template
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
 * Questions
 * *********
 * How will the Search and Other Pages be presented? Templates? Pages? Widgets? other?
 * 
 * 
 */
