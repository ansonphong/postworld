/* ng-infinite-scroll - v1.0.0 - 2013-05-13 */
var mod;

mod = angular.module('infinite-scroll', []);

mod.directive('infiniteScroll', [
  '$rootScope', '$window', '$timeout', function($rootScope, $window, $timeout) {
    return {
      link: function(scope, elem, attrs) {
        var checkWhenEnabled, container, handler, scrollDistance, scrollEnabled;
        $window = angular.element($window);
        scrollDistance = 0;
        if (attrs.infiniteScrollDistance != null) {
          scope.$watch(attrs.infiniteScrollDistance, function(value) {
            return scrollDistance = parseInt(value, 10);
          });
        }
        scrollEnabled = true;
        checkWhenEnabled = false;
        if (attrs.infiniteScrollDisabled != null) {
          scope.$watch(attrs.infiniteScrollDisabled, function(value) {
            scrollEnabled = !value;
            if (scrollEnabled && checkWhenEnabled) {
              checkWhenEnabled = false;
              return handler();
            }
          });
        }
        container = $window;
        if (attrs.infiniteScrollContainer != null) {
          scope.$watch(attrs.infiniteScrollContainer, function(value) {
            value = angular.element(value);
            if (value != null) {
              return container = value;
            } else {
              throw new Exception("invalid infinite-scroll-container attribute.");
            }
          });
        }
        if (attrs.infiniteScrollParent != null) {
          container = elem.parent();
          scope.$watch(attrs.infiniteScrollParent, function() {
            return container = elem.parent();
          });
        }
        handler = function() {
          var containerBottom, elementBottom, remaining, shouldScroll;
          if (container === $window) {
            containerBottom = container.height() + container.scrollTop();
            elementBottom = elem.offset().top + elem.height();
          } else {
            containerBottom = container.height();
            elementBottom = elem.offset().top - container.offset().top + elem.height();
          }
          remaining = elementBottom - containerBottom;
          shouldScroll = remaining <= container.height() * scrollDistance;
          if (shouldScroll && scrollEnabled) {
            if ($rootScope.$$phase) {
              return scope.$eval(attrs.infiniteScroll);
            } else {
              return scope.$apply(attrs.infiniteScroll);
            }
          } else if (shouldScroll) {
            return checkWhenEnabled = true;
          }
        };
        container.on('scroll', handler);
        scope.$on('$destroy', function() {
          return container.off('scroll', handler);
        });
        return $timeout((function() {
          if (attrs.infiniteScrollImmediateCheck) {
            if (scope.$eval(attrs.infiniteScrollImmediateCheck)) {
              return handler();
            }
          } else {
            return handler();
          }
        }), 0);
      }
    };
  }
]);
