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


/*////////// ------------ O-EMBED DIRECTIVE ------------ //////////*/  

postworld.directive( 'oEmbed',
	[ '$sce', '$log', '$timeout', 'pwData', '_',
	function( $sce, $log, $timeout, $pwData, $_ ){

	return { 
		restrict: 'AE',
		
		scope : {
			linkUrl: 	"@oEmbed",
			autoplay: 	"@embedAutoplay",
			run: 		"@embedRun",
		},
		
		//template : '',
		controller: 'pwOEmbedNewCtrl',
		link : function ($scope, element, attrs){

			// WATCH : O-Embed Link Url for changes
			$scope.$watch(
				function( $scope ){
					return $scope.linkUrl;
				},
				function(value) {
					$scope.getEmbedCode( $scope.linkUrl, $scope.getAutoplay() );
				}
			);

			// WATCH : Embed Code for Changes
			$scope.$watch(
				function( $scope ){
					var embedId = $_.sanitizeKey( $scope.linkUrl );
					return $_.getObj( $pwData['embeds'], embedId );

				},
				function( value ) {
					//$scope.setEmbedCode();
					if( _.isString( value ) )
						element.html( value );
					else
						element.html( '' );
				}
			);

		}
	};

}]);

postworld.controller('pwOEmbedNewCtrl',
	[ '$scope', '$attrs', '$sce', 'pwData', '$log', '$pw', 'pwPosts', '_', '$timeout', '$rootScope', 'oEmbedServe',
	function ($scope, $attrs, $sce, $pwData, $log, $pw, $pwPosts, $_, $timeout, $rootScope, $oEmbedServe ) {

		$scope.getAutoplay = function(){
			// Evaluate the value
			var value = $scope.$parent.$eval( $scope.autoplay );
			// Set default
			if( _.isUndefined( value ) )
				value = false;
			// Cast as Boolean
			if( !_.isBoolean( value ) )
				value = ( value === 'true' || value === '1' );
			return value;
		}

		$scope.getRun = function(){
			// Evaluate the value
			var value = $scope.$parent.$eval( $scope.run );
			// Set default
			if( _.isUndefined( value ) )
				value = true;
			// Cast as Boolean
			if( !_.isBoolean( value ) )
				value = ( value === 'true' || value === '1' );
			return value;
		}

		$scope.getEmbedCode = function( linkUrl, autoPlay ){	
			// Return false if run is false
			if( $scope.getRun() == false )
				return false;

			// Get embed code
			return $oEmbedServe.get( linkUrl, autoPlay );

		}

}]);


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

	var cacheEmbed = function( embedId, embedCode ){
		// Cache the embed code in the pwData service 
		$pwData['embeds'][ embedId ] = embedCode;

	};

	var getOEmbed = function( linkUrl, autoPlay ){

		$log.debug( "oEmbedServe.getOEmbed( " + linkUrl + ", " + autoPlay + " )" );

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

				$log.debug( 'oEmbedServe.getOEmbed.$pwData.wp_ajax : RESPONSE : ' + embedId + " : ", embedCode );

				// If not data, return false
				if ( embedCode == false )
					return false;
				
				// Cache the embed code in the pwData service 
				cacheEmbed( embedId, embedCode );

			},
			// Failure
			function(response) {
				$scope.status = "error";
				$log.error( 'oEmbedServe.getOEmbed.$pwData.wp_ajax : RESPONSE : ', response );
			}
		);
	}

	return{
		getOEmbed : function( linkUrl, autoPlay ){
			return getOEmbed( linkUrl, autoPlay );
		},

		get: function( linkUrl, autoPlay ){
			$log.debug( "oEmbedServe.get( " + linkUrl + ", " + autoPlay + " )" );
			
			// Set defaults
			if( _.isUndefined( autoPlay ) )
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
			// Or false if it's still getting it from the server
			return code;
		},

	}	

}]);
