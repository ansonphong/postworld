/*
  _ ____  _               _   _                
 (_)  _ \(_)_ __ ___  ___| |_(_)_   _____  ___ 
 | | | | | | '__/ _ \/ __| __| \ \ / / _ \/ __|
 | | |_| | | | |  __/ (__| |_| |\ V /  __/\__ \
 |_|____/|_|_|  \___|\___|\__|_| \_/ \___||___/

///////////////////////////////////////////////*/

postworld.directive( 'iEditPost', [ function($scope){
    return {
        restrict: 'AE',
        controller: 'iEditPostCtrl',
        link: function( $scope, element, attrs ){}
    };
}]);

postworld.controller('iEditPostCtrl',
    ['$scope', '$rootScope', '$timeout', '$filter',
        'pwData', '$log', '$route', '$routeParams', '$window',  '_', 'pwTemplatePartials', 'iOptionsData',
    function($scope, $rootScope, $timeout, $filter, 
        $pwData, $log, $route, $routeParams,  $window, $_, $pwTemplatePartials, $iOptionsData ) {

        $scope['options'] = $iOptionsData['options'];

}]);


infinite.directive('staticInclude', function($http, $templateCache, $compile) {
	return function(scope, element, attrs) {
		var templatePath = attrs.staticInclude;

		$http.get(templatePath, {cache: $templateCache}).success(function(response) {
			var contents = $('<div/>').html(response).contents();
			element.html(contents);
			$compile(contents)(scope);
		});
	};
});



postworld.directive('iPointerActivate', function( $timeout ) {
    return {
        scope:{
            iPointerActivate:"@",
            inactiveDelay:"@",
        },
        link: function( $scope, element, attrs ) {
        	/*
            $timeout( function(){
                // Evaluate passed local function
                $scope.$eval( $scope.timeoutAction );
                // Destroy Scope
                $scope.$destroy();
            }, parseInt( $scope.pwTimeout ) ); // 

            $scope.addClass = function( classes ){
                element.addClass( classes );
            }
            */
        },
    }
});