
/* ____                 _         _                                  _      _       
  / ___| ___  ___      / \  _   _| |_ ___   ___ ___  _ __ ___  _ __ | | ___| |_ ___ 
 | |  _ / _ \/ _ \    / _ \| | | | __/ _ \ / __/ _ \| '_ ` _ \| '_ \| |/ _ \ __/ _ \
 | |_| |  __/ (_) |  / ___ \ |_| | || (_) | (_| (_) | | | | | | |_) | |  __/ ||  __/
  \____|\___|\___/  /_/   \_\__,_|\__\___/ \___\___/|_| |_| |_| .__/|_|\___|\__\___|
                                                              |_|                   
///////////// ------------ LOCATION AUTOCOMPLETE CONTROLLER ------------ /////////////*/

postworld.directive( 'pwGeoAutocomplete', [function(){
    return { 
        controller: 'pwGeoAutocompleteCtrl'
    };
}]);

postworld.controller('pwGeoAutocompleteCtrl', ['$scope','$http', '$window', '$log', function( $scope, $http, $window, $log ) {

	$scope.locationObject = {};

	// Any function returning a promise object can be used to load values asynchronously
	$scope.getLocation = function(val) {
		return $http.get('http://maps.googleapis.com/maps/api/geocode/json', {
			params: {
				address: val,
				sensor: false
			}
		}).then(function(res){
			var addresses = [];
			angular.forEach(res.data.results, function(item){
				addresses.push(item);
			});
			return addresses;
		});
	};

	$scope.addGeocode = function(val) {
		// DO ADD LOCATION CALLBACK - DEFINED BY THEME / CONTEXT
		// OR... use $emit with location object - caught by parent local context specific controller
	
		//$scope.locationObject = val;
		$scope.$emit('pwAddGeocode', val);
		$log.debug( "pwAddGeocode", val );

	}

	// LISTENER FOR EMITTED LOCATION FROM LOAD POST
	// THEN INSERT INTO addLocation()

	// If 'post' object exists, insert it 
	// handle with callbacks, preset callbacks based on context :
	// ie. add as linear taxonomy terms input (ie. location taxonomy)


	/*
	$scope.addTag = function(){
		// Cycle through the tagOptions Object
		angular.forEach( $scope.tagOptions, function( tag ){
			if( tag.slug == $scope.queryTag ){
				$scope.tags_input_obj.push(tag);
				$scope.queryTag = "";
			}           
		});
	}
	*/


}]);


