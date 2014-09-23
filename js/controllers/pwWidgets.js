'use strict';

/*
 __        ___     _            _       
 \ \      / (_) __| | __ _  ___| |_ ___ 
  \ \ /\ / /| |/ _` |/ _` |/ _ \ __/ __|
   \ V  V / | | (_| | (_| |  __/ |_\__ \
    \_/\_/  |_|\__,_|\__, |\___|\__|___/
                     |___/              
//////////////// WIDGETS ////////////////*/


///// POST SHARE REPORT /////
postworld.controller('postShareReport',
    ['$scope','$window','$timeout','pwData',
    function($scope, $window, $timeout, $pwData) {

    $scope.postShareReport = {};

    if( typeof $window.pwGlobals.view.post != 'undefined' ){
        $scope.post = $window.pwGlobals.view.post;
        var args = { "post_id" : $scope.post.post_id };
        $pwData.post_share_report( args ).then(
            // Success
            function(response) {    
                $scope.postShareReport = response.data;
                $scope.status = "done";
            },
            // Failure
            function(response) {
                //alert('Error loading report.');
            }
        );

    }
}]);


///// POST SHARE REPORT /////
postworld.controller('userShareReportOutgoing',
    ['$scope','$window','$timeout','pwData',
    function($scope, $window, $timeout, $pwData) {

    $scope.postShareReport = {};

    if( typeof $window.pwGlobals.displayed_user.user_id != 'undefined' ){

        $scope.displayed_user_id = $window.pwGlobals.displayed_user.user_id;

        var args = { "displayed_user_id" : $scope.displayed_user_id };

        $pwData.user_share_report_outgoing( args ).then(
            // Success
            function(response) {    
                $scope.shareReportMetaOutgoing = response.data;
                $scope.status = "done";
            },
            // Failure
            function(response) {
                //alert('Error loading report.');
            }
        );

    }
}]);
