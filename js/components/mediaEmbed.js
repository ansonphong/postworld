'use strict';
/*__  __          _ _         _____           _              _ 
 |  \/  | ___  __| (_) __ _  | ____|_ __ ___ | |__   ___  __| |
 | |\/| |/ _ \/ _` | |/ _` | |  _| | '_ ` _ \| '_ \ / _ \/ _` |
 | |  | |  __/ (_| | | (_| | | |___| | | | | | |_) |  __/ (_| |
 |_|  |_|\___|\__,_|_|\__,_| |_____|_| |_| |_|_.__/ \___|\__,_|

////////// -------- MEDIA EMBED CONTROLLER -------- //////////*/   

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


/*            _____           _              _ 
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
        link : function ($scope, element, attrs){
            /*
        	// When the oEmbed Value changes, then change the html here
        	$scope.$watch('oEmbed', function(value) {
        		//console.log('test',value);
        		element.html(value);
        	});
            */
            
            // Set autoplay value if it updates
            //attrs.$observe('autoplay', function(value) {
            //    $scope.setAutoplay();
            //});

        }
    };

}]);


postworld.controller('pwOEmbedController',
    [ '$scope', '$attrs', '$sce', 'pwData', '$log', '$pw', 'pwPosts', '_', '$timeout',
    function ($scope, $attrs, $sce, $pwData, $log, $pw, $pwPosts, $_, $timeout ) {
            
            // AUTOPLAY
            $scope.getAutoplay = function(){
                return (                                    // Autoplay Must be:
                    !_.isUndefined( $scope.autoplay ) &&    // Not Undefined
                    $scope.autoplay !== null &&             // Not Null
                    $scope.autoplay != false &&             // Not Boolean False
                    $scope.autoplay != 'false' ) ?          // Not String False
                    true : false;
            }

            // Status
            if( _.isUndefined( $scope.$parent.oEmbedStatus ) )
                $scope.$parent.oEmbedStatus = {};
            $scope.$parent.oEmbedStatus[$scope.link_ur] = "loading";

            $scope.status = "loading";

            // MEDIA GET
            $scope.oEmbedGet = function(){
                $scope.$parent.oEmbed = "";

                // Check if RUN is false
                if( $scope.run == false )
                    return false;

                // If it already has an embed code for the same link_url
                // No need to get it again
                if( $_.getObj( $scope.$parent, 'post.link_url' ) == $scope.link_url &&
                    $_.getObj( $scope.$parent, 'post.link_url_embed' ) != false ){
                    $log.debug( "Already have this embed code. Not getting it again." );
                    return false;
                }
                    
                // Setup the variables
                var vars = {
                    "link_url": $scope.link_url,
                    "autoplay": $scope.getAutoplay(),
                    };

                // AJAX Call
                $pwData.wp_ajax('ajax_oembed_get', vars ).then(
                    // Success
                    function(response) {    
                        $scope.$parent.oEmbedStatus[$scope.link_url] = "done";

                        // If not data, return false
                        if ( response.data == false )
                            return false;

                        $log.debug( 'GOT EMBED CODE' );
                        $scope.setEmbedCode( response.data )

                    },
                    // Failure
                    function(response) {
                        $scope.status = "error";
                    }
                );
            };


            $scope.setEmbedCode = function( embedCode ){
                
                var sceEmbedCode = embedCode; //$sce.trustAsHtml( embedCode ); //  

                $scope.$parent.oEmbed = sceEmbedCode;
                $scope.$parent.oEmbedCode = embedCode; 

                // Check if there is a post link_url associated with the parent scope post
                // And if it's the same as the a post link_url
                if( $_.getObj( $scope.$parent, 'post.link_url' ) == $scope.link_url ){
                    // Add the embed code to 'post.link_url_embed'
                    $scope.$parent.post.link_url_embed = sceEmbedCode;

                    // Get Feed ID
                    var feedId = $_.getObj( $scope.$parent, 'post.feed.id' );
                    var postId = $_.getObj( $scope.$parent, 'post.ID' );

                    // Check if there is a feed associated with the post
                    if( _.isString( feedId ) ){
                        // Store it in the central feed object
                        $pwPosts.mergeFeedPost( feedId, postId, { link_url_embed: sceEmbedCode } );
                        $log.debug( "MERGE FEED POST : SCE EMBED CODE : ", sceEmbedCode );
                    }

                }

            }

            // Set autoplay value if it updates
            $scope.$watch('link_url', function(value) {
                $timeout( function(){
                    $scope.oEmbedGet();
                }, 0 )
                

            });
     
}]);
