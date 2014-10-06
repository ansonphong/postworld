var depInject = [
    'postworld',
    //'ngResource',
    //'ngSanitize'
    ];

var infinite = angular.module( 'infinite', depInject );

infinite.run( [ '$log', function( $log ) {
    $log.debug("Infinite : RUN");
}]);
