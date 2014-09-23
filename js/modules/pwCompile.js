
///// PW COMPILE MODULE /////
angular.module('pw.compile', [], ['$compileProvider', function($compileProvider) {
	// Allows an attribute's value to be evaluated and compiled against the scope, resulting
	// in an angularized template being injected in its place.
	//
	// Note: This directive is suffixed with "unsafe" because it does not sanitize the HTML. It is up
	// to the developer to ensure that the HTML is safe to insert into the DOM.
	//
	// Usage:
	//     HTML: <div pw-compile-code="templateHtml"></div>
	//     JS: $scope.templateHtml = '<a ng-onclick="doSomething()">Click me!</a>';
	//     Result: DIV will contain an anchor that will call $scope.doSomething() when clicked.
	$compileProvider.directive('pwCompileCode', ['$compile', function($compile) {
		return function(scope, element, attrs) {

			scope.$watch(
				function(scope) {
					// watch the 'compile' expression for changes
					return scope.$eval( attrs.pwCompileCode );
				},
				function(value) {
					// If value is empty
					// Clear the value
					if( _.isEmpty(value) )
						value = "";

					// when the 'compile' expression changes
					// assign it into the current DOM element
					element.html(value);

					// compile the new DOM and link it to the current
					// scope.
					// NOTE: we only compile .childNodes so that
					// we don't get into infinite loop compiling ourselves
					$compile(element.contents())(scope);
				}
			);
			
		};
	}]);
}]);
