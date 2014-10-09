infinite.directive( 'iSaveOption', [ function($scope){
	return {
		restrict: 'A',
		controller: 'iSaveOptionCtrl',
		link: function( $scope, element, attrs ){
			/*
			// OBSERVE Attribute
			attrs.$observe('var', function(value) {
			});
			*/
		}
	};
}]);

infinite.controller( 'iSaveOptionCtrl',
	['$scope', '$window', '$timeout', '$parse', '$log', 'iData', '_',
	function($scope, $window, $timeout, $parse, $log, $iData, $_) {

		////////// SAVE OPTIONS //////////
		$scope.saveOption = function( optionName, optionModel ){
			//	optionName 	=	The option_name column in wp_options table
			//	optionModel =	An object in the scope model - no expression, must be top level
			//					given as the name of the object in current scope

			$scope.status = "saving";

			///// OPTION VALUE /////
			// Get the Option Value from the scope model
			optionValue = $_.getObj( $scope, optionModel );
			valueIsObject = _.isObject( optionValue );

			// If the value is an object
			if( valueIsObject )
				// Convert it to a JSON string
				optionValue = angular.toJson( optionValue );

			// Prepare the object for AJAX function
			var vars = {
				option_name: optionName,
				option_value: optionValue
			};
			
			// Run the AJAX Function
			$iData.i_save_option( vars ).then(
				// Success
				function(response) {
					$log.debug('iData.i_save_option : RESPONSE : ', response);

					/* ///// NOT REQUIRED /////
					// Get the object value from the response
					optionValue = response.data;

					// If it's originally an object
					if( valueIsObject )
						// Decode it from JSON string
						optionValue = angular.fromJson(optionValue);

					// Re-populate the scope value with the value from the DB
					$scope[optionValue] = optionValue;
					*/

					//alert( JSON.stringify($scope[optionValue]) );
					$scope.status = "done";
					// TODO : Add Error Handling for response object { "error": "message" }
				},
				// Failure
				function(response) {
					throw { message:'Error: ' + JSON.stringify(response)};
				}
			);
		};

}]);