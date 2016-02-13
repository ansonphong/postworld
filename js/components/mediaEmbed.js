'use strict';
/*__  __          _ _         _____           _              _ 
 |  \/  | ___  __| (_) __ _  | ____|_ __ ___ | |__   ___  __| |
 | |\/| |/ _ \/ _` | |/ _` | |  _| | '_ ` _ \| '_ \ / _ \/ _` |
 | |  | |  __/ (_| | | (_| | | |___| | | | | | |_) |  __/ (_| |
 |_|  |_|\___|\__,_|_|\__,_| |_____|_| |_| |_|_.__/ \___|\__,_|

////////// -------- MEDIA EMBED CONTROLLER -------- //////////*/   
/*
var mediaEmbed = function ( $scope, $sce, $pwData ) {

	$scope.oEmbed = "";
	$scope.oEmbedGet = function (link_url) {
		var args = { "link_url":link_url };
		var oEmbed = "";
		$pwData.wpAjax('ajax_oembed_get', args ).then(
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
*/

/*////////// ------------ O-EMBED DIRECTIVE ------------ //////////*/  

postworld.directive( 'oEmbed',
	function( $sce, $log, $timeout, $pwData, $_ ){
	return { 
		restrict: 'AE',
		scope : {
			url: 		"@oEmbed",
			autoplay: 	"@embedAutoplay",
			//theme: 		"@embedTheme",
			run: 		"@embedRun",
		},
		
		//template : '',
		controller: 'oEmbedController',
		link : function ($scope, element, attrs){

			// WATCH : O-Embed Link Url for changes
			$scope.$watch(
				function( $scope ){
					return $scope.url;
				},
				function(value) {
					// If link URL isn't empty
					if( !_.isEmpty( $scope.url ) ){
						var vars = {
							url: $scope.url,
						};
						
						// Add autplay if it's not undefined
						var autoplay = $scope.getAutoplay();
						if( autoplay != undefined  )
							vars.autoplay = autoplay;

						// Get the embed code
						$scope.getEmbedCode(vars);
					}
				}
			);

			// WATCH : Embed Code for Changes
			$scope.$watch(
				function( $scope ){
					var embedId = $_.sanitizeKey( $scope.url );
					return $_.getObj( $pwData['embeds'], embedId );

				},
				function( value ) {
					// If the value is a string (HTML)
					if( _.isString( value ) )
						// Insert the Embed Code
						element.html( value );
					// If false
					else
						// Remove the embed code
						element.html( '' );
				}
			);

		}
	};

});

postworld.controller('oEmbedController',
	function ($scope, $attrs, $sce, $pwData, $log, $pw, $pwPosts, $_, $timeout, $rootScope, $oEmbedServe ) {

		$scope.getAutoplay = function(){
			// Evaluate the value
			var value = $scope.$parent.$eval( $scope.autoplay );
			// Set default
			if( _.isUndefined( value ) || _.isNull( value ) )
				return undefined;
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

		$scope.getEmbedCode = function( vars ){	
			// Return false if run is false
			if( $scope.getRun() == false )
				return false;

			// Get embed code
			return $oEmbedServe.get( vars );

		}

});


/*            _____           _              _ 
   ___       | ____|_ __ ___ | |__   ___  __| |
  / _ \ _____|  _| | '_ ` _ \| '_ \ / _ \/ _` |
 | (_) |_____| |___| | | | | | |_) |  __/ (_| |
  \___/      |_____|_| |_| |_|_.__/ \___|\__,_|

*/

/*////////// ------------ O-EMBED SERVICE ------------ //////////*/  
postworld.factory( '$oEmbedServe',
	function( $log, $sce, $_, $pwData, $timeout, $rootScope ){

	var cacheEmbed = function( embedId, embedCode ){
		// Cache the embed code in the $pwData service 
		$pwData['embeds'][ embedId ] = embedCode;
	};

	var getOEmbed = function( vars ){
		var defaultVars = {
			url: 	'',
			autoplay: 	'',
			//theme: 		'',
		};

		$log.debug( "oEmbedServe.getOEmbed()", vars );

		// Generate Embed ID from URL
		var embedId = $_.sanitizeKey( vars.url );

		// AJAX Call
		$pwData.wpAjax('ajax_oembed_get', vars ).then(
			// Success
			function( response ){

				var embedCode = response.data;

				$log.debug( 'oEmbedServe.getOEmbed.$pwData.wpAjax : RESPONSE : ' + embedId + " : ", embedCode );

				// If not data, return false
				if ( embedCode == false )
					return false;
				
				// Cache the embed code in the $pwData service 
				cacheEmbed( embedId, embedCode );

			},
			// Failure
			function(response) {
				$scope.status = "error";
				$log.error( 'oEmbedServe.getOEmbed.$pwData.wpAjax : RESPONSE : ', response );
			}
		);
	}

	return{
		getOEmbed : function( vars ){
			return getOEmbed( vars );
		},

		get: function( vars ){
			$log.debug( "oEmbedServe.get( " + vars.url + ", " + vars.autoplay + " )" );

			// Make key from URL
			var embedId = $_.sanitizeKey( vars.url );

			// Get the cached embed code
			var code = $_.getObj( $pwData['embeds'], embedId );

			// If the embed code doesn't exist yet
			if( code == false ){
				// Create placeholder object
				$pwData['embeds'][ embedId ] = "<div class='pw-embed-loading'></div>"; // Cannot be empty
				// Get and cache it
				getOEmbed( vars );
			}

			// Return the embed code
			// Or false if it's still getting it from the server
			return code;
		},

	}	

});
