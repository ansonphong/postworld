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

						$log.debug( 'pwEvent : eventStart : AT EVENT LOCATION', eventStart.format() );
						$log.debug( 'pwEvent : eventEnd : AT EVENT LOCATION', eventEnd.format() );

						var clientTimezone = $pwDate.getTimezone();
						var eventStartClient = eventStart.clone().tz( clientTimezone.name );
						var eventEndClient = eventEnd.clone().tz( clientTimezone.name );

						$log.debug( 'pwEvent : eventStartClient : AT CLIENT LOCATION', eventStartClient.format() );
						$log.debug( 'pwEvent : eventEndClient : AT CLIENT LOCATION', eventEndClient.format() );

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