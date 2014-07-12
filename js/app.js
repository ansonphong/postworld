/*____           _                      _     _ 
 |  _ \ ___  ___| |___      _____  _ __| | __| |
 | |_) / _ \/ __| __\ \ /\ / / _ \| '__| |/ _` |
 |  __/ (_) \__ \ |_ \ V  V / (_) | |  | | (_| |
 |_|   \___/|___/\__| \_/\_/ \___/|_|  |_|\__,_|

////////////////////////////////////////////////

A Wordpress plugin for
• Extending the Wordpress functions API
• Displaying posts in creative ways.

Framework by : AngularJS
GitHub Repo  : https://github.com/phongmedia/postworld/
ASCII Art by : http://patorjk.com/software/taag/#p=display&f=Standard
*/

'use strict';
var feed_settings = [];



angular.module('myCompile', [], ['$compileProvider', function($compileProvider) {
  // Allows an attribute's value to be evaluated and compiled against the scope, resulting
  // in an angularized template being injected in its place.
  //
  // Note: This directive is suffixed with "unsafe" because it does not sanitize the HTML. It is up
  // to the developer to ensure that the HTML is safe to insert into the DOM.
  //
  // Usage:
  //     HTML: <div my-compile-unsafe="templateHtml"></div>
  //     JS: $scope.templateHtml = '<a ng-onclick="doSomething()">Click me!</a>';
  //     Result: DIV will contain an anchor that will call $scope.doSomething() when clicked.
  $compileProvider.directive('myCompileUnsafe', ['$compile', function($compile) {
    return function(scope, element, attrs) {
      scope.$watch(
        function(scope) {
          // watch the 'compile' expression for changes
          return scope.$eval(attrs.myCompileUnsafe);
        },
        function(value) {
          // when the 'compile' expression changes
          // assign it into the current DOM element
          element.html(value);

          // compile the new DOM and link it to the current
          // scope.
          // NOTE: we only compile .childNodes so that
          // we don't get into infinite loop compiling ourselves
          $compile(element.contents())(scope);
        }
      );
    };
  }]);
}]);


var depInject = [
	'ngResource',
	'ngRoute',
	'ngSanitize',
	// 'ngAnimate', (animate removed for bootstrap carousel)
	'infinite-scroll', 
	'ui.bootstrap',
	'monospaced.elastic',
	'TruncateFilter',
	'pwFilters',
	'timer',
	'angular-parallax',
	'angularMoment',
	'wu.masonry',
	'mgcrea.ngStrap.popover',
	'myCompile',
	];


var postworld = angular.module('postworld', depInject );

//postworld.constant( '$postworld', { version: "1.5.1" } );

postworld.config(function ($routeProvider, $locationProvider, $provide, $logProvider) {   

	var plugin_url = jsVars.pluginurl;

	////////// ROUTE PROVIDERS //////////
	$routeProvider.when('/live-feed-1/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/pwLiveFeed1Widget.html',                           
		});
	$routeProvider.when('/live-feed-2/',    
		{
			templateUrl: plugin_url+'/postworld/templates/samples/pwLiveFeed2Widget.html',
			// reloadOnSearch: false,                
		});
	$routeProvider.when('/live-feed-2-feeds/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/pwLiveFeed3Widget.html',                
		});
	$routeProvider.when('/live-feed-with-ads/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/pwLiveFeed6Widget.html',                
		});
	$routeProvider.when('/live-feed-2-feeds-auto/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/pwLiveFeed4Widget.html',                
		});
	$routeProvider.when('/live-feed-params/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/pwLiveFeed5Widget.html',                
		});
	$routeProvider.when('/load-feed-1/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/pwLoadFeed1Widget.html',                
		});
	$routeProvider.when('/load-feed-2/',
		{
			template: '<h2>Coming Soon</h2>',               
		});
	$routeProvider.when('/load-feed-2-feeds/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/pwLoadFeed3Widget.html',                
		});
	$routeProvider.when('/load-feed-cached-outline/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/pwLoadFeed4Widget.html',                
		});
	$routeProvider.when('/load-feed-ads/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/pwLoadFeed5Widget.html',                
		});
	$routeProvider.when('/load-panel/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/pwLoadPanelWidget.html',                
		});
	$routeProvider.when('/register-feed/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/pwRegisterFeedWidget.html',             
		});
	$routeProvider.when('/home/',
		{
				template: '<h2>Coming Soon</h2>',               
		});
	$routeProvider.when('/edit-post/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/editPost.html',                
		});

	$routeProvider.when('/load-comments/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/pwLoadCommentsWidget.html',             
		});            
	$routeProvider.when('/embedly/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/pwEmbedlyWidget.html',             
		});            
	$routeProvider.when('/load-post/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/pwLoadPostWidget.html',             
		});            
	$routeProvider.when('/o-embed/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/pwEmbedWidget.html',             
		});       
	$routeProvider.when('/media-modal/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/mediaModal.html',             
		});  
	$routeProvider.when('/post-link/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/postLink.html',             
		});  
	$routeProvider.when('/test-post/',
		{
			templateUrl: plugin_url+'/postworld/templates/samples/pwTestWidget.html',             
		});  



	$routeProvider.when('/new/:post_type',
		{
			action: "new_post",
		});

	$routeProvider.when('/new/',
		{
			action: "new_post",
		});

	$routeProvider.when('/edit/:post_id',
		{
			action: "edit_post",
		});

	$routeProvider.when('/home/',
		{
			action: "default",
		});

	// this will be also the default route, or when no route is selected
	// $routeProvider.otherwise({redirectTo: '/home/'});

	// SHOW / HIDE DEBUG LOGS IN CONSOLE
	// Comment out for development
	$logProvider.debugEnabled(true);

	//$locationProvider.html5Mode(true);

});


/*____              
 |  _ \ _   _ _ __  
 | |_) | | | | '_ \ 
 |  _ <| |_| | | | |
 |_| \_\\__,_|_| |_|        
*/
postworld.run(function($rootScope, $window, $templateCache, $log, pwData) {    

	// TODO remove in production
	/*
	$rootScope.$on('$viewContentLoaded', function() {
	  $templateCache.removeAll();
	});
	*/

   //$rootScope.current_user = $window.pwGlobals.current_user;
   //$log.debug('Current user: ', $rootScope.current_user );

});
   


/*
	 __     __  ____    _    _   _ ____  ____   _____  __     __     __
	/ /    / / / ___|  / \  | \ | |  _ \| __ ) / _ \ \/ /    / /    / /
   / /    / /  \___ \ / _ \ |  \| | | | |  _ \| | | \  /    / /    / / 
  / /    / /    ___) / ___ \| |\  | |_| | |_) | |_| /  \   / /    / /  
 /_/    /_/    |____/_/   \_\_| \_|____/|____/ \___/_/\_\ /_/    /_/    
/////////////////////////////////////////////////////////////////*/



angular.module('postworld').constant('angularMomentConfig', {
	preprocess: 'unix', 				// optional
	//timezone: 'America/Los_Angeles' 	// optional
});


/*
postworld.run(function($rootScope, $templateCache) {
   $rootScope.$on('$viewContentLoaded', function() {
	  $templateCache.removeAll();
   });
});
*/