'use strict';

angular.module('angular-parallax', [
]).directive('parallax', ['$window', function($window) {
  return {
    restrict: 'A',
    scope: {
      parallaxRatio: '@',
      parallaxVerticalOffset: '@',
      parallaxHorizontalOffset: '@',
    },
    link: function($scope, elem, $attrs) {
      var setPosition = function () {
        // horizontal positioning
        elem.css('left', $scope.parallaxHorizontalOffset + "px");

        var calcValY = $window.pageYOffset * ($scope.parallaxRatio ? $scope.parallaxRatio : 1.1 );
        if (calcValY <= $window.innerHeight) {
          var topVal = (calcValY < $scope.parallaxVerticalOffset ? $scope.parallaxVerticalOffset : calcValY);
          elem.css('top', topVal + "px");
        }
      };

      setPosition();

      angular.element($window).bind("scroll", setPosition);
    }  // link function
  };
}]).directive('parallaxBackground', ['$window', '$log', function($window, $log) {
  return {
    restrict: 'A',
    transclude: true,
    template: '<div ng-transclude></div>',
    scope: {
      parallaxRatio: '@',
    },
    link: function($scope, elem, attrs) {
      
      var setPosition = function () {
        var calcValY = ( elem.prop('offsetTop') - $window.pageYOffset ) * ($scope.parallaxRatio ? $scope.parallaxRatio : 1.1 );
        calcValY = parseInt(calcValY);
        // horizontal positioning
        elem.css('background-position', "50% " + calcValY + "px");
      };

      /*
      $scope.offsetHeightLock = 0;
      var setPositionBottom = function () {

        // Distance the element is from the top of the window
        var offsetTop = elem.prop('offsetTop');

        // How far down the window is scrolled
        var pageYOffset = $window.pageYOffset;

        // The height of the element
        var offsetHeight = elem[0].offsetHeight;

        if( offsetHeight !== 0 )
          $scope.offsetHeightLock = offsetHeight;
        else
          $scope.offsetHeightLock = $scope.offsetHeightLock;

        // Distance the element's top is from the window top
        var topDistanceFromTop = ( offsetTop - pageYOffset );

        // Distance the element's bottom is from the window top
        var bottomDistanceFromTop = ( offsetTop - pageYOffset + offsetHeight );

        //var calcValY = ( offsetTop - pageYOffset ) * ($scope.parallaxRatio ? $scope.parallaxRatio : 1.1 );
       
        $log.debug( '$scope.offsetHeightLock', $scope.offsetHeightLock );

        //var calcValY = 100 - ( distanceFromTop ) *  ;

        // horizontal positioning
        //elem.css('background-position', "50% " + calcValY + "%");
      };
      */

      // set our initial position - fixes webkit background render bug
      angular.element($window).bind('load', function(e) {
        setPosition();
        $scope.$apply();
      });

      angular.element($window).bind("scroll", setPosition);

      //var height = elem.prop('height');
      

    }  // link function
  };
}]);
