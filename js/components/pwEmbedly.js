/*               _              _   _         _____      _                  _   
   ___ _ __ ___ | |__   ___  __| | | |_   _  | ____|_  _| |_ _ __ __ _  ___| |_ 
  / _ \ '_ ` _ \| '_ \ / _ \/ _` | | | | | | |  _| \ \/ / __| '__/ _` |/ __| __|
 |  __/ | | | | | |_) |  __/ (_| |_| | |_| | | |___ >  <| |_| | | (_| | (__| |_ 
  \___|_| |_| |_|_.__/ \___|\__,_(_)_|\__, | |_____/_/\_\\__|_|  \__,_|\___|\__|
                                      |___/                                     
//////////////// -------- EMBEDLY EXTRACT DIRECTIVE  -------- /////////////////*/

postworld.directive( 'pwEmbedlyExtract', [ function($scope){
	return {
		restrict: 'AE',
		controller: 'pwEmbedlyExtractCtrl',
		scope: {
			extractUrlModel:'=extractUrlModel',
			extractObjectModel:'=extractObjectModel',
			mediaClick:'&'
		},
		link: function( $scope, element, attrs ){
			/*
			// OBSERVE Attribute
			attrs.$observe('var', function(value) {
			});
			// CLICK Object
			element.bind('click', function() {
			});
			*/
		}
	};
}]);

postworld.controller( 'pwEmbedlyExtractCtrl',
	[ '$scope', '$window', '$timeout', '$log', 'pwData', 'embedly', 'pwEditPostFilters', 'ext',
	function( $scope, $window, $timeout, $log, $pwData, $embedly, $pwEditPostFilters, $ext ) {


	$scope.$parent.extractUrl = function() {
		$scope.status = "busy";

		// Update Status in Parent Scope
		if( _.isUndefined( $scope.$parent.statusObj ) )
			$scope.$parent.statusObj = {};
		$scope.$parent.statusObj.extractUrl = 'busy';

		$embedly.liveEmbedlyExtract( $scope.extractUrlModel ).then( // 
				// Success
				function(response) {
					$log.debug( response );

					// Clear the HTML Field
					if( $ext.objExists( response, 'media.html' ) )
						response['media']['html'] = '';

					$scope.extractObjectModel = response;

					$scope.status = "done";
					$scope.$parent.statusObj.extractUrl = 'done';
				},
				// Failure
				function(response) {
					//alert('Could not find URL.');
					throw {message:'Embedly Error'+response};
					$scope.status = "done";
					$scope.$parent.statusObj.extractUrl = 'done';
				}
			);
	}

	$scope.$parent.clearExtract = function() {
		$scope.extractObjectModel = {};
	};

	$scope.$parent.hasExtract = function(){
		if( !_.isEmpty( $scope.extractObjectModel ) )
			return true;
		else
			return false;
	};
	
	// EMBEDLY OBJECT WATCH : Watch for any changes to the embedly data
	$scope.$watch( "extractObjectModel",
		function (){

		// Here Process the data from embedly.extract into the post format
		if( $scope.$parent.hasExtract() ){

			// Translate Embedly Object into WP Object
			$scope.embedlyExtractTranslated = $embedly.translateToPostData( $scope.extractObjectModel );
			// Merge it with the current post
			$scope.$parent.post = $ext.mergeRecursiveObj( $scope.$parent.post, $scope.embedlyExtractTranslated ) ;

			// Extract image meta
			$scope.$parent.extractImageMeta = $embedly.embedlyExtractImageMeta( $scope.extractObjectModel );
			
			// Default Selected Image
			$scope.$parent.extractImageMeta['selectedIndex'] = 0;

			$scope.broadcastSelectedImage();

		}

	}, 1 );

	// http://newyorktimes.com
	///// SELECT IMAGES /////
	// Previous Image
	$scope.$parent.previousExtractImage = function(){
		 if( $scope.$parent.extractImageMeta['selectedIndex'] == 0 ){
			$scope.$parent.extractImageMeta['selectedIndex'] = $scope.$parent.extractImageMeta['count'] - 1;
		}
		else
			$scope.$parent.extractImageMeta['selectedIndex'] --;

		$scope.broadcastSelectedImage();
	};
	// Next Image
	$scope.$parent.nextExtractImage = function(){
		var imageMeta = $scope.$parent.extractImageMeta;

		if( imageMeta['selectedIndex'] >= imageMeta['count']-1 ){
			$scope.$parent.extractImageMeta['selectedIndex'] = 0;
		}
		else
			$scope.$parent.extractImageMeta['selectedIndex'] ++;

		$scope.broadcastSelectedImage();
	};


	///// BROADCAST SELECTED IMAGE CHANGE /////
	$scope.broadcastSelectedImage = function (){
		// Broadcast the change of a changed image index
		$scope.$broadcast(
			'updateSelectedExtractImage',
			$scope.$parent.extractImageMeta['selectedIndex']);
	}

	///// UPDATE SELECTED IMAGE /////
	$scope.$on('updateSelectedExtractImage', function(event, selectedIndex) {

		// If Meta Value doesn't exist, return early
		if( !$ext.objExists( $scope.$parent, 'extractImageMeta' ) )
			return false;

		// Localize Values
		var images = $scope.$parent.extractImageMeta['images'];

		// Set the Thumbnail URL Value of the Post Object
		$scope.$parent.post.thumbnail_url = images[selectedIndex].url;
		//alert($scope.$parent.post.thumbnail_url);
	});


}]);



/* _                       _              _   _       
  | |   _    ___ _ __ ___ | |__   ___  __| | | |_   _ 
 / __) (_)  / _ \ '_ ` _ \| '_ \ / _ \/ _` | | | | | |
 \__ \  _  |  __/ | | | | | |_) |  __/ (_| |_| | |_| |
 (   / (_)  \___|_| |_| |_|_.__/ \___|\__,_(_)_|\__, |
  |_|                                           |___/ 

////////// ------- Embedly SERVICE ------- //////////*/

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
                
                var image_status_set =
                	( embedly_extract.images.length >= 1 ) ?
                	true : false ;

                return{
                    status: image_status_set,
                    count: embedly_extract.images.length,
                    images: embedly_extract.images,
                };
            },
                       
        };

    });






