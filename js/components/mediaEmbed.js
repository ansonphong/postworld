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

*/

/*////////// ------------ O-EMBED SERVICE ------------ //////////*/  
postworld.factory( 'oEmbedServe',
	[ '$log', '$sce', '_', 'pwData', '$timeout', '$rootScope',
	function( $log, $sce, $_, $pwData, $timeout, $rootScope ){

	var cacheEmbed = function( embedCode, embedId ){
		// Cache the embed code in the pwData service 
		$pwData['embeds'][ embedId ] = embedCode;

	};

	var getOEmbed = function( linkUrl, autoPlay ){

		// Generate Embed ID from URL
		var embedId = $_.sanitizeKey( linkUrl );

		// Setup the variables
		var vars = {
			"link_url": linkUrl,
			"autoplay": autoPlay,
			};

		// AJAX Call
		$pwData.wp_ajax('ajax_oembed_get', vars ).then(
			// Success
			function( response ){

				var embedCode = response.data;

				// If not data, return false
				if ( embedCode == false )
					return false;

				$log.debug( 'GOT O-EMBED CODE : ' + embedId + " : ", embedCode );

				// Cache the embed code in the pwData service 
				$pwData['embeds'][ embedId ] = embedCode;

				//cacheEmbed( embedCode, embedId );

			},
			// Failure
			function(response) {
				$scope.status = "error";
			}
		);
	}

	return{
		getOEmbed : function( linkUrl, autoPlay ){
			return getOEmbed( linkUrl, autoPlay );
		},

		get: function( linkUrl, autoPlay ){
			// Set defaults
			if( _.isEmpty( autoPlay ) )
				autoPlay = false;

			// Make key from URL
			var embedId = $_.sanitizeKey( linkUrl );

			// Get the cached embed code
			var code = $_.getObj( $pwData['embeds'], embedId );

			// If the embed code doesn't exist yet
			if( code == false ){
				// Create placeholder object
				$pwData['embeds'][ embedId ] = "<div class='pw-embed-loading'></div>"; // Cannot be empty
				// Get and cache it
				getOEmbed( linkUrl, autoPlay );
			}

			// Return the embed code
			return code;

		},

	}	

}]);



/*////////// ------------ O-EMBED DIRECTIVE ------------ //////////*/  

postworld.directive( 'oEmbed', [ '$sce', function( $sce ){

	return { 
		restrict: 'AE',
		/*
		scope : {
			link_url: 	"=oEmbed",
			autoplay: 	"=autoplay",
			run: 		"=run",
		},
		*/
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
	[ '$scope', '$attrs', '$sce', 'pwData', '$log', '$pw', 'pwPosts', '_', '$timeout', '$rootScope', 'oEmbedServe',
	function ($scope, $attrs, $sce, $pwData, $log, $pw, $pwPosts, $_, $timeout, $rootScope, $oEmbedServe ) {

		$scope.getEmbedCode = function( linkUrl, autoPlay, run ){
			// Set defaults
			if( _.isUndefined( run )  )
				run = true;

			// Return false if run is false
			if( run != true )
				return false;

			// Get embed code
			return $oEmbedServe.get( linkUrl, autoPlay );

		}

}]);
