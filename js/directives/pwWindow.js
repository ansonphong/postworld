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
			scope: {
				windowHeight: '@',
			},
			link: function ($scope, elem, $attrs) {

				// Set the size of the element to X percentage of the window
				var setSize = function () {
					var percentDecimal = parseFloat( $scope.windowHeight ) / 100.0;
					var windowHeight = $window.innerHeight;
					var elementHeight = windowHeight * percentDecimal;
					elem.css('height', elementHeight + "px");
				};
				setSize();
				angular.element($window).bind("resize", setSize);
			}
		};
	});
