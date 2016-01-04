'use strict';
/*____  _               _   _                
 |  _ \(_)_ __ ___  ___| |_(_)_   _____  ___ 
 | | | | | '__/ _ \/ __| __| \ \ / / _ \/ __|
 | |_| | | | |  __/ (__| |_| |\ V /  __/\__ \
 |____/|_|_|  \___|\___|\__|_| \_/ \___||___/
											 
////////// ------------ DIRECTIVES ------------ //////////*/

/**
 * @ngdoc directive
 * @name postworld.directive:pwGlobals
 *
 * @restrict A
 * @description Sets the $pw service object into the local scope.
 * @param {Expression} pwGlobals The expression to bind the Postworld globals to 
 *
 * @example
 * ```<pre><div pw-globals="pw">{{ pw | json }}</div></pre>```
 *
 */
postworld.directive( 'pwGlobals',
	[ '$pw', '_', '$log',
	function( $pw, $_, $log ){
	return{
		restrict:"A",
		scope:{
			pwGlobals:"=",
		},
		link : function( $scope, element, attrs ){
			$scope.pwGlobals = $pw;
		}
	}
}]);

/**
 * @ngdoc directive
 * @name postworld.directive:pwInclude
 *
 * @description
 * Used to include a Postworld template partial within an isolated scope.
 * Post and meta data can be easily make available in the template.
 *
 * @param {string} pwInclude Psuedo-path to the template.
 * Use `panels/widget` to include `templates/panels/widget.html`
 * @param {expression} includeVars Vars to assign within the include as $scope.vars
 * @param {expression} includeMeta Object to be assigned as $scope.meta within the included template
 * @param {expression} includePost Object to be assigned as $scope.post within the included template
 * @param {boolean} includeEnable Dynamic. Whether or not to actually enable the load the include
 * 		Used to prevent the template from loading in certain instances.
 * @param {string} includeEval Evaluated in rootScope context. If false, template will not be included.
 *		Used to prevent the template from loading in certain instances.
 * @param {string} includeClass Class(es) to be added to the include element
 *
 * @example
	<pre><div pw-include="galleries/gallery-frame" include-post="post"></div></pre>
 *
 */
postworld.directive('pwInclude', function($log, $timeout, pwData, $rootScope) {
	return {
		restrict: 'EA',
		template: '<div ng-include="includeUrl" class="pw-include" ng-class="includeClass"></div>',
		scope:{
			vars:"=includeVars",
			includeMeta:"=",	
			includePost:"=",	
			includeEnable:"=",
			includeEval:"@",
			includeClass:"@",
		},
		link: function($scope, element, attrs){

			var getIncludeEval = function(){
				if(_.isUndefined( $scope.includeEval ))
					return true;
				return $rootScope.$eval( $scope.includeEval );
			}

			var setTemplateUrl = function(){
				var pwInclude = attrs.pwInclude;
				var parts = pwInclude.split('/');
				if( parts.length < 2 ){
					$log.debug( 'pwInclude : ERROR : Include must contain 2 parts, dir/basename.' )
					return false;
				}
				// Timeout to allow other controllers to init
				$timeout( function(){
					if($scope.includeEnable !== false &&
						getIncludeEval() !== false )
						$scope.includeUrl = pwData.pw_get_template( { subdir: parts[0], view: parts[1] } );
					else{
						$scope.includeUrl = '';
						$scope.$destroy();
					}
				}, 0 );
			}

			attrs.$observe( 'pwInclude', function( pwInclude ){
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
				setTemplateUrl();
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



/**
 * @ngdoc directive
 * @name postworld.directive:pwLoadPost
 * @restrict A
 *
 * @description
 * Loads a post template and injects it with data.
 *
 * @param {string} postId Optional. Post ID
 * @param {string} postView Which registered view template to use.
 * @param {object} postQuery Query vars to derive post from.
 * @param {string} postClass Optional. Classes to add to the template element.
 * @param {string} postLoading Optional. An expression to assign a loading boolean.
 * 
 */
postworld.directive('pwLoadPost',
	[ '$log', '$timeout', 'pwData', '_',
	function( $log, $timeout, $pwData, $_ ) {
	return {
		restrict: 'A',
		replace: true,
		template: '<div ng-include="templateUrl" ng-class="postClass"></div>',
		scope : {
			postId:'=',
			postView:'@',
			postQuery:'=',
			postClass:'@',
			postLoading:'='
		},
		link: function( $scope, element, attrs ){

			/**
			 * @description
			 * Initializes the directive.
			 * Invokes either post from 'id' or 'query' methods.
			 */
			var init = function(){
				///// DETECT MODE /////
				if( $scope.postId != null )
					$scope.mode = 'id';
				else
					$scope.mode = 'query';

				$log.debug( 'pwLoadPost : MODE : ', $scope.mode );

				///// SWITCH : MODE /////
				switch( $scope.mode ){
					///// POST FROM ID
					case 'id':
						loadPostFromId();
						break;
					///// POST FROM QUERY /////
					case 'query':
						loadPostFromQuery();
						break;
				}
			}
			
			/**
			 * Loads a post via post ID.
			 *
			 * @memberof pwLoadPost
			 * @function loadPostFromId
			 */
			var loadPostFromId = function(){
				$scope.postLoading = true;
				$pwData.getPost( {post_id:$scope.postId} ).then(
					function(response) {
						$scope.postLoading = false;
						var post = response.data;
						if( !_.isEmpty(post) ){
							$scope.post = post;
							setTemplateUrl();
						}
					},
					function(response){}
				);
			}

			/**
			 * Loads a post via query.
			 *
			 * @memberof pwLoadPost
			 * @function loadPostFromQuery
			 */
			var loadPostFromQuery = function(){

				if( !_.isObject($scope.postQuery) ){
					throw { message:"Postworld [directive] loadPost : Wrong query format provided in post-query attribute." }
					return false;
				}

				// Setup query
				var query = $scope.postQuery;
				query.posts_per_page = 1;

				$scope.postLoading = true;
				$pwData.pwQuery( query ).then(
					function(response) {
						$scope.postLoading = false;
						if( _.isArray( response.data.posts ) ){
							if( !_.isEmpty( response.data.posts ) ){
								$scope.post = response.data.posts[0];
								setTemplateUrl();
							}
						}

					},
					function(response){}
				);
			}

			/**
			 * Sets the template URL.
			 *
			 * @memberof pwLoadPost
			 * @function setTemplateUrl
			 */
			var setTemplateUrl = function(){
				var postType = $_.get( $scope, 'post.post_type' ); 
				if( !postType )
					postType = 'post';

				var view = $scope.postView;
				if( view == null )
					view = 'list';

				$scope.templateUrl = $pwData.pw_get_template({
					subdir: 'posts',
					post_type: postType,
					view: view
					});
			}

			init();

		}
	};
}]);


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


/**
 * @ngdoc directive
 * @name postworld.directive:pwBackgroundImage
 * @description
 * Adds a background image style property to an element
 *
 * @param {expression} pwBackgroundImage Binding to the URL of the background image.
 *
 * @example
	<pre><div pw-background-image="post.image.sizes.large.url"></div></pre>
 */
postworld.directive('pwBackgroundImage', function( $log ) {
	return {
		scope:{
		  pwBackgroundImage:"="
		},
		link: function( $scope, element, attrs ) {
			$scope.$watch( 'pwBackgroundImage', function(val){
				element.css( 'background-image', 'url("'+val+'")' );
			});
		},
	}
});

/**
 * @ngdoc directive
 * @name postworld.directive:pwEval
 *
 * @description
 * Evaluates a string as javascript at the time of loading.
 * Works well for initializing third-party libraries.
 *
 * @param {string} pwEval A string to evaluate as Javascript
 * @param {number} evalTimeout Optional. Milliseonds to timeout before evaluating
 * @param {string} evalContext Optional. Context in which to evaluate. Options : 'scope' / 'window'. Default : 'scope'
 * @param {expression} evalWatch Optional. An expression to watch for changes, on change re-evalute.
 */
postworld.directive('pwEval', function($timeout, $log) {
	return {
		scope:{
		  pwEval:"@",
		  evalTimeout:"@",
		  evalContext:"@",
		  evalWatch:"=",
		},
		link: function($scope, element, attrs) {

			if( _.isUndefined( $scope.evalTimeout ) )
				$scope.evalTimeout = 0;
			if( _.isUndefined( $scope.evalContext ) )
				$scope.evalContext = 'window';

			/**
			 * @memberof pwEval
			 * @function evaluate
			 */
			var evaluate = function(){
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
			}
			evaluate();

			///// EVAL WATCH /////
			var firstWatch = true;
			$scope.$watch( 'evalWatch', function(val){
				if( firstWatch ){
					firstWatch = false;
					return;
				}
				evaluate();
			});


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


/**
 * @ngdoc directive
 * @name postworld.directive:pwClickPreventDefault
 * @description Prevents the default click action on the element.
 * @restrict A
 */
postworld.directive('pwClickPreventDefault', function() {
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

///// DEPRECIATED /////
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


/**
 * @ngdoc directive
 * @name postworld.directive:pwClickStopPropagation
 * @description Stops the click event from propagating beyond the given element.
 * @restrict A
 * @depreciated
 */
postworld.directive('pwClickStopPropagation', function() {
	return {
		restrict: 'A',
		link: function (scope, element) {
			element.bind('click', function (event) {
				event.stopPropagation();
			});
		}
	};
});

///// DEPRECIATED /////
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

/**
 * @ngdoc directive
 * @name postworld.directive:pwAutofocus
 * @description Automatically focuses the input element it's applied to.
 * @element input
 */
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
 |_| |_|\___/ \_/ \___|_|     \____|_|\__,_|___/___/*/

/**
 * @ngdoc directive
 * @name postworld.directive:pwHoverClass
 * @restrict A
 * @description Adds specified class(es) to an element on mouseover,
 * and removes the classes on mouseleave.
 *
 * @param {string} pwHoverClass Class(es) to add when hovered.
 * @param {number} hoverOnDelay Optional. Milliseconds after mouseover before classes are added.
 * @param {number} hoverOffDelay Optional. Milliseconds after mouseleave before classes are removed. 
 */
 //  * @property {boolean} mouseIsOver Variable to track if mouse is currently over.
postworld.directive('pwHoverClass', function ( $timeout ) {
    return {
        restrict: 'A',
        scope: {
            pwHoverClass: '@',
            hoverOnDelay: '@',
            hoverOffDelay: '@',
        },
        link: function ( $scope, element, attrs ) {
        	
        	/**
			 * Prevents the hover class from getting locked on,
			 * in the case that the ON delay is greater than the OFF delay
			 * and the mouse passes on and off the element
			 * in less time than their difference.
			 *
			 * @memberof pwHoverClass
			 * @var mouseIsOver
			 */
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


/**
 * @ngdoc directive
 * @name postworld.directive:pwShareLink
 *
 * @description Generates a share link for a particular post ID.
 *
 * @param {expression} pwShareLink The expression to map the share link to
 * @param {string} shareLinkPostId Required. The ID of the post which to generate the share link for
 * @param {boolean} shareLinkDynamic If `true`, sets up a watch on the value of shareLinkPostId
 * 
 * @example
	 <pre>
		<div pw-share-link="shareLink" share-link-post-id="post.ID">{{shareLink}}</div>
	 </pre>
 */
postworld.directive( 'pwShareLink',
	[ '$pw', '_', '$log',
	function( $pw, $_, $log ){
	return{
		scope:{
			pwShareLink:'=',
			shareLinkPostId:'=',
			shareLinkDynamic:'@'
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




postworld.directive('pwDataGet', [ '$log', '_', 'pwData', '$pw', function( $log, $_, $pwData, $pw ){
	return{
		scope:{
			pwDataGet:"@",
		},
		link: function( $scope, element, attrs ){
			$log.debug("ACTIVATE : pwDataGet", element);
			element.html( JSON.stringify( $_.get( $pwData, $scope.pwDataGet ) ) );
		}

	}
}]);
