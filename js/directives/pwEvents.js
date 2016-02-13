'use strict';
/**
 * @ngdoc directive
 * @name postworld.directive:pwEvent
 *
 * @restrict A
 * @description Interprets a Postworld Event object into data for display to the client.
 *
 * @param {Expression} pwEvent A Postworld event object.
 * @param {Expression} eventObj An object to map the interpreted data to.
 *
 * @example
 * ```<pre><div pw-event="post.post_meta.pw_event" event-obj="myEvent">{{ myEvent | json }}</div></pre>```
 *
 */
postworld.directive('pwEvent',
	[
		'$_',
		'$pwDate',
		'$log',

	function( $_, $pwDate, $log ) {

	return {
		restrict: 'A',
		scope:{
			e:'=pwEvent',
			eventObj:'=',
		},
		link: function( $scope, element, attrs ) {
			
			$scope.$watch( 'e',
				function( e ){
					$log.debug( 'pwEvent : $WATCH', $scope.e );
					$scope.eventObj = {};

					/**
					 * Check if the event object has a timezone
					 * And set a boolean.
					 */
					var hasTimezone = _.isObject( e.timezone );

					///// CLIENT /////
					// Get the client and event timezone IDs
					var clientTimezone = $pwDate.getTimezone();
					
					if( hasTimezone ){

						///// EVENT /////
						var eventTimezone = $pwDate.getTimezone( e.timezone.time_zone_id );
						// Get the respective Javascript date objects
						var eventStart = moment.tz($scope.e.date.start_date, e.timezone.time_zone_id );
						var eventEnd = moment.tz($scope.e.date.end_date, e.timezone.time_zone_id );
						$log.debug( 'pwEvent : eventStart : AT EVENT LOCATION', eventStart.format() );
						$log.debug( 'pwEvent : eventEnd : AT EVENT LOCATION', eventEnd.format() );

						///// CLIENT /////
						// Get the respective Javascript date objects
						var eventStartClient = eventStart.clone().tz( clientTimezone.name );
						var eventEndClient = eventEnd.clone().tz( clientTimezone.name );
						$log.debug( 'pwEvent : eventStartClient : AT CLIENT LOCATION', eventStartClient.format() );
						$log.debug( 'pwEvent : eventEndClient : AT CLIENT LOCATION', eventEndClient.format() );

						// Package it into a scope object
						$scope.eventObj = {

							eventTimezone: eventTimezone,
							eventStart: eventStart.format(),
							eventEnd: eventEnd.format(),

							eventStartClient: eventStartClient.format(),
							eventEndClient: eventEndClient.format(),

							eventStartClientCountdownMs: ( eventStartClient - new Date() ),
							eventEndClientCountdownMs:  ( eventEndClient - new Date() ),

							eventStartClientCountdownS: parseInt(( eventStartClient - new Date() )/1000, 10),
							eventEndClientCountdownS:  parseInt(( eventEndClient - new Date() )/1000, 10),

						};

					}

					$scope.eventObj.clientTimezone = clientTimezone;
					$scope.hasTimezone = hasTimezone;


				}, 1);

		}
	};
}])