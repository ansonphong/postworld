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

postworld.controller('pwGeoAutocompleteCtrl', ['$rootScope', '$scope','$http', '$window', '$log', function( $rootScope, $scope, $http, $window, $log ) {

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
		$scope.$emit('pwAddGeocode', val);
		$rootScope.$broadcast('pwAddGeocode', val);
		$log.debug( 'pwAddGeocode', val );

	}

}]);




/* ____                          _        ___                   _   
  / ___| ___  ___   ___ ___   __| | ___  |_ _|_ __  _ __  _   _| |_ 
 | |  _ / _ \/ _ \ / __/ _ \ / _` |/ _ \  | || '_ \| '_ \| | | | __|
 | |_| |  __/ (_) | (_| (_) | (_| |  __/  | || | | | |_) | |_| | |_ 
  \____|\___|\___/ \___\___/ \__,_|\___| |___|_| |_| .__/ \__,_|\__|
                                                   |_|              
///////// ---------------- GEOCODE INPUT ---------------- /////////*/

postworld.directive( 'pwGeoInput', [ '$log', '_', function( $log, $_ ){
    return { 
        controller: 'pwGeoInputCtrl',
        scope:{
        	'geoPost':'=geoPost',
        	'geoLocationObj':'=geoLocationObj',
        },
        link : function( $scope ){

        	// Catch broadcast of geocode change
			$scope.$on('pwAddGeocode', function(event, data) { 
				$log.debug( 'pwGeoInputCtrl : $on : pwAddGeocode : RECEIVED : ', data );
				// Process the Geocode Data
				$scope.geocodeInputs(data);
				
			});

        }
    };
}]);

postworld.controller('pwGeoInputCtrl',
	['$scope', '$window', '$timeout', '_', '$log',
	function($scope, $window, $timeout, $_, $log ) {

	$scope.geocodeInputs = function( geocode ){

		///// EXTRAPOLATE LOCATION OBJECT /////
		var location = {};
		angular.forEach( geocode.address_components, function( address_component ){
			// Country
			if( $_.inArray( 'country', address_component.types) ){
				location.country = address_component.long_name;
				location.country_code = address_component.short_name;
			}
			// Region / State / Province
			if( $_.inArray( 'administrative_area_level_1', address_component.types) ){
				location.region = address_component.long_name;
				location.region_code = address_component.short_name;
			}

			// City (from 'locality')
			if( $_.inArray( 'locality', address_component.types) ){
				location.city = address_component.long_name;
				location.city_code = address_component.short_name;
			}

			// City (from 'administrative_area_level_2')
			if( $_.inArray( 'administrative_area_level_2', address_component.types) ){
				if( _.isUndefined( location.city )  ){
					location.city = address_component.long_name;
					location.city_code = address_component.short_name;
				}
			}

			// Zone (from 'administrative_area_level_3')
			if( $_.inArray( 'administrative_area_level_3', address_component.types) ){
				location.zone = address_component.long_name;
				location.zone_code = address_component.short_name;
			}

			// Postal Code
			if( $_.inArray( 'postal_code', address_component.types) ){
				location.postal_code = address_component.long_name;
			}

			// Street (from 'route')
			if( $_.inArray( 'route', address_component.types) ){
				location.street = address_component.long_name;
				location.street = address_component.short_name;
			}

			// Street Number (from 'street_number')
			if( $_.inArray( 'street_number', address_component.types) ){
				location.street_number = address_component.long_name;
				location.street_number = address_component.short_name;
			}

			// Street Number (from 'street_number')
			if( $_.inArray( 'point_of_interest', address_component.types) ||
				$_.inArray( 'establishment', address_component.types) ){
				location.name = address_component.long_name;
			}

			


		});

		///// ADDRESS /////
		// Default Street Number
		location.street_number = ( _.isUndefined( location.street_number ) ) ?
			'' : location.street_number + ' ';
		// Default Street
		location.street = ( _.isUndefined( location.street ) ) ?
			'' : location.street;
		// Construct the address
		location.address = location.street_number + location.street;

		// Location Formatted Address
		location.formatted_address = geocode.formatted_address;


		///// SET SCOPE /////
		// If location object is defined
		if( !_.isUndefined( $scope.geoPost ) ){
			// Set Location Object
			$scope.geoLocationObj = location;
		}

		// If a post object is defined
		if( !_.isUndefined( $scope.geoPost ) ){
			// Set Lattitude and Longitude into Post Object
			$scope.geoPost['geo_latitude'] = geocode.geometry.location.lat;
			$scope.geoPost['geo_longitude'] = geocode.geometry.location.lng;
		}	

	};

	$scope.clearGeocode = function(){}


}]);






