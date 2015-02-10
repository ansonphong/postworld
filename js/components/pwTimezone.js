/*_____ _                                    
 |_   _(_)_ __ ___   ___ _______  _ __   ___ 
   | | | | '_ ` _ \ / _ \_  / _ \| '_ \ / _ \
   | | | | | | | | |  __// / (_) | | | |  __/
   |_| |_|_| |_| |_|\___/___\___/|_| |_|\___|
                                             
////////////////  TIMEZONE  ////////////////*/

postworld.directive( 'pwTimezone', [function(){
    return { 
        controller: 'pwTimezoneCtrl',
        scope:{
        	latitude: 	'=timezoneLatitude',
        	longitude: 	'=timezoneLongitude',
        	timezoneObj: '=',
        },
    };
}]);

postworld.controller('pwTimezoneCtrl',
	['$rootScope', '$scope','$http', '$window', '$log', '$timeout',
	function( $rootScope, $scope, $http, $window, $log, $timeout ) {


	var timezoneDataPreprocess = function( data ){
		/*
		data = {
				"dstOffset": 0,
		        "rawOffset": -28800,
		        "status": "OK",
		        "timeZoneId": "America/Los_Angeles",
		        "timeZoneName": "Pacific Standard Time"
			}
		*/
		return {
			dst_offset: data.dstOffset,
			raw_offset: data.rawOffset,
			time_zone_id: data.timeZoneId,
			time_zone_name: data.timeZoneName,
			};

		return data;
	}


	// Any function returning a promise object can be used to load values asynchronously
	$scope.getTimezone = function( vars ) {
		/*
			vars = {
				latitude:0.00,
				longitude:0.00,
				date:
				}
		*/
		
		///// DEFAULTS ///// 
		if( _.isUndefined( vars ) )
			vars = {};
		if( _.isUndefined( vars.date ) )
			vars.date = new Date();
		if( _.isUndefined( vars.latitude ) )
			vars.latitude = $scope.latitude;
		if( _.isUndefined( vars.longitude ) )
			vars.longitude = $scope.longitude;

		// Init date object
		var date = new Date(vars.date);
		// Convert into UNIX timestamp
		vars.timestamp = date.getTime()/1000;

		$log.debug( 'TIMEZONE REQUEST', vars );

		return $http.get('https://maps.googleapis.com/maps/api/timezone/json', {
			params: {
				location: vars.latitude+','+vars.longitude,
				timestamp: vars.timestamp,
				key: 'AIzaSyCHOTsa82xjyjKCJcv0MwtVuXJkSGwKUHM',
			}
		}).then(function(res){
			$log.debug( 'TIMEZONE RESPONSE', res.data );
			$scope.timezoneObj = timezoneDataPreprocess( res.data );

		});
	};

	$scope.$watch('[latitude,longitude]', function(val){
		$log.debug( '$watch, [latitude,longitude]', val );

		$timeout( function(){
			$scope.getTimezone();
		}
			
		, 0 );
		

	});

}]);
