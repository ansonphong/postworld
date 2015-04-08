'use strict';
/*____  _               _   _                
 |  _ \(_)_ __ ___  ___| |_(_)_   _____  ___ 
 | | | | | '__/ _ \/ __| __| \ \ / / _ \/ __|
 | |_| | | | |  __/ (__| |_| |\ V /  __/\__ \
 |____/|_|_|  \___|\___|\__|_| \_/ \___||___/
											 
////////// ------------ DIRECTIVES ------------ //////////*/

////////// PW GLOBALS //////////
// This directive sets the $pw service object into the local scope
// Just specifiy which scope object to map it to
// EXAMPLE : <div pw-globals="pw"><pre>{{ pw | json }}</pre></div> 
postworld.directive( 'pwGlobals',
	[ '$pw', '_', '$log',
	function( $pw, $_, $log ){
	return{
		scope:{
			pwGlobals:"=",
		},
		link : function( $scope, element, attrs ){
			$scope.pwGlobals = $pw;
		}
	}
}]);

////////// PW INCLUDE //////////
postworld.directive('pwInclude', function($log, $timeout, pwData) {
	// Used to include a Postworld template partial within an isolated scope
	// Post and meta data can be easily make available in the template
	// Example : <div pw-include="galleries/gallery-frame" include-post="post"></div>
	// Uses ng-include to include the file at {pwTemplatesPath}/galleries/gallery-frame.html
	// And double binds the passed post object into the isolated scope 
	return {
		restrict: 'EA',
		//replace: true,
		template: '<div ng-include="includeUrl" class="pw-include" ng-class="includeClass"></div>',
		scope:{
			// Must use an isolated scope, to allow for using multiple include directives in the same page
			vars:"=includeVars",		//	Vars to assigned within the include as $scope.vars
			includeMeta:"=",		// 	Object to be assigned as $scope.meta within the include
			includePost:"=",		//	Object to be assigned as $scope.post within the include
			includeEnable:"=",		// 	Whether or not to actually enable the load the include
			includeClass:"@",		//	Class(es) to be added to the include element
		},
		link: function($scope, element, attrs){

			var setTemplateUrl = function(){
				var pwInclude = attrs.pwInclude;
				var parts = pwInclude.split('/');
				if( parts.length < 2 ){
					$log.debug( 'pwInclude : ERROR : Include must contain 2 parts, dir/basename.' )
					return false;
				}
				if($scope.includeEnable !== false )
					$scope.includeUrl = pwData.pw_get_template( { subdir: parts[0], view: parts[1] } );
				else
					$scope.includeUrl = '';
				//$log.debug('pwInclude : ' + attrs.pwInclude, $scope.includeUrl );
			}

			attrs.$observe( 'pwInclude', function( pwInclude ){
				setTemplateUrl();
			});

			$scope.$watch('includeEnable', function(val){
				setTemplateUrl();
			});

			// Pipe post data into the isolated include scope as 'post' object
			$scope.$watch('includePost', function( val ){
				//$log.debug( 'pwInclude : includePost', val );
				if( !_.isUndefined( val ) )
					$scope.post = $scope.includePost;
			}, 1 );

			// Pipe post data into the isolated include scope as 'meta' object
			$scope.$watch('includeMeta', function( val ){
				//$log.debug( 'includePanel : includeMeta', val );
				if( !_.isUndefined( val ) )
					$scope.meta = $scope.includeMeta;
			}, 1 );

			// Watch Include Enable and hide element if it's not enabled
			$scope.$watch('includeEnable', function( val ){
				//$log.debug( 'pwInclude : includeEnable', val );
				if( !_.isUndefined( val ) && !_.isNull( val ) ){
					if( val === false )
						element.addClass( 'ng-hide' );
					else
						element.removeClass( 'ng-hide' );
				}
			}, 1 );

		}
	};
});



///// LOAD PANEL /////
// DEPRECIATED AS OF VERSION 1.88
// Instead use pw-include
postworld.directive('loadPanel', function($log, $timeout, pwData) {
	return {
		restrict: 'EA',
		replace: true,
		template: '<div ng-include="templateUrl" class="load-panel" ng-class="panelClass"></div>',
		scope:{
			// Must use an isolated scope, to allow for using multiple panel directives in the same page
			panelVars:"@",		//	Vars to assigned within the panel as $scope.vars
			panelMeta:"=",		// 	Object to be assigned as $scope.meta within the panel
			panelPost:"=",		//	Object to be assigned as $scope.post within the panel
			panelEnable:"=",	// 	Whether or not to actually enable the load panel
			panelClass:"@",		//	Class(es) to be added to the panel element
		},
		link: function($scope, element, attrs){

			attrs.$observe( 'loadPanel', function( loadPanel ){
				if($scope.panelEnable !== false )
					$scope.templateUrl = pwData.pw_get_template( { subdir: 'panels', view: loadPanel } );
				$log.debug('loadPanel :' + attrs.loadPanel, $scope.templateUrl );
			});

			attrs.$observe( 'panelVars', function( val ){
				var panelVars = $scope.$eval( $scope.panelVars );
				$log.debug( 'loadPanel : panelVars :', panelVars );
				if( !_.isUndefined( panelVars ) )
					$scope.vars = $scope.vars;
			});

			// Pipe post data into the isolated panel scope as 'post' object
			$scope.$watch('panelPost', function( val ){
				$log.debug( 'loadPanel : panelPost', val );
				if( !_.isUndefined( val ) )
					$scope.post = $scope.panelPost;
			}, 1 );

			// Pipe post data into the isolated panel scope as 'meta' object
			$scope.$watch('panelMeta', function( val ){
				$log.debug( 'loadPanel : panelMeta', val );
				if( !_.isUndefined( val ) )
					$scope.meta = $scope.panelMeta;
			}, 1 );

			/*
			$scope.getPanelClass = function(){
				// Add the class of the load panel name
				var classes = attrs.loadPanel;
				if( !_.isNull( $scope.panelClass ) && !_.isUndefined( $scope.panelClass ) )
					classes = classes + " " + $scope.panelClass;
				return classes;
			}
			*/

		}
	};
});


///// POSTWORLD SANITIZE DIRECTIVE /////
postworld.directive('pwSanitize', function() {
	return {
		require: '?ngModel',
		link: function( $scope, element, attrs, ngModel ) {

			function process( value ){
				switch( attrs.pwSanitize ){
					case 'id':
						value = value.replace( ' ', '-', 'm' );
						break;
				}
				return value;
			};

			function update(){
				var value = String( element.context.value );
				value = process( value );
				ngModel.$setViewValue( value );
				ngModel.$render();
			}

			$scope.$watch( attrs.ngModel, function(n,o) {
				update();
			});
			
			/*
			ngModel.$formatters.push(function(value) {
				return value;
			});
			*/

		},
	}
});


///// POSTWORLD SRC DIRECTIVE /////
postworld.directive('pwSrc', function( $log ) {
	return {
		scope:{
		  pwSrc:"=pwSrc"
		},
		link: function( scope, element, attrs ) {
			scope.$watch( 'pwSrc', function(){
				attrs.$set('src', scope.pwSrc );
			});
		},
	}
});


///// POSTWORLD HREF DIRECTIVE /////
postworld.directive('pwHref', function() {
	return {
		scope:{
		  pwHref:"=pwHref"
		},
		link: function(scope, element, attrs) {
			
			scope.$watch( 'pwHref', function(){
				attrs.$set('href', scope.pwHref );
			});

			//var fullPathUrl = "http://.../";
			/*
			if(element[0].tagName === "A") {
				attrs.$set('href',fullPathUrl + attrs.fullPath);
			} else {
				attrs.$set('src',fullPathUrl + attrs.fullPath);
			}*/

		},
	}
});


///// POSTWORLD BACKGROUND IMAGE /////
// Add a background image style to an element
postworld.directive('pwBackgroundImage', function( $log ) {
	return {
		scope:{
		  pwBackgroundImage:"="
		},
		link: function( $scope, element, attrs ) {
			$scope.$watch( 'pwBackgroundImage', function(val){
				element.css( 'background-image', 'url('+val+')' );
			});
		},
	}
});


///// POSTWORLD EVAL DIRECTIVE /////
// Evaluates a string as javascript at the time of loading
// Works well for initializing third-party libraries
postworld.directive('pwEval', function($timeout, $log) {
	return {
		scope:{
		  pwEval:"@",
		  evalTimeout:"@",
		  evalContext:"@",
		},
		link: function($scope, element, attrs) {
			if( _.isUndefined( $scope.evalTimeout ) )
				$scope.evalTimeout = 0;
			if( _.isUndefined( $scope.evalContext ) )
				$scope.evalContext = 'window';
			$timeout(
				function(){
					$log.debug( 'pw-eval : ', $scope.pwEval );
					try{
						if( $scope.evalContext == 'scope' )
							$scope.$eval($scope.pwEval);
						else
							eval($scope.pwEval);
					}
					catch(err){
						$log.debug('pw-eval : ERROR : ' + $scope.pwEval, err);
					}
				}, $scope.evalTimeout
			);
		},
	}
});

///// POSTWORLD NEW TARGET DIRECTIVE /////
postworld.directive('pwTarget', function( $log ) {
	return {
		link: function( scope, element, attrs ) {

			scope.$watch(
				function(scope) {
					// watch the 'new target' expression for changes
					return scope.$eval( attrs.pwTarget );
				},
				function(value) {
					// If a boolean is provided
					if( _.isBoolean( value ) ){
						if( value )
							attrs.$set('target', "_blank" );
						else
							attrs.$set('target', "_self" );
					}
					// Set target to the specified value
					else if( !_.isNull( value ) && !_.isUndefined( value ) ){
						attrs.$set('target', value );
					}
				}
			);

		},
	}
});


///// SUBMIT ON ENTER /////
// Submit on Enter, without a real form
postworld.directive('ngEnter', function() {

  	return function(scope, element, attrs) {
			
		element.bind("keydown keypress", function(event) {
		  	if(event.which === 13) {
				scope.$apply(function(){
					if( attrs.ngEnter )
						scope.$eval(attrs.ngEnter);
					else
						scope.$eval("submit()");
				});
				event.preventDefault();
			}
		});
		
	};

});


///// PREVENT DEFAULT ON CLICK /////
postworld.directive('preventDefaultClick', function() {
		return {
			restrict: 'A',
			link: function (scope, element) {
				element.bind('click', function (event) {
					//event.stopPropagation();
					event.preventDefault();
				});
			}
		};
	});


///// PREVENT DEFAULT ON CLICK /////
postworld.directive('stopPropagationClick', function() {
		return {
			restrict: 'A',
			link: function (scope, element) {
				element.bind('click', function (event) {
					event.stopPropagation();
				});
			}
		};
	});


///// SELECT ON CLICK /////
postworld.directive('selectOnClick', function () {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            element.on('click', function () {
                this.select();
            });
        }
    };
});


///// AUTO FOCUS /////
// Automatically focuses the input field it's applied to
postworld.directive('pwAutofocus', function($timeout) {
	return {
		link: function ( scope, element, attrs ) {
			scope.$watch( attrs.ngFocus, function ( val ) {
				$timeout( function () { element[0].focus(); } );
			}, true);
		}
	};
});


/*_   _                        ____ _               
 | | | | _____   _____ _ __   / ___| | __ _ ___ ___ 
 | |_| |/ _ \ \ / / _ \ '__| | |   | |/ _` / __/ __|
 |  _  | (_) \ V /  __/ |    | |___| | (_| \__ \__ \
 |_| |_|\___/ \_/ \___|_|     \____|_|\__,_|___/___/
////////////// POSTWORLD HOVER CLASS //////////////*/
// Adds specified class(es) to an element on mouseover
// And removes the classes on mouseleave
// Optional attributes include hover-on-delay and hover-off-delay
// Which are specified in the number of milliseconds
// Before the class is added / removed 

postworld.directive('pwHoverClass', function ( $timeout ) {
    return {
        restrict: 'A',
        scope: {
            pwHoverClass: '@',	// classes to add when hovered
            hoverOnDelay: '@',	// milliseconds
            hoverOffDelay: '@',	// milliseconds
        },
        link: function ( $scope, element, attrs ) {
        	// mouseIsOver // Variable to track if mouse is currently over
        	// Prevents the hover class from getting locked on
        	// In the case that the ON delay is greater than the OFF delay
        	// And the mouse passes on and off the element in less time than their difference
        	var mouseIsOver = false;

            element.on('mouseenter', function() {
            	mouseIsOver = true;
            	if( _.isUndefined($scope.hoverOnDelay) )
            		$scope.hoverOnDelay = 0;
            	$timeout( function(){
            		if( mouseIsOver == true )
	            		element.addClass($scope.pwHoverClass);
            	}, parseInt($scope.hoverOnDelay) );
            });
            element.on('mouseleave', function() {
            	mouseIsOver = false;
                if( _.isUndefined($scope.hoverOffDelay) )
            		$scope.hoverOffDelay = 0;
                $timeout( function(){
            		element.removeClass($scope.pwHoverClass);
            	}, parseInt($scope.hoverOffDelay) );
            });
        }
    };
})



/*_                                             
 | |    __ _ _ __   __ _ _   _  __ _  __ _  ___ 
 | |   / _` | '_ \ / _` | | | |/ _` |/ _` |/ _ \
 | |__| (_| | | | | (_| | |_| | (_| | (_| |  __/
 |_____\__,_|_| |_|\__, |\__,_|\__,_|\__, |\___|
									 |___/     
////////// POSTWORLD LANGUAGE ACCESS //////////*/
postworld.directive( 'pwLanguage', [function(){
		return { 
			controller: 'pwLanguageCtrl'
		};
}]);

postworld.controller('pwLanguageCtrl',
		['$scope','$window',
		function($scope, $window) {

			$scope.lang = 'en';
			$scope.lang_options = {
				'en': 'English',
				'es': 'Español'
			};

			$scope.language = $window.pwSiteLanguage;
			//$scope.l = $window.pwSiteLanguage;
						
		/*
		$scope.parseHTML = function(string){
				return $sce.parseAsHtml(string);
		};
		*/

}]);



/*_____ _                            _   
 |_   _(_)_ __ ___   ___  ___  _   _| |_ 
   | | | | '_ ` _ \ / _ \/ _ \| | | | __|
   | | | | | | | | |  __/ (_) | |_| | |_ 
   |_| |_|_| |_| |_|\___|\___/ \__,_|\__|

////////// POSTWORLD TIMEOUT //////////*/
// Run an action in an isolated scope on a timeout

postworld.directive('pwTimeout', function( $timeout ) {
	return {
		scope:{
			pwTimeout:"@",
			timeoutAction:"@",
			timeoutMultiplier:"@",
		},
		link: function( $scope, element, attrs ) {
			
			var timeoutPeriod = ( _.isUndefined( $scope.timeoutMultiplier ) || _.isEmpty( $scope.timeoutMultiplier ) ) ?
				$scope.pwTimeout : $scope.pwTimeout * $scope.timeoutMultiplier;

			$timeout( function(){
				// Evaluate passed local function
				$scope.$eval( $scope.timeoutAction );
				// Destroy Scope
				$scope.$destroy();
			}, parseInt(timeoutPeriod) ); // 

			$scope.addClass = function( classes ){
				element.addClass( classes );
			}
		},
	}
});



/**
  ____                 _ _  __ _      
 / ___|  ___ _ __ ___ | | |/ _(_)_  __
 \___ \ / __| '__/ _ \| | | |_| \ \/ /
  ___) | (__| | | (_) | | |  _| |>  < 
 |____/ \___|_|  \___/|_|_|_| |_/_/\_\

 * Adds a 'ui-scrollfix' class to the element when the page scrolls past it's position.
 * @param [offset] {int} optional Y-offset to override the detected offset.
 *   Takes 300 (absolute) or -300 or +300 (relative to detected)
 */
postworld.directive('uiScrollfix', ['$window', function ($window) {
	return {
		require: '^?uiScrollfixTarget',
		link: function (scope, elm, attrs, uiScrollfixTarget) {
			var top = elm[0].offsetTop,
					$target = uiScrollfixTarget && uiScrollfixTarget.$element || angular.element($window);

			if (!attrs.uiScrollfix) {
				attrs.uiScrollfix = top;
			} else if (typeof(attrs.uiScrollfix) === 'string') {
				// charAt is generally faster than indexOf: http://jsperf.com/indexof-vs-charat
				if (attrs.uiScrollfix.charAt(0) === '-') {
					attrs.uiScrollfix = top - parseFloat(attrs.uiScrollfix.substr(1));
				} else if (attrs.uiScrollfix.charAt(0) === '+') {
					attrs.uiScrollfix = top + parseFloat(attrs.uiScrollfix.substr(1));
				}
			}

			function onScroll() {
				// if pageYOffset is defined use it, otherwise use other crap for IE
				var offset;
				if (angular.isDefined($window.pageYOffset)) {
					offset = $window.pageYOffset;
				} else {
					var iebody = (document.compatMode && document.compatMode !== 'BackCompat') ? document.documentElement : document.body;
					offset = iebody.scrollTop;
				}
				if (!elm.hasClass('ui-scrollfix') && offset > attrs.uiScrollfix) {
					elm.addClass('ui-scrollfix');
				} else if (elm.hasClass('ui-scrollfix') && offset < attrs.uiScrollfix) {
					elm.removeClass('ui-scrollfix');
				}
			}

			$target.on('scroll', onScroll);

			// Unbind scroll event handler when directive is removed
			scope.$on('$destroy', function() {
				$target.off('scroll', onScroll);
			});
		}
	};
}]).directive('uiScrollfixTarget', [function () {
	return {
		controller: ['$element', function($element) {
			this.$element = $element;
		}]
	};
}]);


postworld.directive('pwScrollfix', function( $window, $log, $timeout ) {
	return {
		scope:{
			pwScrollfix:"@",
			scrollfixYOffset:"@",
			scrollfixYClass:"@",
		},
		link: function( $scope, element, attrs ) {
			
			// Define default classes
			$scope.scrollfixYClass = ( !_.isUndefined( $scope.scrollfixYClass ) ) ?
				$scope.scrollfixYClass : "scrollfix-y";

			$scope.addClass = function( classes ){
				element.addClass( classes );
			}

			$scope.getIdHeight = function( elementId ){
				var element = angular.element( document.querySelector( '#' + elementId ) ).context;
				return element.offsetHeight;
			}

			$scope.pageYOffset = function(){
				return $window.pageYOffset;
			}

			function onYScroll(){
				var yOffset = $scope.$eval( $scope.scrollfixYOffset );
				//$log.debug( 'yOffset', yOffset  );
				//$log.debug( 'pageYOffset', $window.pageYOffset  );
	
				// If specified Y offset is greater than how far the page is scrolled vertically
				if( yOffset < $window.pageYOffset ){
					
					// Add class to the element
					element.addClass( $scope.scrollfixYClass );
				}
				else{
					// Remove class from the element
					element.removeClass( $scope.scrollfixYClass );
				}
			}

			// If scrollfixYOffset is defined
			if( !_.isUndefined( $scope.scrollfixYOffset ) )
				// Run onYScroll function when window is scrolled 
				angular.element($window).bind("scroll", onYScroll);

		},
	}
});


//////////////////// ADMIN ////////////////////
////////// TEMPLATES //////////
postworld.directive( 'pwAdminTemplates',
	[ '$pw', '_', '$log',
	function( $pw, $_, $log ){
	return{
		scope:{
			pwAdminTemplates:"=",
		},
		link : function( $scope, element, attrs ){
			var templates = $_.get( $pw, 'admin.templates' );
			$scope.pwAdminTemplates = templates;
		}
	}
}]);


////////// SIDEBARS //////////
postworld.directive( 'pwSidebars',
	[ '$pw', '_', '$log',
	function( $pw, $_, $log ){
	return{
		scope:{
			pwSidebars:"=",
		},
		link : function( $scope, element, attrs ){
			var sidebars = $_.get( $pw, 'admin.sidebars' );
			$scope.pwSidebars = sidebars;
		}
	}
}]);


////////// SHARE LINK //////////
postworld.directive( 'pwShareLink',
	[ '$pw', '_', '$log',
	function( $pw, $_, $log ){
	return{
		scope:{
			pwShareLink:'=',		// The expression to map the share link to
			shareLinkPostId:'=',	// The ID of the post which to generate the share link for
			shareLinkDynamic:'@'	// If 'true', sets up a watch on the value of shareLinkPostId
		},
		link : function( $scope, element, attrs ){

			// Generates the share link URL
			var generateShareLink = function(postId){
				if( _.isUndefined(postId) )
					return '';

				var userId = $_.get( $pw, 'user.ID' );
				var shareLink = $pw.paths.home_url + "/?u=" + userId + "&p=" + postId;
				$log.debug('SHARE LINK : ', shareLink);
				return shareLink;
			}

			// If share link is dynamic, such as value = 'true'
			if( $_.stringToBool( $scope.shareLinkDynamic ) ){
				$scope.$watch( 'shareLinkPostId', function(postId){
					$scope.pwShareLink = generateShareLink(postId);
				});
			}

			// Set the share link value
			$scope.pwShareLink = generateShareLink($scope.shareLinkPostId);
			
		}
	}
}]);




