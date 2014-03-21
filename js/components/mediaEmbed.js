'use strict';

/*__  __          _ _         __  __           _       _ 
 |  \/  | ___  __| (_) __ _  |  \/  | ___   __| | __ _| |
 | |\/| |/ _ \/ _` | |/ _` | | |\/| |/ _ \ / _` |/ _` | |
 | |  | |  __/ (_| | | (_| | | |  | | (_) | (_| | (_| | |
 |_|  |_|\___|\__,_|_|\__,_| |_|  |_|\___/ \__,_|\__,_|_|

////////// ------------ MEDIA MODAL ------------ //////////*/                                                         

var mediaModalCtrl = function ($scope, $modal, $log, $window, pwData) {

  $scope.launch = function (post) {
    var modalInstance = $modal.open({
      templateUrl: pwData.pw_get_template('panels','','media_modal'),
      controller: MediaModalInstanceCtrl,
      windowClass: 'media_modal',
      resolve: {
        post: function(){
            return post;
        }
      }
    });
    modalInstance.result.then(function (selectedItem) {
        //$scope.post_title = post_title;
    }, function () {
        // WHEN CLOSE MODAL
        $log.debug('Modal dismissed at: ' + new Date());
    });
  };

};


var MediaModalInstanceCtrl = function ($scope, $sce, $modalInstance, post, pwData) {
    
    // Import the passed post object into the Modal Scope
    $scope.post = post;

    /*
    $scope.ok = function () {
        $modalInstance.close($scope.selected.item);
        // RETURN THIS VALUE TO PAGE
    };
    */
    $scope.status = "loading";

    $scope.oEmbed = '';
    var link_url = post.link_url;
    var args = { "link_url": link_url };

    // MEDIA GET
    pwData.wp_ajax('ajax_oembed_get', args ).then(
        // Success
        function(response) {    
            $scope.oEmbed = $sce.trustAsHtml( response.data );
            $scope.status = "done";
        },
        // Failure
        function(response) {
            $scope.status = "error";
        }
    );

    // MODAL CLOSE
    $scope.close = function () {
        $modalInstance.dismiss('close');
    };
};

///// DIRECTIVE /////


postworld.directive( 'launchMediaModal', ['$sce',function($scope, $sce){
    return { 
        controller: 'mediaModalCtrl'
    };
}]);




/*
  __  __          _ _         _____           _              _ 
 |  \/  | ___  __| (_) __ _  | ____|_ __ ___ | |__   ___  __| |
 | |\/| |/ _ \/ _` | |/ _` | |  _| | '_ ` _ \| '_ \ / _ \/ _` |
 | |  | |  __/ (_| | | (_| | | |___| | | | | | |_) |  __/ (_| |
 |_|  |_|\___|\__,_|_|\__,_| |_____|_| |_| |_|_.__/ \___|\__,_|

////////// ------------ MEDIA EMBED CONTROLLER ------------ //////////*/   

var mediaEmbed = function ( $scope, $sce, pwData ) {

    $scope.oEmbed = "";
    $scope.oEmbedGet = function (link_url) {
        var args = { "link_url":link_url };
        var oEmbed = "";
        pwData.wp_ajax('ajax_oembed_get', args ).then(
            // Success
            function(response) {    
                $scope.oEmbed = $sce.trustAsHtml(response.data);
            },
            // Failure
            function(response) {
                //alert("error");
            }
        );
        
    };

    // Run oEmbedGet on Media
    if(
        $scope.post.link_format == 'video' ||
        $scope.post.link_format == 'audio'
        )
        $scope.oEmbed = $scope.oEmbedGet( $scope.post.link_url );

};



/*
              _____           _              _ 
   ___       | ____|_ __ ___ | |__   ___  __| |
  / _ \ _____|  _| | '_ ` _ \| '_ \ / _ \/ _` |
 | (_) |_____| |___| | | | | | |_) |  __/ (_| |
  \___/      |_____|_| |_| |_|_.__/ \___|\__,_|

////////// ------------ O-EMBED DIRECTIVE ------------ //////////*/  

postworld.directive( 'oEmbed-old', ['$sce','pwData', function($scope, $sce, pwData){

    return { 
        //restrict: 'A',
        //scope : function(){
        //},
        //template : '',
        link : function ($scope, element, attributes){
            
            //alert( attributes.oEmbed );
            $scope.status = "loading";
            $scope.oEmbed = "embed code for : " + attributes.oEmbed;

            var link_url = attributes.oEmbed;
            var args = { "link_url": link_url };

            // MEDIA GET
            $scope.oEmbedGet = function(){
                pwData.wp_ajax('ajax_oembed_get', args ).then(
                    // Success
                    function(response) {    
                        $scope.status = "done";
                        return $sce.trustAsHtml( response.data );
                        
                    },
                    // Failure
                    function(response) {
                        $scope.status = "error";
                    }
                );
            };

            $scope.oEmbed = $scope.oEmbedGet();

        }
    };

}]);

postworld.directive( 'oEmbed', ['$sce',function($scope, $sce){

    return { 
        restrict: 'AE',
        scope : {
            link_url:"=oEmbed",
            autoplay:"=autoplay"
        },
        //template : '',
        controller: 'pwOEmbedController',
        link : function ($scope, element, attributes){
            /*
        	// When the oEmbed Value changes, then change the html here
        	$scope.$watch('oEmbed', function(value) {
        		//console.log('test',value);
        		element.html(value);
        	});
            */
            /*
            attrs.$observe('oEmbed', function(value) {
                element.html(value);
            });
            */
        }
    };

}]);


postworld.controller('pwOEmbedController',
    function pwOEmbedController($scope, $attrs, $sce, pwData, $log) {
            
            var link_url = $scope.link_url;

            // Status
            if( _.isUndefined( $scope.$parent.oEmbedStatus ) )
                $scope.$parent.oEmbedStatus = {};
            $scope.$parent.oEmbedStatus[link_url] = "loading";

            $scope.status = "loading";

            var autoplay = (
                !_.isUndefined( $scope.autoplay ) &&
                $scope.autoplay == true ) ?
                true : false;

            var args = {
                "link_url": link_url,
                "autoplay": autoplay
                };

            // MEDIA GET
            $scope.oEmbedGet = function(){
                pwData.wp_ajax('ajax_oembed_get', args ).then(
                    // Success
                    function(response) {    
                        $scope.$parent.oEmbedStatus[link_url] = "done";
                        $log.debug('return', response.data);
                        $scope.$parent.oEmbed = $sce.trustAsHtml( response.data ); 
                        $scope.$parent.oEmbedCode = response.data;                        
                    },
                    // Failure
                    function(response) {
                        $scope.status = "error";
                    }
                );
            };
            $scope.oEmbedGet();
});


postworld.run(function($rootScope, $templateCache) {
   $rootScope.$on('$viewContentLoaded', function() {
      $templateCache.removeAll();
   });
});
