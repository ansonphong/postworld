/*
  ___                   _     ____                      _     
 |_ _|_ __  _ __  _   _| |_  / ___|  ___  __ _ _ __ ___| |__  
  | || '_ \| '_ \| | | | __| \___ \ / _ \/ _` | '__/ __| '_ \ 
  | || | | | |_) | |_| | |_   ___) |  __/ (_| | | | (__| | | |
 |___|_| |_| .__/ \__,_|\__| |____/ \___|\__,_|_|  \___|_| |_|
           |_|                                                

//////////////////////// INPUT SEARCH //////////////////////*/

postworld.controller('inputSearch',
    ['$scope','$window','$timeout','pwData',
    function($scope, $window, $timeout, $pwData) {
    $scope.input = {};
    $scope.submit = function(){
        //alert( JSON.stringify(search_context) );
        $window.location.href = "/s/#/?s="+$scope.input.s;
    }
}]);
