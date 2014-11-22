/*             __        ___           _               
  _ ____      _\ \      / (_)_ __   __| | _____      __
 | '_ \ \ /\ / /\ \ /\ / /| | '_ \ / _` |/ _ \ \ /\ / /
 | |_) \ V  V /  \ V  V / | | | | | (_| | (_) \ V  V / 
 | .__/ \_/\_/    \_/\_/  |_|_| |_|\__,_|\___/ \_/\_/  
 |_|                                                   
////////////////////////////////////////////////////*/                                   


///// RELATIVE-TO-WINDOW RESIZE /////

// Resize an element relative to the size of the window
postworld.directive('windowWidth', function( $window ) {
	return {
		restrict: 'A',
		scope: {
			windowWidth: '@',
		},
		link: function ($scope, elem, $attrs) {

			// Set the size of the element to X percentage of the window
			var setSize = function () {
				var percentDecimal = parseFloat( $scope.windowWidth ) / 100.0;
				var windowWidth = $window.innerWidth;
				var elementHeight = windowWidth * percentDecimal;
				elem.css('width', elementHeight + "px");
			};
			setSize();
			angular.element($window).bind("resize", setSize);
		}
	};
});


postworld.directive('windowHeight', function( $window ) {
	return {
		restrict: 'A',
		link: function ($scope, $elem, $attrs) {

			// Set the size of the element to X percentage of the window
			var setSize = function () {
				$attrs.$observe('windowHeight', function( value ) {

	            	var percentDecimal = parseFloat( value ) / 100.0;
					var windowHeight = $window.innerHeight;
					var elementHeight = windowHeight * percentDecimal;
					$elem.css('height', elementHeight + "px");
	            	
	            });

			};

			setSize();
			angular.element($window).bind("resize", setSize);

		}
	};
});



postworld.directive('documentHeight', function( $window, $document, $log, $timeout ) {
	return {
		restrict: 'A',
		link: function ($scope, $elem, $attrs) {

			// Set the size of the element to X percentage of the window
			var setSize = function () {
				$attrs.$observe('documentHeight', function( value ) {


					// TODO : Make this a $_ function
					var body = document.body,
					    html = document.documentElement;
					var documentHeight =  Math.max( body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight );


	            	var percentDecimal = parseFloat( value ) / 100.0;
					//var documentHeight = $document.innerHeight;
					var elementHeight = documentHeight * percentDecimal;
					$elem.css( 'height', elementHeight + "px" );
	            	
	            });

			};

			$timeout( function(){
				setSize();
				angular.element($window).bind("resize", setSize);
			}, 0 );
			
			//angular.element($window).bind("scroll", setSize);

		}
	};
});