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

///// KEEP DROPDOWN OPEN ON CLICK /////
postworld.directive('preventDefaultClick', function() {
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


/*
  _____ _           _   _        _____         _                       
 | ____| | __ _ ___| |_(_) ___  |_   _|____  _| |_ __ _ _ __ ___  __ _ 
 |  _| | |/ _` / __| __| |/ __|   | |/ _ \ \/ / __/ _` | '__/ _ \/ _` |
 | |___| | (_| \__ \ |_| | (__    | |  __/>  <| || (_| | | |  __/ (_| |
 |_____|_|\__,_|___/\__|_|\___|   |_|\___/_/\_\\__\__,_|_|  \___|\__,_|

 * angular-elastic v2.1.0
 * (c) 2013 Monospaced http://monospaced.com
 * License: MIT
 */

angular.module('monospaced.elastic', [])

	.constant('msdElasticConfig', {
		append: ''
	})

	.directive('msdElastic', ['$timeout', '$window', 'msdElasticConfig', function($timeout, $window, config) {
		'use strict';

		return {
			require: 'ngModel',
			restrict: 'A, C',
			link: function(scope, element, attrs, ngModel){

				// cache a reference to the DOM element
				var ta = element[0],
						$ta = element;

				// ensure the element is a textarea, and browser is capable
				if (ta.nodeName !== 'TEXTAREA' || !$window.getComputedStyle) {
					return;
				}

				// set these properties before measuring dimensions
				$ta.css({
					'overflow': 'hidden',
					'overflow-y': 'hidden',
					'word-wrap': 'break-word'
				});

				// force text reflow
				var text = ta.value;
				ta.value = '';
				ta.value = text;

				var appendText = attrs.msdElastic || config.append,
						append = appendText === '\\n' ? '\n' : appendText,
						$win = angular.element($window),
						$mirror = angular.element('<textarea tabindex="-1" style="position: absolute; ' +
																			'top: -999px; right: auto; bottom: auto; left: 0 ;' +
																			'overflow: hidden; -webkit-box-sizing: content-box; ' +
																			'-moz-box-sizing: content-box; box-sizing: content-box; ' +
																			'min-height: 0!important; height: 0!important; padding: 0;' +
																			'word-wrap: break-word; border: 0;"/>').data('elastic', true),
						mirror = $mirror[0],
						taStyle = getComputedStyle(ta),
						resize = taStyle.getPropertyValue('resize'),
						borderBox = taStyle.getPropertyValue('box-sizing') === 'border-box' ||
												taStyle.getPropertyValue('-moz-box-sizing') === 'border-box' ||
												taStyle.getPropertyValue('-webkit-box-sizing') === 'border-box',
						boxOuter = !borderBox ? {width: 0, height: 0} : {
													width: parseInt(taStyle.getPropertyValue('border-right-width'), 10) +
																	parseInt(taStyle.getPropertyValue('padding-right'), 10) +
																	parseInt(taStyle.getPropertyValue('padding-left'), 10) +
																	parseInt(taStyle.getPropertyValue('border-left-width'), 10),
													height: parseInt(taStyle.getPropertyValue('border-top-width'), 10) +
																 parseInt(taStyle.getPropertyValue('padding-top'), 10) +
																 parseInt(taStyle.getPropertyValue('padding-bottom'), 10) +
																 parseInt(taStyle.getPropertyValue('border-bottom-width'), 10)
												},
						minHeightValue = parseInt(taStyle.getPropertyValue('min-height'), 10),
						heightValue = parseInt(taStyle.getPropertyValue('height'), 10),
						minHeight = Math.max(minHeightValue, heightValue) - boxOuter.height,
						maxHeight = parseInt(taStyle.getPropertyValue('max-height'), 10),
						mirrored,
						active,
						copyStyle = ['font-family',
												 'font-size',
												 'font-weight',
												 'font-style',
												 'letter-spacing',
												 'line-height',
												 'text-transform',
												 'word-spacing',
												 'text-indent'];

				// exit if elastic already applied (or is the mirror element)
				if ($ta.data('elastic')) {
					return;
				}

				// Opera returns max-height of -1 if not set
				maxHeight = maxHeight && maxHeight > 0 ? maxHeight : 9e4;

				// append mirror to the DOM
				if (mirror.parentNode !== document.body) {
					angular.element(document.body).append(mirror);
				}

				// set resize and apply elastic
				$ta.css({
					'resize': (resize === 'none' || resize === 'vertical') ? 'none' : 'horizontal'
				}).data('elastic', true);

				/*
				 * methods
				 */

				function initMirror(){
					mirrored = ta;
					// copy the essential styles from the textarea to the mirror
					taStyle = getComputedStyle(ta);
					angular.forEach(copyStyle, function(val){
						mirror.style[val] = taStyle.getPropertyValue(val);
					});
				}

				function adjust() {
					var taHeight,
							mirrorHeight,
							width,
							overflow;

					if (mirrored !== ta) {
						initMirror();
					}

					// active flag prevents actions in function from calling adjust again
					if (!active) {
						active = true;

						mirror.value = ta.value + append; // optional whitespace to improve animation
						mirror.style.overflowY = ta.style.overflowY;

						taHeight = ta.style.height === '' ? 'auto' : parseInt(ta.style.height, 10);

						// update mirror width in case the textarea width has changed
						width = parseInt(borderBox ?
														 ta.offsetWidth :
														 getComputedStyle(ta).getPropertyValue('width'), 10) - boxOuter.width;
						mirror.style.width = width + 'px';

						mirrorHeight = mirror.scrollHeight;

						if (mirrorHeight > maxHeight) {
							mirrorHeight = maxHeight;
							overflow = 'scroll';
						} else if (mirrorHeight < minHeight) {
							mirrorHeight = minHeight;
						}
						mirrorHeight += boxOuter.height;

						ta.style.overflowY = overflow || 'hidden';

						if (taHeight !== mirrorHeight) {
							ta.style.height = mirrorHeight + 'px';
						}

						// small delay to prevent an infinite loop
						$timeout(function(){
							active = false;
						}, 1);

					}
				}

				function forceAdjust(){
					active = false;
					adjust();
				}

				/*
				 * initialise
				 */

				// listen
				if ('onpropertychange' in ta && 'oninput' in ta) {
					// IE9
					ta['oninput'] = ta.onkeyup = adjust;
				} else {
					ta['oninput'] = adjust;
				}

				$win.bind('resize', forceAdjust);

				scope.$watch(function(){
					return ngModel.$modelValue;
				}, function(newValue){
					forceAdjust();
				});

				/*
				 * destroy
				 */

				scope.$on('$destroy', function(){
					$mirror.remove();
					$win.unbind('resize', forceAdjust);
				});
			}
		};

	}]);



/*
	_                                             
 | |    __ _ _ __   __ _ _   _  __ _  __ _  ___ 
 | |   / _` | '_ \ / _` | | | |/ _` |/ _` |/ _ \
 | |__| (_| | | | | (_| | |_| | (_| | (_| |  __/
 |_____\__,_|_| |_|\__, |\__,_|\__,_|\__, |\___|
									 |___/             |___/      
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











