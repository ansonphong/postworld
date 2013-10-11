'use strict';
var feed_settings = [];

var pwApp = angular.module('pwApp', ['ngResource','ngRoute','infinite-scroll'])
    .config(function ($routeProvider, $locationProvider) {    	    	
        $routeProvider.when('/live-feed-1/',
            {
                templateUrl: jsVars.pluginurl+'/postworld/templates/samples/pwLiveFeed1Widget.html',				
            });
        $routeProvider.when('/live-feed-2/',
            {
                templateUrl: jsVars.pluginurl+'/postworld/templates/samples/pwLiveFeed2Widget.html',				
            });
        $routeProvider.when('/live-feed-3/',
            {
                template: '<h2>Coming Soon</h2>',				
            });
        $routeProvider.when('/load-feed-1/',
            {
                templateUrl: jsVars.pluginurl+'/postworld/templates/samples/pwLoadFeed1Widget.html',				
            });
        $routeProvider.when('/load-feed-2/',
            {
                template: '<h2>Coming Soon</h2>',				
            });
        $routeProvider.when('/load-panel/',
            {
                templateUrl: jsVars.pluginurl+'/postworld/templates/samples/pwLoadPanelWidget.html',				
            });
        $routeProvider.when('/register-feed/',
            {
                templateUrl: jsVars.pluginurl+'/postworld/templates/samples/pwRegisterFeedWidget.html',				
            });
        $routeProvider.when('/home/',
            {
                templateUrl: jsVars.pluginurl+'/postworld/templates/samples/pwLiveFeed2Widget.html',				
            });
		// this will be also the default route, or when no route is selected
        $routeProvider.otherwise({redirectTo: '/home/'});
    });

// Submit on Enter, without a real form
pwApp.directive('ngEnter', function() {
        return function(scope, element, attrs) {
            element.bind("keydown keypress", function(event) {
                if(event.which === 13) {
                    scope.$apply(function(){
                        //scope.$eval(attrs.ngEnter);
                        scope.$eval("submit()");
                    });
                    event.preventDefault();
                }
            });
        };
    });
    
    

pwApp.run(function($rootScope, $templateCache,pwData) {	
    	// TODO move getting templates to app startup
    	pwData.pw_get_templates(null).then(function(value) {
		    // TODO should we create success/failure responses here?
		    // resolve pwData.templates
		    pwData.templates.resolve(value.data);
		    pwData.templatesFinal = value.data;
		    console.log('pwApp RUN getTemplates=',pwData.templatesFinal);
		  });    	
// TODO remove in production
   $rootScope.$on('$viewContentLoaded', function() {
      $templateCache.removeAll();
   });
});
   
/*
 * Getting Organized (Michel):
 * 
 * Whole Components
 ******************
 * Create Advanced Search Panel [complete missing boxes]
 * Do we need Directives for non-post types?
 * Create Post Types Toggles in Search Panel Dynamically http://jsfiddle.net/BtrZH/5/
 * 
 * Create Edit Fields for Radio, checkbox, TinMCE (WP has an hook for it), Buttons
 * 	Add Validations
 * 	Add Dynamic Sub Forms [ng-switch]
 * 	Add Embedding of URLs [embed.ly?]
 * 	Will be used in URL like #/post/edit/id, #/post/new, etc...
 * 	Will Switch between forms dynamically
 * 
 * TODO List
 * *********
 * Create Startup code that runs at app startup, and put getting templates into it
 *  * 
 * Refactoring Needed
 * ******************
 * Use App Constants
 * 
 * Issues
 * ******
 * Button for Feed Templates need to be toggled and populated from Feed Settings
 * NONCE - not active yet
 * Feed_settings must have a template URL for feed []
 * Remove additional fields added to args and saved with register_feed()
 * Add Parameters to URL of the Live Feed / Search parameters - add that to our menu as an example
 * 
 * Enhancements
 * *************
 * Submitting on Field Change
 * Fix Bootstrap field alignment
 * Shouldnt we get all templates in pw_get_templates, and cache them to be used across the whole session? this will save many uneeded calls as long as we're on the same SPA session?
 * 
 * Testing
 * *******
 * 
 * UI Enhancements
 * ***************
 * Add Animation
 * Add Scrollbar like Facebook
 * Make Simple Searrch panel show number of resuts.
 * 
 * Questions
 * *********
 * How will the Search and Other Pages be presented? Templates? Pages? Widgets? other?
 * 
 * 
 */