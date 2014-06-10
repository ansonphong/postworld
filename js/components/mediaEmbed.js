'use strict';


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

postworld.directive( 'oEmbed', ['$sce',function($scope, $sce){

    return { 
        restrict: 'AE',
        scope : {
            link_url:"=oEmbed",
            autoplay:"=autoplay",
            run:"=run",
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
    function ($scope, $attrs, $sce, pwData, $log) {
            
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


            // MEDIA GET
            $scope.oEmbedGet = function(){

                // Check if RUN is false
                if( $scope.run == false )
                    return false;

                var args = {
                    "link_url": link_url,
                    "autoplay": autoplay
                    };
                pwData.wp_ajax('ajax_oembed_get', args ).then(
                    // Success
                    function(response) {    
                        $scope.$parent.oEmbedStatus[link_url] = "done";

                        // If not data, return false
                        if ( response.data == false )
                            return false;

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

            $scope.$watch('run', function(value) {
                $scope.oEmbedGet();
            },1);
            

            
});
