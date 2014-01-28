'use strict';

/*
   _                       _              _   _       
  | |   _    ___ _ __ ___ | |__   ___  __| | | |_   _ 
 / __) (_)  / _ \ '_ ` _ \| '_ \ / _ \/ _` | | | | | |
 \__ \  _  |  __/ | | | | | |_) |  __/ (_| |_| | |_| |
 (   / (_)  \___|_| |_| |_|_.__/ \___|\__,_(_)_|\__, |
  |_|                                           |___/ 
////////// ------------ Embedly SERVICE ------------ //////////*/  
postworld.factory('embedly', function ($resource, $q, $log, $window) {     
        // TODO Replace this with your Production Key
        // http://api.embed.ly/1/extract?key=:key&url=:url&maxwidth=:maxwidth&maxheight=:maxheight&format=:format&callback=:callback
        var embedlyKey = $window.pwSiteGlobals.embedly.key;
        var embedlyUrl = "http://api.embed.ly/1/:action";
        var resource = $resource(embedlyUrl, {key:embedlyKey, url:''}, 
                                    {   embedly_call: { method: 'GET', isArray: false, params: {action:'extract'} },    }
                                );
        return {
            // A simplified wrapper for doing easy AJAX calls to Wordpress PHP functions
            embedly_call: function(action,url, options) {
                $log.debug('embedly.embedly_call', action, url, options);
                var deferred = $q.defer();
                // works only for non array returns
                resource.embedly_call({action:action, url:url, options:options},
                    function (data) {
                        deferred.resolve(data);
                    },
                    function (response) {
                        deferred.reject(response);
                    });
                return deferred.promise;        
            },          
            liveEmbedlyExtract: function( link_url, options ){
                // LIVE EMBEDLY EXTRACT
                // API : http://embed.ly/docs/extract/api
                if (!link_url) throw {message:'embedly:link_url not provided'};
                // Escape the URL
                escape(link_url);
                // if there are options, add them to the url here.
                // call the service
                return this.embedly_call('extract',link_url,options);
                // for Ajax Calls

                //return: embedly_extract_obj;
            },
            translateToPostData: function( embedly_extract ){
                if (typeof embedly_extract.images[0] !== 'undefined' )
                    var link_url_set = embedly_extract.images[0].url;
                else
                    var link_url_set = ""; // defult image_url

                return{
                    post_title: embedly_extract.title,
                    post_excerpt: embedly_extract.description,
                    link_url: embedly_extract.url,
                    thumbnail_url: link_url_set,
                };
            },
            embedlyExtractImageMeta: function( embedly_extract ){
                
                if ( embedly_extract.images.length >= 1 )
                    var image_status_set = true;
                else
                    var image_status_set = false;

                return{
                    image_status: image_status_set,
                    image_count: embedly_extract.images.length,
                    images: embedly_extract.images,
                };
            },
                       
        };

    });

    
postworld.controller('pwEmbedly', function pwEmbedly($scope, $location, $log, pwData, $attrs, embedly) {
        $scope.embedlyGet = function () {
            
            embedly.liveEmbedlyExtract( $scope.link_url).then(
                // Success
                function(response) {
                    console.log(response);    
                    $scope.embedlyResponse = response;
                },
                // Failure
                function(response) {
                    throw {message:'Embedly Error'+response};
                }
            );
                  
        };      
    }
);



/*
  __  __          _ _         __  __           _       _ 
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
        //restrict: 'A',
        //scope : function(){
        //},
        //template : '',
        controller: 'pwOEmbedController',
        link : function ($scope, element, attributes){
        	// When the oEmbed Value changes, then change the html here
        	$scope.$watch('oEmbed', function(value) {
        		console.log('test',value);
        		element.html(value);
        	});          
        }
    };

}]);


postworld.controller('pwOEmbedController',
    function pwOEmbedController($scope, $attrs, $sce, pwData) {
            //alert( attributes.oEmbed );
            $scope.status = "loading";
            $scope.oEmbed = "embed code for : " + $attrs.oEmbed;

            var link_url = $attrs.oEmbed;
            var args = { "link_url": link_url };

            // MEDIA GET
            $scope.oEmbedGet = function(){
                pwData.wp_ajax('ajax_oembed_get', args ).then(
                    // Success
                    function(response) {    
                        $scope.status = "done";
                        console.log('return',response.data);
                        $scope.oEmbed = response.data; // $sce.trustAsHtml( response.data );                        
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
