/*____           _                      _     _ 
 |  _ \ ___  ___| |___      _____  _ __| | __| |
 | |_) / _ \/ __| __\ \ /\ / / _ \| '__| |/ _` |
 |  __/ (_) \__ \ |_ \ V  V / (_) | |  | | (_| |
 |_|   \___/|___/\__| \_/\_/ \___/|_|  |_|\__,_|

////////////////////////////////////////////////

A Javascript and PHP Wordpress framework for
• Extending the Wordpress functions API
• Displaying posts in creative ways.

JS Framework : AngularJS
GitHub Repo  : https://github.com/phongmedia/postworld/
ASCII Art by : http://patorjk.com/software/taag/#p=display&f=Standard
*/

// Documention by JSDOC
// http://usejsdoc.org/


 /**
 * @ngdoc overview
 * @name postworld
 * @module postworld
 * @description
 *
 * The core module for working with Postworld on the front-end.
 *
 */

'use strict';
pw.partials = {};
pw.templates = {};
pw.feeds = {};
pw.widgets = {};
pw.admin = {};
pw.embeds = {};

// Add Standard Modules
pw.angularModules = pw.angularModules.concat([
	'ngResource',
	'ngRoute',
	'ngSanitize',
	'ngTouch',
	'ngAria',
	'ngAnimate',
	'ui.bootstrap',
	'monospaced.elastic',
	'angular-parallax',
	'wu.masonry',
	'checklist-model',
	'infinite-scroll',
]);

var postworld = angular.module('postworld', pw.angularModules );

///// POSTWORLD ADMIN MODULE /////
var depInjectAdmin = [
	'postworld',
	'ui.slider',
	];

var postworldAdmin = angular.module('postworldAdmin', depInjectAdmin );

var controllerProvider;

postworld.config(function ($routeProvider, $locationProvider, $provide, $logProvider, $controllerProvider ) {   

	// Pass $controllerProvider so that vanilla JS can init new controllers
	controllerProvider = $controllerProvider;

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
	var debugEnabled = ( window.pw.info.mode == 'dev' ) ? true : false;
	$logProvider.debugEnabled( debugEnabled );

	$locationProvider.html5Mode( window.pw.view.location_provider.html_5_mode );
	//$locationProvider.html5Mode( false );

});


/*____              
 |  _ \ _   _ _ __  
 | |_) | | | | '_ \ 
 |  _ <| |_| | | | |
 |_| \_\\__,_|_| |_|        
*/
postworld.run( 
	function( $rootScope, $window, $templateCache, $log, $location, $rootElement, $pw ){    

		///// ALLOW LINK CLICKING /////
		// Critical so that when $locationProvider is in HTML5 mode
		// Normal links can be clicked
		$rootElement.off('click');


		/////// DEV SNIPPETS /////
		// TODO remove in production
		/*
		$rootScope.$on('$viewContentLoaded', function() {
			$templateCache.removeAll();
		});
		*/


		/**
		 * @todo : Refactor these utility function into $pw,
		 * Pass-through functions in rootScope to in service.
		 */

		$rootScope.isLoggedIn = function(){
			return !_.isUndefined( $pw.user.ID );
		}
		$rootScope.isDevice = function(deviceArray){
			if( _.isString(deviceArray) )
				deviceArray = [deviceArray];
			var isDevices = false;
			for( var i = 0; i<deviceArray.length; i++ ){
				// Generate ie. 'is_mobile'
				var checkFor = 'is_'+deviceArray[i];
				// Check if the device is true
				var isDevice = $pw.device[checkFor];
				// If any device is not true, break out
				if( !isDevice )
					break;
				// If all devices checked out
				if( i === (deviceArray.length-1) )
					isDevices = true;
			}
			return isDevices;
		}
		$rootScope.isContext = function(contextArray){
			$log.debug('isContext: ' , contextArray );
			if( _.isString(contextArray) )
				contextArray = [contextArray];
			var context = window
			var isContext = false;
			for( var i = 0; i<contextArray.length; i++ ){
				isContext = _.contains( $pw.view.context, contextArray[i] );
				// If any device is not true, break out
				if( !isContext )
					break;
				// If all context checked out true
				if( i === (contextArray.length-1) )
					isContext = true;
			}
			$log.debug('isContext: ' + JSON.stringify( isContext ), contextArray );
			return isContext;
		}

});


/**
 * Registers a controller in AngularJS after Bootstrapping an app/module.
 *
 * @function pwRegisterController
 * @param {string} controllerName The name of the controller to register
 * @param {string} moduleName The name of the module the controller is part of. Default: 'postworld'
 */
function pwRegisterController( controllerName, moduleName ) {
    // Here I cannot get the controller function directly so I
    // need to loop through the module's _invokeQueue to get it
    if( moduleName == null )
    	moduleName = "postworld";
    
    var queue = angular.module(moduleName)._invokeQueue;
    for(var i=0;i<queue.length;i++) {
        var call = queue[i];
        if(call[0] == "$controllerProvider" &&
           call[1] == "register" &&
           call[2][0] == controllerName) {
           	if( !_.isUndefined( controllerProvider ) )
            	controllerProvider.register(controllerName, call[2][1]);
        }
    }
    //console.log( 'pwRegisterController : ' + controllerName + ', ' + moduleName );
}

///// FUNCTION : COMPILE AND ELEMENT AFTER BOOTSTRAP /////
function pwCompileElement( context, id ){
	// Compile a new element, after the controller is registered
	
	var contextInjector = angular.element(context).injector();

	if( _.isUndefined( contextInjector ) )
		return false;

	contextInjector.invoke(function($compile, $rootScope) {
	    $compile( angular.element('#'+id))($rootScope);
	    $rootScope.$apply();
	});

	/*// USING JQUERY
	jQuery(context).injector().invoke(function($compile, $rootScope) {
	    $compile(jQuery('#'+id))($rootScope);
	    $rootScope.$apply();
	});
	*/
}


////////// REPLACE ALL STRING PROTOTYPE //////////
String.prototype.replaceAll = function(search, replace)
{
    //if replace is null, return original string otherwise it will
    //replace search string with 'undefined'.
    if(!replace) 
        return this;

    return this.replace(new RegExp('[' + search + ']', 'g'), replace);
};


/*
	 __     __  ____    _    _   _ ____  ____   _____  __     __     __
	/ /    / / / ___|  / \  | \ | |  _ \| __ ) / _ \ \/ /    / /    / /
   / /    / /  \___ \ / _ \ |  \| | | | |  _ \| | | \  /    / /    / / 
  / /    / /    ___) / ___ \| |\  | |_| | |_) | |_| /  \   / /    / /  
 /_/    /_/    |____/_/   \_\_| \_|____/|____/ \___/_/\_\ /_/    /_/    
/////////////////////////////////////////////////////////////////*/



postworld.constant('angularMomentConfig', {
	//preprocess: 'unix', 				// optional
	//timezone: 'America/Los_Angeles' 	// optional
});


/*
postworld.run(function($rootScope, $templateCache) {
   $rootScope.$on('$viewContentLoaded', function() {
	  $templateCache.removeAll();
   });
});
*/