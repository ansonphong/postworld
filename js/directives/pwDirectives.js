'use strict';

/*
  ____  _               _   _                
 |  _ \(_)_ __ ___  ___| |_(_)_   _____  ___ 
 | | | | | '__/ _ \/ __| __| \ \ / / _ \/ __|
 | |_| | | | |  __/ (__| |_| |\ V /  __/\__ \
 |____/|_|_|  \___|\___|\__|_| \_/ \___||___/
											 
////////// ------------ DIRECTIVES ------------ //////////*/


///// PW-HREF /////

postworld.directive('pwHref', function() {
	return {
		scope:{
		  pwHref:"=pwHref"
		},
		link: function(scope, element, attrs) {
			
			attrs.$set('href', scope.pwHref );

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
postworld.directive('selectOnClick', function() {
		return function (scope, element, attrs) {
			element.bind('click', function () {
				this.select();
			});
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
		},
		link: function( $scope, element, attrs ) {
			$timeout( function(){
				// Evaluate passed local function
				$scope.$eval( $scope.timeoutAction );
				// Destroy Scope
				$scope.$destroy();
			}, parseInt( $scope.pwTimeout ) ); // 

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











