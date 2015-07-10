
postworld.directive('pwEvent',
	[
		'_',
		'pwDate',
		'$log',

	function( $_, $pwDate, $log ) {

	return {
		restrict: 'A',
		scope:{
			e:'=pwEvent',
			eventObj:'=',
		},
		link: function( $scope, element, attrs ) {
			
			/**
			 * Watch the timezone object for changes
			 * Set the boolean $scope.hasTimezone
			 *
			 * If it has timezone, offset the date/timepicker
			 * To the event time
			 */
			var hasTimezone = false;

			$scope.$watch( 'e',
				function( e ){
					$log.debug( 'pwEvent : EVENT CHANGED', $scope.e );

					/**
					 * Check if the event object has a timezone
					 * And set a boolean.
					 */
					if( _.isObject( e.timezone ) )
						hasTimezone = true;
					else
						hasTimezone = false;

					if( hasTimezone ){

						var clientTimezone = $pwDate.getTimezone();
						var eventTimezone = $pwDate.getTimezone( e.timezone.time_zone_id );
					
						var eventStart = moment.tz($scope.e.date.start_date, e.timezone.time_zone_id );
						var eventEnd = moment.tz($scope.e.date.end_date, e.timezone.time_zone_id );

						$log.debug( 'eventStart : AT EVENT LOCATION', eventStart.format() );
						$log.debug( 'eventEnd : AT EVENT LOCATION', eventEnd.format() );

						var clientTimezone = $pwDate.getTimezone();
						var eventStartClient = eventStart.clone().tz( clientTimezone.name );
						var eventEndClient = eventEnd.clone().tz( clientTimezone.name );

						$log.debug( 'eventStartClient : AT CLIENT LOCATION', eventStartClient.format() );
						$log.debug( 'eventEndClient : AT CLIENT LOCATION', eventEndClient.format() );

						$scope.eventObj = {

							eventTimezone: eventTimezone,
							eventStart: eventStart.format(),
							eventEnd: eventEnd.format(),

							clientTimezone: clientTimezone,
							eventStartClient: eventStartClient.format(),
							eventEndClient: eventEndClient.format(),

							hasTimezone: hasTimezone,

						};

					}


				}, 1);

		}
	};
}])