

/*_____ _           _      ____                          
 |  ___| | __ _ ___| |__  / ___|__ _ _ ____   ____ _ ___ 
 | |_  | |/ _` / __| '_ \| |   / _` | '_ \ \ / / _` / __|
 |  _| | | (_| \__ \ | | | |__| (_| | | | \ V / (_| \__ \
 |_|   |_|\__,_|___/_| |_|\____\__,_|_| |_|\_/ \__,_|___/
														 
 ////////////////////////////////////////////////////////////////////////////*/

postworld.directive( 'flashCanvas',
	[  '$timeout',
	function( $timeout){
	return {
		//restrict: 'AE',
		scope:{
			'canvasId': 	'@canvasId',
			'canvasWidth': 	'@canvasWidth',
			'canvasHeight': '@canvasHeight',
			'canvasClass': '@canvasClass'
		},
		controller: 'flashCanvasCtrl',
		link: function( $scope, element, attrs ){
			$scope.wizardState = {};

			// WIZARD Attribute
			attrs.$observe('flashCanvas', function(value) {
				// If a value is provided
				if ( value ) {
					// Run the function to load the file
					$scope.loadFlashScript( value );
				}
			});

			// Append Canvas
			$scope.appendCanvas = function( flashId ){

				// Get ID from Attributes
				if( !_.isUndefined( $scope.canvasId ) )
					elementId = $scope.canvasId;
				else
				// Set default ID
					elementId = flashId;

				// Set Defaults
				var width = ( !_.isUndefined( $scope.canvasWidth ) ) ? 
					$scope.canvasWidth : 100;
				var height = ( !_.isUndefined( $scope.canvasHeight ) ) ? 
					$scope.canvasHeight : 100;
				var classes = ( !_.isUndefined( $scope.canvasClass ) ) ? 
					$scope.canvasClass : '';

				// Create the element
				var canvas = angular.element( '<canvas></canvas>' );
				
				canvas.attr( 'id', elementId );
				canvas.attr( 'class', classes );
				canvas.attr( 'width', width );
				canvas.attr( 'height', height );
				
				element.append( canvas );
				
				// Init the Canvas
				$timeout( function(){
					$scope.initCanvas( elementId, flashId );
					}, 500 );

			}

		}

	};

}]);

postworld.controller('flashCanvasCtrl',
	['$scope', '$rootScope', '$window', '$timeout', '_', 'pwData', '$log', 'flashData', 'pw',
	function($scope, $rootScope, $window, $timeout, $_, $pwData, $log, $flashData, $pw ) {
	   
	$scope.loadFlashScript = function( file ){
		// ie. file = "loops.loading-A" 

		// Get the URL of the script file to load
		var fileUrl = $_.getObj( $flashData.files() , file );

		// Set the ID of the Canvas object
		var canvasId = $scope.sanitizeId( file );

		// Define the callback to use
		var callback = $scope.appendCanvas( canvasId );

		// Load the script, and run the callback
		$pw.loadScript( fileUrl, callback );

	};

	$scope.initCanvas = function( elementId, flashId ) {
		canvas = document.getElementById( elementId );
		exportRoot = new lib[ flashId ]();

		stage = new createjs.Stage(canvas);
		stage.addChild(exportRoot);
		stage.update();

		createjs.Ticker.setFPS(lib.properties.fps);
		createjs.Ticker.addEventListener("tick", stage);
	}

	$scope.sanitizeId = function( inputString ){

		// Split it on the dot
		var stringParts = inputString.split('.');

		// Get the part after the last dot
		var inputId = stringParts[stringParts.length-1];

		// Sanitize
		inputId = inputId.replace( '-','_' );

		// Return
		return inputId;

	}

}]);


/*_____ _           _       ____        _        
 |  ___| | __ _ ___| |__   |  _ \  __ _| |_ __ _ 
 | |_  | |/ _` / __| '_ \  | | | |/ _` | __/ _` |
 |  _| | | (_| \__ \ | | | | |_| | (_| | || (_| |
 |_|   |_|\__,_|___/_| |_| |____/ \__,_|\__\__,_|
												 
 //////////////////////////////////////////////////////////*/

postworld.factory('flashData',
	['$resource','$q','$log','$window', 'pw',
	function ($resource, $q, $log, $window, $pw) {   
	// DECLARATIONS

	return {

		files: function(){
			var baseUrl = $pw.pluginUrl('canvas/');
			return {
				"loops":{
					"loading-A" : baseUrl + "loops/loading-A.js",
				}
			};
		},
		

	};

}]);


