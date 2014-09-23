/*
     _                      _              ____  _                   
    / \   _ __   __ _ _   _| | __ _ _ __  / ___|| |_ _ __ __ _ _ __  
   / _ \ | '_ \ / _` | | | | |/ _` | '__| \___ \| __| '__/ _` | '_ \ 
  / ___ \| | | | (_| | |_| | | (_| | |     ___) | |_| | | (_| | |_) |
 /_/   \_\_| |_|\__, |\__,_|_|\__,_|_|    |____/ \__|_|  \__,_| .__/ 
                |___/                                         |_|                                                                      

 * AngularStrap - Twitter Bootstrap directives for AngularJS
 * @version v0.7.5 - 2013-07-21
 * @link http://mgcrea.github.com/angular-strap
 * @author Olivier Louvignes <olivier@mg-crea.com>
 * @license MIT License, http://www.opensource.org/licenses/MIT
 */
angular.module('$strap.config', []).value('$strapConfig', {});
angular.module('$strap.filters', ['$strap.config']);
angular.module('$strap.directives', ['$strap.config']);
angular.module('$strap', [
  '$strap.filters',
  '$strap.directives',
  '$strap.config'
]);
'use strict';
angular.module('$strap.directives').directive('bsButton', [
  '$parse',
  '$timeout',
  function ($parse, $timeout) {
    return {
      restrict: 'A',
      require: '?ngModel',
      link: function postLink(scope, element, attrs, controller) {
        if (controller) {
          if (!element.parent('[data-toggle="buttons-checkbox"], [data-toggle="buttons-radio"]').length) {
            element.attr('data-toggle', 'button');
          }
          var startValue = !!scope.$eval(attrs.ngModel);
          if (startValue) {
            element.addClass('active');
          }
          scope.$watch(attrs.ngModel, function (newValue, oldValue) {
            var bNew = !!newValue, bOld = !!oldValue;
            if (bNew !== bOld) {
              $.fn.button.Constructor.prototype.toggle.call(button);
            } else if (bNew && !startValue) {
              element.addClass('active');
            }
          });
        }
        if (!element.hasClass('btn')) {
          element.on('click.button.data-api', function (ev) {
            element.button('toggle');
          });
        }
        element.button();
        var button = element.data('button');
        button.toggle = function () {
          if (!controller) {
            return $.fn.button.Constructor.prototype.toggle.call(this);
          }
          var $parent = element.parent('[data-toggle="buttons-radio"]');
          if ($parent.length) {
            element.siblings('[ng-model]').each(function (k, v) {
              $parse($(v).attr('ng-model')).assign(scope, false);
            });
            scope.$digest();
            if (!controller.$modelValue) {
              controller.$setViewValue(!controller.$modelValue);
              scope.$digest();
            }
          } else {
            scope.$apply(function () {
              controller.$setViewValue(!controller.$modelValue);
            });
          }
        };
      }
    };
  }
]).directive('bsButtonsCheckbox', [
  '$parse',
  function ($parse) {
    return {
      restrict: 'A',
      require: '?ngModel',
      compile: function compile(tElement, tAttrs, transclude) {
        tElement.attr('data-toggle', 'buttons-checkbox').find('a, button').each(function (k, v) {
          $(v).attr('bs-button', '');
        });
      }
    };
  }
]).directive('bsButtonsRadio', [
  '$timeout',
  function ($timeout) {
    return {
      restrict: 'A',
      require: '?ngModel',
      compile: function compile(tElement, tAttrs, transclude) {
        tElement.attr('data-toggle', 'buttons-radio');
        if (!tAttrs.ngModel) {
          tElement.find('a, button').each(function (k, v) {
            $(v).attr('bs-button', '');
          });
        }
        return function postLink(scope, iElement, iAttrs, controller) {
          if (controller) {
            $timeout(function () {
              iElement.find('[value]').button().filter('[value="' + controller.$viewValue + '"]').addClass('active');
            });
            iElement.on('click.button.data-api', function (ev) {
              scope.$apply(function () {
                controller.$setViewValue($(ev.target).closest('button').attr('value'));
              });
            });
            scope.$watch(iAttrs.ngModel, function (newValue, oldValue) {
              if (newValue !== oldValue) {
                var $btn = iElement.find('[value="' + scope.$eval(iAttrs.ngModel) + '"]');
                if ($btn.length) {
                  $btn.button('toggle');
                }
              }
            });
          }
        };
      }
    };
  }
]);

'use strict';
angular.module('$strap.directives').directive('bsPopover', [
  '$parse',
  '$compile',
  '$http',
  '$timeout',
  '$q',
  '$templateCache',
  function ($parse, $compile, $http, $timeout, $q, $templateCache) {
    $('body').on('keyup', function (ev) {
      if (ev.keyCode === 27) {
        $('.popover.in').each(function () {
          $(this).popover('hide');
        });
      }
    });
    return {
      restrict: 'A',
      scope: true,
      link: function postLink(scope, element, attr, ctrl) {
        var getter = $parse(attr.bsPopover), setter = getter.assign, value = getter(scope), options = {};
        if (angular.isObject(value)) {
          options = value;
        }
        $q.when(options.content || $templateCache.get(value) || $http.get(value, { cache: true })).then(function onSuccess(template) {
          if (angular.isObject(template)) {
            template = template.data;
          }
          if (!!attr.unique) {
            element.on('show', function (ev) {
              $('.popover.in').each(function () {
                var $this = $(this), popover = $this.data('popover');
                if (popover && !popover.$element.is(element)) {
                  $this.popover('hide');
                }
              });
            });
          }
          if (!!attr.hide) {
            scope.$watch(attr.hide, function (newValue, oldValue) {
              if (!!newValue) {
                popover.hide();
              } else if (newValue !== oldValue) {
                popover.show();
              }
            });
          }
          if (!!attr.show) {
            scope.$watch(attr.show, function (newValue, oldValue) {
              if (!!newValue) {
                $timeout(function () {
                  popover.show();
                });
              } else if (newValue !== oldValue) {
                popover.hide();
              }
            });
          }
          element.popover(angular.extend({}, options, {
            content: template,
            html: true
          }));
          var popover = element.data('popover');
          
          if( typeof popover !== 'undefined' ){
              popover.hasContent = function () {
                return this.getTitle() || template;
              };
              popover.getPosition = function () {
                var r = $.fn.popover.Constructor.prototype.getPosition.apply(this, arguments);
                $compile(this.$tip)(scope);
                scope.$digest();
                this.$tip.data('popover', this);
                return r;
              };
          }
          
          scope.$popover = function (name) {
            popover(name);
          };
          angular.forEach([
            'show',
            'hide'
          ], function (name) {
            scope[name] = function () {
              popover[name]();
            };
          });
          scope.dismiss = scope.hide;
          angular.forEach([
            'show',
            'shown',
            'hide',
            'hidden'
          ], function (name) {
            element.on(name, function (ev) {
              scope.$emit('popover-' + name, ev);
            });
          });
        });
      }
    };
  }
]);
'use strict';
angular.module('$strap.directives').directive('bsSelect', [
  '$timeout',
  function ($timeout) {
    var NG_OPTIONS_REGEXP = /^\s*(.*?)(?:\s+as\s+(.*?))?(?:\s+group\s+by\s+(.*))?\s+for\s+(?:([\$\w][\$\w\d]*)|(?:\(\s*([\$\w][\$\w\d]*)\s*,\s*([\$\w][\$\w\d]*)\s*\)))\s+in\s+(.*)$/;
    return {
      restrict: 'A',
      require: '?ngModel',
      link: function postLink(scope, element, attrs, controller) {
        var options = scope.$eval(attrs.bsSelect) || {};
        $timeout(function () {
          element.selectpicker(options);
          element.next().removeClass('ng-scope');
        });
        if (controller) {
          scope.$watch(attrs.ngModel, function (newValue, oldValue) {
            if (!angular.equals(newValue, oldValue)) {
              element.selectpicker('refresh');
            }
          });
        }
      }
    };
  }
]);
